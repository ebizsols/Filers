<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Jobs\Banking\CreateBankingDocumentTransaction;
use App\Jobs\Common\CreateContact;
use App\Jobs\Document\CreateDocument;
use App\Jobs\Document\UpdateDocument;
use App\Jobs\Setting\CreateTax;
use App\Models\Common\Contact;
use App\Models\Common\ItemTax;
use App\Models\Document\Document;
use App\Models\Setting\Tax;
use App\Traits\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Modules\DoubleEntry\Models\Account;
use Modules\Zohobooks\Models\Common\Item;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Bills extends Controller
{
    use Modules, ZohoBooksRemote, ZohoBooksTransformer;

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

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->bills->getTotal();
        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.bills.sync', $i),
                ];
            }
        }

        Cache::set('zohobooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('zohobooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('zohobooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'bills';

        $html = view('zohobooks::partial.sync', compact('type', 'total'))->render();

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

        $bills = $this->getClient()->bills->getList();

        foreach ($bills as $bill) {
            $cached[$bill->bill_id] = $this->getClient()->bills->get($bill->bill_id)->toArray();

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type' => trans_choice('zohobooks::general.types.bills', 1),
                        'value' => $this->prepareBillNumber($bill->bill_id),
                    ]
                ),
                'url' => route('zohobooks.sync.bills.store'),
                'id' => $bill->bill_id,
            ];
        }

        if (isset($cached)) {
            Cache::set('zohobooks_bills_' . company_id(), $cached, Date::now()->addHours(6));
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
        $bills = Cache::get('zohobooks_bills_' . company_id());

        $bill = $bills[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $bill_number = $this->prepareBillNumber($bill['bill_id']);

        // Get vendor
        $vendor = Contact::where('reference', 'zohobooks id: ' . $bill['vendor_id'])->first();

        // Create vendor if not available
        if (empty($vendor)) {
            $ak_vendor_data = $this->getClient()->contacts->get($bill['vendor_id']);

            $vendor_prepared = $this->prepareContactData($ak_vendor_data, $ak_vendor_data->contact_type);

            $vendor = $this->dispatch(new CreateContact($vendor_prepared));
        }

        $ak_bill_data = [
            'type' => 'bill',
            'company_id' => company_id(),
            'category_id' => setting('default.expense_category'),
            'document_number' => $bill_number,
            'status' => $bill['status'],
            'issued_at' => $bill['created_time'],
            'due_at' => $bill['due_date'],
            'currency_code' => setting('default.currency'),
            'currency_rate' => 1,
            'notes' => $bill['notes'],
            'footer' => "",

            // Customer data
            'contact_id' => $vendor->id,
            'contact_name' => $vendor->name,
            'contact_email' => $vendor->email,
            'contact_address' => $vendor->address,

            'items' => [],
        ];

        foreach ($bill['line_items'] as $item) {

            $tax_ids = [];

            if ($item['tax_id'] != "") {

                $ak_tax = Tax::where('name', $item['tax_name'])->first();

                if (empty($ak_tax)) {

                    $ak_tax_data = $this->prepareTaxData((object)
                    ['tax_name' => $item['tax_name'],
                        'tax_percentage' => $item['tax_percentage']]);
                    $ak_tax = $this->dispatch(new CreateTax($ak_tax_data));
                }

                $tax_ids[] = $ak_tax->id;
            }

            $ak_item = NULL;
            // Get Akaunting item
            if ($item['item_id'] != "") {
                $ak_item = Item::where('sku', $item['item_id'])->first();
            }

            // Create item if not available
            if (!$ak_item) {
                if ($item['item_id'] != "") {
                    $product_data = $this->getClient()->items->get($item['item_id']);

                } else {
                    $product_data = (object)[
                        'tax_id' => $item['tax_id'],
                        'name' => $item['description'],
                        'description' => $item['description'],
                        'rate' => $item['rate'],
                        'purchase_rate' => $item['bcy_rate'],
                        'account_name' => $item['account_name'],
                        'purchase_account_name' => NULL,
                        'purchase_account_id' => NULL,
                        'status' => 1,
                        'item_id' => NULL,
                    ];
                }
                $ak_item_data = $this->prepareItemData($product_data);
                $ak_item_data['tax_ids'] = $tax_ids;

                $ak_item = Item::create($ak_item_data);

                if ($this->moduleIsEnabled('double-entry') && $item['purchase_account_id'] != "") {
                    $de_expense_account_id = Account::where('code', 'ZOHOBOOK-' . $item['purchase_account_id'])->pluck('id')->first();

                    $product['de_expense_account_id'] = $de_expense_account_id;
                }

                if (!empty($tax_ids)) {
                    $this->createItemTaxes($ak_item, $tax_ids);
                }
            }

            $account_id = null;

            if ($ak_item) {
                if ($this->moduleIsEnabled('double-entry') && $item['account_id'] != "" && $item['account_name'] != "") {
                    $account_id = Account::where('code', 'ZOHOBOOK-' . $item['account_id'])->pluck('id')->first();
                }

                $ak_bill_data['items'][] = [
                    'item_id' => $ak_item->id,
                    'name' => $ak_item->name,
                    'quantity' => $item['quantity'],
                    'sku' => $item['line_item_id'],
                    'price' => $item['rate'],
                    'total' => $item['item_total'],
                    'discount' => $item['discount'],
                    'description' => $item['description'],
                    'tax_ids' => $tax_ids,
                    'de_account_id' => $account_id,
                ];
            }
        }

        $ak_bill = Document::bill()->where('document_number', $bill_number)->first();

        try {
            if (empty($ak_bill)) {
                $ak_bill = $this->dispatch(new CreateDocument($ak_bill_data));
            } else {
                $ak_bill = $this->dispatch(new UpdateDocument($ak_bill, $ak_bill_data));
            }

            $payments = $this->getClient()->bills->getPayments($bill['bill_id']);
            $this->makePaymentForBill($payments, $ak_bill);

        } catch (\Exception $e) {
            report($e);
        }

        $syncCount = Cache::get('zohobooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('zohobooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('zohobooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('zohobooks::general.finished', [
                'type' => trans_choice('zohobooks::general.types.bills', 2)
            ]);

            setting()->set('zohobooks.last_check', $timestamp);
            setting()->save();

            Cache::set('zohobooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
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

    private function makePaymentForBill($payment_items, $ak_bill)
    {
        foreach ($payment_items as $item) {
            if($item->amount>0) {
                $ak_payment_data = [
                    'company_id' => company_id(),
                    'category_id' => setting('default.expense_category'),
                    'type' => 'expense',
                    'contact_id' => $ak_bill->contact_id,
                    'account_id' => setting('default.account'),
                    'currency_code' => $ak_bill->currency_code,
                    'currency_rate' => 1,
                    'amount' => $item->amount,
                    'paid_at' => $item->date,
                    'description' => $item->description,
                    'reference' => $item->payment_id,
                    'payment_method' => setting('default.payment_method'),
                ];

                try {
                    $this->dispatch(new CreateBankingDocumentTransaction($ak_bill, $ak_payment_data));
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
    }
}
