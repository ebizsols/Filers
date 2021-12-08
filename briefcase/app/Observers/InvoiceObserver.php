<?php

namespace App\Observers;

use App\Models\Estimate;
use App\Events\NewInvoiceEvent;
use App\Models\CompanyAddress;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\Notification;
use App\Models\UniversalSearch;
use App\Models\User;

class InvoiceObserver
{

    public function saving(Invoice $invoice)
    {
        if (user()) {
            $invoice->last_updated_by = user()->id;

            if (request()->has('calculate_tax')) {
                $invoice->calculate_tax = request()->calculate_tax;
            }
        }

        if (is_null($invoice->company_address_id)) {
            $defaultCompanyAddress = CompanyAddress::where('is_default', 1)->first();
            $invoice->company_address_id = $defaultCompanyAddress->id;
        }
    }

    public function creating(Invoice $invoice)
    {
        $invoice->hash = \Illuminate\Support\Str::random(32);

        if (!isRunningInConsoleOrSeeding()) {
            if (request()->type && request()->type == 'send' || !is_null($invoice->invoice_recurring_id)) {
                $invoice->send_status = 1;
            }
            else {
                $invoice->send_status = 0;
            }

            if (request()->type && request()->type == 'draft') {
                $invoice->status = 'draft';
            }

            if (!is_null($invoice->estimate_id)) {
                $estimate = Estimate::findOrFail($invoice->estimate_id);

                if ($estimate->status == 'accepted') {
                    $invoice->send_status = 1;
                }
            }

            /* If it is a order invoice, then send_status will be always 1 so that it could be visible to clients */
            if (isset($invoice->order_id)) {
                $invoice->send_status = 1;
            }

        }

        $invoice->added_by = user() ? user()->id : null;

    }

    public function created(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (!empty(request()->item_name) && is_array(request()->item_name)) {

                $itemsSummary = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $hsn_sac_code = request()->hsn_sac_code;
                $quantity = request()->quantity;
                $amount = request()->amount;
                $tax = request()->taxes;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        InvoiceItems::create(
                            [
                                'invoice_id' => $invoice->id,
                                'item_name' => $item,
                                'item_summary' => $itemsSummary[$key] ? $itemsSummary[$key] : '',
                                'type' => 'item',
                                'hsn_sac_code' => (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null,
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                                'taxes' => ($tax ? (array_key_exists($key, $tax) ? json_encode($tax[$key]) : null) : null)
                            ]
                        );
                    }

                endforeach;
            }

            if (($invoice->project && $invoice->project->client_id != null) || $invoice->client_id != null) {
                $clientId = ($invoice->project && $invoice->project->client_id != null) ? $invoice->project->client_id : $invoice->client_id;
                // Notify client
                $notifyUser = User::withoutGlobalScope('active')->findOrFail($clientId);

                if (request()->type && request()->type == 'send') {
                    event(new NewInvoiceEvent($invoice, $notifyUser));
                }
            }
        }
    }

    public function updating(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->type && request()->type == 'send') {
                $invoice->send_status = 1;
                $invoice->status = 'unpaid';
            }
        }
    }

    public function deleting(Invoice $invoice)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $invoice->id)->where('module_type', 'invoice')->get();

        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }

        $notifiData = ['App\Notifications\InvoicePaymentReceived', 'App\Notifications\InvoiceReminder','App\Notifications\NewInvoice','App\Notifications\NewPayment'];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$invoice->id.',%')
            ->delete();
    }

}
