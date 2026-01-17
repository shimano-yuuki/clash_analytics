# コーディング規約

## 🎯 基本方針

- **PSR-12準拠**: PHPの標準コーディング規約に従う
- **Laravelベストプラクティス**: Laravel公式のコーディングスタイルを採用
- **可読性重視**: 誰が見ても理解しやすいコードを書く
- **DRY原則**: Don't Repeat Yourself - 重複を避ける
- **SOLID原則**: オブジェクト指向設計の基本原則を守る

## 📝 PHP コーディング規約

### 1. インデントとスペース

```php
// ✅ Good
class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::all();
        return view('players.index', compact('players'));
    }
}

// ❌ Bad (インデントが不適切)
class PlayerController extends Controller{
public function index(){
$players = Player::all();
return view('players.index', compact('players'));
}
}
```

- **インデント**: スペース4つ (タブは使用しない)
- **改行**: LF (Unix形式)
- **行末スペース**: 削除する
- **ファイル末尾**: 空行1行で終わる

### 2. 命名規則

#### クラス名
```php
// ✅ Good - パスカルケース
class PlayerController
class BattleAnalysisService
class PlayerStatistics

// ❌ Bad
class playerController
class battle_analysis_service
```

#### メソッド名
```php
// ✅ Good - キャメルケース
public function fetchBattleLog()
public function calculateWinRate()
public function getUserData()

// ❌ Bad
public function FetchBattleLog()
public function calculate_win_rate()
```

#### 変数名
```php
// ✅ Good - キャメルケース、意味のある名前
$playerTag = '#2PP';
$winRate = 0.65;
$totalBattles = 100;

// ❌ Bad
$pt = '#2PP';
$wr = 0.65;
$x = 100;
```

#### 定数名
```php
// ✅ Good - アッパースネークケース
const MAX_BATTLES_PER_REQUEST = 25;
const API_BASE_URL = 'https://api.clashroyale.com/v1';

// ❌ Bad
const maxBattlesPerRequest = 25;
const ApiBaseUrl = 'https://api.clashroyale.com/v1';
```

### 3. 配列

```php
// ✅ Good - 短い配列構文
$players = ['John', 'Jane', 'Bob'];
$stats = [
    'wins' => 10,
    'losses' => 5,
    'draws' => 2,
];

// ❌ Bad - 古い配列構文
$players = array('John', 'Jane', 'Bob');
```

### 4. 文字列

```php
// ✅ Good - シングルクォートを基本とし、変数展開時はダブルクォート
$name = 'John';
$greeting = "Hello, {$name}!";
$message = 'This is a simple string';

// ❌ Bad - 不必要にダブルクォート
$message = "This is a simple string";
$greeting = 'Hello, ' . $name . '!';
```

### 5. 型宣言

```php
// ✅ Good - 型宣言を使用
public function calculateWinRate(int $wins, int $total): float
{
    return $wins / $total;
}

// ❌ Bad - 型宣言なし
public function calculateWinRate($wins, $total)
{
    return $wins / $total;
}
```

### 6. Docブロック

```php
/**
 * プレイヤーのバトルログを取得し解析する
 *
 * @param string $playerTag プレイヤータグ
 * @param int $limit 取得件数
 * @return array 解析結果
 * @throws ClashRoyaleApiException APIエラー時
 */
public function analyzeBattleLog(string $playerTag, int $limit = 25): array
{
    // 実装
}
```

## 🏗 Laravel 規約

### 1. コントローラー

```php
// ✅ Good - 単一責任、RESTful
class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::paginate(15);
        return view('players.index', compact('players'));
    }

    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    public function store(StorePlayerRequest $request)
    {
        $player = Player::create($request->validated());
        return redirect()->route('players.show', $player);
    }
}

// ❌ Bad - 複数の責任を持つ
class PlayerController extends Controller
{
    public function doEverything()
    {
        // プレイヤー取得、バトル解析、レポート生成を全部やる
    }
}
```

**コントローラーの責務**:
- リクエスト受付
- バリデーション呼び出し
- サービス層呼び出し
- レスポンス返却

**コントローラーに書いてはいけないこと**:
- ビジネスロジック → Serviceへ
- データベースロジック → Repositoryへ
- 複雑な計算処理 → Serviceへ

### 2. モデル

```php
// ✅ Good - Eloquentの規約に従う
class Player extends Model
{
    protected $fillable = [
        'tag',
        'name',
        'trophies',
        'level',
    ];

    protected $casts = [
        'trophies' => 'integer',
        'level' => 'integer',
        'last_fetched_at' => 'datetime',
    ];

    // リレーション
    public function battles()
    {
        return $this->hasMany(Battle::class);
    }

    // スコープ
    public function scopeActive($query)
    {
        return $query->where('last_fetched_at', '>=', now()->subDays(7));
    }

    // アクセサ
    public function getFormattedTrophiesAttribute(): string
    {
        return number_format($this->trophies);
    }
}

// ❌ Bad - ビジネスロジックをモデルに詰め込みすぎ
class Player extends Model
{
    public function calculateComplexStatistics()
    {
        // 複雑な統計計算 (Serviceに移すべき)
    }

    public function fetchFromApi()
    {
        // API通信 (Serviceに移すべき)
    }
}
```

**モデルの責務**:
- データベーステーブルとの対応
- リレーション定義
- 簡単なアクセサ/ミューテータ
- スコープ定義

### 3. サービス層

```php
// ✅ Good - ビジネスロジックを分離
namespace App\Services;

class BattleAnalysisService
{
    public function __construct(
        private ClashRoyaleApiService $apiService,
        private BattleRepository $battleRepository
    ) {}

    /**
     * プレイヤーのバトルログを解析
     */
    public function analyzePlayerBattles(string $playerTag): array
    {
        // 1. APIからデータ取得
        $battles = $this->apiService->fetchBattleLog($playerTag);

        // 2. データ保存
        $this->battleRepository->storeBattles($battles);

        // 3. 統計計算
        $stats = $this->calculateStatistics($battles);

        return $stats;
    }

    private function calculateStatistics(array $battles): array
    {
        // 統計計算のロジック
    }
}
```

**サービスの責務**:
- ビジネスロジック
- 複数のモデル/リポジトリの調整
- 外部API通信
- 複雑な計算処理

### 4. リポジトリ

```php
// ✅ Good - データアクセスを抽象化
namespace App\Repositories;

class BattleRepository
{
    public function storeBattles(array $battles): void
    {
        foreach ($battles as $battleData) {
            Battle::updateOrCreate(
                ['battle_time' => $battleData['battleTime']],
                $battleData
            );
        }
    }

    public function getRecentBattles(string $playerTag, int $days = 7)
    {
        return Battle::where('player_tag', $playerTag)
            ->where('battle_time', '>=', now()->subDays($days))
            ->orderBy('battle_time', 'desc')
            ->get();
    }
}
```

### 5. ルート定義

```php
// routes/web.php

// ✅ Good - リソースルート、グループ化、名前付き
Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('players')->name('players.')->group(function () {
        Route::get('/', [PlayerController::class, 'index'])->name('index');
        Route::get('/search', [PlayerController::class, 'search'])->name('search');
        Route::get('/{player}', [PlayerController::class, 'show'])->name('show');
        Route::post('/', [PlayerController::class, 'store'])->name('store');
    });
});

// ❌ Bad - バラバラで一貫性がない
Route::get('/get-dashboard', 'DashboardController@index');
Route::post('/player-store', 'PlayerController@storePlayer');
```

### 6. マイグレーション

```php
// ✅ Good - 明確な命名、外部キー制約
public function up()
{
    Schema::create('battles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('player_id')->constrained()->onDelete('cascade');
        $table->string('battle_time');
        $table->string('type');
        $table->boolean('is_win');
        $table->integer('trophy_change')->nullable();
        $table->json('deck');
        $table->json('opponent_deck');
        $table->timestamps();

        $table->index('battle_time');
        $table->index('player_id');
    });
}

// ❌ Bad - 型が不適切、インデックスなし
public function up()
{
    Schema::create('battles', function (Blueprint $table) {
        $table->id();
        $table->text('data'); // すべてJSONで保存
    });
}
```

### 7. Bladeテンプレート

```blade
{{-- ✅ Good - コンポーネント、ディレクティブ活用 --}}
@extends('layouts.app')

@section('title', __('messages.players.title'))

@section('content')
<div class="container">
    <h1>{{ __('messages.players.list') }}</h1>

    @forelse($players as $player)
        <x-player-card :player="$player" />
    @empty
        <p>{{ __('messages.players.no_data') }}</p>
    @endforelse

    {{ $players->links() }}
</div>
@endsection

{{-- ❌ Bad - 生PHPコード、ロジックが多い --}}
<div>
    <?php
    $count = 0;
    foreach ($players as $player) {
        $count++;
        // 複雑な計算
    }
    ?>
    Players: <?php echo $count; ?>
</div>
```

## 🧪 テスト

### 1. テストの命名

```php
// ✅ Good - テストの意図が明確
class BattleAnalysisServiceTest extends TestCase
{
    /** @test */
    public function it_calculates_correct_win_rate()
    {
        // Arrange
        $battles = $this->createBattles(['wins' => 7, 'losses' => 3]);

        // Act
        $winRate = $this->service->calculateWinRate($battles);

        // Assert
        $this->assertEquals(0.7, $winRate);
    }

    /** @test */
    public function it_throws_exception_when_no_battles_found()
    {
        $this->expectException(NoBattlesException::class);
        $this->service->calculateWinRate([]);
    }
}
```

### 2. テストの構造 (AAA パターン)

```php
/** @test */
public function it_stores_player_successfully()
{
    // Arrange (準備)
    $playerData = [
        'tag' => '#2PP',
        'name' => 'TestPlayer',
    ];

    // Act (実行)
    $player = $this->playerRepository->store($playerData);

    // Assert (検証)
    $this->assertDatabaseHas('players', ['tag' => '#2PP']);
    $this->assertEquals('TestPlayer', $player->name);
}
```

## 🌐 多言語化

```php
// ✅ Good - 翻訳キーを使用
echo __('messages.welcome', ['name' => $userName]);
echo __('reports.win_rate');

// ❌ Bad - ハードコードされた文字列
echo "Welcome, {$userName}!";
echo "Win Rate";
```

## 💬 コメント

```php
// ✅ Good - 必要な場所に適切なコメント
// Clash Royale APIは1時間に10回までの制限があるためキャッシュを使用
$battles = Cache::remember("battles:{$playerTag}", 3600, function () use ($playerTag) {
    return $this->apiService->fetchBattleLog($playerTag);
});

// ✅ Good - 複雑なロジックの説明
// トロフィー変動を計算: 最新25試合の平均を算出し、
// 前回の平均との差分をトレンドとして返す
$trophyTrend = $this->calculateTrophyTrend($battles);

// ❌ Bad - 自明なことをコメント
// $iを1増やす
$i++;

// プレイヤーを取得
$player = Player::find($id);
```

## 🔒 セキュリティ

```php
// ✅ Good - バリデーション、エスケープ
$validated = $request->validate([
    'player_tag' => 'required|regex:/^#[0-9A-Z]+$/',
]);

// Blade自動エスケープ
{{ $player->name }}

// ✅ Good - SQLインジェクション対策 (Eloquent使用)
$players = Player::where('trophies', '>', $minTrophies)->get();

// ❌ Bad - 生クエリ、エスケープなし
$players = DB::select("SELECT * FROM players WHERE trophies > {$minTrophies}");
{!! $userInput !!}
```

## 📋 チェックリスト

コードをコミット前に確認:

- [ ] PSR-12に準拠している
- [ ] 命名規則を守っている
- [ ] 適切に型宣言を使用している
- [ ] コメントが適切に書かれている
- [ ] テストが書かれている
- [ ] セキュリティ対策ができている
- [ ] 多言語化対応している
- [ ] ハードコードされた値がない

## 🛠 推奨ツール

- **PHP CS Fixer**: コードスタイルの自動修正
- **PHPStan / Larastan**: 静的解析
- **PHP_CodeSniffer**: コーディング規約チェック

```bash
# PHP CS Fixer実行例
./vendor/bin/php-cs-fixer fix app/

# PHPStan実行例
./vendor/bin/phpstan analyse app/
```

---

**最終更新**: 2026-01-06
