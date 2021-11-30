<?php

namespace Modules\Zohobooks\Traits;

use Modules\DoubleEntry\Models\Type;
use Illuminate\Support\Facades\Validator;

trait DoubleEntry
{
    protected function getAccountType(string $zohobooksTypeValue)
    {
        $map = [
            'bank' => 'double-entry::types.bank_cash',
            'other_current_asset' => 'double-entry::types.current_asset',
            'fixed_asset' => 'double-entry::types.current_asset',
            'other_asset' => 'double-entry::types.current_asset',
            'accounts_receivable' => 'double-entry::types.current_asset',
            'cash' => 'double-entry::types.current_asset',
            'stock' => 'double-entry::types.current_asset',
            'accounts_payable' => 'double-entry::types.current_asset',

            'equity' => 'double-entry::types.equity',

            'expense' => 'double-entry::types.expense',
            'other_expense' => 'double-entry::types.expense',
            'cost_of_goods_sold' => 'double-entry::types.expense',

            'other_current_liability' => 'double-entry::types.liability',
            'credit_card' => 'double-entry::types.liability',
            'long_term_liability' => 'double-entry::types.liability',
            'other_liability' => 'double-entry::types.liability',

            'income' => 'double-entry::types.sales',
            'other_income' => 'double-entry::types.other_income',
        ];

        return Type::where('name', $map[$zohobooksTypeValue])->first()->id;
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
