<?php

namespace App\Models;

use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DiscussionFile
 *
 * @property string|null $external_link
 * @mixin \Eloquent
 */

class DiscussionFile extends Model
{

    use IconTrait;

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        // phpcs:ignore
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('discussion-files/' . $this->hashname);
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

}
