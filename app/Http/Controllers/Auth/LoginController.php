<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(LoginRequest $request)
    {
        $field = 'email';
        if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (is_numeric($request->input('email'))) {
            $field = 'contact_number';
        }

        $request->merge([$field => $request->input('email')]);

        $credentials = $request->only($field, 'password');

        $remember = request('remember');
        if (!Auth::attempt($credentials, $remember)) {
            return redirect("login")->with('error',  __('Email or password is incorrect'));
        }

        $user = User::where('email', $request->email)->first();
        if (isset($user) && ($user->status == USER_STATUS_INACTIVE)) {
            Auth::logout();
            return redirect("login")->with('error', __('Your account is inactive. Please contact with admin'));
        } elseif (isset($user) && ($user->status == USER_STATUS_DELETED)) {
            Auth::logout();
            return redirect("login")->with('error', __('Your account has been deleted.'));
        } elseif (isset($user) && ($user->status == USER_STATUS_ACTIVE)) {
            if (isset($user) && $user->role == USER_ROLE_OWNER) {
                return redirect()->route('owner.dashboard');
            } elseif (isset($user) && ($user->role == USER_ROLE_TENANT)) {
                if (!is_null($user->tenant->property_id) && !is_null($user->tenant->property_id)) {
                    return redirect()->route('tenant.dashboard');
                } else {
                    Auth::logout();
                    return redirect("login")->with('error', __('Your account is inactive. Please contact with admin'));
                }
            } elseif (isset($user) && ($user->role == USER_ROLE_MAINTAINER)) {
                return redirect()->route('maintainer.dashboard');
            } elseif (isset($user) && ($user->role == USER_ROLE_ADMIN)) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return redirect("login")->with('error', __(SOMETHING_WENT_WRONG));
            }
        } else {
            Auth::logout();
            return redirect("login")->with('error', __(SOMETHING_WENT_WRONG));
        }
    }
}
