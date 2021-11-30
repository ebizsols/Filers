<?php

namespace App\Models;

use App\Observers\EmployeeDocsObserver;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Holiday
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $filename
 * @property string $hashname
 * @property string|null $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $added_by
 * @property int|null $last_updated_by
 * @property-read mixed $doc_url
 * @property-read mixed $icon
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereHashname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocs whereUserId($value)
 * @mixin \Eloquent
 */
class EmployeeDocs extends BaseModel
{
    use IconTrait;
    
    // Don't forget to fill this array
    protected $fillable = [];

    protected $guarded = ['id'];
    protected $table = 'employee_docs';
    protected $appends = ['doc_url', 'icon'];

    protected static function boot()
    {
        parent::boot();
        static::observe(EmployeeDocsObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDocUrlAttribute()
    {
        return asset_url_local_s3('employee-docs/'.$this->user_id.'/'.$this->hashname);
    }

}
