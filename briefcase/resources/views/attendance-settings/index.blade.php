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

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.officeStartTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="office_start_time"
                                fieldId="office_start_time"
                                :fieldValue="\Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->office_start_time)->format($global->time_format)"
                                fieldRequired="true" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.officeEndTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="office_end_time"
                                fieldId="office_end_time"
                                :fieldValue="\Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->office_end_time)->format($global->time_format)"
                                fieldRequired="true" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.halfDayMarkTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="halfday_mark_time"
                                fieldId="halfday_mark_time"
                                :fieldValue="$attendanceSetting->halfday_mark_time ? \Carbon\Carbon::createFromFormat('H:i:s', $attendanceSetting->halfday_mark_time)->format($global->time_format) : '11:00'" />
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.lateMark')"
                            fieldName="late_mark_duration" fieldId="late_mark_duration"
                            :fieldValue="$attendanceSetting->late_mark_duration" fieldRequired="true" />
                    </div>

                    <div class="col-lg-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.checkininday')"
                            fieldName="clockin_in_day" fieldId="clockin_in_day"
                            :fieldValue="$attendanceSetting->clockin_in_day" fieldRequired="true" />
                    </div>

                    <div class="col-lg-12">
                        <div class="row mt-3">

                            <div class="col-lg-12 mb-2">
                                <x-forms.checkbox :fieldLabel="__('modules.attendance.allowSelfClock')"
                                    fieldName="employee_clock_in_out" fieldId="employee_clock_in_out" fieldValue="yes"
                                    fieldRequired="true" :checked="$attendanceSetting->employee_clock_in_out == 'yes'" />
                            </div>

                            <div class="col-lg-12 mb-2">
                                <x-forms.checkbox :fieldLabel="__('modules.attendance.checkForRadius')"
                                    fieldName="radius_check" fieldId="radius_check" fieldValue="yes" fieldRequired="true"
                                    :checked="$attendanceSetting->radius_check == 'yes'" />
                            </div>

                            <div class="col-lg-12 @if ($attendanceSetting->radius_check == 'no') d-none @endif " id="radiusBox">
                                <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.radius')"
                                    fieldName="radius" fieldId="radius" :fieldValue="$attendanceSetting->radius" />
                            </div>

                            <div class="col-lg-12 mb-1">
                                <x-forms.checkbox :fieldLabel="__('modules.attendance.checkForIp')" fieldName="ip_check"
                                    fieldId="ip_check" fieldValue="yes" fieldRequired="true"
                                    :checked="$attendanceSetting->ip_check == 'yes'" />
                            </div>

                            <div class="col-lg-12 @if ($attendanceSetting->ip_check == 'no') d-none @endif " id="ipBox">
                                <div id="addMoreBox1" class="row">
                                    @forelse($ipAddresses as $index => $ipAddress)
                                        <div class="col-md-5">
                                            <div class="form-group" id="occasionBox">
                                                <input class="form-control height-35 f-14" type="text"
                                                    value="{{ $ipAddress }}" name="ip[{{ $index }}]"
                                                    placeholder="{{ __('modules.attendance.ipAddress') }}" />
                                                <div id="errorOccasion"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-md-5">
                                            <div class="form-group" id="occasionBox">
                                                <x-forms.text fieldLabel=""
                                                    :fieldPlaceholder="__('modules.attendance.ipAddress')"
                                                    fieldName="ip[0]" fieldId="ip[0]" />
                                                <div id="errorOccasion"></div>
                                            </div>
                                        </div>
                                    @endforelse
                                    <div class="col-md-1"></div>
                                </div>
                                <div id="insertBefore"></div>
                                <div class="clearfix"></div>
                                <a href="javascript:;" id="plusButton" class="text-capitalize"><i
                                        class="f-12 mr-2 fa fa-plus"></i> @lang('app.add')  @lang('modules.attendance.ipAddress')  </a>
                            </div>

                        </div>
                        <hr>

                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <x-forms.label fieldId="office_open_days" :fieldLabel="__('modules.attendance.officeOpenDays')">
                            </x-forms.label>
                            <div class="d-lg-flex d-sm-block justify-content-between ">
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.monday')" fieldName="office_open_days[]"
                                        fieldId="open_mon" fieldValue="1" :checked="in_array('1', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.tuesday')" fieldName="office_open_days[]"
                                        fieldId="open_tues" fieldValue="2" :checked="in_array('2', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.wednesday')" fieldName="office_open_days[]"
                                        fieldId="open_wed" fieldValue="3" :checked="in_array('3', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.thursday')" fieldName="office_open_days[]"
                                        fieldId="open_thurs" fieldValue="4" :checked="in_array('4', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.friday')" fieldName="office_open_days[]"
                                        fieldId="open_fri" fieldValue="5" :checked="in_array('5', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.saturday')" fieldName="office_open_days[]"
                                        fieldId="open_sat" fieldValue="6" :checked="in_array('6', $openDays)" />
                                </div>
                                <div class="mr-3 mb-2">
                                    <x-forms.checkbox :fieldLabel="__('app.sunday')" fieldName="office_open_days[]"
                                        fieldId="open_sun" fieldValue="0" :checked="in_array('0', $openDays)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <x-forms.toggle-switch class="mr-0 mr-lg-12"  :checked="$attendanceSetting->alert_after_status"
                                               :fieldLabel="__('modules.attendance.attendanceReminderStatus')"
                                               fieldName="alert_after_status"
                                               fieldId="alert_after_status"/>
                    </div>

                    <div class="col-lg-6 alert_after_box @if($attendanceSetting->alert_after_status == 0) d-none @endif">
                            <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.ReminderAfterMinutes')"
                                            fieldName="alert_after" fieldId="alert_after"
                                            :fieldValue="$attendanceSetting->alert_after" fieldRequired="true" />
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
                </div>
                <!-- Buttons End -->
            </x-slot>

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {

            var $insertBefore = $('#insertBefore');
            var $i = {{ count($ipAddresses) }};

            $('#office_end_time, #office_start_time, #halfday_mark_time').timepicker({
                @if ($global->time_format == 'H:i')
                    showMeridian: false,
                @endif
            });

            $('#save-form').click(function() {
                $.easyAjax({
                    url: "{{ route('attendance-settings.update', ['1']) }}",
                    container: '#editSettings',
                    disableButton: true,
                    blockUI: true,
                    buttonSelector: "#save-form",
                    type: "POST",
                    redirect: true,
                    data: $('#editSettings').serialize()
                })
            });

            $('#employee_clock_in_out').click(function() {
                if ($(this).prop("checked") == true) {
                    $('#radius_check').removeAttr("disabled");
                    $('#ip_check').removeAttr("disabled");
                } else if ($(this).prop("checked") == false) {
                    if ($('#radius_check').prop("checked") == true) {
                        $('#radius_check').trigger('click');
                    }
                    if ($('#ip_check').prop("checked") == true) {
                        $('#ip_check').trigger('click');
                    }

                    $('#radius_check').attr("disabled", 'disabled');
                    $('#ip_check').attr("disabled", 'disabled');
                }
            });

            $('#radius_check').click(function() {
                $('#radiusBox').toggleClass('d-none');
            });

            $('#ip_check').click(function() {
                $('#ipBox').toggleClass('d-none');
            });

            // Add More Inputs
            $('#plusButton').click(function() {
                $i = $i + 1;
                var indexs = $i + 1;
                $(`<div id="addMoreBox${indexs}" class="row clearfix"><div class="col-md-5"><div class="form-group"><input class="form-control height-35 f-14" name="ip[${$i}]" type="text" value="" placeholder="@lang('modules.attendance.ipAddress')"/></div></div><div class="col-md-1"><div class="task_view mt-1"> <a href="javascript:;" data-ip-index="${indexs}" class="delete-ip-field task_view_more d-flex align-items-center justify-content-center dropdown-toggle" > <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')</a> </div></div></div>`).insertBefore($insertBefore);
            });

            // Remove fields
            function removeBox(index) {
                $('#addMoreBox' + index).remove();
            }

        });

        $('#alert_after_status').click(function() {
            $('.alert_after_box').toggleClass('d-none');
        })

        $('#ipBox').on('click', '.delete-ip-field', function () {
            var ipIndex = $(this).data('ip-index');
            $('#addMoreBox' + ipIndex).remove();
        });
    </script>
@endpush
