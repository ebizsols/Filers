<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProposalItem
 *
 * @property int $id
 * @property int $proposal_id
 * @property string $item_name
 * @property string $type
 * @property float $quantity
 * @property float $unit_price
 * @property float $amount
 * @property string|null $item_summary
 * @property string|null $taxes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $hsn_sac_code
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereHsnSacCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereItemSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProposalItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

}
