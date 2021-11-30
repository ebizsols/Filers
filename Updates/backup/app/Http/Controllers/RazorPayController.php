<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payment;
use App\Models\PaymentGatewayCredentials;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;

class RazorPayController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'Razorpay';
    }

    public function payWithRazorPay()
    {
        $credential = PaymentGatewayCredentials::first();

        $paymentId = request('paymentId');

        $apiKey = $credential->razorpay_mode == 'test' ? $credential->test_razorpay_key : $credential->live_razorpay_key;

        $secretKey = $credential->razorpay_mode == 'test' ? $credential->test_razorpay_secret : $credential->live_razorpay_secret;

        $api = new Api($apiKey, $secretKey);
        $payment = $api->payment->fetch($paymentId); /* @phpstan-ignore-line */ // Returns a particular payment

        $purchaseId = $payment->notes->purchase_id;

        /* Razorpay payment for invoices */
        if(!isset(request()->type)){
            $invoice = Invoice::findOrFail($purchaseId);
        }

        /* Razorpay payment for orders */
        if(isset(request()->type) && request()->type == 'order'){
            $order = Order::findOrFail($purchaseId);
        }

        // If transaction successfully done
        if ($payment->status == 'authorized' && isset($payment->amount) && (isset($invoice) || isset($order))) {

            /** @phpstan-ignore-next-line */
            $currencyCode = isset(request()->type) && request()->type == 'order' ? $order->currency->currency_code : $invoice->currency->currency_code;

            /** @phpstan-ignore-next-line */
            $payment->capture(array('amount' => $payment->amount, 'currency' => $currencyCode));

            $projectId = null;

            /* Mark invoice as paid */
            /** @phpstan-ignore-next-line */
            if(!isset(request()->type) && isset($invoice)){
                $invoice->status = 'paid';
                $invoice->save();

                /* when it's invoice payment, then project_id might have value */
                $projectId = $invoice->project_id;
            }

            /* Mark order as paid and make invoice */
            /** @phpstan-ignore-next-line */
            if(isset(request()->type) && request()->type == 'order' && isset($order)){
                $order->status = 'paid';
                $order->save();

                /* Make invoice for particular invoice */
                $invoice = $this->makeInvoice($order);
            }

            if(isset($invoice)) {
                $payment = $this->makePayment($invoice, $projectId, $paymentId);
            }

            Session::put('success', __('messages.paymentSuccessful'));

            if (!auth()->check() && isset($invoice)) {
                $redirectRoute = 'front.invoice';
                return Reply::redirect(route($redirectRoute, $invoice->hash), __('messages.paymentSuccessful'));
            }

            /** @phpstan-ignore-next-line */
            if(isset(request()->type) && request()->type == 'order' && isset($order)){
                return Reply::redirect(route('orders.show', $order->id), __('messages.paymentSuccessful'));
            }

            if(isset($invoice)){
                return Reply::redirect(route('invoices.show', $invoice->id), __('messages.paymentSuccessful'));
            }
        }

        return Reply::error('Transaction Failed');
    }

    public function makeInvoice($order)
    {
        $invoice = new Invoice();
        $invoice->order_id = $order->id;
        $invoice->client_id = $order->client_id;
        $invoice->sub_total = $order->sub_total;
        $invoice->total = $order->total;
        $invoice->currency_id = $order->currency_id;
        $invoice->status = 'paid';
        $invoice->note = $order->note;
        $invoice->send_status = 1;
        $invoice->due_amount = 0;
        $invoice->issue_date = Carbon::now();
        $invoice->invoice_number = Invoice::lastInvoiceNumber() + 1;
        $invoice->save();

        /* Make invoice items */
        $orderItems = OrderItems::where('order_id', $order->id)->get();

        foreach ($orderItems as $item){
            InvoiceItems::create(
                [
                    'invoice_id'   => $invoice->id,
                    'item_name'    => $item->item_name,
                    'item_summary' => $item->item_summary,
                    'type'         => 'item',
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'amount'       => $item->amount,
                    'taxes'        => $item->taxes
                ]
            );
        }

        return $invoice;
    }

    public function makePayment($invoice, $projectId, $paymentId)
    {
        $payment = new Payment();
        $payment->project_id = $projectId;
        $payment->invoice_id = $invoice->id;
        $payment->currency_id = $invoice->currency_id;
        $payment->amount = $invoice->total;
        $payment->gateway = 'Razorpay';
        $payment->transaction_id = $paymentId;
        $payment->paid_on = Carbon::now();
        $payment->status = 'complete';
        $payment->save();

        return $payment;
    }

}
