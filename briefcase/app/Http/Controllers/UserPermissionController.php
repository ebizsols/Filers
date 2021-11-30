<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class UserPermissionController extends AccountBaseController
{

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permissionId = $request->permissionId;
        $permissionType = $request->permissionType;

        UserPermission::where('permission_id', $permissionId)
            ->where('user_id', $id)
            ->update(['permission_type_id' => $permissionType]);
        return Reply::dataOnly(['status' => 'success']);
    }

    public function customPermissions(Request $request, $id)
    {
        $moduleId = $request->moduleId;
        $this->employee = User::with('role')->findOrFail($id);
        $roleId = $this->employee->role[0]->role_id;
        $this->role = Role::with('permissions')->find($roleId);
        $this->modulesData = Module::with('customPermissions')->findOrFail($moduleId);;
        $html = view('employees.ajax.custom_permissions', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

}
