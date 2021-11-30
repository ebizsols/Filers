<?php

namespace Modules\Quickbooks\Traits;

use Carbon\Carbon;

trait QuickbooksTransformer
{
    use DoubleEntry;

    public function prepareTaxData($tax)
    {
        return [
            'company_id' => company_id(),
            'name' => $tax->Name,
            'rate' => $tax->RateValue,
            'type' => 'normal',
            'enabled' => ($tax->Active) ? 1 : 0,
        ];
    }

    public function prepareCategoryData($product)
    {
        return [
            'company_id' => company_id(),
            'name' => $product->Name,
            'type' => 'item',
            'color' => '#328aef',
            'enabled' => ($product->Active) ? 1 : 0,
        ];
    }

    public function prepareItemData($product, $category = null)
    {
        return [
            'company_id' => company_id(),
            'name' => $product->Name,
            'sku' => $product->Id,
            'quantity' => $product->QtyOnHand,
            'category_id' => $category->id ?? null,
            'description' => $product->Description,
            'sale_price' => $product->UnitPrice,
            'purchase_price' => $product->PurchaseCost,
            'enabled' => ($product->Active) ? 1 : 0,
            'tax_ids' => [],
        ];
    }

    public function prepareContactData($contact, $type)
    {
        $address = "";

        if ($contact->BillAddr) {
            $address = trim(implode(
                PHP_EOL,
                [
                    $contact->BillAddr->Line1,
                    $contact->BillAddr->Line2,
                    $contact->BillAddr->City,
                    $contact->BillAddr->PostalCode,
                ]
            ));
        }

        return [
            'company_id' => company_id(),
            'type' => $type,
            'name' => $contact->DisplayName,
            'email' => $contact->PrimaryEmailAddr->Address ?? null,
            'phone' => $contact->PrimaryPhone->FreeFormNumber ?? null,
            'address' => $address,
            'currency_code' => setting('default.currency'),
            'enabled' => ($contact->Active) ? 1 : 0,
            'reference' => 'quickbooks id: ' . $contact->Id,
        ];
    }

    public function prepareInvoiceNumber($number)
    {
        $prefix = setting('invoice.number_prefix');
        $digit = setting('invoice.number_digit');

        return $prefix . str_pad($number, $digit, '0', STR_PAD_LEFT);
    }

    public function getInvoiceStatus($invoice)
    {
        $status = 'draft';
        if ($invoice->Balance > 0 && is_null($invoice->LinkedTxn)) {
            if ($invoice->EmailStatus == 'EmailSent') {
                $status = 'sent';
            }
            if (now()->diffInDays(Carbon::createFromFormat('Y-m-d', $invoice->DueDate), true) > 0) {
                $status = 'overdue';
            }
        } else {
            $status = 'paid';
        }

        return $status;
    }

    public function prepareBillNumber($number)
    {
        $prefix = setting('bill.number_prefix');
        $digit = setting('bill.number_digit');

        return $prefix . str_pad($number, $digit, '0', STR_PAD_LEFT);
    }

    public function prepareCoaData($account)
    {
        return [
            'company_id' => company_id(),
            'type_id' => $this->getAccountType($account->AccountType),
            'code' => 'QBO-' . $account->Id,
            'name' => $account->Name,
            'description' => $account->Description,
            'opening_balance' => $account->CurrentBalance,
            'enabled' => ($account->Active) ? 1 : 0,
            'currency_code' => $account->CurrencyRef,
        ];
    }

}
