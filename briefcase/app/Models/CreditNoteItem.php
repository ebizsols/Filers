<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CreditNoteItem
 *
 * @property int $id
 * @property int $credit_note_id
 * @property string $item_name
 * @property string $type
 * @property int $quantity
 * @property float $unit_price
 * @property float $amount
 * @property string|null $taxes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $hsn_sac_code
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereCreditNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereHsnSacCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditNoteItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CreditNoteItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

}
