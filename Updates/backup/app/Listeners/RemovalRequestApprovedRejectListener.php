<?php

namespace App\Listeners;

use App\Events\RemovalRequestApprovedRejectUserEvent;
use App\Events\RemovalRequestApproveRejectEvent;
use App\Notifications\RemovalRequestApprovedReject;
use App\Notifications\RemovalRequestApprovedRejectUser;
use Illuminate\Support\Facades\Notification;

class RemovalRequestApprovedRejectListener
{
    /**
     * @param RemovalRequestApproveRejectEvent $event
     */

    public function handle(RemovalRequestApproveRejectEvent $event)
    {
        Notification::send($event->removalRequest->user, new RemovalRequestApprovedReject($event->removalRequest->status));
    }

}
