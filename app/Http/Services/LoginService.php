<?php

namespace App\Http\Services;

use App\User;
use Auth;
use Log;
use Socialite;

class LoginService
{
    public function providerLogin($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
        }

        $existedUser = User::where('email', $user->email)->first();

        if (!$existedUser) {
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->avatar = $user->avatar;
            $newUser->api_token = uniqid(base64_encode(str_random(60)));
            $newUser->save();

            Auth::login($newUser);
        } else {
            User::where('email', $user->email)->update(['api_token' => uniqid(base64_encode(str_random(60)))]);
            Auth::login($existedUser);
        }
    }

    public function login($request)
    {
        $user = User::where('email', '=', $request->input('email'))->first();

        if (!$user) {
            return redirect('/login')->withErrors(
                [
                    'email' => '電子郵件不存在',
                ]
            )->withInput();
        }

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $api_token = uniqid(base64_encode(str_random(60)));
            User::where('email', $request->input('email'))->update(['api_token' => $api_token]);
            Auth::login($user);
            return redirect()->to('/home');
        } else {
            return redirect('/login')->withErrors(
                [
                    'password' => '密碼錯誤',
                ]
            )->withInput();
        }

    }
}
