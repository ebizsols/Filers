@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>

            @if ($global->hide_cron_message == 0)
                <x-slot name="buttons">
                    <div class="alert alert-primary">
                        <h6>Set following cron command on your server (Ignore if already done)</h6>
                        @php
                            try {
                                echo '<code>* * * * * ' . PHP_BINDIR . '/php  ' . base_path() . '/artisan schedule:run >> /dev/null 2>&1</code>';
                            } catch (\Throwable $th) {
                                echo '<code>* * * * * /php' . base_path() . '/artisan schedule:run >> /dev/null 2>&1</code>';
                            }
                        @endphp
                    </div>
                </x-slot>
            @endif

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
                @method('PUT')
                <div class="row">

                    <div class="col-lg-3">
                        <x-forms.select fieldId="date_format" :fieldLabel="__('modules.accountSettings.dateFormat')"
                            fieldName="date_format" search="true">
                            @foreach ($dateFormat as $format)
                                <option value="{{ $format }}" @if ($global->date_format == $format) selected @endif>
                                    {{ $format }} ({{ $dateObject->format($format) }})
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select fieldId="time_format" :fieldLabel="__('modules.accountSettings.timeFormat')"
                            fieldName="time_format" search="true">
                            <option value="h:i A" @if ($global->time_format == 'h:i A') selected @endif>
                                12 Hour (6:20 PM)
                            </option>
                            <option value="h:i a" @if ($global->time_format == 'h:i a') selected @endif>
                                12 Hour (6:20 pm)
                            </option>
                            <option value="H:i" @if ($global->time_format == 'H:i') selected @endif>
                                24 Hour (18:20)
                            </option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select fieldId="timezone" :fieldLabel="__('modules.accountSettings.defaultTimezone')"
                            fieldName="timezone" search="true">
                            @foreach ($timezones as $tz)
                                <option @if ($global->timezone == $tz) selected @endif>{{ $tz }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select fieldId="currency_id" :fieldLabel="__('modules.accountSettings.defaultCurrency')"
                            fieldName="currency_id" search="true">
                            @foreach ($currencies as $currency)
                                <option @if ($currency->id == $global->currency_id)
                                    selected
                                    @endif value="{{ $currency->id }}">
                                    {{ $currency->currency_symbol . ' (' . $currency->currency_code . ')' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-4">
                        <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.changeLanguage')"
                            fieldName="locale" search="true">
                            <option data-content="<span class='flag-icon flag-icon-gb flag-icon-squared'></span> English"
                                {{ $global->locale == 'en' ? 'selected' : '' }} value="en">English
                            </option>
                            @foreach ($languageSettings as $language)
                                <option {{ $global->locale == $language->language_code ? 'selected' : '' }}
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($language->language_code) }} flag-icon-squared'></span> {{ $language->language_name }}"
                                    value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-4">
                        <x-forms.text :fieldLabel="__('modules.accountSettings.latitude')" fieldPlaceholder="e.g. 38.895"
                            fieldName="latitude" fieldId="latitude" :fieldValue="$global->latitude" />
                    </div>
                    <div class="col-lg-4">
                        <x-forms.text :fieldLabel="__('modules.accountSettings.longitude')" fieldPlaceholder="e.g. -77.0364"
                            fieldName="longitude" fieldId="longitude" :fieldValue="$global->longitude" />
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select fieldId="session_driver" :fieldLabel="__('modules.accountSettings.sessionDriver')"
                            :popover="__('modules.accountSettings.sessionInfo')" fieldName="session_driver">
                            <option {{ $global->session_driver == 'file' ? 'selected' : '' }} value="file">
                                @lang('modules.accountSettings.sessionFile')</option>
                            <option {{ $global->session_driver == 'database' ? 'selected' : '' }} value="database">
                                @lang('modules.accountSettings.sessionDatabase')</option>
                        </x-forms.select>
                        @if ($global->session_driver == 'database')
                            <small><a id="delete-sessions" href="javascript:;"><i class="fa fa-trash"></i>
                                    @lang('modules.accountSettings.deleteSessions')</a></small>
                        @endif
                    </div>
                    <div class="col-lg-3 mt-lg-5">
                        <x-forms.checkbox :checked="$global->app_debug" :fieldLabel="__('modules.accountSettings.appDebug')"
                            fieldName="app_debug" :popover="__('modules.accountSettings.appDebugInfo')"
                            fieldId="app_debug" />
                    </div>
                    <div class="col-lg-3 mt-lg-5">
                        <x-forms.checkbox :checked="$global->system_update"
                            :fieldLabel="__('modules.accountSettings.updateEnableDisable')" fieldName="system_update"
                            :popover="__('modules.accountSettings.updateEnableDisableTest')" fieldId="system_update" />
                    </div>

                    <div class="col-lg-3 mt-lg-5">
                        @php
                            $cleanCache = '';
                        @endphp
                        @if ($cachedFile)
                            @php
                                $cleanCache =
                                    '<a id="clear-cache" href="javascript:;"><i class="fa fa-trash"></i>
                                                                                                                                                                                                                                ' .
                                    __('modules.accountSettings.clearCache') .
                                    '</a>';
                            @endphp

                        @endif
                        <x-forms.checkbox :checked="$cachedFile" :fieldLabel="__('app.enableCache')" fieldName="cache"
                            fieldId="cache" :fieldHelp="$cleanCache" />
                    </div>

                    <div class="col-sm-12 mt-5 mb-4">
                        <h4 class="f-16 font-weight-500 text-capitalize">
                            @lang('app.client') @lang('app.signUp') @lang('app.settings')</h4>    
                    </div>

                    <div class="col-lg-3">
                        <x-forms.checkbox :checked="$global->allow_client_signup"
                            :fieldLabel="__('modules.accountSettings.allowClientSignup')" fieldName="allow_client_signup"
                            fieldId="allow_client_signup" />
                    </div>
                    <div class="col-lg-5 {{ !$global->allow_client_signup ? 'd-none' : '' }}" id="admin-approval">
                        <x-forms.checkbox :checked="$global->admin_client_signup_approval"
                            :fieldLabel="__('modules.accountSettings.needClientSignupApproval')"
                            fieldName="admin_client_signup_approval" fieldId="admin_client_signup_approval" />
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

                    </x-setting-form-actions>
                    <div class="d-flex d-lg-none d-md-none p-4">
                        <div class="d-flex w-100">
                            <x-forms.button-primary class="mr-3 w-100" icon="check">@lang('app.save')
                            </x-forms.button-primary>
                        </div>
                        <x-forms.button-cancel :link="url()->previous()" class="w-100">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </div>
                </div>
                <!-- Buttons End -->
            </x-slot>

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>

        $('#allow_client_signup').change(function() {
            $('#admin-approval').toggleClass('d-none');
        });

        $('#save-form').click(function() {
            const url = "{{ route('app-settings.update', ['1']) }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function() {
                    window.location.reload();
                }
            })
        });

        $('#clear-cache').click(function() {
            const url = "{{ url('clear-cache') }}";
            $.easyAjax({
                url: url,
                type: "GET",
                success: function() {
                    window.location.reload();
                }
            })
        });

        $('body').on('click', '#delete-sessions', function() {
            var id = $(this).data('invoice-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.sessionDeleteConfirmation')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('app.delete')",
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

                    var url = "{{ route('app-settings.delete_sessions') }}";

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        url: url,
                        type: "POST",
                        container: '#editSettings',
                        data: {
                            _token: token
                        },
                        success: function() {
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
