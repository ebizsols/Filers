@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.accountSettings.companyName')"
                            fieldPlaceholder="e.g. Acme Corporation" fieldRequired="true" fieldName="company_name"
                            fieldId="company_name" :fieldValue="$global->company_name" />
                    </div>
                    <div class="col-lg-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.accountSettings.companyEmail')"
                            :fieldPlaceholder="__('placeholders.email')" fieldRequired="true" fieldName="company_email"
                            fieldId="company_email" :fieldValue="$global->company_email" />

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.accountSettings.companyPhone')"
                            fieldPlaceholder="e.g. +19876543" fieldRequired="true" fieldName="company_phone"
                            fieldId="company_phone" :fieldValue="$global->company_phone" />
                    </div>
                    <div class="col-lg-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                            :fieldLabel="__('modules.accountSettings.companyWebsite')"
                            fieldPlaceholder="e.g. https://www.spacex.com/" fieldRequired="false" fieldName="website"
                            fieldId="website" :fieldValue="$global->website" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                            :fieldLabel="__('modules.accountSettings.companyAddress')" fieldName="address" fieldId="address"
                            :fieldValue="$global->address" fieldRequired="true"
                            fieldPlaceholder="e.g. Hawthorne, California, United States"></x-forms.textarea>
                    </div>
                </div>


            </div>

            <x-slot name="action">
                <!-- Buttons Start -->
                <div class="w-100 border-top-grey">
                    <x-setting-form-actions>
                        <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>

                        <x-forms.button-cancel :link="url()->previous()" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                        </x-settingsform-actions>
                        {{-- <div class="d-flex d-lg-none d-md-none p-4">
                            <div class="d-flex w-100">
                                <x-forms.button-primary class="mr-3 w-100" icon="check">@lang('app.save')
                                </x-forms.button-primary>
                            </div>
                            <x-forms.button-cancel :link="url()->previous()" class="w-100 ">@lang('app.cancel')
                            </x-forms.button-cancel>
                        </div> --}}
                </div>
                <!-- Buttons End -->
            </x-slot>

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>
        $('#save-form').click(function() {
            var url = "{{ route('company-settings.update', ['1']) }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
            })
        });
    </script>
@endpush
