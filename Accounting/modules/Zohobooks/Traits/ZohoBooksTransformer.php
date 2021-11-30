<?php

namespace Modules\Zohobooks\Traits;

use App\Models\Setting\Tax;

trait ZohoBooksTransformer
{
    use DoubleEntry;

    public function prepareTaxData($tax)
    {
        return [
            'company_id' => company_id(),
            'name' => $tax->tax_name,
            'rate' => $tax->tax_percentage,
            'type' => 'normal',
            'enabled' => 1,
        ];
    }

    public function prepareItemData($product)
    {
        $tax_ids = [];
        if ($product->tax_id != "") {
            $tax_ids = [Tax::where('name', $product->tax_name)->pluck('id')->first()];
        }
        return [
            'company_id' => company_id(),
            'name' => $product->name,
            'sku' => $product->item_id,
            'quantity' => 1,
            'category_id' => null,
            'description' => $product->description,
            'sale_price' => $product->rate,
            'purchase_price' => $product->purchase_rate,
            'enabled' => ($product->status == 'active') ? 1 : 0,
            'tax_ids' => $tax_ids,
            'account_name' => $product->account_name,
            'purchase_account_name' => $product->purchase_account_name,
            'purchase_account_id' => $product->purchase_account_id,
        ];
    }

    public function prepareContactData($contact, $type)
    {
        return [
            'company_id' => company_id(),
            'type' => $type,
            'name' => $contact->contact_name,
            'email' => $contact->email ?? null,
            'phone' => ($contact->mobile=="")? $contact->phone: $contact->mobile,
            'website' => $contact->website,
            'address' => "",
            'currency_code' => setting('default.currency'),
            'enabled' => ($contact->status=='active') ? 1 : 0,
            'reference' => 'zohobooks id: ' . $contact->contact_id,
            'id' => $contact->contact_id,
        ];
    }

    public function prepareInvoiceNumber($number)
    {
        $prefix = setting('invoice.number_prefix');
        $digit = setting('invoice.number_digit');

        return $prefix . str_pad($number, $digit, '0', STR_PAD_LEFT);
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
            'type_id' => $this->getAccountType($account->account_type),
            'code' => 'ZOHOBOOK-' . $account->account_id,
            'name' => $account->account_name,
            'description' => $account->description,
            'enabled' => ($account->is_active) ? 1 : 0,
            'currency_code' => setting('default.currency'),
        ];
    }

}
