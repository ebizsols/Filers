<?php

namespace App\Listeners;

use App\Events\SubTaskCompletedEvent;
use App\Notifications\SubTaskCompleted;
use App\Notifications\SubTaskCreated;
use App\Models\SubTask;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SubTaskCompletedListener
{

    /**
     * Handle the event.
     *
     * @param  SubTaskCompletedEvent  $event
     * @return void
     */

    public function handle(SubTaskCompletedEvent $event)
    {
        if ($event->status == 'completed') {
            Notification::send($event->subTask->task->users, new SubTaskCompleted($event->subTask));
        }

        elseif ($event->status == 'created') {
            Notification::send($event->subTask->task->users, new SubTaskCreated($event->subTask));
        }
    }

}
