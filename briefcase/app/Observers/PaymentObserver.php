<?php

namespace App\Observers;

use App\Events\InvoicePaymentReceivedEvent;
use App\Events\NewPaymentEvent;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{

    public function saving(Payment $payment)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $payment->last_updated_by = user()->id;
        }
    }

    public function creating(Payment $payment)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $payment->added_by = user() ? user()->id : null;
        }
    }

    public function saved(Payment $payment)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (($payment->project_id && $payment->project->client_id != null) || ($payment->invoice_id && $payment->invoice->client_id != null) && $payment->gateway != 'Offline') {
                // Notify client
                $clientId = ($payment->project_id && $payment->project->client_id != null) ? $payment->project->client_id : $payment->invoice->client_id;

                $notifyUser = User::findOrFail($clientId);
                event(new NewPaymentEvent($payment, $notifyUser));
            }
        }
    }

    public function created(Payment $payment)
    {
        if (($payment->invoice_id || $payment->order_id) && $payment->status == 'complete')
        {

            if($payment->invoice_id) {
                $invoice = Invoice::latest()->find($payment->invoice_id);
            }
            elseif($payment->order_id) {
                $invoice = Invoice::where('order_id', $payment->invoice_id)->latest()->first();
            }

            $due = 0;

            if(isset($payment->invoice)){
                $due = $payment->invoice->due_amount;
            }
            elseif(isset($payment->order)) {
                $due = $payment->order->total;
            }

            $dueAmount = $due - $payment->amount;

            if(isset($invoice)){
                $invoice->due_amount = $dueAmount;
                $invoice->save();
            }

            // Notify all admins
            try{
                if (!isRunningInConsoleOrSeeding() ) {
                    if($payment->gateway != 'Offline'){
                        event(new InvoicePaymentReceivedEvent($payment));
                    }
                }
            }catch (\Exception $e){
                Log::info($e);
            }

        }
    }

    public function deleting(Payment $payment)
    {
        $notifiData = ['App\Notifications\NewPayment','App\Notifications\PaymentReminder'
        ];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$payment->id.',%')
            ->delete();
    }

}
