    @extends('layouts.app')

    @push('styles')
        @if (!is_null($viewEventPermission) && $viewEventPermission != 'none')
            <link rel="stylesheet" href="{{ asset('vendor/full-calendar/main.min.css') }}">
        @endif
    @endpush

    @section('content')
        <!-- CONTENT WRAPPER START -->
        <div class="px-4 py-2 border-top-0 emp-dashboard">
            <!-- WELOCOME START -->
            <div class="d-lg-flex d-md-flex d-block py-4">
                <!-- WELOCOME NAME START -->
                <div class="">
                    <h4 class=" mb-0 f-21 text-capitalize font-weight-bold">@lang('app.welcome')
                        {{ $user->name }}</h4>
                </div>
                <!-- WELOCOME NAME END -->

                @if (in_array('attendance', user_modules()))
                    <!-- CLOCK IN CLOCK OUT START -->
                    <div
                        class="ml-auto d-flex clock-in-out mb-3 mb-lg-0 mb-md-0 m mt-4 mt-lg-0 mt-md-0 justify-content-between">
                        <p
                            class="mb-0 text-lg-right text-md-right f-18 font-weight-bold text-dark-grey d-grid align-items-center">
                            <input type="hidden" id="current-latitude">
                            <input type="hidden" id="current-longitude">
                            {{ now()->timezone($global->timezone)->format($global->time_format) }}
                            @if (!is_null($currentClockIn))
                                <span class="f-11 font-weight-normal text-lightest">
                                    @lang('app.clockInAt') -
                                    {{ $currentClockIn->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                                </span>
                            @endif
                        </p>

                        @if (is_null($currentClockIn) && is_null($checkTodayLeave))
                            <button type="button" class="btn-primary rounded f-15 ml-4" id="clock-in"><i
                                    class="icons icon-login mr-2"></i>@lang('modules.attendance.clock_in')</button>
                        @endif
                        @if (!is_null($currentClockIn) && is_null($currentClockIn->clock_out_time))
                            <button type="button" class="btn-danger rounded f-15 ml-4" id="clock-out"><i
                                    class="icons icon-login mr-2"></i>@lang('modules.attendance.clock_out')</button>
                        @endif

                    </div>
                    <!-- CLOCK IN CLOCK OUT END -->

                @endif
            </div>
            <!-- WELOCOME END -->
            <!-- EMPLOYEE DASHBOARD DETAIL START -->
            <div class="row emp-dash-detail">
                <!-- EMP DASHBOARD INFO NOTICES START -->
                <div class="col-xl-5 col-lg-12 col-md-12 e-d-info-notices">
                    <div class="row">
                        <!-- EMP DASHBOARD INFO START -->
                        <div class="col-md-12">
                            <div class="card border-0 b-shadow-4 mb-3 e-d-info">
                                <div class="card-horizontal align-items-center">
                                    <div class="card-img">
                                        <img class="" src=" {{ $user->image_url }}" alt="Card image">
                                    </div>
                                    <div class="card-body border-0 pl-0">
                                        <h4 class="card-title f-18 f-w-500 mb-0">{{ $user->name }}</h4>
                                        <p class="f-14 font-weight-normal text-dark-grey mb-2">
                                            {{ $user->employeeDetails->designation->name ?? '--' }}</p>
                                        <p class="card-text f-12 text-lightest"> @lang('app.employeeId') :
                                            {{ strtoupper($user->employeeDetails->employee_id) }}</p>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-top-grey py-3">
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <span>
                                            <label class="f-12 text-dark-grey mb-12 text-capitalize" for="usr">
                                                @lang('app.menu.tasks') </label>
                                            <p class="mb-0 f-18 f-w-500">
                                                <a href="{{ route('tasks.index') . '?assignee=me&status=all' }}"
                                                    class="text-dark">
                                                    {{ $counts->totalPendingTasks + $counts->totalCompletedTasks }}
                                                </a>
                                            </p>
                                        </span>
                                        <span>
                                            <label class="f-12 text-dark-grey mb-12 text-capitalize" for="usr">
                                                @lang('app.menu.projects') </label>
                                            <p class="mb-0 f-18 f-w-500">
                                                <a href="{{ route('projects.index') . '?assignee=me&status=all' }}"
                                                    class="text-dark">{{ $totalProjects }}</a>
                                            </p>
                                        </span>
                                        <span>
                                            <label class="f-12 text-dark-grey mb-12 text-capitalize" for="usr">
                                                @lang('modules.dashboard.totalHoursLogged') </label>
                                            <p class="mb-0 f-18 f-w-500">
                                                <a href="{{ route('timelogs.index') . '?assignee=me&start=' . now()->format($global->date_format) . '&end=' . now()->format($global->date_format) }}"
                                                    class="text-dark">{{ intdiv($todayTotalHours, 60) }}
                                                </a>
                                            </p>
                                        </span>

                                        @if (isset($totalOpenTickets))
                                            <span>
                                                <label class="f-12 text-dark-grey mb-12 text-capitalize" for="usr">
                                                    @lang('modules.dashboard.totalOpenTickets') </label>
                                                <p class="mb-0 f-18 f-w-500">
                                                    <a href="{{ route('tickets.index') . '?agent=me&status=open' }}"
                                                        class="text-dark">{{ $totalOpenTickets }}</a>
                                                </p>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- EMP DASHBOARD INFO END -->

                        @isset($notices)
                            <!-- EMP DASHBOARD NOTICE START -->
                            <div class="col-md-12">
                                <div class="mt-4 mb-3 b-shadow-4 rounded bg-white">
                                    <!-- NOTICE HEADING START -->
                                    <div class="d-flex align-items-center b-shadow-4 p-20">
                                        <p class="mb-0 f-18 f-w-500"> @lang('app.menu.notices') </p>
                                    </div>
                                    <!-- NOTICE HEADING END -->
                                    <!-- NOTICE DETAIL START -->
                                    <div class="b-shadow-4 cal-info scroll ps" data-menu-vertical="1" data-menu-scroll="1"
                                        data-menu-dropdown-timeout="500" id="empDashNotice" style="overflow: hidden;">


                                        @foreach ($notices as $notice)
                                            <div class="card border-0 b-shadow-4 p-20 rounded-0">
                                                <div class="card-horizontal">
                                                    <div class="card-header m-0 p-0 bg-white rounded">
                                                        <x-date-badge :month="$notice->created_at->format('M')"
                                                            :date="$notice->created_at->timezone($global->timezone)->format('d')" />
                                                    </div>
                                                    <div class="card-body border-0 p-0 ml-3">
                                                        <h4 class="card-title f-14 font-weight-normal text-capitalize mb-0">
                                                            <a href="{{ route('notices.show', $notice->id) }}"
                                                                class="openRightModal text-darkest-grey">{{ $notice->heading }}</a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div><!-- card end -->
                                        @endforeach


                                        <div class="ps__rail-x" style="left: 0px; top: 0px;">
                                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                        </div>
                                        <div class="ps__rail-y" style="top: 0px; left: 0px;">
                                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                                        </div>
                                    </div>
                                    <!-- NOTICE DETAIL END -->
                                </div>
                            </div>
                            <!-- EMP DASHBOARD NOTICE END -->
                        @endisset

                    </div>
                </div>
                <!-- EMP DASHBOARD INFO NOTICES END -->
                <!-- EMP DASHBOARD TASKS PROJECTS EVENTS START -->
                <div class="col-xl-7 col-lg-12 col-md-12 e-d-tasks-projects-events">
                    <!-- EMP DASHBOARD TASKS PROJECTS START -->
                    <div class="row mb-3 mt-xl-0 mt-lg-4 mt-md-4 mt-4">
                        <div class="col-md-6">
                            <div
                                class="bg-white p-20 rounded b-shadow-4 d-flex justify-content-between align-items-center mb-4 mb-md-0 mb-lg-0">
                                <div class="d-block text-capitalize">
                                    <h5 class="f-15 f-w-500 mb-20 text-darkest-grey">@lang('app.menu.tasks')</h5>
                                    <div class="d-flex">
                                        <a href="{{ route('tasks.index') . '?assignee=me' }}">
                                            <p class="mb-0 f-21 font-weight-bold text-blue d-grid mr-5">
                                                {{ $inProcessTasks }}<span class="f-12 font-weight-normal text-lightest">
                                                    @lang('app.pending') </span>
                                            </p>
                                        </a>
                                        <a href="{{ route('tasks.index') . '?assignee=me&overdue=yes' }}">
                                            <p class="mb-0 f-21 font-weight-bold text-red d-grid">{{ $dueTasks }}<span
                                                    class="f-12 font-weight-normal text-lightest">@lang('app.overdue')</span>
                                            </p>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <i class="fa fa-list text-lightest f-27"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                class="bg-white p-20 rounded b-shadow-4 d-flex justify-content-between align-items-center mt-3 mt-lg-0 mt-md-0">
                                <div class="d-block text-capitalize">
                                    <h5 class="f-15 f-w-500 mb-20 text-darkest-grey"> @lang('app.menu.projects') </h5>
                                    <div class="d-flex">
                                        <a href="{{ route('projects.index') . '?assignee=me&status=in progress' }}">
                                            <p class="mb-0 f-21 font-weight-bold text-blue d-grid mr-5">
                                                {{ $totalProjects }}<span
                                                    class="f-12 font-weight-normal text-lightest">@lang('app.inProgress')</span>
                                            </p>
                                        </a>

                                        <a href="{{ route('projects.index') . '?assignee=me&status=overdue' }}">
                                            <p class="mb-0 f-21 font-weight-bold text-red d-grid">{{ $dueProjects }}<span
                                                    class="f-12 font-weight-normal text-lightest">@lang('app.overdue')</span>
                                            </p>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-block">
                                    <i class="fa fa-layer-group text-lightest f-27"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- EMP DASHBOARD TASKS PROJECTS END -->
                    <!-- EMP DASHBOARD EVENTS START -->
                    @if (!is_null($viewEventPermission) && $viewEventPermission != 'none')
                        <div class="row">
                            <div class="col-md-12">
                                <x-cards.data>
                                    <div id="calendar"></div>
                                </x-cards.data>
                            </div>
                        </div>
                    @endif
                    <!-- EMP DASHBOARD EVENTS END -->
                </div>
                <!-- EMP DASHBOARD TASKS PROJECTS EVENTS END -->
            </div>
            <!-- EMPLOYEE DASHBOARD DETAIL END -->
        </div>
        <!-- CONTENT WRAPPER END -->
    @endsection

    @push('scripts')
        @if (!is_null($viewEventPermission) && $viewEventPermission != 'none')
            <script src="{{ asset('vendor/full-calendar/main.min.js') }}"></script>
            <script src="{{ asset('vendor/full-calendar/locales-all.min.js') }}"></script>
            <script>
                var initialLocaleCode = '{{ user()->locale }}';
                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: initialLocaleCode,
                    timeZone: '{{ $global->timezone }}',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    navLinks: true, // can click day/week names to navigate views
                    selectable: false,
                    initialView: 'listWeek',
                    selectMirror: true,
                    select: function(arg) {
                        addEventModal(arg.start, arg.end, arg.allDay);
                        calendar.unselect()
                    },
                    eventClick: function(arg) {
                        getEventDetail(arg.event.id);
                    },
                    editable: false,
                    dayMaxEvents: true, // allow "more" link when too many events
                    events: {
                        url: "{{ route('events.index') }}",
                    },
                    eventDidMount: function(info) {
                        $(info.el).css('background-color', info.event.extendedProps.bg_color);
                        $(info.el).css('color', info.event.extendedProps.color);
                    },
                    eventTimeFormat: { // like '14:30:00'
                        hour: global_setting.time_format == 'H:i' ? '2-digit' : 'numeric',
                        minute: '2-digit',
                        meridiem: global_setting.time_format == 'H:i' ? false : true
                    }
                });

                calendar.render();

                // Task Detail show in sidebar
                var getEventDetail = function(id) {
                    openTaskDetail();
                    var url = "{{ route('events.show', ':id') }}";
                    url = url.replace(':id', id);

                    $.easyAjax({
                        url: url,
                        blockUI: true,
                        container: RIGHT_MODAL,
                        historyPush: true,
                        success: function(response) {
                            if (response.status == "success") {
                                $(RIGHT_MODAL_CONTENT).html(response.html);
                                $(RIGHT_MODAL_TITLE).html(response.title);
                            }
                        },
                        error: function(request, status, error) {
                            if (request.status == 403) {
                                $(RIGHT_MODAL_CONTENT).html(
                                    '<div class="align-content-between d-flex justify-content-center mt-105 f-21">403 | Permission Denied</div>'
                                );
                            } else if (request.status == 404) {
                                $(RIGHT_MODAL_CONTENT).html(
                                    '<div class="align-content-between d-flex justify-content-center mt-105 f-21">404 | Not Found</div>'
                                );
                            } else if (request.status == 500) {
                                $(RIGHT_MODAL_CONTENT).html(
                                    '<div class="align-content-between d-flex justify-content-center mt-105 f-21">500 | Something Went Wrong</div>'
                                );
                            }
                        }
                    });
                };
            </script>
        @endif

        <script>
            $('#clock-in').click(function() {
                const url = "{{ route('attendances.clock_in_modal') }}";
                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_LG, url);
            });

            /** clock timer start here */
            function currentTime() {
                let date = new Date();
                date = moment.tz(date, "{{ $global->timezone }}");

                let hour = date.hour();
                let min = date.minutes();
                let sec = date.seconds();
                let midday = "AM";
                midday = (hour >= 12) ? "PM" : "AM";
                @if ($global->time_format == 'h:i A')
                    hour = (hour == 0) ? 12 : ((hour > 12) ? (hour - 12): hour); /* assigning hour in 12-hour format */
                @endif
                hour = updateTime(hour);
                min = updateTime(min);
                document.getElementById("clock").innerText = `${hour} : ${min} ${midday}`
                const time = setTimeout(function() {
                    currentTime()
                }, 1000);
            }

            /* appending 0 before time elements if less than 10 */
            function updateTime(timer) {
                if (timer < 10) {
                    return "0" + timer;
                } else {
                    return timer;
                }
            }

            @if (!is_null($currentClockIn))
                $('#clock-out').click(function () {
            
                    var token = "{{ csrf_token() }}";
                    var currentLatitude = document.getElementById("current-latitude").value;
                    var currentLongitude = document.getElementById("current-longitude").value;
                
                    $.easyAjax({
                        url: "{{ route('attendances.update_clock_in') }}",
                        type: "GET",
                        data: {
                        currentLatitude: currentLatitude,
                        currentLongitude: currentLongitude,
                        _token: token,
                        id: '{{ $currentClockIn->id }}'
                        },
                        success: function (response) {
                            if(response.status == 'success') {
                                window.location.reload();
                            }
                        }
                    });
                });
            @endif

        </script>
    @endpush
