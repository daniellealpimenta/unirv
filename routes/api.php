<?php

use App\Http\Controllers\IngressosController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\WebhookController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('lotes')->group(function () {
    Route::post('/store', LoteController::class .'@store')->name('loteStore');
    Route::get('/show', LoteController::class .'@show')->name('loteShow');
    Route::put('/enable/{loteid}', LoteController::class .'@enable')->name('loteEnable');
    Route::put('/disable/{loteid}', LoteController::class .'@disable')->name('loteDisable');
    Route::put('/updateQuantidade/{loteid}', LoteController::class .'@updateQuantidade')->name('loteUpdateQuantidade');
    Route::put('/update/{loteid}', LoteController::class .'@update')->name('loteUpdate');
    Route::delete('/delete/{loteid}', LoteController::class .'@destroy')->name('loteDestroy');   
});

Route::prefix('ingressos')->group(function () {
    Route::post('/store/{loteid}', IngressosController::class .'@store')->name('ingressoStore');
    Route::get('/show', IngressosController::class .'@show')->name('ingressoShow');
    Route::put('/enable/{ingressoid}', IngressosController::class .'@enable')->name('ingressoEnable');
    Route::put('/disable/{ingressoid}', IngressosController::class .'@disable')->name('ingressoDisable');
    Route::put('/update/{loteid}', IngressosController::class .'@update')->name('ingressoUpdate');
    Route::delete('/delete/{ingressoid}', IngressosController::class .'@destroy')->name('ingressoDestroy');
});

Route::post('/pagamento/pix/{ingresso_id}', [PagamentoController::class, 'pagarPix']);

Route::post('/webhooks/mercadopago', [WebhookController::class, 'handle'])
    ->withoutMiddleware([\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class])
    ->name('webhooks.mercadopago');

require __DIR__.'/auth.php';
