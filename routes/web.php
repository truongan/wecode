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

Route::get('/users/add', 'UserController@add')->name('users.add');

//Resource route phải được  ghi cuối cùng, nếu không các route sau dính tới /usres sẽ ăn shit 
Route::resource('users','UserController');