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
use Auth;


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

Route::get('/send/reminder/email','UserController@sendReminderEmail'); // queue

Route::get('/account_kit/login/success', function() {
    // Initialize variables
$app_id = 'app id';
$secret = 'secret';
$version = 'v1.1'; // 'v1.1' for example

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
    '&code=' . $_POST['code'] .
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

return 'phone: ' . $phone . ' | email: ' . $email; 
});
