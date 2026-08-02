<?php

use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InspectionServiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/autos', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/autos/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');

Route::get('/vende-tu-auto', [AppraisalController::class, 'create'])->name('sell.create');
Route::post('/vende-tu-auto', [AppraisalController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('sell.store');

Route::get('/revision-precompra', [InspectionServiceController::class, 'create'])->name('inspection.create');
Route::post('/revision-precompra', [InspectionServiceController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('inspection.store');

Route::get('/contacto', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contacto', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/terminos', [PageController::class, 'terms'])->name('terms');
Route::get('/privacidad', [PageController::class, 'privacy'])->name('privacy');
