<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. URLパラメータから言語を取得 (?locale=ja)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            Session::put('locale', $locale);
        }
        
        // 2. セッションから言語を取得
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        
        // 3. ユーザー設定から言語を取得 (認証済みの場合)
        elseif (auth()->check() && auth()->user()->locale ?? null) {
            $locale = auth()->user()->locale;
        }
        
        // 4. ブラウザの言語設定から取得
        else {
            $availableLocales = ['ja', 'en'];
            $locale = $request->getPreferredLanguage($availableLocales) ?: config('app.locale', 'ja');
        }
        
        // 利用可能な言語かチェック
        $availableLocales = ['ja', 'en'];
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.fallback_locale', 'en');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}
