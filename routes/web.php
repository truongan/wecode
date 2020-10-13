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
    return redirect(route('home'));
});

Auth::routes();
Route::get('/home/', 'home_controller@index')->name('home');
Route::get('/htmleditor', 'html_editor_controller@index')->name('htmleditor');
Route::post('/htmleditor/autosave', 'html_editor_controller@autosave')->name('htmleditor.autosave');

Route::get('/moss/{id?}', 'moss_controller@index')->name('moss.index');
Route::post('/moss/{id}', 'moss_controller@update')->name('moss.update');
Route::post('/moss/detect/{id}', 'moss_controller@detect')->name('moss.detect');

Route::post('lops/{lop}/enrol/{in}', 'lop_controller@enrol')->name('lops.enrol');

Route::get('/settings', 'setting_controller@index')->name('settings.index');
Route::post('/settings', 'setting_controller@update')->name('settings.update');

Route::get('/admin','UserController@admin_index')->name('admin.admin');
Route::get('/users/add_multiple', 'UserController@add_multiple');
Route::post('/users/adds', 'UserController@add')->name('users.add');
Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');

Route::get('/problems/downloadtestsdesc/{id}', 'problem_controller@downloadtestsdesc')->name('problems.downloadtestsdesc');
Route::get('/problems/downloadpdf/{id}', 'problem_controller@pdf')->name('problems.pdf');
Route::get('/problems/downloadtemplate/{problem_id}/{assignment_id}', 'problem_controller@template')->name('problems.template');
Route::post('/problems/edit_description/{problem_id}', 'problem_controller@edit_description')->name('problems.edit_description');
// Route::get('/view_problem/{problem_id}', 'view_problem_controller@index');

Route::get('/submissions/assignment/{assignment_id}/user/{user_id}/problem/{problem_id}/view/{choose}', 'submission_controller@index')->name('submissions.index');
Route::get('/submissions/create/assignment/{assignment}/problem/{problem}/', 'submission_controller@create')->name('submissions.create');
Route::post('/submissions/store/', 'submission_controller@store')->name('submissions.store');
Route::post('/submissions/get_template/', 'submission_controller@get_template')->name('submissions.get_template');
Route::post('/submissions/rejudge/', 'submission_controller@rejudge')->name('submissions.rejudge');
Route::post('/submissions/view_code/', 'submission_controller@view_code')->name('submissions.view_code');
Route::post('/submissions/view_status/', 'submission_controller@view_status')->name('submissions.view_status');
Route::get('/rejudge', 'submission_controller@rejudge_view')->name('submissions.rejudge_view');
Route::post('/submissions/rejudge_all_problems_assignment/', 'submission_controller@rejudge_all_problems_assignment')->name('submissions.rejudge_all_problems_assignment');


Route::get('/queue', 'queue_controller@index')->name('queue.index');
Route::post('/queue', 'queue_controller@work')->name('queue.work');
Route::post('/queue/{item}/unlock', 'queue_controller@unlock')->name('queue.unlock');
Route::post('/queue/empty', 'queue_controller@empty')->name('queue.empty');

Route::get('/practice', 'practice_controller@index')->name('practice');
Route::get('/practice/show/{problem_id}', 'practice_controller@show')->name('practices.show');

Route::get('/scoreboard/full/{id}', 'scoreboard_controller@index')->name('scoreboards.index');

Route::get('/assignment/{assignment}/{problem_id}/', 'assignment_controller@show')->where(['assignment'=>'[0-9]+','problem_id'=>'[0-9]+'])->name('assignments.show');

Route::get('/assignment/download_submissions/{type}/{assignment_id}/', 'assignment_controller@download_submissions')->name('assignments.download_submissions');
Route::get('/assignment/download_all_submissions/{assignment_id}/', 'assignment_controller@download_all_submissions')->name('assignments.download_all_submissions');
Route::get('/assignment/reload_scoreboard/{assignment_id}/', 'assignment_controller@reload_scoreboard')->name('assignments.reload_scoreboard');

Route::get('/assignment/scores/accepted/', 'assignment_controller@score_accepted')->name('assignments.score_accepted');
Route::get('/assignment/scores/sum/', 'assignment_controller@score_sum')->name('assignments.score_sum');

Route::post('/assignment/check_open/', 'assignment_controller@check_open')->name('assignments.check_open');

Route::get('/scoreboard/simplify/{id}', 'scoreboard_controller@simplify')->name('scoreboards.simplify');
Route::get('/scoreboard/plain/{id}', 'scoreboard_controller@plain')->name('scoreboards.plain');

Route::get('/server_time', function(){echo  date(DATE_ISO8601);});

//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users','UserController');
Route::resource('notifications','notification_controller');
Route::resource('lops','lop_controller');
Route::resource('languages','language_controller');
Route::resource('tags','tag_controller');
Route::resource('problems','problem_controller');

Route::resource('assignments','assignment_controller')->except(['show']);
