<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGatewayCredentials;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class StripeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $stripeCredentials = PaymentGatewayCredentials::first();

        /** setup Stripe credentials **/
        Stripe::setApiKey($stripeCredentials->stripe_mode == 'test' ? $stripeCredentials->test_stripe_secret : $stripeCredentials->live_stripe_secret);
        $this->pageTitle = __('app.stripe');
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentWithStripe(Request $request, $id)
    {
        $redirectRoute = 'invoices.show';
        $invoiceId = $id;

        $paymentIntentId = $request->paymentIntentId;

        if(isset($request->type) && $request->type == 'order'){
            $redirectRoute = 'orders.show';
            $invoice = Invoice::where('order_id', $id)->latest()->first();
            $invoiceId = $invoice->id;
        }

        $this->makePayment($invoiceId, $paymentIntentId);

        return $this->makeStripePayment($redirectRoute, $id);
    }

    public function paymentWithStripePublic(Request $request, $hash)
    {
        $redirectRoute = 'front.invoice';
        $paymentIntentId = $request->paymentIntentId;

        $invoice = Invoice::where('hash', $hash)->first();

        $this->makePayment($invoice->id, $paymentIntentId);

        return $this->makeStripePayment($redirectRoute, $hash);
    }

    private function makeStripePayment($redirectRoute, $id)
    {
        Session::put('success', __('messages.paymentSuccessful'));
        return Reply::redirect(route($redirectRoute, $id), __('messages.paymentSuccessful'));
    }

    public function makePayment($invoiceId, $paymentIntentId)
    {
        $invoice = Invoice::find($invoiceId);

        $payment = new Payment();
        $payment->project_id = $invoice->project_id ? $invoice->project_id : null;
        $payment->invoice_id = $invoice->id;
        $payment->order_id = $invoice->order_id ? $invoice->order_id : null;
        $payment->currency_id = $invoice->currency_id;
        $payment->amount = $invoice->total;
        $payment->payload_id = $paymentIntentId;
        $payment->gateway = 'Stripe';
        $payment->paid_on = Carbon::now();
        $payment->status = 'complete';
        $payment->save();

        $invoice->status = 'paid';
        $invoice->save();
    }

}
