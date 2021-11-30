<?php

namespace App\Observers;

use App\Events\NewProposalEvent;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProposalItem;

class ProposalObserver
{

    public function saving(Proposal $proposal)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $proposal->last_updated_by = user()->id;
        }

        if (request()->has('calculate_tax')) {
            $proposal->calculate_tax = request()->calculate_tax;
        }
    }

    public function creating(Proposal $proposal)
    {
        $proposal->hash = \Illuminate\Support\Str::random(32);

        if (!isRunningInConsoleOrSeeding()) {
            $proposal->added_by = user()->id;
        }
    }

    public function created(Proposal $proposal)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (!empty(request()->item_name)) {

                $itemsSummary = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $hsn_sac_code = request()->hsn_sac_code;
                $quantity = request()->quantity;
                $amount = request()->amount;
                $tax = request()->taxes;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        ProposalItem::create(
                            [
                                'proposal_id' => $proposal->id,
                                'item_name' => $item,
                                'item_summary' => $itemsSummary[$key],
                                'type' => 'item',
                                'hsn_sac_code' => (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null,
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                                'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                            ]
                        );
                    }

                endforeach;
            }
        }
    }

    public function updated(Proposal $proposal)
    {
        if ($proposal->isDirty('status')) {
            $type = 'signed';
            event(new NewProposalEvent($proposal, $type));
        }
    }

    public function deleting(Proposal $proposal)
    {
        $notifiData = ['App\Notifications\NewProposal','App\Notifications\ProposalSigned'
        ];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$proposal->id.',%')
            ->delete();
    }

}
