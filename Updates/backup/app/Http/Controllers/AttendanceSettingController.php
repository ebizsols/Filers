<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\AttendanceSetting\UpdateAttendanceSetting;
use App\Models\AttendanceSetting;
use Carbon\Carbon;

class AttendanceSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.attendanceSettings';
        $this->activeSettingMenu = 'attendance_settings';
        $this->middleware(function ($request, $next) {
            abort_403(user()->permission('manage_attendance_setting') !== 'all');
            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->ipAddresses = [];
        $this->attendanceSetting = attendance_setting();
        $this->openDays = json_decode($this->attendanceSetting->office_open_days);

        if (json_decode($this->attendanceSetting->ip_address)) {
            $this->ipAddresses = json_decode($this->attendanceSetting->ip_address, true);
        }

        return view('attendance-settings.index', $this->data);
    }

    /**
     * @param UpdateAttendanceSetting $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(UpdateAttendanceSetting $request, $id)
    {
        $setting = AttendanceSetting::findOrFail($id);

        $setting->office_start_time = Carbon::createFromFormat($this->global->time_format, $request->office_start_time);
        $setting->office_end_time = Carbon::createFromFormat($this->global->time_format, $request->office_end_time);
        $setting->halfday_mark_time = Carbon::createFromFormat($this->global->time_format, $request->halfday_mark_time);
        $setting->late_mark_duration = $request->late_mark_duration;
        $setting->clockin_in_day = $request->clockin_in_day;
        ($request->employee_clock_in_out == 'yes') ? $setting->employee_clock_in_out = 'yes' : $setting->employee_clock_in_out = 'no';
        $setting->office_open_days = json_encode($request->office_open_days);
        ($request->radius_check == 'yes') ? $setting->radius_check = 'yes' : $setting->radius_check = 'no';
        ($request->ip_check == 'yes') ? $setting->ip_check = 'yes' : $setting->ip_check = 'no';
        $setting->radius = $request->radius;
        $setting->ip_address = json_encode($request->ip);
        $setting->alert_after = $request->alert_after;
        $setting->alert_after_status = ($request->alert_after_status == 'on') ? 1 : 0;
        $setting->save();

        session()->forget('attendance_setting');

        return Reply::success(__('messages.settingsUpdated'));
    }

}
