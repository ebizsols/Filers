<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\ProjectSetting\UpdateProjectSetting;
use App\Models\ProjectSetting;

class ProjectSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projectSettings';
        $this->activeSettingMenu = 'project_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!(user()->permission('manage_project_setting') == 'all'));
            return $next($request);
        });
    }

    public function index()
    {
        $this->projectSetting = ProjectSetting::first();

        return view('project-settings.index', $this->data);
    }

    public function update(UpdateProjectSetting $request, $id)
    {
        $projectSetting = ProjectSetting::find($id);

        if ($request->send_reminder) {
            $projectSetting->send_reminder = 'yes';
        }
        else {
            $projectSetting->send_reminder = 'no';
        }

        $projectSetting->remind_time = $request->remind_time;
        $projectSetting->remind_type = $request->remind_type;

        $remindTo = [];

        if ($request->send_reminder_member) {
            $remindTo[] = 'members';
        }

        if ($request->send_reminder_admin) {
            $remindTo[] = 'admins';
        }

        $projectSetting->remind_to = $remindTo;
        $projectSetting->save();

        return Reply::success(__('messages.settingsUpdated'));
    }

}
