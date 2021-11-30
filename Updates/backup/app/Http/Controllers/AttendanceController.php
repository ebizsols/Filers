<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\Attendance\StoreAttendance;
use App\Http\Requests\Attendance\StoreBulkAttendance;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.attendance';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('attendance', $this->user->modules));
            $this->viewAttendancePermission = user()->permission('view_attendance');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $managePermission = user()->permission('manage_attendance');
        abort_403(!(in_array($this->viewAttendancePermission, ['all', 'added', 'owned', 'both']) || $managePermission == 'all'));

        if (request()->ajax()) {
            return $this->summaryData($request);
        }

        $this->employees = User::allEmployees();
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->departments = Team::all();

        return view('attendances.index', $this->data);
    }

    public function summaryData($request)
    {
        $employees = User::with(
            ['attendance' => function ($query) use ($request) {
                $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);

                if ($request->late != 'all') {
                    $query = $query->where('attendances.late', $request->late);
                }

                if ($this->viewAttendancePermission == 'added') {
                    $query = $query->where('attendances.added_by', user()->id);

                } elseif ($this->viewAttendancePermission == 'owned') {
                    $query = $query->where('attendances.user_id', user()->id);
                }
            }]
        )->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'employee_details.department_id', 'users.image')
            ->where('roles.name', '<>', 'client')->groupBy('users.id');

        if ($request->department != 'all') {
            $employees = $employees->where('employee_details.department_id', $request->department);
        }

        if ($request->userId != 'all') {
            $employees = $employees->where('users.id', $request->userId);
        }

        if ($this->viewAttendancePermission == 'owned') {
            $employees = $employees->where('users.id', user()->id);
        }

        $employees = $employees->get();

        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $holidayOccasions = [];

        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = Carbon::now()->timezone($this->global->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {

            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');

            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ((int)$this->daysInMonth - (int)$now->copy()->format('d')), '-');
            }
            else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ((int)$this->daysInMonth - (int)$now->copy()->format('d')), 'Absent');
            }

            $final[$employee->id . '#' . $employee->name] = array_replace($dataTillToday, $dataFromTomorrow);



            foreach ($employee->attendance as $attendance) {
                $final[$employee->id . '#' . $employee->name][Carbon::parse($attendance->clock_in_time)->timezone($this->global->timezone)->day] = '<a href="javascript:;" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-check text-primary"></i></a>';
            }

            $emplolyeeName = '<div class="media align-items-center">
            <a href="' . route('employees.show', [$employee->id]) . '">
            <img src="' . $employee->image_url . '" class="mr-3 taskEmployeeImg rounded-circle" alt="' . ucfirst($employee->name) . '" title="' . ucfirst($employee->name) . '"></a>
            <div class="media-body">
            <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('employees.show', [$employee->id]) . '">' . ucfirst($employee->name) . '</a></h5>
            </div>
          </div>';

            $final[$employee->id . '#' . $employee->name][] = $emplolyeeName;

            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')){
                    if($employee->employeeDetail->joining_date->format('d') == '01'){
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->format('d'), '-');
                    }
                    else{
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                    }
                }

                if(($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y'))
                {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if(Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()){
                $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }

            if(isset($dataBeforeJoin)){
                $final[$employee->id . '#' . $employee->name] = array_replace($final[$employee->id . '#' . $employee->name], $dataBeforeJoin);
            }

            foreach ($this->holidays as $holiday) {
                if ($final[$employee->id . '#' . $employee->name][$holiday->date->day] == 'Absent' || $final[$employee->id . '#' . $employee->name][$holiday->date->day] == '-') {
                    $final[$employee->id . '#' . $employee->name][$holiday->date->day] = 'Holiday';
                    $holidayOccasions[$holiday->date->day] = $holiday->occassion;
                }
            }
        }

        $this->employeeAttendence = $final;
        $this->holidayOccasions = $holidayOccasions;

        $view = view('attendances.ajax.summary_data', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $managePermission = user()->permission('manage_attendance');
        $viewPermission = user()->permission('view_attendance');

        abort_403(!($viewPermission == 'all' || $viewPermission == 'added' || $managePermission == 'all'));

        $attendance = Attendance::findOrFail($id);
        $this->attendanceActivity = Attendance::userAttendanceByDate($attendance->clock_in_time->format('Y-m-d'), $attendance->clock_in_time->format('Y-m-d'), $attendance->user_id);
        $this->lastClockOut = $this->firstClockIn = Attendance::where(DB::raw('DATE(attendances.clock_in_time)'), $attendance->clock_in_time->format('Y-m-d'))
            ->where('user_id', $attendance->user_id)->orderBy('id', 'asc')->first();

        $this->startTime = Carbon::parse($this->firstClockIn->clock_in_time)->timezone($this->global->timezone);

        if (!is_null($this->lastClockOut->clock_out_time)) {
            $this->endTime = Carbon::parse($this->lastClockOut->clock_out_time)->timezone($this->global->timezone);
        }
        elseif (($this->lastClockOut->clock_in_time->timezone($this->global->timezone)->format('Y-m-d') != Carbon::now()->timezone($this->global->timezone)->format('Y-m-d')) && is_null($this->lastClockOut->clock_out_time)) {
            $this->endTime = Carbon::parse($this->startTime->format('Y-m-d') . ' ' . attendance_setting()->office_end_time, $this->global->timezone);
            $this->notClockedOut = true;
        }
        else {
            $settingEndTime = Carbon::createFromFormat('H:i:s', attendance_setting()->office_end_time, $this->global->timezone);

            if ($settingEndTime->greaterThan(Carbon::now()->timezone($this->global->timezone))) {
                $this->endTime = Carbon::now()->timezone($this->global->timezone);
            }
            else {
                $settingEndTime = Carbon::createFromFormat('H:i:s', attendance_setting()->office_end_time, $this->global->timezone);

                if ($settingEndTime->greaterThan(Carbon::now()->timezone($this->global->timezone))) {
                    $this->endTime = Carbon::now()->timezone($this->global->timezone);
                }
                else {
                    $this->endTime = $settingEndTime;
                }

                $this->notClockedOut = true;
            }

            $this->notClockedOut = true;
        }

        $this->totalTime = $this->endTime->timezone($this->global->timezone)->diff($this->startTime, true)->format('%h.%i');

        $this->attendance = $attendance;

        return view('attendances.ajax.show', $this->data);

    }

    public function edit($id)
    {
        $attendance = Attendance::find($id);

        $this->date = $attendance->clock_in_time->format('Y-m-d');
        $this->row = $attendance;
        $this->clock_in = 1;
        $this->userid = $attendance->user_id;
        $this->total_clock_in  = Attendance::where('user_id', $attendance->user_id)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();
        $this->type = 'edit';

        $this->maxAttendanceInDay = attendance_setting()->clockin_in_day;
        return view('attendances.ajax.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $date = Carbon::parse($request->attendance_date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }
        else {
            $clockOut = null;
        }

        $attendance->user_id = $request->user_id;
        $attendance->clock_in_time = $clockIn;
        $attendance->clock_in_ip = $request->clock_in_ip;
        $attendance->clock_out_time = $clockOut;
        $attendance->clock_out_ip = $request->clock_out_ip;
        $attendance->working_from = $request->working_from;
        $attendance->late = ($request->has('late')) ? 'yes' : 'no';
        $attendance->half_day = ($request->has('half_day')) ? 'yes' : 'no';
        $attendance->save();

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    public function mark(Request $request, $userid, $day, $month, $year)
    {
        $userDetail = User::find($userid);

        $this->date = Carbon::createFromFormat('d-m-Y', $day . '-' . $month . '-' . $year)->format('Y-m-d');
        $this->row = Attendance::attendanceByUserDate($userid, $this->date);
        $this->clock_in = 0;
        $this->total_clock_in = Attendance::where('user_id', $userid)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();

        $this->userid = $userid;
        $this->type = 'add';
        $this->maxAttendanceInDay = attendance_setting()->clockin_in_day;
        return view('attendances.ajax.edit', $this->data);
    }

    public function store(StoreAttendance $request)
    {
        $date = Carbon::parse($request->attendance_date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }
        else {
            $clockOut = null;
        }

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
            ->whereNull('clock_out_time')
            ->first();

        $clockInCount = Attendance::getTotalUserClockIn($date, $request->user_id);

        if (!is_null($attendance)) {
            $attendance->update([
                'user_id' => $request->user_id,
                'clock_in_time' => $clockIn,
                'clock_in_ip' => $request->clock_in_ip,
                'clock_out_time' => $clockOut,
                'clock_out_ip' => $request->clock_out_ip,
                'working_from' => $request->working_from,
                'late' => ($request->has('late')) ? 'yes' : 'no',
                'half_day' => ($request->has('half_day')) ? 'yes' : 'no'
            ]);
        }
        else {

            // Check maximum attendance in a day
            if ($clockInCount < attendance_setting()->clockin_in_day) {
                Attendance::create([
                    'user_id' => $request->user_id,
                    'clock_in_time' => $clockIn,
                    'clock_in_ip' => $request->clock_in_ip,
                    'clock_out_time' => $clockOut,
                    'clock_out_ip' => $request->clock_out_ip,
                    'working_from' => $request->working_from,
                    'late' => ($request->has('late')) ? 'yes' : 'no',
                    'half_day' => ($request->has('half_day')) ? 'yes' : 'no'
                ]);
            }
            else {
                return Reply::error(__('messages.maxColckIn'));
            }
        }

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function byMember()
    {
        $managePermission = user()->permission('manage_attendance');
        $viewPermission = user()->permission('view_attendance');
        $this->pageTitle = 'modules.attendance.attendanceByMember';

        abort_403(!($viewPermission == 'all' || $viewPermission == 'added' || $managePermission == 'all'));
        $this->employees = User::allEmployees();
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        return view('attendances.by_member', $this->data);
    }

    public function employeeData(Request $request, $startDate = null, $endDate = null, $userId = null)
    {
        $ant = []; // Array For attendance Data indexed by similar date
        $dateWiseData = []; // Array For Combine Data

        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->startOfMonth()->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        $userId = $request->userId;

        $attendances = Attendance::userAttendanceByDate($startDate, $endDate, $userId); // Getting Attendance Data
        $holidays = Holiday::getHolidayByDates($startDate, $endDate); // Getting Holiday Data

        $openDays = json_decode(attendance_setting()->office_open_days);
        $userId = $request->userId;

        $totalWorkingDays = $startDate->diffInDaysFiltered(function (Carbon $date) use ($openDays) {
            foreach ($openDays as $day) {
                if ($date->dayOfWeek == $day) {
                    return $date;
                }
            }
        }, $endDate);
        $daysPresent = Attendance::countDaysPresentByUser($startDate, $endDate, $userId);
        $daysLate = Attendance::countDaysLateByUser($startDate, $endDate, $userId);
        $halfDays = Attendance::countHalfDaysByUser($startDate, $endDate, $userId);
        $daysAbsent = (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent);
        $holidayCount = Count($holidays);

        // Getting Leaves Data
        $leavesDates = Leave::where('user_id', $userId)
            ->where('leave_date', '>=', $startDate)
            ->where('leave_date', '<=', $endDate)
            ->where('status', 'approved')
            ->select('leave_date', 'reason', 'duration')
            ->get()->keyBy('date')->toArray();

        $holidayData = $holidays->keyBy('holiday_date');
        $holidayArray = $holidayData->toArray();

        // Set Date as index for same date clock-ins
        foreach ($attendances as $attand) {
            $ant[$attand->clock_in_date][] = $attand; // Set attendance Data indexed by similar date
        }

        // Set All Data in a single Array
        // @codingStandardsIgnoreStart

        for($date = $endDate; $date->diffInDays($startDate) > 0; $date->subDay()) {
        // @codingStandardsIgnoreEnd

            if ($date->isPast() || $date->isToday()) {

                // Set default array for record
                $dateWiseData[$date->toDateString()] = [
                    'holiday' => false,
                    'attendance' => false,
                    'leave' => false
                ];

                // Set Holiday Data
                if (array_key_exists($date->toDateString(), $holidayArray)) {
                    $dateWiseData[$date->toDateString()]['holiday'] = $holidayData[$date->toDateString()];
                }

                // Set Attendance Data
                if (array_key_exists($date->toDateString(), $ant)) {
                    $dateWiseData[$date->toDateString()]['attendance'] = $ant[$date->toDateString()];
                }

                // Set Leave Data
                if (array_key_exists($date->toDateString(), $leavesDates)) {
                    $dateWiseData[$date->toDateString()]['leave'] = $leavesDates[$date->toDateString()];
                }
            }
        }

        if ($startDate->isPast() || $startDate->isToday()) {
            // Set default array for record
            $dateWiseData[$startDate->toDateString()] = [
                'holiday' => false,
                'attendance' => false,
                'leave' => false
            ];

            // Set Holiday Data
            if (array_key_exists($startDate->toDateString(), $holidayArray)) {
                $dateWiseData[$startDate->toDateString()]['holiday'] = $holidayData[$startDate->toDateString()];
            }

            // Set Attendance Data
            if (array_key_exists($startDate->toDateString(), $ant)) {
                $dateWiseData[$startDate->toDateString()]['attendance'] = $ant[$startDate->toDateString()];
            }

            // Set Leave Data
            if (array_key_exists($startDate->toDateString(), $leavesDates)) {
                $dateWiseData[$startDate->toDateString()]['leave'] = $leavesDates[$startDate->toDateString()];
            }
        }

        // Getting View data
        $view = view('attendances.ajax.user_attendance', ['dateWiseData' => $dateWiseData, 'global' => $this->global])->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view, 'daysPresent' => $daysPresent, 'daysLate' => $daysLate, 'halfDays' => $halfDays, 'totalWorkingDays' => $totalWorkingDays, 'absentDays' => $daysAbsent, 'holidays' => $holidayCount]);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $managePermission = user()->permission('manage_attendance');
        $addPermission = user()->permission('add_attendance');

        abort_403 (!($addPermission == 'all' || $addPermission == 'added' || $managePermission == 'all'));
        $this->employees = User::allEmployees();
        $this->departments = Team::allDepartments();
        $this->pageTitle = __('modules.attendance.markAttendance');
        $this->year = now()->format('Y');
        $this->month = now()->format('m');

        if (request()->ajax()) {
            $html = view('attendances.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'attendances.ajax.create';

        return view('attendances.create', $this->data);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkMark(StoreBulkAttendance $request)
    {
        $employees = $request->user_id;
        $employeeData = User::with('employeeDetail')->whereIn('id', $employees)->get();

        $date = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date . ' ' . $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                return Reply::error(__('messages.clockOutTimeError'));
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }

        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $period = CarbonPeriod::create($startDate, $endDate);

        $holidays = Holiday::getHolidayByDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))->pluck('holiday_date')->toArray();

        $insertData = [];
        $currentDate = Carbon::now();

        foreach ($employees as $key => $userId) {
            $userData = $employeeData->filter(function ($value) use($userId) {
                return $value->id == $userId;
            })->first();

            foreach ($period as $date) {
                $attendance = Attendance::where('user_id', $userId)
                    ->where(DB::raw('DATE(`clock_in_time`)'), $date->format('Y-m-d'))
                    ->first();

                if (is_null($attendance) && $date->greaterThanOrEqualTo($userData->employeeDetail->joining_date) && $date->lessThanOrEqualTo($currentDate)) { // Attendance should not exist for the user for the same date
                    if (!in_array($date->format('Y-m-d'), $holidays)) { // date should not be a holiday

                        $clockIn = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date->format('Y-m-d') . ' ' . $request->clock_in_time, $this->global->timezone);
                        $clockIn->setTimezone('UTC');

                        $clockOut = Carbon::createFromFormat('Y-m-d ' . $this->global->time_format, $date->format('Y-m-d') . ' ' . $request->clock_out_time, $this->global->timezone);
                        $clockOut->setTimezone('UTC');

                        $insertData[] = [
                            'user_id' => $userId,
                            'clock_in_time' => $clockIn,
                            'clock_in_ip' => request()->ip(),
                            'clock_out_time' => $clockOut,
                            'clock_out_ip' => request()->ip(),
                            'working_from' => $request->working_from,
                            'late' => $request->late,
                            'half_day' => $request->half_day,
                            'added_by' => user()->id,
                            'last_updated_by' => user()->id
                        ];
                    }
                }
            }
        }

        Attendance::insertOrIgnore($insertData);

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('attendances.index');
        }

        return Reply::redirect($redirectUrl, __('messages.attendanceSaveSuccess'));
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $deleteAttendancePermission = user()->permission('delete_attendance');
        $manageAttendancePermission = user()->permission('manage_attendance');

        abort_403 (!($deleteAttendancePermission == 'all' || $manageAttendancePermission == 'all' || ($deleteAttendancePermission == 'added' && $attendance->added_by == user()->id)));
        Attendance::destroy($id);
        return Reply::success(__('messages.attendanceDelete'));
    }

}
