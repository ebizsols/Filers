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
use App\Http\Controllers\Controller;
use net\authorize\api\contract\v1 as AuthorizeAPI;
use App\Http\Requests\PaymentGateway\AuthorizeDetails;
use net\authorize\api\controller\CreateTransactionController;

class AuthorizeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.authorize');
    }

    public function paymentWithAuthorizePublic(AuthorizeDetails $request, $id)
    {

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($id);
            $amount = $invoice->amountDue();
            $currency = $invoice->currency ? $invoice->currency->currency_code : 'USD';
            break;

        case 'order':
            $order = Order::find($id);
            $amount = $order->total;
            $currency = $order->currency ? $order->currency->currency_code : 'USD';
            break;

        default:
            return Reply::error(__('messages.paymentTypeNotFound'));
        }


        /* Create a merchantAuthenticationType object with authentication details retrieved from the constants file */
        $merchantAuthentication = new AuthorizeAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorize.login'));
        $merchantAuthentication->setTransactionKey(config('services.authorize.transaction'));

        // Set the transaction's refId and use this as transaction id because authorize.net give transaction id 0
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AuthorizeAPI\CreditCardType();
        $creditCard->setCardNumber($request->card_number);
        $creditCard->setExpirationDate($request->expiration_year . '-' . $request->expiration_month);
        $creditCard->setCardCode($request->cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AuthorizeAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AuthorizeAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setCurrencyCode($currency);
        $transactionRequestType->setPayment($paymentOne);

        // Assemble the complete transaction request
        $requests = new AuthorizeAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);

        // Set the transaction's refId
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new CreateTransactionController($requests);

        $response = $controller->executeWithApiResponse(config('services.authorize.sandbox') ? \net\authorize\api\constants\ANetEnvironment::SANDBOX : \net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                /** @phpstan-ignore-next-line */
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $message_text = $tresponse->getMessages()[0]->getDescription() . ', Transaction ID: ' . $tresponse->getTransId();
                    $msg_type = 'success';

                    switch ($request->type) {
                    case 'invoice':
                        $invoice = Invoice::findOrFail($id);
                        $invoice->status = 'paid';
                        $invoice->save();
                        $this->makePayment($amount, $invoice, 'complete', $refId);

                        break;

                    case 'order':
                        $invoice = $this->makeOrderInvoice($id);
                        $this->makePayment($amount, $invoice, 'complete', $tresponse->getTransId() ?: $refId);
                        break;

                    default:
                        break;
                    }
                }
                else {
                    $message_text = __('modules.authorize.errorMessage');
                    $msg_type = 'error';

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = 'error';
                    }
                }

                // Or, print errors if the API request wasn't successful
            }
            else {
                $message_text = __('modules.authorize.errorMessage');
                $msg_type = 'error';

                /** @phpstan-ignore-next-line */
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type = 'error_msg';
                }
                else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type = 'error';
                }
            }

            if ($msg_type == 'error' && $request->type == 'invoice') {
                $invoice = Invoice::findOrFail($id);
                $this->makePayment($amount, $invoice, 'failed', $tresponse->getTransId() ?: $refId);
            }
        }
        else {
            $message_text = __('modules.authorize.errorNoResponse');
            $msg_type = 'error';
        }

        return ($msg_type == 'success') ? Reply::success($message_text) : Reply::error($message_text);
    }

    public function makePayment($amount, $invoice, $status = 'pending', $transactionId = null, $gateway = 'Authorize')
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

        if ($order->invoice) {
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

        foreach ($orderItems as $item) {
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
