<?php

namespace App\Http\Controllers\Payment;

use Log;
use App\Helper\Reply;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OrderItems;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Unicodeveloper\Paystack\Paystack;
use GuzzleHttp\Exception\ClientException;

class PaystackController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.paystack');
    }

    public function paymentWithPaystackPublic(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        $paystack = new Paystack();

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($id);
            $request->orderID = $invoice->id;
            $request->metadata = [
                'id' => $invoice->id,
                'payment_type' => $request->type
            ];
            $request->amount = ($invoice->amountDue() * 100);
            $request->currency = $invoice->currency ? $invoice->currency->currency_code : 'ZAR';
            $request->callback_url = route('paystack.callback', [$id, 'invoice']);
            break;

        case 'order':
            $order = Order::find($id);
            $request->orderID = $order->id;
            $request->metadata = [
                'id' => $order->id,
                'payment_type' => $request->type
            ];
            $request->amount = ($order->total * 100);
            $request->currency = $order->currency ? $order->currency->currency_code : 'ZAR';
            $request->callback_url = route('paystack.callback', [$id, 'order']);
            break;

        default:
            return Reply::error(__('messages.paymentTypeNotFound'));
        }

        $request->first_name = $request->name;
        $request->email = $request->email;
        $request->quantity = 1;
        $request->reference = $paystack->genTranxRef();

        try {
            return Reply::redirect($paystack->getAuthorizationUrl()->url); /** @phpstan-ignore-line */
        } catch (ClientException $e) {
            Log::info($e->getMessage());
            return Reply::error(json_decode($e->getResponse()->getBody(), true)['message']);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return Reply::error($th->getMessage());
        }
    }

    public function handleGatewayCallback($id, $type = 'invoice')
    {
        $paystack  = new Paystack();
        $paymentDetails = $paystack->getPaymentData();

        switch ($type) {
        case 'invoice':
            $invoice = Invoice::findOrFail($id);
            $invoice->status = ($paymentDetails['data']['status'] == 'success') ? 'paid' : 'unpaid';
            $invoice->save();

            $this->makePayment(($paymentDetails['data']['amount'] / 100), $invoice, (($paymentDetails['data']['status'] == 'success') ? 'complete' : 'failed'), $paymentDetails['data']['reference']);

            return redirect(route('front.invoice', $invoice->hash));

        case 'order':

            if ($paymentDetails['data']['status'] == 'success') {
                $invoice = $this->makeOrderInvoice($id);
                $this->makePayment(($paymentDetails['data']['amount'] / 100), $invoice, 'complete', $paymentDetails['data']['reference']);
            }

            return redirect()->route('orders.show', $id);

        default:
            return redirect()->route('dashboard');
        }
    }

    public function handleGatewayWebhook(Request $request)
    {
        $paymentDetails = $request->toArray();

        switch ($paymentDetails['data']['metadata']['payment_type']) {
        case 'invoice':
            $invoice = Invoice::findOrFail($paymentDetails['data']['metadata']['id']);
            $invoice->status = ($paymentDetails['data']['status'] == 'success') ? 'paid' : 'unpaid';
            $invoice->save();

            $this->makePayment(($paymentDetails['data']['amount'] / 100), $invoice, (($paymentDetails['data']['status'] == 'success') ? 'complete' : 'failed'), $paymentDetails['data']['reference']);

            break;

        case 'order':

            if ($paymentDetails['data']['status'] == 'success') {
                $invoice = $this->makeOrderInvoice($paymentDetails['data']['metadata']['id']);
                $this->makePayment(($paymentDetails['data']['amount'] / 100), $invoice, 'complete', $paymentDetails['data']['reference']);
            }

            break;

        default:
            break;
        }

        return response()->json(['status' => 'success']);
    }

    public function makePayment($amount, $invoice, $status = 'pending', $transactionId = null, $gateway = 'Paystack')
    {
        $payment = Payment::where('transaction_id', $transactionId)->whereNotNull('transaction_id')->first();

        $payment = ($payment && $transactionId) ? $payment : new Payment();
        $payment = new Payment();
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
