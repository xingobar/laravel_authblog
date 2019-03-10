<?php

namespace App\Http\Services;

use App\User;
use Auth;
use Log;
use Socialite;

class LoginService
{
    public function login($provider)
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
            $newUser->save();

            Auth::login($newUser);
        } else {
            Auth::login($existedUser);
        }
    }
}
