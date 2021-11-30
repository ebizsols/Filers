<?php

namespace Modules\Zohobooks\Http\Controllers;

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
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\DoubleEntry\Models\Account;
use Modules\Zohobooks\Models\Common\Item;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Sync extends Controller
{
    use Modules, ZohoBooksRemote, ZohoBooksTransformer;

    public function taxes()
    {
        $taxes = $this->getClient()->settings->taxes->getList();

        foreach ($taxes as $tax) {
            $ak_tax_data = $this->prepareTaxData($tax);

            $ak_tax = Tax::where('name', $tax->tax_name)->first();

            try {
                if ($ak_tax) {
                    $ak_tax = $this->dispatch(new UpdateTax($ak_tax, $ak_tax_data));
                } else {
                    $ak_tax = $this->dispatch(new CreateTax($ak_tax_data));
                }
            } catch (\Exception $e) {
                report($e);

                // @todo display to user
                continue;
            }
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans('zohobooks::general.form.sync.taxes'),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }


    public function products()
    {
        $items = $this->getClient()->items->getList();

        foreach ($items as $item) {
            $ak_item_data = $this->prepareItemData($item);

            $ak_item = Item::where('sku', $ak_item_data['id'])->first();

            $ak_item_data['de_account_id'] = NULL;

            if ($this->moduleIsEnabled('double-entry') && ($ak_item_data['purchase_account_name'] != "" || $ak_item_data['account_name'] != "")) {
                if ($ak_item_data['account_name'] != "") {
                    $de_income_account_id = Account::where('code', 'ZOHOBOOK-' . $ak_item_data['account_name'])->pluck('id')->first();

                    $ak_item_data['de_income_account_id'] = $de_income_account_id;
                }

                if ($ak_item_data['purchase_account_name'] != "") {
                    $de_expense_account_id = Account::where('code', 'ZOHOBOOK-' . $ak_item_data['purchase_account_name'])->pluck('id')->first();

                    $ak_item_data['de_expense_account_id'] = $de_expense_account_id;
                }
            }
            try {
                if (empty($ak_item)) {
                    $ak_item = Item::create($ak_item_data);
                } else {
                    $ak_item = $ak_item->update($ak_item_data);
                }
            } catch (\Exception $e) {
                report($e);
            }
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans('zohobooks::general.form.sync.products'),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }

    public function customers()
    {
        $customers = $this->getClient()->contacts->getList();

        foreach ($customers as $customer) {

            $ak_customer_data = $this->prepareContactData($customer, $customer->contact_type);

            $ak_customer = Contact::where('reference', $ak_customer_data['reference'])->first();

            try {
                if (empty($ak_customer)) {
                    $ak_customer = $this->dispatch(new CreateContact($ak_customer_data));
                } else {
                    $ak_customer = $this->dispatch(new UpdateContact($ak_customer, $ak_customer_data));
                }
            } catch (\Exception $e) {
                report($e);

                // @todo display to user
                continue;
            }
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans('zohobooks::general.form.sync.customers'),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }


    public function invoices()
    {
        $invoices = $this->getClient()->invoices->getList();

        foreach ($invoices as $invoice) {
            $invoice = $this->getClient()->invoices->get($invoice->invoice_id)->toArray();
            $invoice_number = $this->prepareInvoiceNumber($invoice->invoice_id);

            // Get customer
            $customer = Contact::where('reference', 'zohobooks id: ' . $invoice['customer_id'])->first();

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

            $ak_invoice = Document::invoice()->where('document_number', $invoice_number)->first();

            try {
                if (empty($ak_invoice)) {
                    $ak_invoice = $this->dispatch(new CreateDocument($ak_invoice_data));
                } else {
                    $ak_invoice = $this->dispatch(new UpdateDocument($ak_invoice, $ak_invoice_data));
                }

                $payments = $this->getClient()->invoices->getPayments($invoice['invoice_id']);
                if (count($payments->items) > 0) {
                    $this->makePaymentForInvoice($payments->items, $ak_invoice);
                }

            } catch (\Exception $e) {
                report($e);
            }
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans('zohobooks::general.form.sync.invoices'),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }

    private function makePaymentForInvoice($payment_items, $ak_invoice)
    {
        foreach($payment_items as $item) {
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

    public function bills()
    {
        $bills = $this->getClient()->bills->getList();

        foreach ($bills as $bill) {
            $invoice = $this->getClient()->bills->get($bill->bill_id)->toArray();
            $bill_number = $this->prepareBillNumber($invoice->bill_id);

            // Get customer
            $vendor = Contact::where('reference', 'zohobooks id: ' . $invoice['vendor_id'])->first();

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
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans('zohobooks::general.form.sync.bills'),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }

    private function makePaymentForBill($payment_items, $ak_bill)
    {
        foreach($payment_items as $item) {
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

    public function coa()
    {
        $accounts = $this->getClient()->chartofaccounts->getList();
        $de_accounts = Account::all();

        foreach ($accounts as $account) {
            $ak_account_data = $this->prepareCoaData($account);

            $ak_account = $de_accounts->first(function ($account) use ($ak_account_data) {
                return $account->type_id == $ak_account_data['type_id'] && trans($account->name) == $ak_account_data['name'];
            });

            if ($this->isNotValidAccount($ak_account_data, $ak_account)) {
                continue;
            }

            try {
                if (empty($ak_account)) {
                    $ak_account = $this->dispatch(new CreateAccount($ak_account_data));
                } else {
                    $ak_account = $this->dispatch(new UpdateAccount($ak_account, $ak_account_data));
                }
            } catch (\Exception $e) {
                report($e);
            }
        }

        $message = trans(
            'zohobooks::general.finished',
            [
                'type' => trans_choice('zohobooks::general.form.sync.coa', 2),
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }


}
