<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\ReportController;

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

// APIルートは自動的に/apiプレフィックスが付きます
Route::middleware('api')->group(function () {
    // Dashboard API
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // Videos API
    Route::prefix('videos')->group(function () {
        Route::get('/', [VideoController::class, 'index']);
        Route::post('/upload', [VideoController::class, 'store']);
        Route::get('/{id}', [VideoController::class, 'show']);
        Route::post('/{id}/analyze', [VideoController::class, 'analyze']);
        Route::delete('/{id}', [VideoController::class, 'destroy']);
    });

    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('/{videoId}', [ReportController::class, 'show']);
    });
});
