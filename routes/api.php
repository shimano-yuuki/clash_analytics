<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VideoController;

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
    // Videos API
    Route::prefix('videos')->group(function () {
        Route::get('/', [VideoController::class, 'index']); // 動画一覧取得
        Route::post('/upload', [VideoController::class, 'store']); // 動画アップロード
        Route::get('/{id}', [VideoController::class, 'show']); // 動画詳細・解析結果取得
        Route::post('/{id}/analyze', [VideoController::class, 'analyze']); // 解析開始
        Route::delete('/{id}', [VideoController::class, 'destroy']); // 動画削除
    });
});
