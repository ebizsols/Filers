<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Jobs\Banking\CreateBankingDocumentTransaction;
use App\Jobs\Common\CreateContact;
use App\Jobs\Common\UpdateContact;
use App\Jobs\Document\CreateDocument;
use App\Jobs\Document\UpdateDocument;
use App\Jobs\Setting\CreateTax;
use App\Jobs\Setting\UpdateTax;
use App\Models\Common\Contact;
use App\Models\Common\ItemTax;
use App\Models\Document\Document;
use App\Models\Setting\Tax;
use App\Traits\Modules;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\DoubleEntry\Models\Account;
use Modules\Quickbooks\Models\Common\Item;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\Quickbooks\Traits\QuickbooksTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Invoices extends Controller
{
    use Modules, QuickbooksRemote, QuickbooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-sales-invoices')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-sales-invoices')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-sales-invoices')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-sales-invoices')->only('destroy');
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

        $total = $this->getInvoicesCount();
        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.invoices.sync', $i),
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

        $type = 'invoices';

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

        $invoices = $this->getInvoices();

        foreach ($invoices as $invoice) {

            $cached[$invoice->Id] = $invoice;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type' => trans_choice('quickbooks::general.types.invoices', 1),
                        'value' => $invoice->DocNumber,
                    ]
                ),
                'url' => route('quickbooks.sync.invoices.store'),
                'id' => $invoice->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_invoices_' . company_id(), $cached, Date::now()->addHours(6));
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
        $invoices = Cache::get('quickbooks_invoices_' . company_id());

        $invoice = $invoices[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $invoice_number = $this->prepareInvoiceNumber($invoice->Id);

        // Get customer
        $customer = Contact::where('reference', 'quickbooks id: ' . $invoice->CustomerRef)->first();

        // Create customer if not available
        if (empty($customer)) {
            $ak_contact_data = $this->getOne('Customer', $invoice->CustomerRef);

            $customer_prepared = $this->prepareContactData($ak_contact_data, 'customer');

            $customer = $this->dispatch(new CreateContact($customer_prepared));
        }

        $ak_invoice_data = [
            'type' => 'invoice',
            'company_id' => company_id(),
            'category_id' => setting('default.income_category'),
            'document_number' => $invoice_number,
            'order_number' => $invoice->DocNumber,
            'status' => $this->getInvoiceStatus($invoice),
            'issued_at' => $invoice->MetaData->CreateTime,
            'due_at' => $invoice->DueDate ?? null,
            'currency_code' => setting('default.currency'),
            'currency_rate' => 1,
            'notes' => $invoice->CustomerMemo->value ?? null,
            'footer' => "",

            // Customer data
            'contact_id' => $customer->id,
            'contact_name' => $customer->name,
            'contact_email' => $customer->email,
            'contact_address' => $customer->address,

            'items' => [],
        ];

        //QBO have all taxes in one place and related to all items
        $tax_ids = [];

        if (!empty($invoice->TxnTaxDetail->TaxLine)) {
            foreach ($invoice->TxnTaxDetail->TaxLine as $tax) {
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
        $items = Arr::wrap($invoice->Line);

        foreach ($items as $item) {
            $ak_item = null;
            if (isset($item->SalesItemLineDetail->ItemRef)) {
                // Get Akaunting item
                $ak_item = Item::where('sku', $item->SalesItemLineDetail->ItemRef)->first();

                // Create item if not available
                if (!$ak_item) {
                    $product_data = $this->getOne('Item', $item->SalesItemLineDetail->ItemRef);

                    $ak_item_data = $this->prepareItemData($product_data);
                    $ak_item_data['tax_ids'] = $tax_ids;

                    $ak_item = Item::create($ak_item_data);

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

                    if (!empty($tax_ids)) {
                        $this->createItemTaxes($ak_item, $tax_ids);
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

                if ($this->moduleIsEnabled('double-entry') && isset($item->SalesItemLineDetail->ItemAccountRef)
                    && !is_null($item->SalesItemLineDetail->ItemAccountRef)) {
                    $account_id = Account::where('code', 'QBO-' . $item->SalesItemLineDetail->ItemAccountRef)->pluck('id')->first();
                } else if ($this->moduleIsEnabled('double-entry') && isset($item->AccountBasedExpenseLineDetail->AccountRef)
                    && !is_null($item->AccountBasedExpenseLineDetail->AccountRef)) {
                    $account_id = Account::where('code', 'QBO-' . $item->AccountBasedExpenseLineDetail->AccountRef)->pluck('id')->first();
                }

                $ak_invoice_data['items'][] = [
                    'item_id' => $ak_item->id,
                    'name' => $ak_item->name,
                    'quantity' => !empty($item->SalesItemLineDetail->Qty) ? $item->SalesItemLineDetail->Qty : 1,
                    'sku' => $item->Id,
                    'price' => $item->SalesItemLineDetail->UnitPrice,
                    'total' => $item->Amount,
                    'description' => $item->Description ?? $ak_item->description,
                    'tax_ids' => $tax_ids,
                    'de_account_id' => $account_id,
                ];
            }
        }

        $ak_invoice = Document::invoice()->where('document_number', $invoice_number)->first();

        try {
            if (empty($ak_invoice)) {
                $ak_invoice = $this->dispatch(new CreateDocument($ak_invoice_data));
            } else {
                $ak_invoice = $this->dispatch(new UpdateDocument($ak_invoice, $ak_invoice_data));
            }

            if (!is_null($invoice->LinkedTxn)) {
                if (isset($invoice->LinkedTxn->TxnId)) {
                    $this->makePaymentForInvoice($invoice->LinkedTxn, $ak_invoice);
                } else {
                    foreach ($invoice->LinkedTxn as $paymentItem) {
                        $this->makePaymentForInvoice($paymentItem, $ak_invoice);
                    }
                }
            }

        } catch (\Exception $e) {
            report($e);
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
                'type' => trans_choice('quickbooks::general.types.invoices', 2)
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

    private function makePaymentForInvoice($LinkedTxn, $ak_invoice)
    {
        $revenue = $this->getOne($LinkedTxn->TxnType, $LinkedTxn->TxnId);

        if (isset($revenue->Line->LinkedTxn->TxnId) &&
            Str::endsWith($ak_invoice->document_number, '0' . $revenue->Line->LinkedTxn->TxnId)) {
            $this->linkPaymentAndInvoice($revenue->Line, $revenue, $ak_invoice);
        } else {
            foreach ($revenue->Line as $item) {
                if (isset($item->LinkedTxn->TxnId) &&
                    Str::endsWith($ak_invoice->document_number, '0' . $item->LinkedTxn->TxnId)) {
                    $this->linkPaymentAndInvoice($item, $revenue, $ak_invoice);
                }
            }
        }
    }

    private function linkPaymentAndInvoice($revenueItem, $revenue, $ak_invoice)
    {
        if ($revenueItem->Amount > 0) {
            $ak_payment_data = [
                'company_id' => company_id(),
                'category_id' => setting('default.income_category'),
                'type' => 'income',
                'contact_id' => $ak_invoice->contact_id,
                'account_id' => setting('default.account'),
                'currency_code' => $ak_invoice->currency_code,
                'currency_rate' => 1,
                'amount' => $revenueItem->Amount,
                'paid_at' => $revenue->TxnDate,
                'description' => $revenueItem->Description ?? "",
                'reference' => $revenue->Id,
                'payment_method' => setting('default.payment_method'),
            ];

            try {
                $this->dispatch(new CreateBankingDocumentTransaction($ak_invoice, $ak_payment_data));
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}
