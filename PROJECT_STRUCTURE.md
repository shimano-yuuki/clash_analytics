# プロジェクト構造

## 📁 ディレクトリ構成

```
clash-royale-analytics/
├── .docker/                        # Docker設定ファイル
│   ├── nginx/
│   │   └── default.conf           # Nginx設定
│   ├── php/
│   │   └── Dockerfile             # PHPコンテナ設定
│   └── mysql/
│       └── my.cnf                 # MySQL設定
│
├── docs/                          # プロジェクトドキュメント
│   ├── PROJECT_OVERVIEW.md
│   ├── PROJECT_STRUCTURE.md
│   ├── CODING_STANDARDS.md
│   ├── API_DESIGN.md
│   ├── DATABASE_SCHEMA.md
│   ├── SETUP_GUIDE.md
│   ├── DEPLOYMENT_GUIDE.md
│   └── LOCALIZATION_GUIDE.md
│
├── app/                           # Laravelアプリケーションコア
│   ├── Console/
│   │   ├── Commands/              # Artisanコマンド
│   │   │   ├── AnalyzeVideo.php          # 動画解析コマンド
│   │   │   ├── GenerateReport.php        # レポート生成コマンド
│   │   │   └── CleanupOldVideos.php      # 古い動画ファイル削除
│   │   └── Kernel.php
│   │
│   ├── Exceptions/
│   │   ├── VideoAnalysisException.php    # 動画解析例外
│   │   ├── GoogleAiApiException.php      # Google AI API例外
│   │   └── Handler.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                      # APIコントローラー
│   │   │   │   ├── VideoController.php
│   │   │   │   ├── VideoAnalysisController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Web/                      # Webコントローラー
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── VideoController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── LanguageController.php
│   │   │   └── Controller.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── SetLocale.php            # 言語設定ミドルウェア
│   │   │   └── ValidateVideoFile.php
│   │   │
│   │   ├── Requests/                    # フォームリクエスト
│   │   │   ├── UploadVideoRequest.php
│   │   │   └── GenerateReportRequest.php
│   │   │
│   │   └── Resources/                   # APIリソース
│   │       ├── VideoResource.php
│   │       ├── VideoAnalysisResource.php
│   │       └── ReportResource.php
│   │
│   ├── Models/                          # Eloquentモデル
│   │   ├── Video.php                    # 動画
│   │   ├── VideoAnalysis.php            # 動画解析結果
│   │   └── Report.php                   # レポート
│   │
│   ├── Repositories/                    # リポジトリパターン
│   │   ├── VideoRepository.php
│   │   ├── VideoAnalysisRepository.php
│   │   └── ReportRepository.php
│   │
│   ├── Services/                        # ビジネスロジック
│   │   ├── GoogleAiApiService.php       # Google AI API通信サービス
│   │   ├── VideoAnalysisService.php     # 動画解析サービス
│   │   ├── VideoStorageService.php      # 動画ストレージサービス
│   │   ├── ReportGenerationService.php  # レポート生成サービス
│   │   ├── ElixirAnalysisService.php    # エリクサー分析サービス
│   │   ├── TimingAnalysisService.php    # タイミング分析サービス
│   │   ├── RiskAnalysisService.php      # リスク分析サービス
│   │   └── CacheService.php             # キャッシュサービス
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── VideoAnalysisServiceProvider.php
│   │
│   └── View/
│       └── Components/                  # Bladeコンポーネント
│           ├── StatCard.php
│           ├── WinRateChart.php
│           └── DeckCard.php
│
├── bootstrap/
│   ├── app.php
│   └── cache/
│
├── config/                              # 設定ファイル
│   ├── app.php
│   ├── database.php
│   ├── cache.php
│   ├── queue.php
│   └── clashroyale.php                  # Clash Royale API設定
│
├── database/
│   ├── factories/                       # モデルファクトリー
│   │   ├── PlayerFactory.php
│   │   └── BattleFactory.php
│   │
│   ├── migrations/                      # マイグレーション
│   │   ├── 2024_01_01_000001_create_players_table.php
│   │   ├── 2024_01_01_000002_create_battles_table.php
│   │   ├── 2024_01_01_000003_create_decks_table.php
│   │   ├── 2024_01_01_000004_create_cards_table.php
│   │   ├── 2024_01_01_000005_create_reports_table.php
│   │   └── 2024_01_01_000006_create_player_statistics_table.php
│   │
│   └── seeders/                         # シーダー
│       ├── DatabaseSeeder.php
│       └── CardSeeder.php               # カード情報の初期データ
│
├── public/                              # 公開ディレクトリ
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── images/
│
├── resources/
│   ├── css/
│   │   └── app.css
│   │
│   ├── js/
│   │   ├── app.js
│   │   └── components/
│   │       ├── ChartComponent.js
│   │       └── PlayerSearch.js
│   │
│   ├── lang/                            # 多言語ファイル
│   │   ├── ja/
│   │   │   ├── messages.php
│   │   │   ├── validation.php
│   │   │   └── reports.php
│   │   ├── en/
│   │   │   ├── messages.php
│   │   │   ├── validation.php
│   │   │   └── reports.php
│   │   └── es/
│   │       └── (同様の構成)
│   │
│   └── views/                           # Bladeテンプレート
│       ├── layouts/
│       │   ├── app.blade.php            # メインレイアウト
│       │   └── guest.blade.php          # ゲストレイアウト
│       │
│       ├── components/                  # Bladeコンポーネント
│       │   ├── stat-card.blade.php
│       │   ├── win-rate-chart.blade.php
│       │   └── deck-card.blade.php
│       │
│       ├── dashboard/
│       │   └── index.blade.php          # ダッシュボード
│       │
│       ├── videos/
│       │   ├── index.blade.php          # 動画一覧
│       │   ├── show.blade.php           # 動画詳細
│       │   ├── upload.blade.php         # 動画アップロード
│       │   └── player.blade.php         # 動画プレーヤー
│       │
│       ├── reports/
│       │   ├── index.blade.php          # レポート一覧
│       │   ├── show.blade.php           # レポート詳細
│       │   └── partials/
│       │       ├── elixir-analysis.blade.php
│       │       ├── cost-analysis.blade.php
│       │       ├── timing-analysis.blade.php
│       │       ├── risk-analysis.blade.php
│       │       └── timeline.blade.php
│       │
│       └── errors/
│           ├── 404.blade.php
│           └── 500.blade.php
│
├── routes/
│   ├── web.php                          # Webルート
│   ├── api.php                          # APIルート
│   └── console.php                      # Consoleルート
│
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   └── videos/                  # アップロードされた動画ファイル
│   │   └── reports/                     # 生成されたレポート
│   ├── framework/
│   ├── logs/
│   └── cache/
│
├── tests/
│   ├── Feature/                         # 機能テスト
│   │   ├── Api/
│   │   │   ├── PlayerApiTest.php
│   │   │   └── BattleApiTest.php
│   │   └── Web/
│   │       ├── DashboardTest.php
│   │       └── PlayerTest.php
│   │
│   ├── Unit/                            # 単体テスト
│   │   ├── Services/
│   │   │   ├── BattleAnalysisServiceTest.php
│   │   │   └── StatisticsServiceTest.php
│   │   └── Models/
│   │       ├── PlayerTest.php
│   │       └── BattleTest.php
│   │
│   └── TestCase.php
│
├── .env                                 # 環境変数(gitignore)
├── .env.example                         # 環境変数テンプレート
├── .gitignore
├── composer.json                        # PHP依存関係
├── composer.lock
├── package.json                         # JS依存関係
├── package-lock.json
├── docker-compose.yml                   # Docker Compose設定
├── Dockerfile                           # メインDockerfile
├── artisan                              # Artisan CLI
├── phpunit.xml                          # PHPUnit設定
└── README.md
```

## 🎯 主要ディレクトリの役割

### `/app`
アプリケーションのコアロジックを格納。MVCパターンの中心。

### `/app/Services`
ビジネスロジックを分離して配置。Controller から呼び出される。
- Google AI API通信
- 動画解析処理
- エリクサー/コスト/タイミング/リスク分析
- レポート生成

### `/app/Repositories`
データアクセスロジックを抽象化。テスト可能性を向上。

### `/database/migrations`
データベーススキーマのバージョン管理。

### `/resources/lang`
多言語対応ファイル。言語ごとにサブディレクトリを作成。

### `/resources/views`
Bladeテンプレート。UI表示を担当。

### `/tests`
テストコード。Feature(機能テスト)とUnit(単体テスト)に分類。

## 📝 ファイル命名規則

### コントローラー
- 単数形 + Controller: `PlayerController.php`
- RESTful: `index`, `show`, `store`, `update`, `destroy`

### モデル
- 単数形、パスカルケース: `Player.php`, `Battle.php`

### マイグレーション
- スネークケース: `create_players_table.php`
- 日付プレフィックス: `2024_01_01_000001_`

### サービス
- 役割 + Service: `BattleAnalysisService.php`

### ビュー
- ケバブケース: `player-detail.blade.php`

## 🔄 データフロー

```
User Request (動画アップロード)
    ↓
Routes (web.php / api.php)
    ↓
Controller
    ↓
Video Storage Service (動画保存)
    ↓
Video Analysis Service ←→ Google AI API
    ↓
Analysis Services (エリクサー/コスト/タイミング/リスク)
    ↓
Report Generation Service
    ↓
Repository
    ↓
Model (Eloquent)
    ↓
Database
    ↓
Response → View (Blade) / JSON (API)
```

## 🚀 開発時のディレクトリ作成順序

1. `docs/` - ドキュメント作成
2. `.docker/` - Docker環境構築
3. `database/migrations/` - DB設計
4. `app/Models/` - モデル作成
5. `app/Services/` - サービス層実装 (Google AI API連携)
6. `app/Http/Controllers/` - コントローラー実装
7. `resources/views/` - ビュー作成 (動画プレーヤー含む)
8. `resources/lang/` - 多言語ファイル作成
9. `tests/` - テスト作成

---

**最終更新**: 2026-01-06
