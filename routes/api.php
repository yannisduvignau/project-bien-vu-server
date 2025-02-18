<?php

use App\Http\Controllers\IAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Unauthenticated IA routes */

Route::prefix('ia')
    ->name('ia.')
    ->group(function () {
        Route::post('/analyse', [IAController::class, 'analyse'])->name('analyse');
        Route::post('/estimation', [IAController::class, 'estimation'])->name('estimation');
        Route::post('/generation', [IAController::class, 'generation'])->name('generation');
    });
