<?php

use App\Http\Controllers\Scraping\BieniciScraperController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Scraping\FonciaScraperController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\Scraping\LeboncoinScraperController;
use App\Http\Controllers\Scraping\LogicimmoScraperController;
use App\Http\Controllers\ScrapController;
use App\Http\Controllers\Scraping\SelogerScraperController;
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
        Route::post('/analyse/description', [IAController::class, 'analyserAnnonce'])->name('analyse');
        Route::post('/analyse/url', [ScrapController::class, 'analyserAnnonce'])->name('analyse');
        Route::post('/estimation/description', [IAController::class, 'estimerPrix'])->name('estimation');
        Route::post('/estimation/url', [ScrapController::class, 'estimerPrix'])->name('estimation');
        Route::post('/generation', [IAController::class, 'genererAnnonce'])->name('generation');
    });

Route::prefix('scrape')
    ->name('scrape.')
    ->group(function () {
        Route::post('/seloger', [SelogerScraperController::class, 'scrape'])->name('seloger');
        Route::post('/leboncoin', [LeboncoinScraperController::class, 'scrape'])->name('leboncoin');
        Route::post('/logicimmo', [LogicimmoScraperController::class, 'scrape'])->name('logicimmo');
        Route::post('/foncia', [FonciaScraperController::class, 'scrape'])->name('foncia');
        Route::post('/bienici', [BieniciScraperController::class, 'scrape'])->name('bienici');
    });

Route::get('/dpe', [Controller::class, 'getDpe']);
Route::get('/loyers/tendance', [Controller::class, 'getRentTrend']);
Route::get('/estimation', [Controller::class, 'estimatePropertyPrice']);
