<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{

    public function saving(Event $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $event->last_updated_by = user()->id;
        }
    }

    public function creating(Event $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $event->added_by = user()->id;
        }
    }

}
