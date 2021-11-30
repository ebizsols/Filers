<?php

namespace Modules\Quickbooks\Traits;

use Modules\DoubleEntry\Models\Type;
use Illuminate\Support\Facades\Validator;

trait DoubleEntry
{
    protected function getAccountType(string $quickbooksTypeValue)
    {
        $map = [
            'Bank' => 'double-entry::types.bank_cash',
            'Other Current Asset' => 'double-entry::types.current_asset',
            'Fixed Asset' => 'double-entry::types.current_asset',
            'Other Asset' => 'double-entry::types.current_asset',
            'Accounts Receivable' => 'double-entry::types.current_asset',
            'Equity' => 'double-entry::types.equity',
            'Expense' => 'double-entry::types.expense',
            'Other Expense' => 'double-entry::types.expense',
            'Cost of Goods Sold' => 'double-entry::types.expense',
            'Accounts Payable' => 'double-entry::types.liability',
            'Credit Card' => 'double-entry::types.liability',
            'Long Term Liability' => 'double-entry::types.liability',
            'Other Current Liability' => 'double-entry::types.liability',
            'Income' => 'double-entry::types.sales',
            'Other Income' => 'double-entry::types.other_income',
        ];

        return Type::where('name', $map[$quickbooksTypeValue])->first()->id;
    }

    protected function isNotValidAccount($account, $existingAccount) {
        $id = null;

        if (!is_null($existingAccount)) {
            $id = $existingAccount->id;
        }

        $validator = Validator::make($account, [
            'name' => 'required|string',
            'code' => 'integer|unique:double_entry_accounts,NULL,' . $id . ',id,company_id,' . $account['company_id'] . ',deleted_at,NULL',
            'type_id' => 'integer',
        ]);

        return $validator->fails();
    }
}
