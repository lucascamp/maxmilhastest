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

Route::post('/find', 'CpfController@find')->name('find');
Route::post('/block', 'CpfController@block')->name('block');
Route::post('/unblock', 'CpfController@unblock')->name('unblock');
Route::resource('/', 'CpfController');

Route::get('/status', 'CpfController@status')->name('status');

