<?php

namespace Modules\Quickbooks\Models\Common;

use App\Models\Common\Item as BaseItem;

class Item extends BaseItem
{
    protected $fillable = ['company_id', 'name', 'description', 'sale_price', 'purchase_price', 'category_id', 'tax_id', 'enabled', 'sku'];
}
