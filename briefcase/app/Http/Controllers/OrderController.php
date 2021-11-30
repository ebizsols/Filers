<?php

namespace App\Http\Controllers;

use App\DataTables\OrdersDataTable;
use App\Helper\Reply;
use App\Http\Requests\Orders\UpdateOrder;
use App\Http\Requests\Stripe\StoreStripeDetail;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\OfflinePaymentMethod;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payment;
use App\Models\PaymentGatewayCredentials;
use App\Models\Product;
use App\Models\Project;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Stripe;

class OrderController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.orders';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('orders', $this->user->modules));
            return $next($request);
        });
    }

    public function index(OrdersDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_order');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned']));

        if (!request()->ajax()) {
            $this->projects = Project::allProjects();

            if (in_array('client', user_roles())) {
                $this->clients = User::client();
            }
            else {
                $this->clients = User::allClients();
            }
        }

        return $dataTable->render('orders.index', $this->data);
    }

    public function edit($id)
    {
        $this->order = Order::with('client')->findOrFail($id);

        $this->editPermission = user()->permission('edit_order');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->order->added_by == user()->id)));

        abort_403($this->order->status == 'paid');
        $this->pageTitle = __('app.order').'#'.$this->order->id;

        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        $this->clients = User::allClients();

        if (request()->ajax()) {
            $html = view('orders.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'orders.ajax.edit';
        return view('orders.create', $this->data);
    }

    public function update(UpdateOrder $request, $id)
    {
        $items = $request->item_name;
        $itemsSummary = $request->item_summary;
        $hsn_sac_code = $request->hsn_sac_code;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $amount = $request->amount;
        $tax = $request->taxes;

        if ($request->total == 0) {
            return Reply::error(__('messages.amountIsZero'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }

        $order = Order::findOrFail($id);

        if ($order->status == 'paid') {
            return Reply::error(__('messages.invalidRequest'));
        }

        $order->sub_total = round($request->sub_total, 2);
        $order->total = round($request->total, 2);
        $order->note = $request->note;
        $order->show_shipping_address = $request->show_shipping_address;
        $order->save();

        // delete and create new
        OrderItems::where('order_id', $order->id)->delete();

        foreach ($items as $key => $item) :
            OrderItems::create(
                [
                    'order_id' => $order->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'type' => 'item',
                    'hsn_sac_code' => (isset($hsn_sac_code[$key]) ? $hsn_sac_code[$key] : null),
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                ]
            );
        endforeach;

        if ($request->has('shipping_address')) {
            if ($order->client_id != null && $order->client_id != '') {
                /** @phpstan-ignore-next-line */
                $client = $order->clientdetails;
            }

            if (isset($client)) {
                $client->shipping_address = $request->shipping_address;
                $client->save();
            }
        }

        return Reply::redirect(route('orders.index'), __('messages.orderUpdated'));
    }

    public function show($id)
    {
        $this->order = Order::with('client')->findOrFail($id);

        $this->viewPermission = user()->permission('view_order');

        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'owned' && $this->order->added_by == user()->id)));

        $this->pageTitle = __('app.order').'#'.$id;

        $this->discount = 0;

        /** @phpstan-ignore-next-line */
        if ($this->order->discount > 0) {
            /** @phpstan-ignore-next-line */
            if ($this->order->discount_type == 'percent') {
                $this->discount = (($this->order->discount / 100) * $this->order->sub_total);
            }
            else {
                $this->discount = $this->order->discount;
            }
        }

        $taxList = array();

        /** @phpstan-ignore-next-line */
        $items = OrderItems::whereNotNull('taxes')
            ->where('order_id', $this->order->id)
            ->get();

        foreach ($items as $item) {
            /** @phpstan-ignore-next-line */
            if (isset($this->order) && $this->order->discount > 0 && $this->order->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->order->discount / 100) * $item->amount);
            }

            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = OrderItems::taxbyid($tax)->first();

                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                }
                else {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                }
            }
        }

        $this->taxes = $taxList;
        $this->settings = global_setting();
        $this->creditNote = 0;

        $this->credentials = PaymentGatewayCredentials::first();
        $this->methods = OfflinePaymentMethod::activeMethod();

        return view('orders.show', $this->data);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $this->deletePermission = user()->permission('delete_order');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $order->added_by == user()->id)));

        Order::destroy($id);

        return Reply::success(__('messages.orderDeleted'));
    }

    public function offlinePaymentModal(Request $request)
    {
            $this->orderID = $request->order_id;
            $this->methods = OfflinePaymentMethod::activeMethod();

            return view('orders.offline.index', $this->data);
    }

    public function stripeModal(Request $request)
    {
        $this->orderID = $request->order_id;
        $this->countries = Country::get();
        return view('orders.stripe.index', $this->data);
    }

    public function saveStripeDetail(StoreStripeDetail $request)
    {
        $id = $request->order_id;
        $this->order = Order::with(['client'])->findOrFail($id);
        $this->settings = $this->global;
        $this->credentials = PaymentGatewayCredentials::first();

        $client = null;

        if (isset($this->order) && !is_null($this->order->client_id)) {
            /** @phpstan-ignore-next-line */
            $client = $this->order->client;
        }

        if (($this->credentials->test_stripe_secret || $this->credentials->live_stripe_secret) && !is_null($client)) {
            Stripe::setApiKey($this->credentials->stripe_mode == 'test' ? $this->credentials->test_stripe_secret : $this->credentials->live_stripe_secret);

            $total = $this->order->total;
            $totalAmount = $total;

            $customer = \Stripe\Customer::create([
                'email' => $client->email,
                'name' => $request->clientName,
                'address' => [
                    'line1' => $request->clientName,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                ],
            ]);

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $totalAmount * 100,
                /** @phpstan-ignore-next-line */
                'currency' => $this->order->currency->currency_code,
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session',
                'payment_method_types' => ['card'],
                'description' => $this->order->id . ' Payment',
                'metadata' => ['integration_check' => 'accept_a_payment', 'order_id' => $id]
            ]);

            $this->intent = $intent;
        }

        $customerDetail = [
            'email' => $client->email,
            'name' => $request->clientName,
            'line1' => $request->clientName,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ];

        $this->customerDetail = $customerDetail;

        $view = view('orders.stripe.stripe-payment', $this->data)->render();

        return Reply::dataOnly(['view' => $view, 'intent' => $this->intent]);
    }

    /* This method will be called when payment fails from front end */
    public function paymentFailed($orderId)
    {
        $order = Order::find($orderId);
        $errorMessage = null;

        if(request()->gateway == 'Razorpay'){
            $errorMessage = ['code' => request()->errorMessage['code'], 'message' => request()->errorMessage['description']];
        }

        if(request()->gateway == 'Stripe'){
            $errorMessage = ['code' => request()->errorMessage['type'], 'message' => request()->errorMessage['message']];
        }

        /* make new payment entry with status=failed and other details */
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->currency_id = $order->currency_id;
        $payment->amount = $order->total;
        $payment->gateway = request()->gateway;
        $payment->paid_on = Carbon::now();
        $payment->status = 'failed';
        $payment->payment_gateway_response = $errorMessage;
        $payment->save();

        return Reply::error(__('messages.paymentFailed'));
    }

    public function makeInvoice($orderId)
    {
        /* Step1 -  Set order status paid */
        $order = Order::find($orderId);
        $order->status = 'paid';
        $order->save();

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

        /* Step3 - make payment of recently created invoice_id */
        $payment = new Payment();
        $payment->invoice_id = $invoice->id;
        $payment->order_id = $orderId;
        $payment->currency_id = $order->currency_id;
        $payment->amount = request()->paymentIntent['amount'] / 100;
        $payment->payload_id = request()->paymentIntent['id'];
        $payment->gateway = 'Stripe';
        $payment->paid_on = Carbon::now();
        $payment->status = 'complete';
        $payment->save();

        return Reply::success(__('Order successful'));
    }

}
