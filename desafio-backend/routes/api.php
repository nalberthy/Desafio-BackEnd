<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('classificar', 'Api\\Sentimentanalyzer@classificar_Ticket');
Route::get('ordenar', 'Api\\Sentimentanalyzer@ordenar');

// Route::get('filtro/intervalo/{data_inicio}/{data_fim}', 'Api\\Sentimentanalyzer@search_intervalo');
Route::get('filtro/intervalo', 'Api\\Sentimentanalyzer@search_intervalo');

Route::get('filtro/prioridade', 'Api\\Sentimentanalyzer@search_prioridade');