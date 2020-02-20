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
Route::view('/admin','admin.admin')->name('admin.index');
Route::get('/home', 'home_controller@index')->name('home');
Route::get('/htmleditor', 'html_editor_controller@index');
Route::post('/htmleditor/autosave', 'html_editor_controller@autosave');

Route::get('/settings', 'setting_controller@index')->name('settings.index');
Route::post('/settings', 'setting_controller@update')->name('settings.update');

Route::get('/users/add_multiple', 'UserController@add_multiple');
Route::post('/users/adds', 'UserController@add')->name('users.add');
Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');


// Route::get('/problems/show/{id}', 'problem_controller@show');
Route::get('/problems/download/{id}', 'problem_controller@pdf')->name('problems.pdf');
Route::get('/problems/test', 'problem_controller@test');
Route::get('/submissions/{choose}','submission_controller@index')->name('submissions.index');
// Route::get('/problems/add', 'problem_controller@create')->name('problems.create');

//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users','UserController');
Route::resource('notifications','notification_controller');
Route::resource('lops','lop_controller');
Route::resource('languages','language_controller');
Route::resource('tags','tag_controller');
Route::resource('problems','problem_controller');
Route::resource('assignments','assignment_controller');
Route::resource('queue','queue_controller');
