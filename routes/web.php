<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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
    return ['Laravel' => app()->version()];
});

// Route::get('/run-migrate-fresh', function () {
//     Artisan::call('migrate:fresh --force');
//     return 'Migrations fresh rodadas com sucesso!';
// });

use Illuminate\Support\Facades\Log;

Route::post('/teste-webhook', function () {
    Log::info('Webhook de teste recebido!');
    return response()->json(['message' => 'OK']);
});

