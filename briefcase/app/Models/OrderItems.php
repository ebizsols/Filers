<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderItems
 *
 * @property int $id
 * @property int $order_id
 * @property string $item_name
 * @property string|null $item_summary
 * @property string $type
 * @property int $quantity
 * @property int $unit_price
 * @property float $amount
 * @property string|null $hsn_sac_code
 * @property string|null $taxes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereHsnSacCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereItemSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderItems extends Model
{

    protected $fillable = ['order_id', 'item_name', 'item_summary', 'type', 'quantity', 'unit_price', 'amount', 'hsn_sac_code', 'taxes'];

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

}
