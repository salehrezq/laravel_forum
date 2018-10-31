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
auth()->loginUsingId(9);

Route::get('/', function () {

    // We insert a notification into the database:
    // auth()->user()->notify(new App\Notifications\ThreadNotification());

    return view('note');
});

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/create', 'ThreadsController@create')->name('threads.create');
Route::get('/threads/{channelSlug}', 'ThreadsController@index')->name('threads.channel');
Route::post('/threads', 'ThreadsController@store')->name('threads.store');
Route::get('/threads/{channelSlug}/{thread}', 'ThreadsController@show')->name('threads.show');
// Called by XMLHttpRequest in JS file
Route::delete('/threads/delete', 'ThreadsController@destroy')->name('threads.destroy');

// Called by XMLHttpRequest in JS file
Route::post('/replies', 'RepliesController@store')->name('replies.store');
// Called by XMLHttpRequest in JS file
Route::delete('/replies/delete', 'RepliesController@destroy')->name('replies.destroy');
// Called by XMLHttpRequest in JS file
Route::patch('/replies/edit', 'RepliesController@update')->name('replies.update');

Route::post('/users/likereply', 'UsersController@storeLikeReplyToggle')->name('user.like.reply.toggle');
Route::get('/users/{user}', 'UsersController@show')->name('users.show');
Route::get('/users/{user}/settings', 'SettingsController@index')->name('users.settings.get');
Route::post('/users/{user}/settings', 'SettingsController@update')->name('users.settings.post');

Route::post('/subscriptions', 'SubscriptionsController@store')->name('subscriptions.store');

Route::get('/notifications/{page}', 'NotificationsController@index')->name('notifications.index');

Route::patch('/notifications/markasread/{notification}', 'NotificationsController@markAsRead')->name('notifications.markasread');
Route::patch('/notifications/markallasread/', 'NotificationsController@markAllAsRead')->name('notifications.markallasread');

Route::get('/confirmationurl/{hash}', 'UsersConfirmationController@show');

Route::get('api/users', 'Api\UsersController@index');

