<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/css/select2.min.css') }}">

    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/simple-line-icons.css') }}">

    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>@lang($pageTitle)</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $global->favicon_url }}">
    <meta name="theme-color" content="#ffffff">

    @isset($activeSettingMenu)
        <style>
            .preloader-container {
                margin-left: 510px;
                width: calc(100% - 510px)
            }

        </style>
    @endisset

    @stack('styles')

    <style>
        :root {
            --fc-border-color: #E8EEF3;
            --fc-button-text-color: #99A5B5;
            --fc-button-border-color: #99A5B5;
            --fc-button-bg-color: #ffffff;
            --fc-button-active-bg-color: #171f29;
            --fc-today-bg-color: #f2f4f7;
        }

        .preloader-container {
            height: 100vh;
            width: 100%;
            margin-left: 0;
            margin-top: 0;
        }

        .fc a[data-navlink] {
            color: #99a5b5;
        }

    </style>
    <style>
        #logo {
            height: 33px;
        }

    </style>


    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/modernizr.min.js') }}"></script>

    <script>
        var checkMiniSidebar = localStorage.getItem("mini-sidebar");
    </script>

</head>


<body id="body">

    <!-- BODY WRAPPER START -->
    <div class="body-wrapper clearfix">


        <!-- MAIN CONTAINER START -->
        <section class="bg-additional-grey" id="fullscreen">

            <div class="preloader-container d-flex justify-content-center align-items-center">
                <div class="spinner-border" role="status" aria-hidden="true"></div>
            </div>

            <x-app-title class="d-block d-lg-none" :pageTitle="__($pageTitle)"></x-app-title>

            <!-- CONTENT WRAPPER START -->
            <div class="content-wrapper container">
                <!-- INVOICE CARD START -->
                @if (!is_null($invoice->project) && !is_null($invoice->project->client) && !is_null($invoice->project->client->clientDetails))
                    @php
                        $client = $invoice->project->client;
                    @endphp
                @elseif(!is_null($invoice->client_id) && !is_null($invoice->clientdetails))
                    @php
                        $client = $invoice->client;
                    @endphp
                @endif
                <div class="card border-0 invoice">
                    <!-- CARD BODY START -->
                    <div class="card-body">

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true"></button>
                                <i class="fa fa-check"></i> {!! $message !!}
                            </div>
                            <?php Session::forget('success'); ?>
                        @endif

                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true"></button>
                                {!! $message !!}
                            </div>
                            <?php Session::forget('error'); ?>
                        @endif

                        <div class="invoice-table-wrapper">
                            <table width="100%" class="___class_+?14___">
                                <tr class="inv-logo-heading">
                                    <td><img src="{{ invoice_setting()->logo_url }}"
                                            alt="{{ ucwords($global->company_name) }}" id="logo" /></td>
                                    <td align="right"
                                        class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                                        @lang('app.invoice')</td>
                                </tr>
                                <tr class="inv-num">
                                    <td class="f-14 text-dark">
                                        <p class="mt-3 mb-0">
                                            {{ ucwords($global->company_name) }}<br>
                                            @if (!is_null($settings))
                                                {!! nl2br($global->address) !!}<br>
                                                {{ $global->company_phone }}
                                            @endif
                                            @if ($invoiceSetting->show_gst == 'yes' && !is_null($invoiceSetting->gst_number))
                                                <br>@lang('app.gstIn'): {{ $invoiceSetting->gst_number }}
                                            @endif
                                        </p><br>
                                    </td>
                                    <td align="right">
                                        <table class="inv-num-date text-dark f-13 mt-3">
                                            <tr>
                                                <td class="bg-light-grey border-right-0 f-w-500">
                                                    @lang('modules.invoices.invoiceNumber')</td>
                                                <td class="border-left-0">{{ $invoice->invoice_number }}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light-grey border-right-0 f-w-500">
                                                    @lang('modules.invoices.invoiceDate')</td>
                                                <td class="border-left-0">
                                                    {{ $invoice->issue_date->format($global->date_format) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light-grey border-right-0 f-w-500">@lang('app.dueDate')
                                                </td>
                                                <td class="border-left-0">
                                                    {{ $invoice->due_date->format($global->date_format) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20"></td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr class="inv-unpaid">
                                    <td class="f-14 text-dark">
                                        <p class="mb-0 text-left"><span
                                                class="text-dark-grey text-capitalize">@lang("modules.invoices.billedTo")</span><br>
                                            {{ ucwords($client->name) }}<br>
                                            {{ ucwords($client->clientDetails->company_name) }}<br>
                                            {!! nl2br($client->clientDetails->address) !!}</p>
                                    </td>
                                    @if ($invoice->shipping_address)
                                        <td class="f-14 text-black">
                                            <p class="mb-0 text-left"><span
                                                    class="text-dark-grey text-capitalize">@lang("app.shippingAddress")</span><br>
                                                {!! nl2br($client->clientDetails->address) !!}</p>
                                        </td>
                                    @endif
                                    <td align="right" class="mt-4 mt-lg-0 mt-md-0">
                                        <span
                                            class="unpaid {{ $invoice->status == 'partial' ? 'text-primary border-primary' : '' }} {{ $invoice->status == 'paid' ? 'text-success border-success' : '' }} rounded f-15 ">@lang('modules.invoices.'.$invoice->status)</span>
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
                                                <td class="border-right-0 border-left-0" align="right">
                                                    @lang("modules.invoices.qty")
                                                </td>
                                                <td class="border-right-0 border-left-0" align="right">
                                                    @lang("modules.invoices.unitPrice")
                                                    ({{ $invoice->currency->currency_code }})
                                                </td>
                                                <td class="border-left-0" align="right">
                                                    @lang("modules.invoices.amount")
                                                    ({{ $invoice->currency->currency_code }})</td>
                                            </tr>
                                            @foreach ($invoice->items as $item)
                                                @if ($item->type == 'item')
                                                    <tr class="text-dark">
                                                        <td>{{ ucfirst($item->item_name) }}</td>
                                                        <td align="right">{{ $item->quantity }}</td>
                                                        <td align="right">
                                                            {{ number_format((float) $item->unit_price, 2, '.', '') }}
                                                        </td>
                                                        <td align="right">
                                                            {{ number_format((float) $item->amount, 2, '.', '') }}
                                                        </td>
                                                    </tr>
                                                    @if ($item->item_summary != '')
                                                        <tr class="text-dark">
                                                            <td colspan="4" class="border-bottom-0">
                                                                {!! $item->item_summary !!}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach


                                            <tr>
                                                <td colspan="2"
                                                    class="blank-td border-bottom-0 border-left-0 border-right-0"></td>
                                                <td colspan="2" class="p-0 ">
                                                    <table width="100%">
                                                        <tr class="text-dark-grey" align="right">
                                                            <td class="w-50 border-top-0 border-left-0">
                                                                @lang("modules.invoices.subTotal")</td>
                                                            <td class="border-top-0 border-right-0">
                                                                {{ number_format((float) $invoice->sub_total, 2, '.', '') }}
                                                            </td>
                                                        </tr>
                                                        @if ($discount != 0 && $discount != '')
                                                            <tr class="text-dark-grey" align="right">
                                                                <td class="w-50 border-top-0 border-left-0">
                                                                    @lang("modules.invoices.discount")</td>
                                                                <td class="border-top-0 border-right-0">
                                                                    {{ number_format((float) $discount, 2, '.', '') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @foreach ($taxes as $key => $tax)
                                                            <tr class="text-dark-grey" align="right">
                                                                <td class="w-50 border-top-0 border-left-0">
                                                                    {{ strtoupper($key) }}</td>
                                                                <td class="border-top-0 border-right-0">
                                                                    {{ number_format((float) $tax, 2, '.', '') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                                            <td class="w-50 border-bottom-0 border-left-0">
                                                                @lang("modules.invoices.total")</td>
                                                            <td class="border-bottom-0 border-right-0">
                                                                {{ number_format((float) $invoice->total, 2, '.', '') }}
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                                            <td class="w-50 border-bottom-0 border-left-0">
                                                                @lang("modules.invoices.total")
                                                                @lang("modules.invoices.due")</td>
                                                            <td class="border-bottom-0 border-right-0">
                                                                {{ number_format((float) $invoice->amountDue(), 2, '.', '') }}
                                                                {{ $invoice->currency->currency_code }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                </tr>
                            </table>
                            <table width="100%" class="inv-desc-mob d-block d-lg-none d-md-none">

                                @foreach ($invoice->items as $item)
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
                                                ({{ $invoice->currency->currency_code }})</th>
                                            <td width="50%">
                                                {{ number_format((float) $item->unit_price, 2, '.', '') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                                @lang("modules.invoices.amount")
                                                ({{ $invoice->currency->currency_code }})</th>
                                            <td width="50%">{{ number_format((float) $item->amount, 2, '.', '') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="3" class="p-0 " colspan="2"></td>
                                        </tr>
                                    @endif
                                @endforeach

                                <tr>
                                    <th width="50%" class="text-dark-grey font-weight-normal">
                                        @lang("modules.invoices.subTotal")
                                    </th>
                                    <td width="50%" class="text-dark-grey font-weight-normal">
                                        {{ number_format((float) $invoice->sub_total, 2, '.', '') }}</td>
                                </tr>
                                @if ($discount != 0 && $discount != '')
                                    <tr>
                                        <th width="50%" class="text-dark-grey font-weight-normal">
                                            @lang("modules.invoices.discount")
                                        </th>
                                        <td width="50%" class="text-dark-grey font-weight-normal">
                                            {{ number_format((float) $discount, 2, '.', '') }}</td>
                                    </tr>
                                @endif

                                @foreach ($taxes as $key => $tax)
                                    <tr>
                                        <th width="50%" class="text-dark-grey font-weight-normal">
                                            {{ strtoupper($key) }}</th>
                                        <td width="50%" class="text-dark-grey font-weight-normal">
                                            {{ number_format((float) $tax, 2, '.', '') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th width="50%" class="text-dark-grey font-weight-bold">
                                        @lang("modules.invoices.total")</th>
                                    <td width="50%" class="text-dark-grey font-weight-bold">
                                        {{ number_format((float) $invoice->total, 2, '.', '') }}</td>
                                </tr>
                                <tr>
                                    <th width="50%" class="f-16 bg-light-grey text-dark font-weight-bold">
                                        @lang("modules.invoices.total")
                                        @lang("modules.invoices.due")</th>
                                    <td width="50%" class="f-16 bg-light-grey text-dark font-weight-bold">
                                        {{ number_format((float) $invoice->total, 2, '.', '') }}
                                        {{ $invoice->currency->currency_code }}</td>
                                </tr>
                            </table>
                            <table class="inv-note">
                                <tr>
                                    <td height="30" colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table>
                                            <tr>@lang('app.note')</tr>
                                            <tr>
                                                <p class="text-dark-grey">{!! nl2br($invoice->note) !!}</p>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr>@lang('modules.invoiceSettings.invoiceTerms')</tr>
                                            <tr>
                                                <p class="text-dark-grey">{!! nl2br($invoiceSetting->invoice_terms) !!}
                                                </p>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- CARD BODY END -->
                    <!-- CARD FOOTER START -->
                    <div
                        class="card-footer bg-white border-0 d-flex justify-content-end py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">

                        <x-forms.link-secondary icon="download" :link="route('front.invoice_download', [md5($invoice->id)])" class="mr-3">@lang('app.download')</x-forms.link-secondary>

                        <div class="d-flex">
                            @if ($invoice->total > 0 && $invoice->status != 'paid' && ($credentials->paypal_status == 'active' || $credentials->stripe_status == 'active' || $credentials->razorpay_status == 'active'))
                                <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
                                    <button class="dropdown-toggle btn-secondary" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @lang('modules.invoices.payNow')
                                        <span><i class="fa fa-chevron-down f-15"></i></span>
                                    </button>
                                    <!-- DROPDOWN - INFORMATION -->
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                        tabindex="0">
                                        @if ($credentials->paypal_status == 'active')
                                            <li>
                                                <a class="dropdown-item f-14 text-dark"
                                                    href="{{ route('paypal_public', [$invoice->id]) }}">
                                                    <i class="fab fa-paypal f-w-500 mr-2 f-11"></i>
                                                    @lang('modules.invoices.payPaypal')
                                                </a>
                                            </li>
                                        @endif
                                        @if ($credentials->razorpay_status == 'active')
                                            <li>
                                                <a class="dropdown-item f-14 text-dark" href="javascript:;"
                                                    id="razorpayPaymentButton">
                                                    <i class="fa fa-credit-card f-w-500 mr-2 f-11"></i>
                                                    @lang('modules.invoices.payRazorpay')
                                                </a>
                                            </li>
                                        @endif
                                        @if ($credentials->stripe_status == 'active')
                                            <li>
                                                <a class="dropdown-item f-14 text-dark" href="javascript:;"
                                                    data-invoice-id="{{ $invoice->id }}" id="stripeModal">
                                                    <i class="fab fa-stripe-s f-w-500 mr-2 f-11"></i>
                                                    @lang('modules.invoices.payStripe')
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- CARD FOOTER END -->
                </div>
                <!-- INVOICE CARD END -->

            </div>
            <!-- CONTENT WRAPPER END -->


        </section>
        <!-- MAIN CONTAINER END -->
    </div>
    <!-- BODY WRAPPER END -->

    <!-- also the modal itself -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog d-flex justify-content-center align-items-center modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelHeading">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    Some content
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel rounded mr-3" data-dismiss="modal">Close</button>
                    <button type="button" class="btn-primary rounded">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Required Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://js.stripe.com/v3/"></script>


    <script>
        const MODAL_LG = '#myModal';
        const MODAL_HEADING = '#modelHeading';

        const dropifyMessages = {
            default: '@lang("app.dragDrop")',
            replace: '@lang("app.dragDropReplace")',
            remove: '@lang("app.remove")',
            error: '@lang("app.largeFile")'
        };

        $(window).on('load', function() {
            // Animate loader off screen
            init();
            $(".preloader-container").fadeOut("slow", function() {
                $(this).removeClass("d-flex");
            });
        });

        @if ($credentials->stripe_status == 'active')

            $('body').on('click', '#stripeModal', function() {

                let invoiceId = $(this).data('invoice-id');
                let queryString = "?invoice_id="+invoiceId;

                let url = "{{ route('front.stripe_modal') }}"+queryString;

                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_LG, url);

            })

        @endif

        @if ($credentials->razorpay_status == 'active')
            $('body').on('click', '#razorpayPaymentButton', function() {
            var amount = {{ number_format((float) $invoice->amountDue(), 2, '.', '') * 100 }};

            var invoiceId = {{ $invoice->id }};
            @if ($invoice->project && $invoice->project->client)
                var clientEmail = "{{ $invoice->project->client->email }}";
            @else
                var clientEmail = "{{ $invoice->client->email }}";
            @endif

            var options = {
                "key": "{{ $credentials->razorpay_mode == 'test' ? $credentials->test_razorpay_key : $credentials->live_razorpay_key }}",
                "amount": amount,
                "currency": '{{ $invoice->currency->currency_code }}',
                "name": "{{ $companyName }}",
                "description": "Invoice Payment",
                "image": "{{ $global->logo_url }}",
                "handler": function (response) {
                    confirmRazorpayPayment(response.razorpay_payment_id, invoiceId);
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
                    "purchase_id": invoiceId //invoice ID
                }
            };

            var rzp1 = new Razorpay(options);

            /* Make an entry to payment table when payment fails */
            rzp1.on('payment.failed', function (response){
                /* Response will be like this - code: "BAD_REQUEST_ERROR", reason: "payment_failed"
                    , description: "Payment failed"
                */

                url = "{{ route('front.invoice_payment_failed', ':id') }}";
                url = url.replace(':id', invoiceId);

                $.easyAjax({
                    url: url,
                    type: "POST",
                    data: {errorMessage: response.error, gateway: 'Razorpay',  "_token" : "{{ csrf_token() }}"},
                })
            });

            rzp1.open();

            })

            //Confirmation after transaction
            function confirmRazorpayPayment(id,invoiceId) {
                $.easyAjax({
                type : 'POST',
                url : "{{ route('pay_with_razorpay') }}",
                data : {paymentId: id, invoiceId: invoiceId, _token:'{{ csrf_token() }}'}
                })
            }

        @endif

    </script>

</body>

</html>
