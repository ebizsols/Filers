<?php

namespace App\Models;

use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;

class UserchatFile extends Model
{
    use IconTrait;

    protected $appends = ['file_url', 'icon'];
    protected $table = 'users_chat_files';

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('message-files/' . $this->hashname);
    }

    public function chat()
    {
        return $this->belongsTo(UserChat::class, 'users_chat_id');
    }

}
