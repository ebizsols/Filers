<?php

namespace App\Observers;

use App\Events\NewOrderEvent;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;

class OrderObserver
{

    public function creating(Order $order)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $order->added_by = user()->id;
        }
    }

    public function created(Order $order)
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
                        OrderItems::create(
                            [
                                'order_id' => $order->id,
                                'item_name' => $item,
                                'item_summary' => $itemsSummary[$key] ? $itemsSummary[$key] : '',
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

            if ($order->added_by != null) {
                $clientId = $order->added_by;
                // Notify client
                $notifyUser = User::withoutGlobalScope('active')->findOrFail($clientId);

                if (request()->type && request()->type == 'send') {
                    event(new NewOrderEvent($order, $notifyUser));
                }
            }

        }
    }

}
