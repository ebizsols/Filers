<?php

namespace App\Observers;

use App\Events\InvoicePaymentReceivedEvent;
use App\Models\ClientPayment;
use Illuminate\Support\Facades\Log;

class InvoicePaymentReceivedObserver
{

    public function created(ClientPayment $payment)
    {
        try{
            if (!isRunningInConsoleOrSeeding() ) {
                if($payment->invoice){
                    event(new InvoicePaymentReceivedEvent($payment->invoice));
                }
            }
        }catch (\Exception $e){
            Log::info($e);
        }

    }

}
