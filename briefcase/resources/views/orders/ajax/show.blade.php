<style>
    #logo {
        height: 33px;
    }
</style>

<!-- INVOICE CARD START -->
@if(!is_null($order->client_id) && !is_null($order->clientdetails))
    @php
        $client = $order->client;
    @endphp
@endif
<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                <i class="fa fa-check"></i> {!! $message !!}
            </div>
            <?php Session::forget('success'); ?>
        @endif

        @if ($message = Session::get('error'))
            <div class="custom-alerts alert alert-danger fade in">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                {!! $message !!}
            </div>
            <?php Session::forget('error'); ?>
        @endif

        <div class="invoice-table-wrapper">
            <table width="100%" class="">
                <tr class="inv-logo-heading">
                    <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ ucwords($global->company_name) }}"
                            id="logo" /></td>
                    <td align="right" class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('app.order')</td>
                </tr>
                <tr class="inv-num">
                    <td class="f-14 text-dark">
                        {{ ucwords($order->client->name) }}<br>
                        {{ ucwords($order->client->clientDetails->company_name) }}<br>
                    </td>
                    <td align="right">
                        <table class="inv-num-date text-dark f-13 mt-3">
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('modules.orders.orderNumber')</td>
                                <td class="border-left-0">{{ $pageTitle }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('modules.orders.orderDate')</td>
                                <td class="border-left-0">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format($global->date_format) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="50"></td>
                </tr>
            </table>
            <table width="100%">
                <tr class="inv-unpaid">

                    <td align="right" class="mt-4 mt-lg-0 mt-md-0">
                        @if ($order->credit_note)
                            <span class="unpaid text-warning border-warning">@lang('app.credit-note')</span>
                        @else
                            <span
                                class="unpaid {{ $order->status == 'partial' ? 'text-primary border-primary' : '' }} {{ $order->status == 'paid' ? 'text-success border-success' : '' }} rounded f-15 ">@lang('modules.invoices.'.$order->status)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
            </table>
            <table width="100%" class="inv-desc d-none d-lg-table d-md-table">
                <tr>
                    <td colspan="2">
                        <table class="inv-detail f-14 table-responsive-sm" width="100%">
                            <tr class="i-d-heading bg-light-grey text-dark-grey font-weight-bold">
                                <td class="border-right-0">@lang('app.description')</td>
                                @if($invoiceSetting->hsn_sac_code_show)
                                    <td class="border-right-0 border-left-0" align="right">@lang("app.hsnSac")</td>
                                @endif
                                <td class="border-right-0 border-left-0" align="right">@lang("modules.invoices.qty")
                                </td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang("modules.invoices.unitPrice") ({{ $order->currency->currency_code }})
                                </td>
                                <td class="border-left-0" align="right">
                                    @lang("modules.invoices.amount")
                                    ({{ $order->currency->currency_code }})</td>
                            </tr>
                            @foreach ($order->items as $item)
                                @if ($item->type == 'item')
                                    <tr class="text-dark">
                                        <td>{{ ucfirst($item->item_name) }}</td>
                                        @if($invoiceSetting->hsn_sac_code_show)
                                            <td align="right">{{ $item->hsn_sac_code }}</td>
                                        @endif
                                        <td align="right">{{ $item->quantity }}</td>
                                        <td align="right">
                                            {{ number_format((float) $item->unit_price, 2, '.', '') }}</td>
                                        <td align="right">{{ number_format((float) $item->amount, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    @if ($item->item_summary != '')
                                        <tr class="text-dark">
                                            <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '5' : '4' }}" class="border-bottom-0">{!! $item->item_summary !!}
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach


                            <tr>
                                <td colspan="2" class="blank-td border-bottom-0 border-left-0 border-right-0"></td>
                                <td colspan="3" class="p-0 ">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="w-50 border-top-0 border-left-0">
                                                @lang("modules.invoices.subTotal")</td>
                                            <td class="border-top-0 border-right-0">
                                                {{ number_format((float) $order->sub_total, 2, '.', '') }}</td>
                                        </tr>
                                        @if ($discount != 0 && $discount != '')
                                            <tr class="text-dark-grey" align="right">
                                                <td class="w-50 border-top-0 border-left-0">
                                                    @lang("modules.invoices.discount")</td>
                                                <td class="border-top-0 border-right-0">
                                                    {{ number_format((float) $discount, 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                        @foreach ($taxes as $key => $tax)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="w-50 border-top-0 border-left-0">
                                                    {{ strtoupper($key) }}</td>
                                                <td class="border-top-0 border-right-0">
                                                    {{ number_format((float) $tax, 2, '.', '') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang("modules.invoices.total")</td>
                                            <td class="border-bottom-0 border-right-0">
                                                {{ number_format((float) $order->total, 2, '.', '') }}
                                                {{ $order->currency->currency_code }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <table width="100%" class="inv-desc-mob d-block d-lg-none d-md-none">

                @foreach ($order->items as $item)
                    @if ($item->type == 'item')

                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang('app.description')</th>
                            <td class="p-0 ">
                                <table>
                                    <tr width="100%">
                                        <td class="border-left-0 border-right-0 border-top-0">
                                            {{ ucfirst($item->item_name) }}</td>
                                    </tr>
                                    @if ($item->item_summary != '')
                                        <tr>
                                            <td class="border-left-0 border-right-0 border-bottom-0">
                                                {!! $item->item_summary !!}</td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang("modules.invoices.qty")</th>
                            <td width="50%">{{ $item->quantity }}</td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang("modules.invoices.unitPrice")
                                ({{ $order->currency->currency_code }})</th>
                            <td width="50%">{{ number_format((float) $item->unit_price, 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang("modules.invoices.amount")
                                ({{ $order->currency->currency_code }})</th>
                            <td width="50%">{{ number_format((float) $item->amount, 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <td height="3" class="p-0 " colspan="2"></td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <th width="50%" class="text-dark-grey font-weight-normal">@lang("modules.invoices.subTotal")
                    </th>
                    <td width="50%" class="text-dark-grey font-weight-normal">
                        {{ number_format((float) $order->sub_total, 2, '.', '') }}</td>
                </tr>
                @if ($discount != 0 && $discount != '')
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">@lang("modules.invoices.discount")
                        </th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ number_format((float) $discount, 2, '.', '') }}</td>
                    </tr>
                @endif

                @foreach ($taxes as $key => $tax)
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">{{ strtoupper($key) }}</th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ number_format((float) $tax, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th width="50%" class="text-dark-grey font-weight-bold">@lang("modules.invoices.total")</th>
                    <td width="50%" class="text-dark-grey font-weight-bold">
                        {{ number_format((float) $order->total, 2, '.', '') }}</td>
                </tr>
            </table>
            <table class="inv-note">
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>@lang('app.clientNote')</tr>
                            <tr>
                                <p class="text-dark-grey">{!! $order->note ?? '--' !!}</p>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- CARD BODY END -->
    <!-- CARD FOOTER START -->
    <div class="card-footer bg-white border-0 d-flex justify-content-start py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">

        <div class="d-flex">


            {{-- PAYMENT GATEWAY --}}
            @if (in_array('client', user_roles()) && $order->total > 0 && $order->status != 'paid' 
            && ($credentials->paypal_status == 'active' || $credentials->stripe_status == 'active' || $credentials->razorpay_status == 'active'))
                <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
                    <button class="dropdown-toggle btn-primary rounded mr-3 mr-lg-0 mr-md-0 f-15" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">@lang('modules.invoices.payNow')
                        <span><i class="fa fa-chevron-down f-15"></i></span>
                    </button>
                    <!-- DROPDOWN - INFORMATION -->
                    <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton" tabindex="0">
                        @if ($credentials->stripe_status == 'active')
                            <li>
                                <a class="dropdown-item f-14 text-dark" href="javascript:;"
                                    data-order-id="{{ $order->id }}" id="stripeModal">
                                    <i class="fab fa-stripe-s f-w-500 mr-2 f-11"></i>
                                    @lang('modules.invoices.payStripe')
                                </a>
                            </li>
                        @endif
                        @if ($credentials->razorpay_status == 'active')
                            <li>
                                <a class="dropdown-item f-14 text-dark" href="javascript:;" id="razorpayPaymentButton">
                                    <i class="fa fa-credit-card f-w-500 mr-2 f-11"></i>
                                    @lang('modules.invoices.payRazorpay')
                                </a>
                            </li>
                        @endif
                        @if ($credentials->paypal_status == 'active')
                            <li>
                                <a class="dropdown-item f-14 text-dark" href="{{ route('paypal', [$order->id]) }}?type=order">
                                    <i class="fab fa-paypal f-w-500 mr-2 f-11"></i> @lang('modules.invoices.payPaypal')
                                </a>
                            </li>
                        @endif
                        @if ($methods->count() > 0)
                            <li>
                                <a class="dropdown-item f-14 text-dark" href="javascript:;" id="offlinePaymentModal"
                                    data-invoice-id="{{ $order->id }}">
                                    <i class="fa fa-money-bill f-w-500 mr-2 f-11"></i>
                                    @lang('modules.invoices.payOffline')
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
            {{-- PAYMENT GATEWAY --}}

        </div>

        <x-forms.button-cancel :link="route('orders.index')" class="border-0 mr-3">@lang('app.cancel')
        </x-forms.button-cancel>
    </div>
    <!-- CARD FOOTER END -->

</div>
<!-- INVOICE CARD END -->

{{-- Custom fields data --}}
@if (isset($fields) && count($fields) > 0)
    <div class="row mt-4">
        <!-- TASK STATUS START -->
        <div class="col-md-12">
            <x-cards.data>
                @foreach ($fields as $field)
                    @if ($field->type == 'text' || $field->type == 'password' || $field->type == 'number')
                        <x-cards.data-row :label="$field->label"
                            :value="$order->custom_fields_data['field_'.$field->id] ?? '--'" />
                    @elseif($field->type == 'textarea')
                        <x-cards.data-row :label="$field->label" html="true"
                            :value="$order->custom_fields_data['field_'.$field->id] ?? '--'" />
                    @elseif($field->type == 'radio')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($order->custom_fields_data['field_' . $field->id]) ? $order->custom_fields_data['field_' . $field->id] : '--')" />
                    @elseif($field->type == 'select')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($order->custom_fields_data['field_' . $field->id]) && $order->custom_fields_data['field_' . $field->id] != '' ? $field->values[$order->custom_fields_data['field_' . $field->id]] : '--')" />
                    @elseif($field->type == 'date')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($order->custom_fields_data['field_' . $field->id]) && $order->custom_fields_data['field_' . $field->id] != '' ? \Carbon\Carbon::parse($order->custom_fields_data['field_' . $field->id])->format($global->date_format) : '--')" />
                    @endif
                @endforeach
            </x-cards.data>
        </div>
    </div>
@endif


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $('body').on('click', '#stripeModal', function() {

        let orderId = $(this).data('order-id');
        let queryString = "?order_id=" + orderId;

        let url = "{{ route('orders.stripe_modal') }}" + queryString;

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '#offlinePaymentModal', function() {

        let orderId = $(this).data('order-id');
        let queryString = "?order_id=" + orderId;

        let url = "{{ route('orders.offline_payment_modal') }}" + queryString;

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    @if ($credentials->razorpay_status == 'active')
        $('body').on('click', '#razorpayPaymentButton', function() {
        var amount = "{{ number_format((float) $order->total, 2, '.', '') * 100 }}";
        var orderId = {{ $order->id }};
        var clientEmail = "{{ $user->email }}";

        var options = {
            "key": "{{ $credentials->razorpay_mode == 'test' ? $credentials->test_razorpay_key : $credentials->live_razorpay_key }}",
            "amount": amount,
            "currency": '{{ $order->currency->currency_code }}',
            "name": "{{ $companyName }}",
            "description": "Invoice Payment",
            "image": "{{ $global->logo_url }}",
            "handler": function (response) {
                confirmRazorpayPayment(response.razorpay_payment_id, orderId);
            },
            "modal": {
            "ondismiss": function () {
            // On dismiss event
            }
            },
            "prefill": {
                "email": clientEmail
            },
            "notes": {
                "purchase_id": orderId //invoice ID
            }
        };
        var rzp1 = new Razorpay(options);

        /* Make an entry to payment table when payment fails */
        rzp1.on('payment.failed', function (response){
            /* Response will be like this - code: "BAD_REQUEST_ERROR", reason: "payment_failed"
                , description: "Payment failed"
            */

            url = "{{ route('orders.payment_failed', ':id') }}";
            url = url.replace(':id', orderId);

            $.easyAjax({
                url: url,
                type: "POST",
                data: {errorMessage: response.error, gateway: 'Razorpay',  "_token" : "{{ csrf_token() }}"},
            })
        });

        rzp1.open();

        })

        // Confirmation after transaction
        function confirmRazorpayPayment(id, orderId) {
            $.easyAjax({
                type: 'POST',
                url: "{{ route('pay_with_razorpay') }}",
                data: {_token:'{{ csrf_token() }}', paymentId: id, orderId: orderId, type: 'order'}
            })
        }

    @endif

</script>
