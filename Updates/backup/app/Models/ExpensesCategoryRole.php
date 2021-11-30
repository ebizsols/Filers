<?php

namespace App\Models;

class ExpensesCategoryRole extends BaseModel
{
    protected $table = 'expenses_category_roles';

    public function category()
    {
        return $this->belongsTo(ExpensesCategory::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
