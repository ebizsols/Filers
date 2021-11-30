<?php

namespace App\Http\Controllers;

use App\Models\ProjectActivity;
use App\Models\ProjectTimeLog;
use App\Models\TaskHistory;
use App\Models\UniversalSearch;
use App\Models\UserActivity;
use App\Traits\FileSystemSettingTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

class AccountBaseController extends Controller
{
    use FileSystemSettingTrait;

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!isRunningInConsoleOrSeeding()) {
            $this->currentRouteName = request()->route()->getName();
        }

        $this->middleware(function ($request, $next) {
            $this->setFileSystemConfigs();
            $this->languageSettings = language_setting();
            $this->adminTheme = admin_theme();
            $this->pushSetting = push_setting();
            $this->smtpSetting = smtp_setting();
            $this->pusherSettings = pusher_settings();

            $this->invoiceSetting = invoice_setting();

            App::setLocale(user()->locale);
            Carbon::setLocale(user()->locale);
            setlocale(LC_TIME, user()->locale . '_' . strtoupper($this->global->locale));

            $this->user = user();
            $this->modules = $this->user->modules;
            $this->unreadNotificationCount = count($this->user->unreadNotifications);

            $this->stickyNotes = $this->user->sticky;

            $this->viewTimelogPermission = user()->permission('view_timelogs');

            $this->activeTimerCount = ProjectTimeLog::whereNull('end_time')
                ->join('users', 'users.id', '=', 'project_time_logs.user_id')
                ->select('project_time_logs.id');

            if ($this->viewTimelogPermission != 'all') {
                $this->activeTimerCount->where('project_time_logs.user_id', $this->user->id);
            }

            $this->activeTimerCount = $this->activeTimerCount->count();


            $this->worksuitePlugins = worksuite_plugins();

            if (in_array('admin', user_roles())) {
                $this->appTheme = admin_theme();
            }
            else if (in_array('employee', user_roles())) {
                $this->appTheme = employee_theme();
            }
            else if (in_array('employee', user_roles())) {
                $this->appTheme = client_theme();
            }
            else {
                $this->appTheme = employee_theme();
            }

            if (in_array('admin', user_roles())) {
                $this->checkListTotal = 7;
                $checkListCompleted = 2; // Installation and Admin Account setup

                if (!is_null(global_setting()->logo)) {
                    $checkListCompleted++;
                }

                if (!is_null(global_setting()->favicon)) {
                    $checkListCompleted++;
                }

                if (smtp_setting()->mail_from_email != 'from@email.com') {
                    $checkListCompleted++;
                }

                if (!is_null(global_setting()->last_cron_run)) {
                    $checkListCompleted++;
                }

                if (!is_null(user()->image)) {
                    $checkListCompleted++;
                }

                $this->checkListCompleted = $checkListCompleted;
            }

            $this->sidebarUserPermissions = sidebar_user_perms();

            return $next($request);
        });
    }

    public function logProjectActivity($projectId, $text)
    {
        $activity = new ProjectActivity();
        $activity->project_id = $projectId;
        $activity->activity = $text;
        $activity->save();
    }

    /**
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function logUserActivity($userId, $text)
    {
        $activity = new UserActivity();
        $activity->user_id = $userId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logSearchEntry($searchableId, $title, $route, $type)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->module_type = $type;
        $search->save();
    }

    public function logTaskActivity($taskID, $userID, $text, $boardColumnId = null, $subTaskId = null)
    {
        $activity = new TaskHistory();
        $activity->task_id = $taskID;

        if (!is_null($subTaskId)) {
            $activity->sub_task_id = $subTaskId;
        }

        $activity->user_id = $userID;
        $activity->details = $text;

        if (!is_null($boardColumnId)) {
            $activity->board_column_id = $boardColumnId;
        }

        $activity->save();
    }

    public function triggerPusher($channel, $event, $data)
    {
        if ($this->pusherSettings->status) {
            $pusher = new Pusher($this->pusherSettings->pusher_app_key, $this->pusherSettings->pusher_app_secret, $this->pusherSettings->pusher_app_id, array('cluster' => $this->pusherSettings->pusher_cluster, 'useTLS' => $this->pusherSettings->force_tls));
            $pusher->trigger($channel, $event, $data);
        }
    }

    public function innerSettingMenu()
    {
        $route = Route::currentRouteName();
        $data = [];

        foreach ($this->subMenuSettings as $menu) {
            if ($menu['route'] == $route) {
                $data = $menu;
                break;
            }

            if (isset($menu['children'])) {
                foreach ($menu['children'] as $subMenu) {
                    if ($route == $subMenu['route']) {
                        $data = $menu;
                        break;
                    }
                }
            }
        }

        return $data;
    }

}
