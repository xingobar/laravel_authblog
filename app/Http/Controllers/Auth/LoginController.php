<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Log;
use Socialite;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
        }

        $existedUser = User::where('email', $user->email)->first();

        if (!$existedUser) {
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->avatar = $user->avatar;
            $newUser->save();

            Auth::login($newUser);
        } else {
            Auth::login($existedUser);
        }

        return redirect()->to('/home');
    }
}
