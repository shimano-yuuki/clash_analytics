<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\VideoController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\LanguageController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Videos
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('index');
    Route::get('/upload', [VideoController::class, 'create'])->name('upload');
    Route::post('/', [VideoController::class, 'store'])->name('store');
    Route::get('/{id}', [VideoController::class, 'show'])->name('show');
    Route::post('/{id}/analyze', [VideoController::class, 'analyze'])->name('analyze');
    Route::delete('/{id}', [VideoController::class, 'destroy'])->name('destroy');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/{videoId}', [ReportController::class, 'show'])->name('show');
});

// Language
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
