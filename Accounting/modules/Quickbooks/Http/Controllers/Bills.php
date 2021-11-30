<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Traits\Modules;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Common\Contact;
use App\Models\Common\ItemTax;
use App\Models\Document\Document;
use App\Abstracts\Http\Controller;
use App\Jobs\Common\CreateContact;
use Illuminate\Support\Facades\Date;
use App\Jobs\Document\CreateDocument;
use App\Jobs\Document\UpdateDocument;
use Illuminate\Support\Facades\Cache;
use Modules\DoubleEntry\Models\Account;
use Modules\Quickbooks\Models\Common\Item;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\Quickbooks\Traits\QuickbooksTransformer;
use App\Jobs\Banking\CreateBankingDocumentTransaction;

class Bills extends Controller
{
    use Modules, QuickbooksRemote, QuickbooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-purchases-bills')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-purchases-bills')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-purchases-bills')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-purchases-bills')->only('destroy');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function count()
    {
        $success = true;
        $error = false;
        $message = null;
        $pages = [];

        Cache::set('quickbooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getBillsCount();
        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.bills.sync', $i),
                ];
            }
        }

        Cache::set('quickbooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('quickbooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('quickbooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'bills';

        $html = view('quickbooks::partial.sync', compact('type', 'total'))->render();

        return response()->json([
            'success' => $success,
            'error' => $error,
            'message' => $message,
            'count' => $total,
            'pages' => $pages,
            'html' => $html,
        ]);
    }

    public function sync()
    {
        $steps = [];

        $bills = $this->getBills();

        foreach ($bills as $bill) {

            $cached[$bill->Id] = $bill;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type' => trans_choice('quickbooks::general.types.bills', 1),
                        'value' => $bill->Id,
                    ]
                ),
                'url' => route('quickbooks.sync.bills.store'),
                'id' => $bill->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_bills_' . company_id(), $cached, Date::now()->addHours(6));
        }

        return response()->json([
            'errors' => false,
            'success' => true,
            'steps' => $steps,
        ]);
    }

    /**
     * Enable the specified resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function store(Request $request)
    {
        $bills = Cache::get('quickbooks_bills_' . company_id());

        $bill = $bills[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $bill_number = $this->prepareBillNumber($bill->Id);

        // Get vendor
        $vendor = Contact::where('reference', 'quickbooks id: ' . $bill->VendorRef)->first();

        // Create vendor if not available
        if (empty($vendor)) {
            $ak_vendor_data = $this->getOne('Vendor', $bill->VendorRef);

            $vendor_prepared = $this->prepareContactData($ak_vendor_data, 'vendor');

            $vendor = $this->dispatch(new CreateContact($vendor_prepared));
        }

        $ak_bill_data = [
            'type' => 'bill',
            'company_id' => company_id(),
            'category_id' => setting('default.expense_category'),
            'document_number' => $bill_number,
            'order_number' => $bill->DocNumber,
            'status' => 'draft',
            'issued_at' => $bill->MetaData->CreateTime,
            'due_at' => $bill->DueDate ?? null,
            'currency_code' => setting('default.currency'),
            'currency_rate' => 1,
            'notes' => $bill->CustomerMemo->value ?? null,
            'footer' => "",

            // Customer data
            'contact_id' => $vendor->id,
            'contact_name' => $vendor->name,
            'contact_email' => $vendor->email,
            'contact_address' => $vendor->address,

            'items' => [],
        ];

        //QBO have all taxes in one place and related to all items
        $tax_ids = [];

        if (!empty($bill->TxnTaxDetail->TaxLine)) {
            foreach ($bill->TxnTaxDetail->TaxLine as $tax) {
                if (isset($tax->TaxLineDetail->TaxRateRef)) {
                    $qb_tax = $this->getOne('TaxRate', $tax->TaxLineDetail->TaxRateRef);

                    $ak_tax_data = $this->prepareTaxData($qb_tax);

                    $ak_tax = Tax::where('name', $qb_tax->Name)->first();

                    if (empty($ak_tax)) {
                        $ak_tax = $this->dispatch(new CreateTax($ak_tax_data));
                    }

                    $tax_ids[] = $ak_tax->id;
                }
            }
        }

        // Add items
        $items = Arr::wrap($bill->Line);

        foreach ($items as $item) {
            $ak_item = null;

            // Get Akaunting item
            if (isset($item->ItemBasedExpenseLineDetail->ItemRef)) {
                $ak_item = Item::where('sku', $item->ItemBasedExpenseLineDetail->ItemRef)->first();

                if (!$ak_item) {
                    $product_data = $this->getOne('Item', $item->ItemBasedExpenseLineDetail->ItemRef);

                    $ak_item_data = $this->prepareItemData($product_data);

                    if ($this->moduleIsEnabled('double-entry') && (isset($product_data->IncomeAccountRef) || isset($product_data->ExpenseAccountRef))) {
                        if (isset($product_data->IncomeAccountRef) && !is_null($product_data->IncomeAccountRef)) {
                            $de_income_account_id = Account::where('code', 'QBO-' . $product_data->IncomeAccountRef)->pluck('id')->first();

                            $ak_item_data['de_income_account_id'] = $de_income_account_id;
                        }

                        if (isset($product_data->ExpenseAccountRef) && !is_null($product_data->ExpenseAccountRef)) {
                            $de_expense_account_id = Account::where('code', 'QBO-' . $product_data->ExpenseAccountRef)->pluck('id')->first();

                            $ak_item_data['de_expense_account_id'] = $de_expense_account_id;
                        }
                    }

                    $ak_item = Item::create($ak_item_data);

                    if (!empty($tax_ids)) {
                        $this->createItemTaxes($ak_item, $tax_ids);
                    }
                }
            } else {
                $ak_item = Item::where('sku', '-qbo-na-')->first();

                if (!$ak_item) {
                    $ak_item = Item::create([
                        'company_id' => company_id(),
                        'name' => !empty($item->Description) ? $item->Description : '-NA-',
                        'sku' => '-qbo-na-',
                        'quantity' => 0,
                        'category_id' => null,
                        'description' => $item->Description,
                        'sale_price' => 0,
                        'purchase_price' => 0,
                        'enabled' => 1,
                        'tax_ids' => [],
                    ]);
                }
            }

            $account_id = null;

            if ($this->moduleIsEnabled('double-entry') && isset($item->ItemBasedExpenseLineDetail->ItemAccountRef)
                && !is_null($item->ItemBasedExpenseLineDetail->ItemAccountRef)) {
                $account_id = Account::where('code', 'QBO-' . $item->ItemBasedExpenseLineDetail->ItemAccountRef)->pluck('id')->first();
            } else if ($this->moduleIsEnabled('double-entry') && isset($item->AccountBasedExpenseLineDetail->AccountRef)
            && !is_null($item->AccountBasedExpenseLineDetail->AccountRef)) {
                $account_id = Account::where('code', 'QBO-' . $item->AccountBasedExpenseLineDetail->AccountRef)->pluck('id')->first();
            }

            if (!is_null($ak_item)) {
                $ak_bill_data['items'][] = [
                    'item_id' => $ak_item->id,
                    'name' => $ak_item->name,
                    'quantity' => !empty($item->ItemBasedExpenseLineDetail->Qty) ? $item->ItemBasedExpenseLineDetail->Qty : 1,
                    'sku' => $ak_item->sku,
                    'price' => $item->Amount,
                    'total' => $item->Amount,
                    'description' => $item->Description ?? $ak_item->description,
                    'tax_ids' => $tax_ids,
                    'de_account_id' => $account_id,
                ];
            }
        }

        if (!empty($ak_bill_data['items'])) {
            $ak_bill = Document::bill()->where('document_number', $bill_number)->first();

            try {
                if (empty($ak_bill)) {
                    $ak_bill = $this->dispatch(new CreateDocument($ak_bill_data));
                } else {
                    $ak_bill = $this->dispatch(new UpdateDocument($ak_bill, $ak_bill_data));
                }
            } catch (\Exception $e) {
                report($e);
            }
        }

        $syncCount = Cache::get('quickbooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('quickbooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('quickbooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('quickbooks::general.finished', [
                'type' => trans_choice('quickbooks::general.types.bills', 2)
            ]);

            setting()->set('quickbooks.last_check', $timestamp);
            setting()->save();

            Cache::set('quickbooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }

    public function createItemTaxes($item, $item_data)
    {
        if (empty($item_data)) {
            return;
        }

        foreach ($item_data as $tax_id) {
            ItemTax::create([
                'company_id' => $item->company_id,
                'item_id' => $item->id,
                'tax_id' => $tax_id,
            ]);
        }
    }
}
