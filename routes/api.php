<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}) */;

Route::get('consulta/cpf', 'API\ConsultaController@cpf');

Route::post('consulta/criar', 'API\ConsultaController@store');

Route::post('consulta/deletar', 'API\ConsultaController@destroy');

Route::get('consulta/status', 'API\ConsultaController@status');

Route::post('consulta/block', 'API\ConsultaController@block');

Route::post('consulta/unblock', 'API\ConsultaController@unblock');