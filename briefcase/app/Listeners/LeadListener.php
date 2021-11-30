<?php

namespace App\Listeners;

use App\Events\LeadEvent;
use App\Notifications\LeadAgentAssigned;
use Illuminate\Support\Facades\Notification;

class LeadListener
{

    /**
     * Handle the event.
     *
     * @param  LeadEvent $event
     * @return void
     */

    public function handle(LeadEvent $event)
    {
        if ($event->notificationName == 'LeadAgentAssigned') {
            Notification::send($event->lead->leadAgent->user, new LeadAgentAssigned($event->lead));
        }
    }

}
