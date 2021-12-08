<?php

namespace App\Observers;

use App\Events\DiscussionEvent;
use App\Models\Discussion;
use App\Models\Notification;

class DiscussionObserver
{

    public function created(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new DiscussionEvent($discussion));
        }
    }

    public function deleting(Discussion $discussion)
    {
        $notifiData = ['App\Notifications\NewDiscussion','App\Notifications\NewDiscussionReply'];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$discussion->id.',%')
            ->delete();
    }

}
