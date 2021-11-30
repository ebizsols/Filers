<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItems;

class InvoiceRecurringObserver
{

    public function saving(RecurringInvoice $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $expense->last_updated_by = user()->id;
        }
    }

    public function creating(RecurringInvoice $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $expense->added_by = user()->id;
        }
    }

    public function created(RecurringInvoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if (!empty(request()->item_name)) {

                $itemsSummary  = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $quantity      = request()->quantity;
                $hsn_sac_code  = request()->hsn_sac_code;
                $amount        = request()->amount;
                $tax           = request()->taxes;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        RecurringInvoiceItems::create(
                            [
                                'invoice_recurring_id'   => $invoice->id,
                                'item_name'    => $item,
                                'item_summary' => $itemsSummary[$key] ? $itemsSummary[$key] : '',
                                'type'         => 'item',
                                'hsn_sac_code' => (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null,
                                'quantity'     => $quantity[$key],
                                'unit_price'   => round($cost_per_item[$key], 2),
                                'amount'       => round($amount[$key], 2),
                                'taxes'        => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                            ]
                        );
                    }

                endforeach;
            }

        }
    }

    public function deleting(RecurringInvoice $invoice)
    {
            $notifiData = ['App\Notifications\InvoiceRecurringStatus', 'App\Notifications\NewRecurringInvoice',];

            Notification::whereIn('type', $notifiData)
                ->whereNull('read_at')
                ->where('data', 'like', '{"id":'.$invoice->id.',%')
                ->delete();
    }

}
