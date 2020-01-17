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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('users','UserController');

Route::get('/dashboard', 'HomeController@index')->name('home');
Route::get('/notifications', 'NotificationController@index')->name('notification');
Route::get('/settings', 'SettingController@index')->name('setting');
Route::get('/users', 'UserController@index')->name('user');
Route::get('/problems', 'ProblemController@index')->name('problem');
Route::get('/assignments', 'AssignmentController@index')->name('assignment');
Route::get('/view_problem', 'ViewProblemController@index')->name('view_problem');
Route::get('/submit', 'SubmitController@index')->name('submit');
Route::get('/submissions', 'SubmissionController@index')->name('submission');
Route::get('/scoreboard', 'ScoreboardController@index')->name('scoreboard');
