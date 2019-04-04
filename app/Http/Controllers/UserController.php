<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Jobs\SendReminderEmail;

class UserController extends Controller
{
    //
    public function sendReminderEmail() 
    {
        $user = User::findOrFail(1);

        // $this->dispatch(new SendReminderEmail($user));
        $job = (new SendReminderEmail($user))->onQueue('emails');

        $this->dispatch($job);
    }
}
