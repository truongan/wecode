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

Route::get('/settings', 'setting_controller@index')->name('settings.index');
Route::post('/settings', 'setting_controller@update')->name('settings.update');

Route::get('/users/add', 'UserController@add')->name('users.add');
Route::post('/users/adds', 'UserController@add')->name('users.add');
Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');

Route::get('/languages/order', 'language_controller@get_language_order_by_sorting');

Route::get('/problems/add_prolem', 'problem_controller@add_problem');
Route::get('/problems/show', 'problem_controller@index');
Route::get('/problems/show/{id}', 'problem_controller@show');
Route::get('/problems/add_problem', 'problem_controller@add_problem');


//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users','UserController');
Route::resource('notifications','notification_controller');
Route::resource('lops','lop_controller');
Route::resource('languages','language_controller');
Route::resource('tags','tag_controller');
Route::resource('problemtags','problem_tag_controller');
Route::resource('problems','problem_controller');
