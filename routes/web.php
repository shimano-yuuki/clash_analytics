<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

// Next.jsフロントエンドに移行したため、Webルートは最小限に
// APIルートは routes/api.php を参照

// Language switching (必要に応じてAPIからも利用可能)
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
