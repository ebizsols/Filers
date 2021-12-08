<?php

namespace App\Http\Controllers\Payment;

use App\Helper\Reply;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use Square\Models\Money;
use Square\SquareClient;
use App\Models\OrderItems;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Square\Models\OrderLineItem;
use Illuminate\Support\Facades\Log;
use Square\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Square\Models\CreateOrderRequest;
use Square\Models\Order as SquareOrder;
use Square\Models\CreateCheckoutRequest;

class SquareController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.square');
    }

    public function paymentWithSquarePublic(Request $request)
    {

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($request->id);

            $description = __('app.invoice') . ' #' . $invoice->id;
            $metadata = [
                'id' => strval($invoice->id),
                'type' => $request->type
            ];
            $amount = $invoice->amountDue();
            $callback_url = route('square.callback', [$request->id, $request->type]);
                break;

        case 'order':
            $order = Order::find($request->id);

            $description = __('app.order') . ' #' . $order->id;
            $metadata = [
                'id' => strval($order->id),
                'type' => $request->type
            ];
            $amount = $order->total;
            $callback_url = route('square.callback', [$request->id, $request->type]);
                break;

        default:
                return Reply::error(__('messages.paymentTypeNotFound'));
        }

        $client = new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => config('services.square.environment'),
        ]);
        $location_id = config('services.square.location_id');
        try {
            $checkout_api = $client->getCheckoutApi();

            // Set currency to the currency for the location
            $currency = $client->getLocationsApi()->retrieveLocation($location_id)->getResult()->getLocation()->getCurrency();

            $money = new Money();
            $money->setCurrency($currency);
            $money->setAmount($amount * 100);

            $item = new OrderLineItem(1);
            $item->setName($description);
            $item->setBasePriceMoney($money);


            // Create a new order and add the line items as necessary.
            $order = new SquareOrder($location_id);
            $order->setLineItems([$item]);
            // set metadata
            $order->setMetaData($metadata);

            $create_order_request = new CreateOrderRequest();
            $create_order_request->setOrder($order);

            // Similar to payments you must have a unique idempotency key.
            $checkout_request = new CreateCheckoutRequest(uniqid(), $create_order_request);
            // Set a custom redirect URL, otherwise a default Square confirmation page will be used
            $checkout_request->setRedirectUrl($callback_url);


            $response = $checkout_api->createCheckout($location_id, $checkout_request);

            return $response->isError() ? Reply::error($response->getErrors()[0]->getDetail()) : Reply::redirect($response->getResult()->getCheckout()->getCheckoutPageUrl(), __('modules.square.redirectMessage'));
        } catch (ApiException $e) {
            return Reply::error($e->getMessage());
        } catch (\Throwable $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function handleGatewayCallback(Request $request, $id, $type)
    {

        $client = new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => config('services.square.environment'),
        ]);

        try {

            $order_api = $client->getOrdersApi();
            $order = $order_api->retrieveOrder($request->transactionId)->getResult()->getOrder();

            $amount = ($order->getTotalMoney()->getAmount() / 100);

            switch ($type) {
            case 'invoice':
                $invoice = Invoice::findOrFail($id);

                if ($invoice->status != 'paid') {
                    $invoice->status = ($order->getState() == 'COMPLETED') ? 'paid' : 'unpaid';
                    $invoice->save();
                    $this->makePayment($amount, $invoice, (($order->getState() == 'COMPLETED') ? 'complete' : 'failed'), $request->transactionId);
                }

                return redirect(route('front.invoice', $invoice->hash));

            case 'order':
                if ($order->getState() == 'COMPLETED') {
                    $invoice = $this->makeOrderInvoice($id);
                    $this->makePayment($amount, $invoice, 'complete', $request->transactionId);
                }

                return redirect()->route('orders.show', $id);

            default:
                return redirect()->route('dashboard');
            }

        } catch (ApiException $e) {
            Log::info($e->getMessage());
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        return redirect()->route('dashboard');
    }

    public function makePayment($amount, $invoice, $status = 'pending', $transactionId = null, $gateway = 'Square')
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
