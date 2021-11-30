<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Social;
use App\Models\User;
use App\Traits\SocialAuthSettings;
use Exception;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Http\Request;
use \Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    use AppBoot, SocialAuthSettings;

    protected $redirectTo = 'account/dashboard';

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
                }
                else {
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
            }
            else {
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
