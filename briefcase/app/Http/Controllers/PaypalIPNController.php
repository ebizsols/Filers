<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaypalIPNController extends Controller
{

    public function verifyIpn(Request $request)
    {

        $txnType = $request->get('txn_type');

        if ($txnType == 'recurring_payment') {

            $recurringPaymentId = $request->get('recurring_payment_id');
            $eventId = $request->get('ipn_track_id');

            $event = Payment::where('event_id', $eventId)->count();

            if ($event == 0) {
                $payment = Payment::where('transaction_id', $recurringPaymentId)->first();

                $clientPayment = new Payment();
                $clientPayment->invoice_id = $payment->invoice_id;
                $clientPayment->amount = $payment->amount;
                $clientPayment->gateway = 'Paypal';
                $clientPayment->status = 'complete';
                $clientPayment->event_id = $eventId;
                $clientPayment->paid_on = Carbon::now();
                $clientPayment->save();

                return response('IPN Handled', 200);
            }
        }
    }

}
