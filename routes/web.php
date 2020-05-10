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
Route::post('/htmleditor/autosave', 'html_editor_controller@autosave')->name('htmleditor.autosave');

Route::get('/settings', 'setting_controller@index')->name('settings.index');
Route::post('/settings', 'setting_controller@update')->name('settings.update');

Route::get('/users/add_multiple', 'UserController@add_multiple');
Route::post('/users/adds', 'UserController@add')->name('users.add');
Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');

Route::get('/problems/download/{id}', 'problem_controller@pdf')->name('problems.pdf');
Route::post('/problems/edit_description/{problem_id}', 'problem_controller@edit_description')->name('problems.edit_description');
// Route::get('/view_problem/{problem_id}', 'view_problem_controller@index');

Route::get('/submissions/assignment/{assignment_id}/user/{user_id}/problem/{problem_id}/view/{choose}', 'submission_controller@index')->name('submissions.index');
Route::get('/submissions/create/assignment/{assignment}/problem/{problem}/', 'submission_controller@create')->name('submissions.create');
Route::post('/submissions/store/', 'submission_controller@store')->name('submissions.store');
Route::post('/submissions/get_template/', 'submission_controller@get_template')->name('submissions.get_template');
Route::post('/submissions/rejudge/', 'submission_controller@rejudge')->name('submissions.rejudge');
Route::post('/submissions/view_code/', 'submission_controller@view_code')->name('submissions.view_code');
Route::get('/rejudge', 'submission_controller@rejudge_view')->name('submissions.rejudge_view');


Route::get('/queue', 'queue_controller@index')->name('queue.index');
Route::post('/queue', 'queue_controller@work')->name('queue.work');
Route::post('/queue/{item}/unlock', 'queue_controller@unlock')->name('queue.unlock');
Route::post('/queue/empty', 'queue_controller@empty')->name('queue.empty');

Route::get('/practice', 'practice_controller@index');
Route::get('/practice/show/{problem_id}', 'practice_controller@show')->name('practices.show');

Route::get('/scoreboard/full/{id}', 'scoreboard_controller@index')->name('scoreboards.index');

Route::get('/assignment/{assignment}/{problem_id}/', 'assignment_controller@show')->name('assignments.show');
Route::get('/assignment/download/{assignment}/{type}/', 'assignment_controller@show')->name('assignments.download');
Route::get('/scoreboard/simplify/{id}', 'scoreboard_controller@simplify')->name('scoreboards.simplify');
Route::get('/scoreboard/plain/{id}', 'scoreboard_controller@plain')->name('scoreboards.plain');

//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users','UserController');
Route::resource('notifications','notification_controller');
Route::resource('lops','lop_controller');
Route::resource('languages','language_controller');
Route::resource('tags','tag_controller');
Route::resource('problems','problem_controller');

Route::resource('assignments','assignment_controller')->except(['show']);
