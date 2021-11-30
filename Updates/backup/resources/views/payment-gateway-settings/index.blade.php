@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">

                            <a class="nav-item nav-link f-15 active paypal" data-toggle="tab"
                                href="{{ route('payment-gateway-settings.index') }}" role="tab" aria-controls="nav-paypal"
                                aria-selected="true">@lang('app.paypal') <i
                                    class="fa fa-circle ml-1 {{ $credentials->paypal_status == 'active' ? 'text-light-green' : 'text-red' }}"></i></a>

                            <a class="nav-item nav-link f-15 stripe" data-toggle="tab"
                                href="{{ route('payment-gateway-settings.index') }}?tab=stripe" role="tab"
                                aria-controls="nav-stripe" aria-selected="false">@lang('app.stripe') <i
                                    class="fa fa-circle ml-1 {{ $credentials->stripe_status == 'active' ? 'text-light-green' : 'text-red' }}"></i></a>

                            <a class="nav-item nav-link f-15 razorpay" data-toggle="tab"
                                href="{{ route('payment-gateway-settings.index') }}?tab=razorpay" role="tab"
                                aria-controls="nav-razorpay" aria-selected="false">@lang('app.razorpay') <i
                                    class="fa fa-circle ml-1 {{ $credentials->razorpay_status == 'active' ? 'text-light-green' : 'text-red' }}"></i></a>

                            <a class="nav-item nav-link f-15 offline" data-toggle="tab"
                                href="{{ route('payment-gateway-settings.index') }}?tab=offline" role="tab"
                                aria-controls="nav-offline" aria-selected="false">@lang('modules.offlinePayment.title')</a>

                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.button-primary icon="plus" id="addMethod" class="addMethod d-none">
                            @lang('modules.offlinePayment.addMethod')
                        </x-forms.button-primary>
                    </div>
                </div>
            </x-slot>

            {{-- include tabs here --}}
            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->

@endsection

@push('scripts')
    <script>
        /* manage menu active class */
        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        (activeTab == 'offline') ? $('.addMethod').removeClass('d-none'): $('.addMethod').addClass('d-none');

       $("body").on("click", "#editSettings .nav a", function(event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            ($(this).hasClass('offline')) ? $('.addMethod').removeClass('d-none'): $('.addMethod').addClass(
                'd-none');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $('#nav-tabContent').html(response.html);
                        init('.settings-box');
                        init('#nav-tabContent');
                    }
                }
            });
        });

        $("body").on("change", "#paypal_status", function(event) {
            $('#paypal_details').toggleClass('d-none');
        });

        $("body").on("change", "#paypal_mode", function() {
            $('#sandbox_paypal_details').toggleClass('d-none');
            $('#live_paypal_details').toggleClass('d-none');
        });

        $("body").on("change", "#stripe_mode", function() {
            $('#test_stripe_details').toggleClass('d-none');
            $('#live_stripe_details').toggleClass('d-none');
        });

        $("body").on("change", "#razorpay_mode", function() {
            $('#test_razorpay_details').toggleClass('d-none');
            $('#live_razorpay_details').toggleClass('d-none');
        });

        $("body").on("change", "#stripe_status", function(event) {
            $('#stripe_details').toggleClass('d-none');
        });

        $("body").on("change", "#razorpay_status", function(event) {
            $('#razorpay_details').toggleClass('d-none');
        });

        // Save paypal, stripe and razorpay credentials
        $("body").on("click", "#save_paypal_data, #save_stripe_data, #save_razorpay_data", function(event) {
            $.easyAjax({
                url: "{{ route('payment-gateway-settings.update', [$credentials->id]) }}",
                container: '#editSettings',
                type: "POST",
                redirect: true,
                disableButton: true,
                blockUI: true,
                data: $('#editSettings').serialize(),
                success: function() {
                    window.location.reload();
                }
            })
        });

        // Edit new offline payment method
        $('body').on('click', '.edit-type', function() {
            var typeId = $(this).data('type-id');
            var url = "{{ route('offline-payment-setting.edit', ':id') }}";
            url = url.replace(':id', typeId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        // Add new offline payment method
        $('body').on('click', '.addMethod', function() {
            var url = "{{ route('offline-payment-setting.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);

        });

        // Delete offline payment method
        $('body').on('click', '.delete-type', function() {
            var id = $(this).data('type-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.removeMethodText')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {

                    var url = "{{ route('offline-payment-setting.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row' + id).fadeOut();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
