<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Middleware\CheckConstellation;
use Illuminate\Support\Facades\Mail;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => '/login/social'], function () {
    Route::get('{provider}/redirect', [
        'as' => 'social.redirect',
        'uses' => 'Auth\LoginController@redirectProvider',
    ]);

    Route::get('{provider}/callback', [
        'as' => 'social.callback',
        'uses' => 'Auth\LoginController@callback',
    ]);
});

Route::get('/constellation/{id}', 'ConstellationController@showConstellationDetail')->middleware(CheckConstellation::class);

Route::get('/sendmail', function () {
    $data = ['name' => 'Test'];
    Mail::send('email.welcome', $data, function ($message) {
        $message->to('yourmail@.com')->subject('This is test email');
    });
    return 'Your email has been sent successfully!';
});

Route::get('/send/reminder/email','UserController@sendReminderEmail');
