<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\EmployeeLeaveQuota;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeavesQuotaController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('leaves', $this->user->modules));
            return $next($request);
        });
    }

    public function update(Request $request, $id)
    {
        if ($request->leaves < 0) {
            return Reply::error('messages.leaveTypeValueError');
        }

        $type = EmployeeLeaveQuota::findOrFail($id);
        $type->no_of_leaves = $request->leaves;
        $type->save();

        session()->forget('user');

        return Reply::success(__('messages.leaveTypeAdded'));
    }

}
