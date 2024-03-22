<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\home_controller;
// use App\Http\Controllers\html_editor_controller;
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
Route::get('/home/', [home_controller::class, 'index'])->name('home');
// Route::get('/home/', [App\Http\Controllers\home_controller::class, 'index'])->name('home');
Route::get('/htmleditor', [App\Http\Controllers\html_editor_controller::class, 'index'])->name('htmleditor');
Route::post('/htmleditor/autosave', [App\Http\Controllers\html_editor_controller::class, 'autosave'])->name('htmleditor.autosave');

Route::get('/moss/{id?}', [App\Http\Controllers\moss_controller::class, 'index'])->name('moss.index');
Route::post('/moss/{id}', [App\Http\Controllers\moss_controller::class, 'update'])->name('moss.update');
Route::post('/moss/detect/{id}', [App\Http\Controllers\moss_controller::class, 'detect'])->name('moss.detect');

Route::post('lops/{lop}/enrol/{in}', [App\Http\Controllers\lop_controller::class, 'enrol'])->name('lops.enrol');

Route::get('/settings', [App\Http\Controllers\setting_controller::class, 'index'])->name('settings.index');
Route::post('/settings', [App\Http\Controllers\setting_controller::class, 'update'])->name('settings.update');


Route::get('/users/add_multiple', [App\Http\Controllers\UserController::class, 'add_multiple']);
Route::post('/users/adds', [App\Http\Controllers\UserController::class, 'add'])->name('users.add');
Route::post('/users/delete_submissions/{user}', [App\Http\Controllers\UserController::class, 'delete_submissions'])->name('users.delete_submissions');
Route::delete('users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
Route::get('users/ranking', [App\Http\Controllers\UserController::class, 'rank'])->name('users.rank');
Route::view('users/set_trial', 'users.set_trial')->name('users.set_trial')->middleware('auth');
Route::post('users/set_trial', [App\Http\Controllers\UserController::class, 'set_trial'])->name('users.set_trial_post');
// Route::get('/users/delete_multiple', [App\Http\Controllers\UserController::class, 'delete_multiple']);
// Route::post('/users/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');


Route::get('/problems/downloadtestsdesc/{id}', [App\Http\Controllers\problem_controller::class, 'downloadtestsdesc'])->name('problems.downloadtestsdesc');
Route::get('/problems/downloadpdf/{id}', [App\Http\Controllers\problem_controller::class, 'pdf'])->name('problems.pdf');
Route::get('/problems/downloadtemplate/{problem_id}/{assignment_id}', [App\Http\Controllers\problem_controller::class, 'template'])->name('problems.template');
Route::post('/problems/edit_description/{problem_id}', [App\Http\Controllers\problem_controller::class, 'edit_description'])->name('problems.edit_description');
Route::post('/problems/toggle_practice/{query?}', [App\Http\Controllers\problem_controller::class, 'toggle_practice'])->name('problems.toggle_practice');
Route::post('/problems/edit_tags/{problem?}', [App\Http\Controllers\problem_controller::class, 'edit_tags'])->name('problems.edit_tags');

Route::get('/submissions/assignment/{assignment_id}/user/{user_id}/problem/{problem_id}/view/{choose}', [App\Http\Controllers\submission_controller::class, 'index'])->name('submissions.index');
Route::get('/submissions/create/assignment/{assignment}/problem/{problem}/{oldsub?}', [App\Http\Controllers\submission_controller::class, 'create'])->name('submissions.create');
Route::post('/submissions/store/', [App\Http\Controllers\submission_controller::class, 'store'])->name('submissions.store');
Route::post('/submissions/get_template/', [App\Http\Controllers\submission_controller::class, 'get_template'])->name('submissions.get_template');
Route::post('/submissions/rejudge/', [App\Http\Controllers\submission_controller::class, 'rejudge'])->name('submissions.rejudge');
Route::post('/submissions/view_code/', [App\Http\Controllers\submission_controller::class, 'view_code'])->name('submissions.view_code');
Route::post('/submissions/view_status/', [App\Http\Controllers\submission_controller::class, 'view_status'])->name('submissions.view_status');
Route::post('/submissions/select/', [App\Http\Controllers\submission_controller::class, 'select_final'])->name('submissions.select');
Route::get('/rejudge/{assignment}', [App\Http\Controllers\submission_controller::class, 'rejudge_view'])->name('submissions.rejudge_view');
Route::post('/submissions/rejudge_all_problems_assignment/', [App\Http\Controllers\submission_controller::class, 'rejudge_all_problems_assignment'])->name('submissions.rejudge_all_problems_assignment');


Route::get('/queue', [App\Http\Controllers\queue_controller::class, 'index'])->name('queue.index');
Route::post('/queue', [App\Http\Controllers\queue_controller::class, 'work'])->name('queue.work');
Route::post('/queue/{item}/unlock', [App\Http\Controllers\queue_controller::class, 'unlock'])->name('queue.unlock');
Route::post('/queue/empty', [App\Http\Controllers\queue_controller::class, 'empty'])->name('queue.empty');

Route::get('/practice', [App\Http\Controllers\practice_controller::class, 'index'])->name('practice');
Route::get('/practice/show/{problem}', [App\Http\Controllers\practice_controller::class, 'show'])->name('practices.show');

Route::get('/scoreboard/full/{id}', [App\Http\Controllers\scoreboard_controller::class, 'index'])->name('scoreboards.index');

Route::get('/assignment/{assignment}/{problem_id}/', [App\Http\Controllers\assignment_controller::class, 'show'])->where(['assignment'=>'[0-9]+','problem_id'=>'[0-9]+'])->name('assignments.show');

Route::get('/assignment/download_submissions/{type}/{assignment_id}/', [App\Http\Controllers\assignment_controller::class, 'download_submissions'])->name('assignments.download_submissions');
Route::get('/assignment/download_all_submissions/{assignment_id}/', [App\Http\Controllers\assignment_controller::class, 'download_all_submissions'])->name('assignments.download_all_submissions');
Route::get('/assignment/reload_scoreboard/{assignment_id}/', [App\Http\Controllers\assignment_controller::class, 'reload_scoreboard'])->name('assignments.reload_scoreboard');

Route::get('/assignment/scores/accepted/', [App\Http\Controllers\assignment_controller::class, 'score_accepted'])->name('assignments.score_accepted');
Route::get('/assignment/scores/sum/', [App\Http\Controllers\assignment_controller::class, 'score_sum'])->name('assignments.score_sum');

Route::post('/assignment/check_open/', [App\Http\Controllers\assignment_controller::class, 'check_open'])->name('assignments.check_open');
Route::get('/assignment/duplicate/{assignment}', [App\Http\Controllers\assignment_controller::class, 'duplicate'])->name('assignments.duplicate');

Route::get('/scoreboard/simplify/{id}', [App\Http\Controllers\scoreboard_controller::class, 'simplify'])->name('scoreboards.simplify');
Route::get('/scoreboard/plain/{id}', [App\Http\Controllers\scoreboard_controller::class, 'plain'])->name('scoreboards.plain');

Route::get('/server_time', function(){echo  date(DATE_ISO8601);});
Route::get('/lop/scoreboard/{lop}', [App\Http\Controllers\lop_controller::class, 'scoreboard'])->name('lop.scoreboard');

//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users',App\Http\Controllers\UserController::class);
Route::resource('notifications',App\Http\Controllers\notification_controller::class);
Route::resource('lops',App\Http\Controllers\lop_controller::class);
Route::resource('languages',App\Http\Controllers\language_controller::class);
Route::resource('tags',App\Http\Controllers\tag_controller::class);
Route::resource('problems',App\Http\Controllers\problem_controller::class);

Route::resource('assignments',App\Http\Controllers\assignment_controller::class)->except(['show']);
