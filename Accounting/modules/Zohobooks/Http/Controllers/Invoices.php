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

class Invoices extends Controller
{
    use Modules, ZohoBooksRemote, ZohoBooksTransformer;

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

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->invoices->getTotal();
        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.invoices.sync', $i),
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

        $type = 'invoices';

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

        $invoices = $this->getClient()->invoices->getList();

        foreach ($invoices as $invoice) {
            $cached[$invoice->invoice_id] = $this->getClient()->invoices->get($invoice->invoice_id)->toArray();

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type' => trans_choice('zohobooks::general.types.invoices', 1),
                        'value' => $this->prepareInvoiceNumber($invoice->invoice_id),
                    ]
                ),
                'url' => route('zohobooks.sync.invoices.store'),
                'id' => $invoice->invoice_id,
            ];
        }

        if (isset($cached)) {
            Cache::set('zohobooks_invoices_' . company_id(), $cached, Date::now()->addHours(6));
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
        $invoices = Cache::get('zohobooks_invoices_' . company_id());

        $invoice = $invoices[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $invoice_number = $this->prepareInvoiceNumber($invoice['invoice_id']);

        // Get customer
        $customer = Contact::where('reference', 'zohobooks id: ' . $invoice['customer_id'])->first();

        // Create customer if not available
        if (empty($customer)) {
            $ak_contact_data = $this->getClient()->contacts->get($invoice['customer_id']);

            $customer_prepared = $this->prepareContactData($ak_contact_data, $ak_contact_data->contact_type);

            $customer = $this->dispatch(new CreateContact($customer_prepared));
        }

        $ak_invoice_data = [
            'type' => 'invoice',
            'company_id' => company_id(),
            'category_id' => setting('default.income_category'),
            'document_number' => $invoice_number,
            'status' => $invoice['current_sub_status'],
            'issued_at' => $invoice['created_time'],
            'due_at' => $invoice['due_date'],
            'currency_code' => setting('default.currency'),
            'currency_rate' => 1,
            'notes' => $invoice['notes'],
            'footer' => "",

            // Customer data
            'contact_id' => $customer->id,
            'contact_name' => $customer->name,
            'contact_email' => $customer->email,
            'contact_address' => $customer->address,

            'items' => [],
        ];

        // Add items
        foreach ($invoice['line_items'] as $item) {

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

                if ($this->moduleIsEnabled('double-entry') && (isset($item['purchase_account_id'])) && $item['purchase_account_id'] != "") {
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

                $ak_invoice_data['items'][] = [
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

        $ak_invoice = Document::invoice()->where('document_number', $invoice_number)->first();

        try {
            if (empty($ak_invoice)) {
                $ak_invoice = $this->dispatch(new CreateDocument($ak_invoice_data));
            } else {
                $ak_invoice = $this->dispatch(new UpdateDocument($ak_invoice, $ak_invoice_data));
            }

            $payments = $this->getClient()->invoices->getPayments($invoice['invoice_id']);
            $this->makePaymentForInvoice($payments, $ak_invoice);

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
                'type' => trans_choice('zohobooks::general.types.invoices', 2)
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

    private function makePaymentForInvoice($payment_items, $ak_invoice)
    {
        foreach ($payment_items as $item) {
            if($item->amount>0) {
                $ak_payment_data = [
                    'company_id' => company_id(),
                    'category_id' => setting('default.income_category'),
                    'type' => 'income',
                    'contact_id' => $ak_invoice->contact_id,
                    'account_id' => setting('default.account'),
                    'currency_code' => $ak_invoice->currency_code,
                    'currency_rate' => 1,
                    'amount' => $item->amount,
                    'paid_at' => $item->date,
                    'description' => $item->description,
                    'reference' => $item->payment_id,
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
}
