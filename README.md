# Clash Royale Analytics Platform

Clash Royaleのプレイ動画をアップロードし、Google AIで解析して詳細なレポートを提供するWebアプリケーション

## 🎯 プロジェクト概要

このプラットフォームは、Google AI技術を活用してClash Royaleのプレイ動画を解析し、エリクサー管理、デッキコスト、攻撃タイミング、リスク分析などの詳細なレポートを生成します。

### 主な機能

1. **動画アップロード** - Clash Royaleのプレイ動画をアップロード
2. **AI動画解析** - Google AIで動画を自動解析
3. **解析結果の確認** - アップロードした動画と解析結果を確認

### 画面構成

このアプリケーションは、シンプルな3画面構成で設計されています：

1. **トップ画面** (`/`)
   - アップロード済みの動画一覧を表示
   - 動画アップロードへの導線

2. **動画アップロード画面** (`/upload`)
   - 動画ファイルのアップロード
   - アップロード後、自動的にGoogle AI解析が開始

3. **動画詳細・解析結果確認画面** (`/videos/[id]`)
   - アップロードした動画の再生
   - 解析結果の詳細表示
   - エリクサー分析、コスト分析、タイミング分析、リスク分析

## 🛠 技術スタック

### バックエンド
- **PHP**: 8.2.30
- **Laravel**: 12.47.0 (REST API)
- **Webサーバー**: Nginx (Alpine)
- **PHP-FPM**: 8.2-fpm
- **Composer**: PHP依存関係管理

### フロントエンド
- **フレームワーク**: React 18.3.1 + Next.js 14.2.0
- **言語**: TypeScript
- **CSSフレームワーク**: Tailwind CSS 3.4.6
- **HTTPクライアント**: Axios 1.7.7
- **チャートライブラリ**: Recharts 2.12.7
- **開発環境**: Next.js Dev Server (Hot Reload対応)

### データベース・キャッシュ
- **データベース**: MySQL 8.0
- **キャッシュ/セッション**: Redis (Alpine)
- **DB管理ツール**: phpMyAdmin

### コンテナ・インフラ
- **コンテナ化**: Docker / Docker Compose
- **ネットワーク**: Docker Network
- **ストレージ**: Docker Volumes

### 外部API
- **動画解析**: Google Video Intelligence API
- **AI解析**: Google Gemini API
- **認証**: Google Cloud Platform (Service Account)

### 開発ツール
- **Node.js**: 18.20.8 (Docker内)
- **npm**: パッケージ管理
- **テスト**: PHPUnit 11.5.3
- **コード品質**: Laravel Pint 1.24

### ストレージ
- **動画ファイル**: ローカルストレージ (storage/app/public/videos)
- **将来的に**: AWS S3 / Google Cloud Storage対応予定

## 📋 必要なドキュメント

プロジェクトを進める前に、以下のドキュメントを確認してください:

1. [PROJECT_OVERVIEW.md](./docs/PROJECT_OVERVIEW.md) - 詳細な機能要件
2. [PROJECT_STRUCTURE.md](./docs/PROJECT_STRUCTURE.md) - ディレクトリ構成
3. [CODING_STANDARDS.md](./docs/CODING_STANDARDS.md) - コーディング規約
4. [API_DESIGN.md](./docs/API_DESIGN.md) - API設計
5. [DATABASE_SCHEMA.md](./docs/DATABASE_SCHEMA.md) - データベース設計
6. [DIAGRAMS.md](./docs/DIAGRAMS.md) - システム図・フロー図
7. [SETUP_GUIDE.md](./docs/SETUP_GUIDE.md) - 環境構築手順
8. [DEPLOYMENT_GUIDE.md](./docs/DEPLOYMENT_GUIDE.md) - デプロイ手順

## 🚀 クイックスタート

### 前提条件

- Docker Desktop がインストール済み
- Google Cloud Platform アカウントとAPIキーを取得済み
- Google Video Intelligence API / Gemini API が有効化済み
- Git がインストール済み

### 初回セットアップ

```bash
# リポジトリのクローン
git clone <your-repo-url>
cd clash-royale-analytics

# 環境変数ファイルのコピー
cp .env.example .env

# .envファイルを編集してGoogle AI APIキーを設定
# GOOGLE_CLOUD_PROJECT_ID=your_project_id
# GOOGLE_AI_API_KEY=your_api_key_here

# Dockerコンテナの起動
docker-compose up -d --build

# Laravelの依存パッケージインストール
docker-compose exec app composer install

# アプリケーションキーの生成
docker-compose exec app php artisan key:generate

# ストレージへのシンボリックリンク作成
docker-compose exec app php artisan storage:link

# データベースマイグレーション
docker-compose exec app php artisan migrate
```

### アクセス

- **フロントエンド (Next.js)**: http://localhost:3000
- **API (Laravel)**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

## 📚 開発の進め方

1. **環境構築** - [SETUP_GUIDE.md](./docs/SETUP_GUIDE.md)を参照
2. **コーディング規約の確認** - [CODING_STANDARDS.md](./docs/CODING_STANDARDS.md)を参照
3. **DB設計の確認** - [DATABASE_SCHEMA.md](./docs/DATABASE_SCHEMA.md)を参照
4. **機能実装** - 各ドキュメントを参照しながら実装

## 🔑 APIキーの取得方法

### Google AI API キー

1. Google Cloud Platform にアクセス
2. プロジェクトを作成
3. Video Intelligence API と Gemini API を有効化
4. サービスアカウントを作成してJSONキーをダウンロード
5. `.env`に設定（詳細は [SETUP_GUIDE.md](./docs/SETUP_GUIDE.md) を参照）

## 🔄 アプリケーションの動作フロー

1. **動画アップロード**
   - ユーザーがトップ画面またはアップロード画面から動画をアップロード
   - 動画ファイルはサーバーに保存

2. **自動解析開始**
   - アップロード完了後、自動的にGoogle AI解析が開始
   - バックグラウンドで解析処理が実行

3. **解析結果の確認**
   - 動画詳細画面で動画を再生
   - 解析結果（エリクサー、コスト、タイミング、リスク）を確認

## 🎓 目標

このプロジェクトを通じて以下を習得:

- Laravel REST APIの実装
- Next.js + React + TypeScriptでのフロントエンド開発
- Google AI APIとの連携
- Docker環境での開発フロー
- データベース設計とマイグレーション

**作成日**: 2026-01-06
**最終更新**: 2026-01-17
