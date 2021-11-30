<?php

namespace App\Observers;

use App\Models\Contract;
use App\Events\NewContractEvent;
use App\Models\Notification;

class ContractObserver
{

    public function saving(Contract $contract)
    {
        if (user()) {
            $contract->last_updated_by = user()->id;
        }
    }

    public function creating(Contract $contract)
    {
        $contract->hash = \Illuminate\Support\Str::random(32);

        if (user()) {
            $contract->added_by = user()->id;
        }
    }

    // Notify client when new contract is created
    public function created(Contract $contract)
    {
        event(new NewContractEvent($contract));
    }

    public function deleting(Contract $contract)
    {
        $notifiData = ['App\Notifications\NewContract', 'App\Notifications\ContractSigned'];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$contract->id.',%')
            ->delete();
    }

}
