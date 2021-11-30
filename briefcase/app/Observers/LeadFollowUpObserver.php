<?php

namespace App\Observers;

use App\Models\LeadFollowUp;

class LeadFollowUpObserver
{

    public function saving(LeadFollowUp $leadFollowUp)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $leadFollowUp->last_updated_by = user()->id;
        }
    }

    public function creating(LeadFollowUp $leadFollowUp)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $leadFollowUp->added_by = user()->id;
        }
    }

}
