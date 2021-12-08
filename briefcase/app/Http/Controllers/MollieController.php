<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OrderItems;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Log;
use Mollie\Laravel\Facades\Mollie;
use Mollie\Api\Exceptions\ApiException;

class MollieController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.mollie');
    }

    public function paymentWithMolliePublic(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        $customer = Mollie::api()->customers()->create([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($id);

            $description = __('app.invoice').' '.$invoice->id;
            $metadata = [
                'id' => $invoice->id,
                'type' => $request->type
            ];
            $amount = $invoice->amountDue();
            $currency = $invoice->currency ? $invoice->currency->currency_code : 'ZAR';
            $callback_url = route('mollie.callback', [$id, 'invoice']);
            break;

        case 'order':
            $order = Order::find($id);

            $description = __('app.order').' '.$order->id;
            $metadata = [
                'id' => $order->id,
                'type' => $request->type
            ];
            $amount = $order->total;
            $currency = $order->currency ? $order->currency->currency_code : 'USD';
            $callback_url = route('mollie.callback', [$id, 'order']);
            break;

        default:
            return Reply::error(__('messages.paymentTypeNotFound'));
        }

        try {
            $payment = Mollie::api()->payments->create([
                'amount' => [
                    'currency' => $currency,
                    'value' => number_format((float)$amount, 2, '.', '') // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'description' => $description,
                'customerId'   => $customer->id,
                'redirectUrl' => $callback_url,
                'webhookUrl' => route('mollie.webhook'),
                'metadata' => $metadata,
            ]);

            session()->put('mollie_payment_id', $payment->id);

        } catch (ApiException $e) {
            Log::info($e->getMessage());

            if ($e->getField() == 'webhookUrl' && $e->getCode() == '422') {
                return Reply::error('Mollie Webhook will work on live server or you can try ngrok. It will not work on localhost'. $e->getMessage());
            }

            return Reply::error($e->getMessage());
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return Reply::error($th->getMessage());
        }

        return Reply::redirect($payment->getCheckoutUrl());

    }

    public function handleGatewayCallback(Request $request, $id, $type)
    {
        try {
            $payment = Mollie::api()->payments()->get(session()->get('mollie_payment_id'));

            switch ($type) {
            case 'invoice':
                $invoice = Invoice::findOrFail($id);
                $invoice->status = $payment->isPaid() ? 'paid' : 'unpaid';
                $invoice->save();

                $this->makePayment($invoice, ($payment->isPaid() ? 'complete' : 'failed'), $payment->id);

                return redirect(route('front.invoice', $invoice->hash));

            case 'order':

                if ($payment->isPaid()) {
                    $invoice = $this->makeOrderInvoice($id);
                    $this->makePayment($invoice, 'complete', $payment->id);
                }

                return redirect()->route('orders.show', $id);

            default:
                return redirect()->route('dashboard');
            }

        } catch (ApiException $e) {
            Log::info($e->getMessage());
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return redirect()->route('dashboard');
    }

    public function handleGatewayWebhook(Request $request)
    {
        try {
            $payment = Mollie::api()->payments()->get($request->id);

            switch ($payment->metadata->type) {
            case 'invoice':
                $invoice = Invoice::findOrFail($payment->metadata->id);
                $invoice->status = $payment->isPaid() ? 'paid' : 'unpaid';
                $invoice->save();
                $this->makePayment($invoice, ($payment->isPaid() ? 'complete' : 'failed'), $payment->id);
                break;

            case 'order':

                if ($payment->isPaid()) {
                    $invoice = $this->makeOrderInvoice($payment->metadata->id);
                    $this->makePayment($invoice, 'complete', $payment->id);
                }

                break;

            default:
                break;
            }

        } catch (ApiException $e) {
            Log::info($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function makePayment($invoice, $status = 'pending', $transactionId = null, $gateway = 'Mollie')
    {

        $payment = Payment::where('transaction_id', $transactionId)->whereNotNull('transaction_id')->first();

        $payment = ($payment && $transactionId) ? $payment : new Payment();
        $payment->project_id = $invoice->project_id;
        $payment->invoice_id = $invoice->id;
        $payment->order_id = $invoice->order_id;
        $payment->gateway = $gateway;
        $payment->transaction_id = $transactionId;
        $payment->event_id = $transactionId;
        $payment->currency_id = $invoice->currency_id;
        $payment->amount = $invoice->amountDue();
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
