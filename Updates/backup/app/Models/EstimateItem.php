<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EstimateItem
 *
 * @property int $id
 * @property int $estimate_id
 * @property string $item_name
 * @property string|null $item_summary
 * @property string $type
 * @property float $quantity
 * @property float $unit_price
 * @property float $amount
 * @property string|null $taxes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $hsn_sac_code
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereEstimateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereHsnSacCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereItemSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EstimateItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EstimateItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

}
