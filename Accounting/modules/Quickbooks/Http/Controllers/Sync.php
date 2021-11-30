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
use App\Models\Document\Document;
use App\Models\Setting\Tax;
use App\Traits\Modules;
use Illuminate\Support\Str;
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\DoubleEntry\Models\Account;
use Modules\Quickbooks\Models\Common\Item;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\Quickbooks\Traits\QuickbooksTransformer;

class Sync extends Controller
{
    use Modules, QuickbooksRemote, QuickbooksTransformer;

    public function taxes()
    {
        $taxes = $this->getSalesTaxes();

        foreach ($taxes as $tax) {
            $ak_tax_data = $this->prepareTaxData($tax->node);

            $ak_tax = Tax::where('name', $tax->node->name)->first();

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
            'quickbooks::general.finished',
            [
                'type' => trans('quickbooks::general.form.sync.taxes'),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }


    public function products()
    {
        $products = $this->getProducts();

        foreach ($products as $product) {

            if($product->Type == 'Category'){
                $ak_category_data = $this->prepareCategoryData($product);

                $ak_category = Category::where('name', $product->Name)->first();

                try {
                    if (empty($ak_category)) {
                        $ak_category = Category::create($ak_category_data);
                    }
                } catch (\Exception $e) {
                    report($e);
                }

            }else{
                $ak_category = null;
                if(!is_null($product->ParentRef)){

                    $parent = $this->getOne("Item", $product->ParentRef);
                    $ak_category_data = $this->prepareCategoryData($parent);

                    $ak_category = Category::where('name', $parent->Name)->first();
                    if (empty($ak_category)) {
                        $ak_category = Category::create($ak_category_data);
                    }
                }

                $ak_item_data = $this->prepareItemData($product, $ak_category);

                $ak_item = Item::where('sku', $product->Id)->first();

                $ak_item_data['de_account_id'] = NULL;

                if ($this->moduleIsEnabled('double-entry') && (isset($product->ExpenseAccountRef) || isset($product->AssetAccountRef))) {
                    if (isset($product->ExpenseAccountRef) && !is_null($product->ExpenseAccountRef)) {
                        $account_id = Account::where('code', 'QBO-' . $product->ExpenseAccountRef)->pluck('id')->first();
                        $ak_item_data['de_account_id'] = $account_id;
                    } else if (isset($product->AssetAccountRef) && !is_null($product->AssetAccountRef)) {
                        $account_id = Account::where('code', 'QBO-' . $product->AssetAccountRef)->pluck('id')->first();
                        $ak_item_data['de_account_id'] = $account_id;
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
        }

        $message = trans(
            'quickbooks::general.finished',
            [
                'type' => trans('quickbooks::general.form.sync.products'),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }

    public function customers()
    {
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            $ak_customer_data = $this->prepareContactData($customer, 'customer');

            $ak_customer = Contact::where('reference', 'quickbooks id: ' . $customer->Id)->first();

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
            'quickbooks::general.finished',
            [
                'type' => trans('quickbooks::general.form.sync.customers'),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }

    public function vendors()
    {
        $vendors = $this->getVendors();

        foreach ($vendors as $vendor) {
            $ak_vendor_data = $this->prepareContactData($vendor, 'vendor');

            $ak_vendor = Contact::where('reference', 'quickbooks id: ' . $vendor->Id)->first();

            try {
                if (empty($ak_contact)) {
                    $ak_vendor = $this->dispatch(new CreateContact($ak_vendor_data));
                } else {
                    $ak_vendor = $this->dispatch(new UpdateContact($ak_vendor, $ak_vendor_data));
                }
            } catch (\Exception $e) {
                report($e);

                // @todo display to user
                continue;
            }
        }

        $message = trans(
            'quickbooks::general.finished',
            [
                'type' => trans('quickbooks::general.form.sync.vendors'),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }

    public function invoices()
    {
        $invoices = $this->getInvoices();

        foreach ($invoices as $invoice) {
            $invoice_number = $this->prepareInvoiceNumber($invoice->Id);

            // Get customer
            $customer = Contact::where('reference', 'quickbooks id: ' . $invoice->CustomerRef->value)->first();

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
                    $qb_tax = $this->getOne('TaxRate', $tax->TaxLineDetail->TaxRateRef);

                    $ak_tax_data = $this->prepareTaxData($qb_tax);

                    $ak_tax = Tax::where('name', $qb_tax->Name)->first();

                    if (empty($ak_tax)) {
                        $ak_tax = $this->dispatch(new CreateTax($ak_tax_data));
                    }
                    $tax_ids[] = $ak_tax->id;
                }
            }

            // Add items
            foreach ($invoice->Line as $item) {
                $ak_item = null;
                if (isset($item->SalesItemLineDetail->ItemRef)) {
                    // Get Akaunting item
                    $ak_item = Item::where('sku', $item->SalesItemLineDetail->ItemRef)->first();

                    $account_id = null;

                    if ($this->moduleIsEnabled('double-entry') && isset($item->SalesItemLineDetail->ItemAccountRef)
                        && !is_null($item->SalesItemLineDetail->ItemAccountRef)) {
                        $account_id = Account::where('code', 'QBO-' . $item->ItemAccountRef)->pluck('id')->first();
                    }

                    $ak_invoice_data['items'][] = [
                        'item_id' => $ak_item->id,
                        'name' => $ak_item->name,
                        'quantity' => $item->SalesItemLineDetail->Qty,
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
        }

        $message = trans(
            'quickbooks::general.finished',
            [
                'type' => trans('quickbooks::general.form.sync.invoices'),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
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

    public function bills()
    {
        $bills = $this->getBills();

        foreach ($bills as $bill) {
            $bill_number = $this->prepareBillNumber($bill->Id);

            // Get vendor
            $vendor = Contact::where('reference', 'quickbooks id: ' . $bill->VendorRef)->first();

            $ak_bill_data = [
                'type' => 'bill',
                'company_id' => company_id(),
                'category_id' => setting('default.expense_category'),
                'document_number' => $bill_number,
                'order_number' => $bill->Id,
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
                    if(isset($tax->TaxLineDetail->TaxRateRef)) {
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
            foreach ($bill->Line as $item) {
                $ak_item = null;
                if (isset($item->ItemBasedExpenseLineDetail->ItemRef)) {
                    $ak_item = Item::where('sku', $item->ItemBasedExpenseLineDetail->ItemRef)->first();
                    if (!$ak_item) {
                        $product_data = $this->getOne('Item', $item->ItemBasedExpenseLineDetail->ItemRef);

                        $ak_item_data = $this->prepareItemData($product_data);
                        $ak_item = Item::create($ak_item_data);
                    }
                }

                $account_id = null;

                if ($this->moduleIsEnabled('double-entry') && isset($item->ItemBasedExpenseLineDetail->ItemAccountRef)
                    && !is_null($item->ItemBasedExpenseLineDetail->ItemAccountRef)) {
                    $account_id = Account::where('code', 'QBO-' . $item->ItemBasedExpenseLineDetail->ItemAccountRef)->pluck('id')->first();
                }

                if (!is_null($ak_item)) {
                    $ak_bill_data['items'][] = [
                        'item_id' => $ak_item->id,
                        'name' => $ak_item->name,
                        'quantity' => $item->ItemBasedExpenseLineDetail->Qty,
                        'sku' => $ak_item->sku,
                        'price' => $item->Amount,
                        'total' => $item->Amount,
                        'description' => $item->Description ?? $ak_item->description,
                        'tax_ids' => $tax_ids,
                        'de_account_id' => $account_id,
                    ];
                }
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

        $message = trans(
            'quickbooks::general.finished',
            ['type' => trans('quickbooks::general.form.sync.bills'),]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }

    public function coa()
    {
        $accounts = $this->getAccounts();
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
            'quickbooks::general.finished',
            [
                'type' => trans_choice('quickbooks::general.form.sync.coa', 2),
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }


}
