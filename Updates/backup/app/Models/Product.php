<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property string|null $taxes
 * @property int $allow_purchase
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $description
 * @property int|null $category_id
 * @property int|null $sub_category_id
 * @property int|null $added_by
 * @property int|null $last_updated_by
 * @property string|null $hsn_sac_code
 * @property-read mixed $icon
 * @property-read mixed $total_amount
 * @property-read \App\Models\Tax $tax
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAllowPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereHsnSacCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\ProductCategory|null $category
 */
class Product extends BaseModel
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['name', 'price', 'description', 'taxes'];

    protected $appends = ['total_amount'];

    protected $with = ['tax'];

    protected static function boot()
    {
        parent::boot();

        static::observe(ProductObserver::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->price) && !is_null($this->tax)) {
            return (int)$this->price + ((int)$this->price * ((int)$this->tax->rate_percent / 100));
        }

        return '';
    }

}
