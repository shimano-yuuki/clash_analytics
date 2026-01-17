# セットアップ実行手順

Dockerが起動している状態で、以下の手順を実行してください。

### 1. Dockerコンテナの起動

```bash
docker-compose up -d --build
```

### 2. 環境変数の設定

```bash
# .envファイルをコピー
cp .env.example .env

# .envファイルを編集してGoogle AI APIキーを設定
# エディタで .env を開いて以下を設定:
# - GOOGLE_CLOUD_PROJECT_ID
# - GOOGLE_AI_API_KEY
```

### 3. Laravel初期設定

```bash
# Composerの依存関係をインストール
docker-compose exec app composer install

# アプリケーションキーの生成
docker-compose exec app php artisan key:generate

# ストレージへのシンボリックリンク作成
docker-compose exec app php artisan storage:link

# データベースマイグレーション
docker-compose exec app php artisan migrate
```

### 4. Google Cloud認証情報の配置

```bash
# Google Cloud PlatformからダウンロードしたJSONキーファイルを配置
# storage/app/google-cloud-key.json に配置してください
```

### 5. 動作確認

ブラウザで以下にアクセス:
- **アプリケーション**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

## トラブルシューティング

### Dockerコンテナが起動しない場合

```bash
# コンテナの状態を確認
docker-compose ps

# ログを確認
docker-compose logs

# コンテナを再起動
docker-compose restart
```

### データベース接続エラーの場合

```bash
# MySQLコンテナが起動しているか確認
docker-compose ps mysql

# .envファイルのDB設定を確認
# DB_HOST=mysql になっているか確認
```

---

**最終更新**: 2026-01-06
