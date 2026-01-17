<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * 言語を切り替える
     */
    public function switch(Request $request, $locale)
    {
        // 利用可能な言語かチェック
        $availableLocales = ['ja', 'en'];
        if (!in_array($locale, $availableLocales)) {
            abort(400, 'Invalid locale');
        }
        
        // セッションに保存
        Session::put('locale', $locale);
        
        // ユーザー設定にも保存 (認証済みの場合)
        if (auth()->check() && method_exists(auth()->user(), 'update')) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        // 元のページに戻る
        return Redirect::back();
    }
}
