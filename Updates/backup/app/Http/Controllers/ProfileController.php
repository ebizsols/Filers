<?php

namespace App\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\User\UpdateProfile;
use App\Models\EmployeeDetails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends AccountBaseController
{

    public function update(UpdateProfile $request, $id)
    {
        config(['filesystems.default' => 'local']);

        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->salutation = $request->salutation;
        $user->gender = $request->gender;
        $user->country_id = $request->phone_code;
        $user->mobile = $request->mobile;
        $user->email_notifications = $request->email_notifications;
        $user->locale = $request->locale;
        $user->rtl = $request->rtl;
        $user->email_notifications = $request->email_notifications;

        if (!is_null($request->password)) {
            $user->password = Hash::make($request->password);
        }

        if ($request->image_delete == 'yes') {
            Files::deleteFile($user->image, 'avatar');
            $user->image = null;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }

        $user->save();

        if ($user->clientDetails) {
            $fields = $request->only($user->clientDetails->getFillable());

            $user->clientDetails->fill($fields);
            $user->clientDetails->save();
        }

        // adding address to employee_details
        $this->addEmployeeDetail($request, $user);
        session()->forget('user');

        $this->logUserActivity($user->id, __('messages.updatedProfile'));

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('profile-settings.index');
        }

        return Reply::redirect($redirectUrl, __('messages.profileUpdated'));
    }

    public function addEmployeeDetail($request, $user)
    {
        $employee = EmployeeDetails::where('user_id', $user->id)->first();

        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }

        $employee->address = $request->address;
        $employee->save();
    }

    public function darkTheme(Request $request)
    {
        $user = User::find(user()->id);
        $user->dark_theme = $request->darkTheme;
        $user->save();
        session()->forget('user');
        return Reply::success(__('messages.settingsUpdated'));
    }

    public function updateOneSignalId(Request $request)
    {
        $user = User::find($this->user->id);
        $user->onesignal_player_id = $request->userId;
        $user->save();
        session()->forget('user');
    }

}
