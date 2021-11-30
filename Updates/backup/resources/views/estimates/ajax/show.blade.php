<style>
    #logo {
        height: 33px;
    }

    .signature_wrap {
        position: relative;
        height: 150px;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width: 400px;
    }

    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width: 400px;
        height: 150px;
    }
</style>

<!-- INVOICE CARD START -->

<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">
        <div class="invoice-table-wrapper">
            <table width="100%" class="">
                <tr class="inv-logo-heading">
                    <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ ucwords($global->company_name) }}"
                            id="logo" /></td>
                    <td align="right" class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('app.estimate')</td>
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
                                    @lang('modules.estimates.estimatesNumber')</td>
                                <td class="border-left-0">{{ $invoice->estimate_number }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('modules.estimates.validTill')</td>
                                <td class="border-left-0">{{ $invoice->valid_till->format($global->date_format) }}
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
                            {{ ucwords($invoice->client->name) }}<br>
                            {{ ucwords($invoice->client->clientDetails->company_name) }}<br>
                            {!! nl2br($invoice->client->clientDetails->address) !!}</p>
                    </td>

                    <td align="right" class="mt-4 mt-lg-0 mt-md-0">
                        <span
                            class="unpaid {{ $invoice->status == 'draft' ? 'text-primary border-primary' : '' }} {{ $invoice->status == 'accepted' ? 'text-success border-success' : '' }} rounded f-15 ">@lang('modules.estimates.'.$invoice->status)</span>
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
                                <td class="border-right-0 border-left-0" align="right">@lang("modules.invoices.qty")</td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang("modules.invoices.unitPrice") ({{ $invoice->currency->currency_code }})
                                </td>
                                <td class="border-left-0" align="right">
                                    @lang("modules.invoices.amount")
                                    ({{ $invoice->currency->currency_code }})</td>
                            </tr>
                            @foreach ($invoice->items as $item)
                                @if ($item->type == 'item')
                                    <tr class="text-dark">
                                        <td>{{ ucfirst($item->item_name) }}</td>
                                        @if($invoiceSetting->hsn_sac_code_show)
                                            <td align="right">{{ $item->hsn_sac_code ? $item->hsn_sac_code : '--' }}</td>
                                        @endif
                                        <td align="right">{{ $item->quantity }}</td>
                                        <td align="right">
                                            {{ number_format((float) $item->unit_price, 2, '.', '') }}</td>
                                        <td align="right">{{ number_format((float) $item->amount, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    @if ($item->item_summary != '')
                                        <tr class="text-dark">
                                            <td colspan="5" class="border-bottom-0">{{ $item->item_summary }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach

                            <tr>
                                <td colspan="2" class="blank-td border-bottom-0 border-left-0 border-right-0"></td>
                                <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? 3 : 2 }}" class="p-0 ">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="w-50 border-top-0 border-left-0">
                                                @lang("modules.invoices.subTotal")</td>
                                            <td class="border-top-0 border-right-0">
                                                {{ number_format((float) $invoice->sub_total, 2, '.', '') }}</td>
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
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang("modules.invoices.total")</td>
                                            <td class="border-bottom-0 border-right-0">
                                                {{ number_format((float) $invoice->total, 2, '.', '') }}</td>
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
                                                {{ $item->item_summary }}</td>
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
                            <td width="50%">{{ number_format((float) $item->unit_price, 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang("modules.invoices.amount")
                                ({{ $invoice->currency->currency_code }})</th>
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
                        {{ number_format((float) $invoice->sub_total, 2, '.', '') }}</td>
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
                        {{ number_format((float) $invoice->total, 2, '.', '') }}</td>
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
                                <p class="text-dark-grey">{!! $invoice->note ?? '--' !!}</p>
                            </tr>
                        </table>
                    </td>
                    <td align="right">
                        <table>
                            <tr>@lang('modules.invoiceSettings.invoiceTerms')</tr>
                            <tr>
                                <p class="text-dark-grey">{!! nl2br($invoiceSetting->invoice_terms) !!}</p>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        @if ($invoice->sign)
            <div class="row">
                <div class="col-sm-12 mt-4">
                    <h6>@lang('modules.estimates.signature')</h6>
                    <img src="{{ $invoice->sign->signature }}" style="width: 200px;">
                    <p>({{ $invoice->sign->full_name }})</p>
                </div>
            </div>
        @endif

    </div>
    <!-- CARD BODY END -->
    <!-- CARD FOOTER START -->
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">

        <x-forms.button-cancel :link="route('estimates.index')" class="border-0 mr-3">@lang('app.cancel')
        </x-forms.button-cancel>

        <div class="d-flex">
            <div class="inv-action dropup">
                <button class="dropdown-toggle btn-secondary" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.action')
                    <span><i class="fa fa-chevron-down f-15 text-dark-grey"></i></span>
                </button>
                <!-- DROPDOWN - INFORMATION -->
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" tabindex="0">
                    @if ($invoice->status == 'waiting' && $invoice->client_id == user()->id)
                        <li>
                            <a class="dropdown-item f-14 text-dark" data-toggle="modal" data-target="#signature-modal"
                                href="javascript:;">
                                <i class="fa fa-check f-w-500 mr-2 f-11"></i> @lang('app.accept')
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item f-14 text-dark" id="decline-estimate" href="javascript:;">
                                <i class="fa fa-times f-w-500 mr-2 f-11"></i> @lang('app.decline')
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item f-14 text-dark"
                            href="{{ route('estimates.download', [$invoice->id]) }}">
                            <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                        </a>
                    </li>
                </ul>
            </div>

        </div>
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
                            :value="$invoice->custom_fields_data['field_'.$field->id] ?? '--'" />
                    @elseif($field->type == 'textarea')
                        <x-cards.data-row :label="$field->label" html="true"
                            :value="$invoice->custom_fields_data['field_'.$field->id] ?? '--'" />
                    @elseif($field->type == 'radio')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($invoice->custom_fields_data['field_' . $field->id]) ? $invoice->custom_fields_data['field_' . $field->id] : '--')" />
                    @elseif($field->type == 'checkbox')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($invoice->custom_fields_data['field_' . $field->id]) ? $invoice->custom_fields_data['field_' . $field->id] : '--')" />
                    @elseif($field->type == 'select')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($invoice->custom_fields_data['field_' . $field->id]) && $invoice->custom_fields_data['field_' . $field->id] != '' ? $field->values[$invoice->custom_fields_data['field_' . $field->id]] : '--')" />
                    @elseif($field->type == 'date')
                        <x-cards.data-row :label="$field->label"
                            :value="(!is_null($invoice->custom_fields_data['field_' . $field->id]) && $invoice->custom_fields_data['field_' . $field->id] != '' ? \Carbon\Carbon::parse($invoice->custom_fields_data['field_' . $field->id])->format($global->date_format) : '--')" />
                    @endif
                @endforeach
            </x-cards.data>
        </div>
    </div>
@endif


<div id="signature-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog d-flex justify-content-center align-items-center modal-xl">
        <div class="modal-content">
            @include('estimates.ajax.accept-estimate')
        </div>
    </div>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        var canvas = document.getElementById('signature-pad');

        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
        });

        document.getElementById('clear-signature').addEventListener('click', function(e) {
            e.preventDefault();
            signaturePad.clear();
        });

        document.getElementById('undo-signature').addEventListener('click', function(e) {
            e.preventDefault();
            var data = signaturePad.toData();
            if (data) {
                data.pop(); // remove the last dot or line
                signaturePad.fromData(data);
            }
        });

        $('#decline-estimate').click(function() {
            $.easyAjax({
                type: 'POST',
                url: "{{ route('estimates.decline', $invoice->id) }}",
                blockUI: true,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        });

        $('#save-signature').click(function() {
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var signature = signaturePad.toDataURL('image/png');

            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'error',
                    text: '{{ __('messages.signatureRequired') }}',

                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                });
                return false;
            }


            $.easyAjax({
                url: "{{ route('estimates.accept', $invoice->id) }}",
                container: '#acceptEstimate',
                type: "POST",
                blockUI: true,
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    email: email,
                    signature: signature,
                    _token: '{{ csrf_token() }}'
                },
            })
        });

    </script>
@endpush
