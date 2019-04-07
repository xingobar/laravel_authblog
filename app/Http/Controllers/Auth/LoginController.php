<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\LoginService;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;
use Log;

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
    public function __construct(LoginService $loginService)
    {
        $this->middleware('guest')->except('logout');
        $this->loginService = $loginService;
    }

    public function redirectProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $this->loginService->providerLogin($provider);
        return redirect()->to('/home');
    }

    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'Username or email is required',
                'password.required' => 'Password is required',
            ]
        );

        return $this->loginService->login($request);

    }

    public function smsLogin(Request $request)
    {
        // Initialize variables
        $app_id = env('FACEBOOK_CLIENT_ID');
        $secret = env('ACCOUNT_KEY_SECRET');
        $version = env('ACCOUNT_KIT_VERSION'); // 'v1.1' for example

        // Method to send Get request to url
        function doCurl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = json_decode(curl_exec($ch), true);
            curl_close($ch);
            return $data;
        }

        // Exchange authorization code for access token
        $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?' .
            'grant_type=authorization_code' .
            '&code=' . $request->input('code') .
            "&access_token=AA|$app_id|$secret";
        $data = doCurl($token_exchange_url);
        $user_id = $data['id'];
        $user_access_token = $data['access_token'];
        $refresh_interval = $data['token_refresh_interval_sec'];

        // Get Account Kit information
        $me_endpoint_url = 'https://graph.accountkit.com/' . $version . '/me?' .
            'access_token=' . $user_access_token;
        $data = doCurl($me_endpoint_url);
        $phone = isset($data['phone']) ? $data['phone']['number'] : '';
        $email = isset($data['email']) ? $data['email']['address'] : '';

        Log::info('phone: ' . $phone . '  |email: ' . $email );
        Log::info(json_encode($data));

        return 'phone: ' . $phone . ' | email: ' . $email;

    }
}
