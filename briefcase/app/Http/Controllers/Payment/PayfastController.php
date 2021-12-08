<?php

namespace App\Http\Controllers\Payment;

use App\Helper\Reply;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OrderItems;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Billow\Payfast;
use Config;

class PayfastController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.payfast');
    }

    public function paymentWithPayfastPublic(Request $request)
    {

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($request->id);
            $client = $invoice->client_id ? $invoice->client : $invoice->project->client;
            $description = __('app.invoice').' '.$invoice->id;
            $amount = $invoice->amountDue();
            break;

        case 'order':
            $order = Order::find($request->id);
            $client = $order->client;
            $description = __('app.order').' '.$order->id;
            $amount = $order->total;

            break;

        default:
            return Reply::error(__('messages.paymentTypeNotFound'));
        }

        try {
            Config::set('payfast.merchant.return_url', route('payfast.callback', [$request->id, $request->type, 'success']));
            Config::set('payfast.merchant.cancel_url', route('payfast.callback', [$request->id, $request->type, 'cancel']));
            Config::set('payfast.merchant.notify_url', route('payfast.webhook'));


            $payfast = new Payfast();
            $payfast->setBuyer($client->name, '', $client->email);
            $payfast->setAmount($amount);
            $payfast->setItem($request->type, $description);
            $payfast->setMerchantReference($request->type.'_'.$request->id);
            $payfast->setCustomStr1($request->type);
            $payfast->setCustomInt1($request->id);

            // Return the payment form.
            return Reply::successWithData(__('modules.payfast.redirectMessage'), ['form' => $payfast->paymentForm(false)]);

        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return Reply::error($th->getMessage());
        }

    }

    public function handleGatewayCallback($id, $type, $status)
    {

        switch ($type) {
        case 'invoice':
            $invoice = Invoice::findOrFail($id);

            if ($invoice->status != 'paid') {

                $invoice->status = $status == 'success' ? 'paid' : 'unpaid';
                $invoice->save();
                $this->makePayment($invoice->amountDue(), $invoice, ($status == 'success' ? 'complete' : 'failed'), 'payfast_'.$invoice->id);
            }

            return redirect(route('front.invoice', $invoice->hash));

        case 'order':

            if ($status == 'success') {
                $invoice = $this->makeOrderInvoice($id);
                $this->makePayment($invoice->amountDue(), $invoice, 'complete', 'payfast_'.$invoice->id);
            }

            return redirect()->route('orders.show', $id);

        default:
            return redirect()->route('dashboard');
        }


        return redirect()->route('dashboard');
    }

    public function handleGatewayWebhook(Request $request)
    {
        switch ($request->custom_str1) {
        case 'invoice':
            $invoice = Invoice::findOrFail($request->custom_int1);
            $invoice->status = ($request->payment_status == 'COMPLETE') ? 'paid' : 'unpaid';
            $invoice->save();
            $this->makePayment($invoice->amountDue(), $invoice, (($request->payment_status == 'COMPLETE') ? 'complete' : 'failed'), $request->pf_payment_id);
            break;

        case 'order':

            if (($request->payment_status == 'COMPLETE')) {
                $invoice = $this->makeOrderInvoice($request->custom_int1);
                $this->makePayment($invoice->amountDue(), $invoice, 'complete', $request->pf_payment_id);
            }

            break;

        default:
            break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function makePayment($amount, $invoice, $status = 'pending', $transactionId = null, $gateway = 'Payfast')
    {

        $payment = Payment::where('transaction_id', $transactionId)->orWhere('transaction_id', 'payfast_'.$invoice->id)->whereNotNull('transaction_id')->first();

        $payment = ($payment && $transactionId) ? $payment : new Payment();
        $payment->project_id = $invoice->project_id;
        $payment->invoice_id = $invoice->id;
        $payment->order_id = $invoice->order_id;
        $payment->gateway = $gateway;
        $payment->transaction_id = $transactionId;
        $payment->event_id = $transactionId;
        $payment->currency_id = $invoice->currency_id;
        $payment->amount = $amount;
        $payment->paid_on = Carbon::now();
        $payment->status = $status;
        $payment->save();

        return $payment;
    }

    public function makeOrderInvoice($orderId)
    {
        $order = Order::find($orderId);
        $order->status = 'paid';
        $order->save();

        if($order->invoice)
        {
            return $order->invoice;
        }

        /* Step2 - make an invoice related to recently paid order_id */
        $invoice = new Invoice();
        $invoice->order_id = $orderId;
        $invoice->client_id = $order->client_id;
        $invoice->sub_total = $order->sub_total;
        $invoice->total = $order->total;
        $invoice->currency_id = $order->currency_id;
        $invoice->status = 'paid';
        $invoice->note = $order->note;
        $invoice->issue_date = Carbon::now();
        $invoice->send_status = 1;
        $invoice->invoice_number = Invoice::lastInvoiceNumber() + 1;
        $invoice->due_amount = 0;
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

}

