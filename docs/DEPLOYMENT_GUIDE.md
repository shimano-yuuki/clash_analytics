# デプロイガイド

## 🎯 デプロイ先の選択

無料プランで利用できる主なプラットフォーム:

| プラットフォーム | 無料枠 | Docker対応 | DB込み | 推奨度 |
|-----------------|--------|-----------|--------|--------|
| **Railway** | $5/月クレジット | ✅ | ✅ MySQL | ⭐⭐⭐⭐⭐ |
| **Render** | 750時間/月 | ✅ | ✅ PostgreSQL | ⭐⭐⭐⭐ |
| **Fly.io** | 3台のVM | ✅ | 別途必要 | ⭐⭐⭐ |
| **Heroku** | 550時間/月 | ❌ | 別途必要 | ⭐⭐ |

**推奨**: Railway (簡単、MySQL対応、Dockerサポート)

---

## 🚂 Railway へのデプロイ

### 前提条件

- Railwayアカウント (https://railway.app)
- GitHubアカウント
- プロジェクトがGitHubにプッシュ済み

### Step 1: プロジェクト準備

#### Dockerfileの作成 (本番用)

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# 作業ディレクトリ
WORKDIR /var/www/html

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nginx

# PHP拡張のインストール
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js のインストール (フロントエンドビルド用)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# アプリケーションファイルのコピー
COPY . /var/www/html

# 依存関係のインストール
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 権限設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx設定
COPY .railway/nginx.conf /etc/nginx/sites-available/default

# 起動スクリプト
COPY .railway/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
```

#### Nginx設定

```bash
mkdir -p .railway
cat > .railway/nginx.conf << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
```

#### 起動スクリプト

```bash
cat > .railway/start.sh << 'EOF'
#!/bin/bash

# PHP-FPMを起動
php-fpm -D

# Laravelの初期化
php artisan config:cache
php artisan route:cache
php artisan view:cache

# マイグレーション実行
php artisan migrate --force

# Nginxを起動
nginx -g "daemon off;"
EOF
```

#### railway.jsonの作成

```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "DOCKERFILE",
    "dockerfilePath": "Dockerfile"
  },
  "deploy": {
    "startCommand": "/start.sh",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

### Step 2: Railwayでのセットアップ

1. **プロジェクト作成**
   - Railway ダッシュボードにログイン
   - 「New Project」→ 「Deploy from GitHub repo」
   - リポジトリを選択

2. **MySQL追加**
   - プロジェクト画面で「+ New」
   - 「Database」→ 「Add MySQL」
   - 自動で接続情報が設定される

3. **環境変数の設定**
   
   Railwayが自動設定する変数:
   - `DATABASE_URL`
   - `MYSQL_URL`
   - `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

   手動で追加する変数:
   ```env
   APP_NAME="Clash Royale Analytics"
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=  # php artisan key:generate で生成
   APP_URL=https://your-app.railway.app
   
   # Railway MySQL接続 (自動設定を使用)
   DB_CONNECTION=mysql
   DB_HOST=${MYSQLHOST}
   DB_PORT=${MYSQLPORT}
   DB_DATABASE=${MYSQLDATABASE}
   DB_USERNAME=${MYSQLUSER}
   DB_PASSWORD=${MYSQLPASSWORD}
   
   # Google AI API
   GOOGLE_CLOUD_PROJECT_ID=your_project_id
   GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/app/google-cloud-key.json
   GOOGLE_AI_API_KEY=your_production_api_key
   
   # キャッシュ (fileドライバーを使用)
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=database
   ```

4. **デプロイ**
   - Settingsで「Deploy Trigger」を有効化
   - GitHubにプッシュすると自動デプロイ

5. **ドメイン設定**
   - Settingsタブ → Domains
   - Railway提供のドメインが自動生成
   - カスタムドメイン追加も可能

---

## 🎨 Render へのデプロイ

### Step 1: render.yamlの作成

```yaml
# render.yaml
services:
  - type: web
    name: clash-royale-analytics
    env: docker
    plan: free
    dockerfilePath: ./Dockerfile
    envVars:
      - key: APP_NAME
        value: Clash Royale Analytics
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: APP_KEY
        generateValue: true
      - key: DATABASE_URL
        fromDatabase:
          name: clash-royale-db
          property: connectionString
      - key: GOOGLE_CLOUD_PROJECT_ID
        value: your_project_id
      - key: GOOGLE_AI_API_KEY
        sync: false  # 手動設定

databases:
  - name: clash-royale-db
    plan: free
    databaseName: clash_royale_analytics
    user: cr_user
```

### Step 2: Renderでの設定

1. https://render.com/ にログイン
2. 「New」→ 「Blueprint」
3. GitHubリポジトリを接続
4. `render.yaml` が自動検出される
5. 環境変数を手動で設定
6. 「Create New Resources」

**注意**: Renderの無料プランは15分間アクセスがないとスリープします。

---

## ✈️ Fly.io へのデプロイ

### Step 1: Fly CLIのインストール

```bash
# Mac/Linux
curl -L https://fly.io/install.sh | sh

# Windows (PowerShell)
iwr https://fly.io/install.ps1 -useb | iex
```

### Step 2: 認証とプロジェクト作成

```bash
# ログイン
fly auth login

# アプリケーション作成
fly launch

# 表示される質問に回答
# - App name: clash-royale-analytics
# - Region: Tokyo (nrt)
# - Database: PostgreSQL (無料) or MySQL (有料)
```

### Step 3: fly.tomlの編集

```toml
# fly.toml
app = "clash-royale-analytics"
primary_region = "nrt"

[build]
  dockerfile = "Dockerfile"

[env]
  APP_ENV = "production"
  LOG_CHANNEL = "stderr"
  LOG_LEVEL = "info"
  DB_CONNECTION = "mysql"

[http_service]
  internal_port = 80
  force_https = true
  auto_stop_machines = true
  auto_start_machines = true
  min_machines_running = 0

[[vm]]
  cpu_kind = "shared"
  cpus = 1
  memory_mb = 256
```

### Step 4: Secretsの設定

```bash
# APP_KEY生成
php artisan key:generate --show

# Secrets登録
fly secrets set APP_KEY=base64:xxxxx
fly secrets set GOOGLE_CLOUD_PROJECT_ID=your_project_id
fly secrets set GOOGLE_AI_API_KEY=your_api_key
fly secrets set DB_PASSWORD=your_db_password
```

### Step 5: デプロイ

```bash
# デプロイ実行
fly deploy

# 状態確認
fly status

# ログ確認
fly logs
```

---

## 🔐 本番環境のセキュリティ設定

### 1. APP_KEYの生成

```bash
php artisan key:generate --show
# base64:ランダムな文字列が生成される
```

### 2. HTTPS強制

**app/Providers/AppServiceProvider.php**:
```php
use Illuminate\Support\Facades\URL;

public function boot()
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### 3. CORS設定

```bash
php artisan config:publish cors
```

**config/cors.php**:
```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://your-frontend-domain.com'
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### 4. レート制限

**routes/api.php**:
```php
Route::middleware(['throttle:60,1'])->group(function () {
    // 1分間に60リクエストまで
});
```

---

## 🔄 CI/CD パイプライン

### GitHub Actions

**.github/workflows/deploy.yml**:
```yaml
name: Deploy to Railway

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Deploy to Railway
        run: |
          # Railwayへのデプロイコマンド
          echo "Deployment triggered"
```

---

## 📊 モニタリング

### ログ確認

#### Railway
```bash
railway logs
```

#### Render
ダッシュボード → Logs

#### Fly.io
```bash
fly logs
```

### エラー追跡

**Sentry統合** (推奨):
```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=https://your-dsn@sentry.io/project-id
```

---

## 🧪 デプロイ前チェックリスト

- [ ] `.env` の `APP_DEBUG=false` に設定
- [ ] `APP_KEY` が本番用に生成されている
- [ ] データベース接続情報が正しい
- [ ] Google AI APIキーが本番用に設定されている
- [ ] Google Cloud Project IDが正しく設定されている
- [ ] 認証情報ファイルが適切に配置されている
- [ ] 動画ファイル保存用のストレージ容量が十分
- [ ] キャッシュドライバーが適切に設定されている
- [ ] ストレージディレクトリの権限が正しい
- [ ] マイグレーションが最新状態
- [ ] フロントエンドがビルド済み (`npm run build`)
- [ ] HTTPS強制が有効
- [ ] CORS設定が適切
- [ ] レート制限が設定されている

---

## 🔧 デプロイ後の作業

### 1. データベースマイグレーション

```bash
# Railway
railway run php artisan migrate --force

# Render
render run php artisan migrate --force

# Fly.io
fly ssh console
php artisan migrate --force
```

### 2. ストレージ設定

```bash
# 動画ファイル保存用ディレクトリの作成
php artisan storage:link
# またはS3等の外部ストレージを使用する場合は設定を確認
```

### 3. キャッシュ最適化

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. スケジューラ設定

**Railwayの場合**:
- Cron Jobsサービスを追加
- コマンド: `php artisan schedule:run`
- スケジュール: `* * * * *`

**Render/Fly.ioの場合**:
- 外部cronサービス (cron-job.org) を使用
- エンドポイント: `https://your-app.com/api/cron`

---

## 📝 トラブルシューティング

### 問題: 500 Internal Server Error

```bash
# ログ確認
railway logs --tail=100

# ストレージ権限確認
chmod -R 775 storage bootstrap/cache
```

### 問題: データベース接続エラー

```bash
# 環境変数確認
railway variables

# .envを確認
php artisan config:clear
```

### 問題: メモリ不足

```bash
# Composer最適化
composer install --no-dev --optimize-autoloader

# キャッシュクリア
php artisan cache:clear
```

---

## 🚀 スケーリング

### Railway
- Settings → Resources → Increase Memory/CPU

### Render
- 有料プランにアップグレード

### Fly.io
```bash
fly scale vm shared-cpu-1x --memory 512
fly scale count 2  # インスタンス数を増やす
```

---

**最終更新**: 2026-01-06
