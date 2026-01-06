# Clash Royale Analytics Platform

クラッシュ・ロワイヤルのプレイヤーデータを解析し、詳細なレポートを提供するWebアプリケーション

## 🎯 プロジェクト概要

このプラットフォームは、Clash Royale APIを活用して以下の機能を提供します:

1. **ユーザー設定機能** - プレイヤータグでユーザーを検索・登録
2. **試合データ解析** - バトルログから詳細な統計とパターンを分析
3. **レポート生成** - 分析結果をデータベースに保存し、可視化

## 🌍 多言語対応

- 日本語
- 英語
- その他主要言語(拡張予定)

## 🛠 技術スタック

- **バックエンド**: PHP 8.2+ / Laravel 10.x
- **フロントエンド**: Blade Templates / Vue.js (検討中)
- **データベース**: MySQL 8.0
- **コンテナ**: Docker / Docker Compose
- **外部API**: Clash Royale API
- **開発ツール**: Cursor IDE

## 📋 必要なドキュメント

プロジェクトを進める前に、以下のドキュメントを確認してください:

1. [PROJECT_OVERVIEW.md](./docs/PROJECT_OVERVIEW.md) - 詳細な機能要件
2. [PROJECT_STRUCTURE.md](./docs/PROJECT_STRUCTURE.md) - ディレクトリ構成
3. [CODING_STANDARDS.md](./docs/CODING_STANDARDS.md) - コーディング規約
4. [API_DESIGN.md](./docs/API_DESIGN.md) - API設計
5. [DATABASE_SCHEMA.md](./docs/DATABASE_SCHEMA.md) - データベース設計
6. [SETUP_GUIDE.md](./docs/SETUP_GUIDE.md) - 環境構築手順
7. [DEPLOYMENT_GUIDE.md](./docs/DEPLOYMENT_GUIDE.md) - デプロイ手順
8. [LOCALIZATION_GUIDE.md](./docs/LOCALIZATION_GUIDE.md) - 多言語化ガイド

## 🚀 クイックスタート

### 前提条件

- Docker Desktop がインストール済み
- Clash Royale API トークンを取得済み ([https://developer.clashroyale.com](https://developer.clashroyale.com))
- Git がインストール済み

### 初回セットアップ

```bash
# リポジトリのクローン
git clone <your-repo-url>
cd clash-royale-analytics

# 環境変数ファイルのコピー
cp .env.example .env

# .envファイルを編集してClash Royale APIトークンを設定
# CLASH_ROYALE_API_TOKEN=your_token_here

# Dockerコンテナの起動
docker-compose up -d

# Laravelの依存パッケージインストール
docker-compose exec app composer install

# アプリケーションキーの生成
docker-compose exec app php artisan key:generate

# データベースマイグレーション
docker-compose exec app php artisan migrate

# 多言語ファイルの準備
docker-compose exec app php artisan lang:publish
```

### アクセス

- アプリケーション: http://localhost:8000
- phpMyAdmin: http://localhost:8080

## 📚 開発の進め方

1. **環境構築** - [SETUP_GUIDE.md](./docs/SETUP_GUIDE.md)を参照
2. **コーディング規約の確認** - [CODING_STANDARDS.md](./docs/CODING_STANDARDS.md)
3. **DB設計の確認** - [DATABASE_SCHEMA.md](./docs/DATABASE_SCHEMA.md)
4. **機能実装** - 各ドキュメントを参照しながら実装

## 🎓 学習目標

このプロジェクトを通じて以下を習得:

- Laravelの基本構造(MVC、ルーティング、Eloquent)
- 外部API連携の実装方法
- Docker環境での開発フロー
- データベース設計とマイグレーション
- 多言語化(i18n)の実装
- AIペアプログラミング(Cursor)の活用

## 📝 ライセンス

MIT License

## 🤝 コントリビューション

プルリクエストを歓迎します!

---

**作成日**: 2026-01-06
