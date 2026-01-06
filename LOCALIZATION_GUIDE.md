# 多言語化ガイド

## 🌍 概要

このアプリケーションは日本語、英語を中心に多言語対応を行います。Laravelの標準機能を使用して実装します。

## 🎯 対応言語

### Phase 1 (MVP)
- **日本語** (ja) - デフォルト
- **英語** (en)

### Phase 2 (将来的)
- スペイン語 (es)
- ドイツ語 (de)
- フランス語 (fr)
- 中国語 (zh)
- 韓国語 (ko)

---

## 📁 ディレクトリ構造

```
resources/
└── lang/
    ├── ja/                  # 日本語
    │   ├── messages.php
    │   ├── validation.php
    │   ├── auth.php
    │   ├── reports.php
    │   └── battles.php
    ├── en/                  # 英語
    │   ├── messages.php
    │   ├── validation.php
    │   ├── auth.php
    │   ├── reports.php
    │   └── battles.php
    └── ja.json             # SPAフロントエンド用 (オプション)
```

---

## 🔧 基本設定

### 1. デフォルト言語の設定

**config/app.php**:
```php
return [
    'locale' => 'ja',                // デフォルト言語
    'fallback_locale' => 'en',       // フォールバック言語
    'available_locales' => ['ja', 'en'],  // 利用可能な言語
];
```

### 2. ミドルウェアの作成

言語を自動検出・設定するミドルウェア:

```bash
php artisan make:middleware SetLocale
```

**app/Http/Middleware/SetLocale.php**:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. URLパラメータから言語を取得 (?lang=en)
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }
        
        // 2. セッションから言語を取得
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        
        // 3. ユーザー設定から言語を取得 (認証済みの場合)
        elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }
        
        // 4. ブラウザの言語設定から取得
        else {
            $locale = $request->getPreferredLanguage(config('app.available_locales'));
        }
        
        // 利用可能な言語かチェック
        if (!in_array($locale, config('app.available_locales'))) {
            $locale = config('app.fallback_locale');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}
```

### 3. ミドルウェアの登録

**app/Http/Kernel.php**:
```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\SetLocale::class,
    ],
];
```

---

## 📝 翻訳ファイルの作成

### 1. 共通メッセージ

**resources/lang/ja/messages.php**:
```php
<?php

return [
    'welcome' => 'クラッシュ・ロワイヤル解析プラットフォームへようこそ',
    'app_name' => 'クラロワ解析',
    
    // ナビゲーション
    'nav' => [
        'dashboard' => 'ダッシュボード',
        'players' => 'プレイヤー',
        'reports' => 'レポート',
        'settings' => '設定',
    ],
    
    // ボタン
    'buttons' => [
        'search' => '検索',
        'save' => '保存',
        'cancel' => 'キャンセル',
        'delete' => '削除',
        'edit' => '編集',
        'refresh' => '更新',
        'back' => '戻る',
    ],
    
    // 共通メッセージ
    'success' => '成功しました',
    'error' => 'エラーが発生しました',
    'no_data' => 'データがありません',
    'loading' => '読み込み中...',
    
    // ページネーション
    'pagination' => [
        'showing' => ':total 件中 :from - :to 件を表示',
        'per_page' => '1ページあたりの表示件数',
    ],
];
```

**resources/lang/en/messages.php**:
```php
<?php

return [
    'welcome' => 'Welcome to Clash Royale Analytics Platform',
    'app_name' => 'CR Analytics',
    
    // Navigation
    'nav' => [
        'dashboard' => 'Dashboard',
        'players' => 'Players',
        'reports' => 'Reports',
        'settings' => 'Settings',
    ],
    
    // Buttons
    'buttons' => [
        'search' => 'Search',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'refresh' => 'Refresh',
        'back' => 'Back',
    ],
    
    // Common messages
    'success' => 'Success',
    'error' => 'An error occurred',
    'no_data' => 'No data available',
    'loading' => 'Loading...',
    
    // Pagination
    'pagination' => [
        'showing' => 'Showing :from to :to of :total results',
        'per_page' => 'Items per page',
    ],
];
```

### 2. プレイヤー関連

**resources/lang/ja/players.php**:
```php
<?php

return [
    'title' => 'プレイヤー',
    'list' => 'プレイヤー一覧',
    'search' => 'プレイヤー検索',
    'add' => 'プレイヤーを追加',
    
    'fields' => [
        'tag' => 'プレイヤータグ',
        'name' => '名前',
        'level' => 'レベル',
        'trophies' => 'トロフィー',
        'best_trophies' => '最高トロフィー',
        'wins' => '勝利数',
        'losses' => '敗北数',
        'win_rate' => '勝率',
    ],
    
    'messages' => [
        'added' => 'プレイヤーを追加しました',
        'updated' => 'プレイヤー情報を更新しました',
        'deleted' => 'プレイヤーを削除しました',
        'not_found' => 'プレイヤーが見つかりません',
        'already_exists' => 'このプレイヤーは既に登録されています',
    ],
    
    'placeholders' => [
        'tag' => '#2PP のように入力',
        'search' => 'プレイヤー名またはタグで検索',
    ],
];
```

**resources/lang/en/players.php**:
```php
<?php

return [
    'title' => 'Players',
    'list' => 'Player List',
    'search' => 'Search Players',
    'add' => 'Add Player',
    
    'fields' => [
        'tag' => 'Player Tag',
        'name' => 'Name',
        'level' => 'Level',
        'trophies' => 'Trophies',
        'best_trophies' => 'Best Trophies',
        'wins' => 'Wins',
        'losses' => 'Losses',
        'win_rate' => 'Win Rate',
    ],
    
    'messages' => [
        'added' => 'Player added successfully',
        'updated' => 'Player updated successfully',
        'deleted' => 'Player deleted successfully',
        'not_found' => 'Player not found',
        'already_exists' => 'This player already exists',
    ],
    
    'placeholders' => [
        'tag' => 'Enter like #2PP',
        'search' => 'Search by name or tag',
    ],
];
```

### 3. レポート関連

**resources/lang/ja/reports.php**:
```php
<?php

return [
    'title' => 'レポート',
    'generate' => 'レポート生成',
    'view' => 'レポートを見る',
    
    'types' => [
        'daily' => '日次レポート',
        'weekly' => '週次レポート',
        'monthly' => '月次レポート',
        'custom' => 'カスタム期間',
    ],
    
    'statistics' => [
        'total_battles' => '総バトル数',
        'wins' => '勝利',
        'losses' => '敗北',
        'draws' => '引き分け',
        'win_rate' => '勝率',
        'trophy_change' => 'トロフィー変動',
        'avg_trophy_change' => '平均トロフィー変動',
        'three_crown_wins' => '3クラウン勝利',
        'crowns_earned' => '獲得クラウン数',
        'crowns_lost' => '失ったクラウン数',
    ],
    
    'deck_analysis' => [
        'title' => 'デッキ分析',
        'most_used' => '最も使用したデッキ',
        'best_performing' => '最高勝率デッキ',
        'usage_count' => '使用回数',
    ],
    
    'card_analysis' => [
        'title' => 'カード分析',
        'most_used' => '最も使用したカード',
        'usage_rate' => '使用率',
        'win_rate_with_card' => 'このカードを使った勝率',
    ],
    
    'opponent_analysis' => [
        'title' => '対戦相手分析',
        'avg_trophies' => '平均トロフィー',
        'common_decks' => 'よく当たるデッキ',
    ],
    
    'messages' => [
        'generated' => 'レポートを生成しました',
        'no_data' => 'この期間のデータがありません',
    ],
];
```

**resources/lang/en/reports.php**:
```php
<?php

return [
    'title' => 'Reports',
    'generate' => 'Generate Report',
    'view' => 'View Report',
    
    'types' => [
        'daily' => 'Daily Report',
        'weekly' => 'Weekly Report',
        'monthly' => 'Monthly Report',
        'custom' => 'Custom Period',
    ],
    
    'statistics' => [
        'total_battles' => 'Total Battles',
        'wins' => 'Wins',
        'losses' => 'Losses',
        'draws' => 'Draws',
        'win_rate' => 'Win Rate',
        'trophy_change' => 'Trophy Change',
        'avg_trophy_change' => 'Average Trophy Change',
        'three_crown_wins' => '3-Crown Wins',
        'crowns_earned' => 'Crowns Earned',
        'crowns_lost' => 'Crowns Lost',
    ],
    
    'deck_analysis' => [
        'title' => 'Deck Analysis',
        'most_used' => 'Most Used Deck',
        'best_performing' => 'Best Performing Deck',
        'usage_count' => 'Usage Count',
    ],
    
    'card_analysis' => [
        'title' => 'Card Analysis',
        'most_used' => 'Most Used Cards',
        'usage_rate' => 'Usage Rate',
        'win_rate_with_card' => 'Win Rate with This Card',
    ],
    
    'opponent_analysis' => [
        'title' => 'Opponent Analysis',
        'avg_trophies' => 'Average Trophies',
        'common_decks' => 'Common Opponent Decks',
    ],
    
    'messages' => [
        'generated' => 'Report generated successfully',
        'no_data' => 'No data for this period',
    ],
];
```

### 4. バリデーションメッセージ

**resources/lang/ja/validation.php**:
```php
<?php

return [
    'required' => ':attributeは必須です',
    'string' => ':attributeは文字列である必要があります',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください',
    ],
    'regex' => ':attributeの形式が正しくありません',
    'unique' => 'この:attributeは既に使用されています',
    'exists' => '選択された:attributeは存在しません',
    'date' => ':attributeは有効な日付である必要があります',
    'after' => ':attributeは:dateより後の日付である必要があります',
    'in' => '選択された:attributeは無効です',
    
    'attributes' => [
        'player_tag' => 'プレイヤータグ',
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'period_start' => '開始日',
        'period_end' => '終了日',
        'type' => 'タイプ',
    ],
    
    'custom' => [
        'player_tag' => [
            'regex' => 'プレイヤータグは#から始まり、英数字で構成される必要があります',
        ],
    ],
];
```

---

## 💻 実装例

### 1. コントローラーでの使用

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::paginate(15);
        
        return view('players.index', [
            'players' => $players,
            'title' => __('players.list'),
        ]);
    }
    
    public function store(Request $request)
    {
        // バリデーション (多言語メッセージ自動適用)
        $validated = $request->validate([
            'tag' => 'required|regex:/^#[0-9A-Z]+$/',
        ]);
        
        // プレイヤー作成
        $player = Player::create($validated);
        
        // フラッシュメッセージ (多言語)
        return redirect()
            ->route('players.show', $player)
            ->with('success', __('players.messages.added'));
    }
}
```

### 2. Bladeテンプレートでの使用

```blade
{{-- resources/views/players/index.blade.php --}}
@extends('layouts.app')

@section('title', __('players.title'))

@section('content')
<div class="container">
    <h1>{{ __('players.list') }}</h1>
    
    <a href="{{ route('players.create') }}" class="btn btn-primary">
        {{ __('players.add') }}
    </a>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('players.fields.tag') }}</th>
                <th>{{ __('players.fields.name') }}</th>
                <th>{{ __('players.fields.trophies') }}</th>
                <th>{{ __('players.fields.win_rate') }}</th>
                <th>{{ __('messages.buttons.edit') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($players as $player)
                <tr>
                    <td>{{ $player->tag }}</td>
                    <td>{{ $player->name }}</td>
                    <td>{{ number_format($player->trophies) }}</td>
                    <td>{{ number_format($player->win_rate * 100, 2) }}%</td>
                    <td>
                        <a href="{{ route('players.edit', $player) }}">
                            {{ __('messages.buttons.edit') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">{{ __('messages.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{ $players->links() }}
</div>
@endsection
```

### 3. APIレスポンスでの使用

```php
<?php

namespace App\Http\Controllers\Api;

class PlayerController extends Controller
{
    public function show($id)
    {
        $player = Player::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $player,
            'message' => __('players.messages.found'),
        ]);
    }
    
    public function destroy($id)
    {
        Player::findOrFail($id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('players.messages.deleted'),
        ]);
    }
}
```

### 4. 言語切り替えコントローラー

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * 言語を切り替える
     */
    public function switch(Request $request, $locale)
    {
        // 利用可能な言語かチェック
        if (!in_array($locale, config('app.available_locales'))) {
            abort(400);
        }
        
        // セッションに保存
        Session::put('locale', $locale);
        
        // ユーザー設定にも保存 (認証済みの場合)
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        return redirect()->back();
    }
}
```

---

## 🎨 言語切り替えUI

### ナビゲーションバーに言語切り替えを追加

```blade
{{-- resources/views/layouts/app.blade.php --}}
<nav class="navbar">
    <div class="container">
        <a href="/" class="navbar-brand">{{ __('messages.app_name') }}</a>
        
        <ul class="navbar-nav">
            <li><a href="{{ route('dashboard') }}">{{ __('messages.nav.dashboard') }}</a></li>
            <li><a href="{{ route('players.index') }}">{{ __('messages.nav.players') }}</a></li>
            <li><a href="{{ route('reports.index') }}">{{ __('messages.nav.reports') }}</a></li>
        </ul>
        
        {{-- 言語切り替えドロップダウン --}}
        <div class="dropdown">
            <button class="dropdown-toggle">
                {{ strtoupper(app()->getLocale()) }}
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('language.switch', 'ja') }}">
                        🇯🇵 日本語
                    </a>
                </li>
                <li>
                    <a href="{{ route('language.switch', 'en') }}">
                        🇺🇸 English
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

---

## 🗂 データベースの多言語化

カード名などマスターデータの多言語対応:

### 1. マイグレーション

```php
Schema::create('cards', function (Blueprint $table) {
    $table->id();
    $table->json('name'); // {"ja": "ナイト", "en": "Knight"}
    $table->json('description'); // 説明文も多言語化
    // ...
});
```

### 2. モデルでのアクセサ

```php
class Card extends Model
{
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];
    
    /**
     * 現在の言語でのカード名を取得
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }
    
    /**
     * 現在の言語での説明文を取得
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? '';
    }
}
```

### 3. 使用例

```blade
<div class="card">
    <h3>{{ $card->localized_name }}</h3>
    <p>{{ $card->localized_description }}</p>
</div>
```

---

## 🧪 テスト

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    /** @test */
    public function it_displays_japanese_by_default()
    {
        $response = $this->get('/');
        
        $response->assertSee('ダッシュボード');
    }
    
    /** @test */
    public function it_switches_to_english()
    {
        $response = $this->get('/?lang=en');
        
        $response->assertSee('Dashboard');
    }
    
    /** @test */
    public function it_persists_language_in_session()
    {
        $this->get('/language/switch/en');
        
        $response = $this->get('/');
        $response->assertSee('Dashboard');
    }
}
```

---

## 📝 翻訳作業のワークフロー

1. **英語でキーを定義** (デフォルト)
2. **日本語に翻訳**
3. **他言語に展開** (Phase 2)
4. **プロの翻訳者にレビュー依頼** (オプション)

### 翻訳サービス
- **DeepL API**: 高品質な機械翻訳
- **Lokalise / Crowdin**: 翻訳管理プラットフォーム

---

## ✅ チェックリスト

- [ ] すべてのUIテキストが翻訳ファイルに定義されている
- [ ] ハードコードされた文字列がない
- [ ] バリデーションメッセージが多言語化されている
- [ ] エラーメッセージが多言語化されている
- [ ] 日付・数値フォーマットが各言語に対応している
- [ ] 言語切り替えUIが実装されている
- [ ] デフォルト言語とフォールバック言語が設定されている

---

**最終更新**: 2026-01-06
