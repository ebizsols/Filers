<?php

namespace App\Observers;

use App\Models\Leave;
use App\Events\LeaveEvent;

class LeaveObserver
{

    public function saving(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $leave->last_updated_by = user()->id;
        }
    }

    public function creating(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $leave->added_by = user()->id;
        }
    }

    public function created(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->duration == 'multiple') {
                if (session()->has('leaves_duration')) {
                    event(new LeaveEvent($leave, 'created', request()->multi_date));
                }
            } else {
                event(new LeaveEvent($leave, 'created'));
            }
        }
    }

    public function updated(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            // Send from ManageLeavesController
            if ($leave->isDirty('status')) {
                event(new LeaveEvent($leave, 'statusUpdated'));
            }
            else {
                event(new LeaveEvent($leave, 'updated'));
            }
        }
    }

}
