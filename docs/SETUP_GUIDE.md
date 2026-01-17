# 環境構築ガイド

## 🎯 前提条件

開発を始める前に、以下をインストールしてください:

### 必須
- **Docker Desktop**: https://www.docker.com/products/docker-desktop
  - Windows: WSL2推奨
  - Mac: Apple Silicon / Intel対応
  - Linux: Docker Engine + Docker Compose
- **Git**: https://git-scm.com/
- **テキストエディタ**: Cursor / VS Code / PHPStorm

### 推奨
- **Postman / Insomnia**: API テスト用
- **TablePlus / DBeaver**: データベース管理ツール

---

## 🚀 初回セットアップ

### Step 1: プロジェクトの作成

```bash
# プロジェクトディレクトリを作成
mkdir clash-royale-analytics
cd clash-royale-analytics

# Gitリポジトリの初期化
git init

# .gitignoreファイルの作成
cat > .gitignore << 'EOF'
/vendor
/node_modules
/.env
/.env.backup
/.phpunit.result.cache
/storage/*.key
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/testing/*
/storage/framework/views/*
/storage/logs/*
/public/hot
/public/storage
.DS_Store
Thumbs.db
EOF
```

### Step 2: Laravelプロジェクトの作成

```bash
# Docker経由でLaravelをインストール
docker run --rm \
    -v $(pwd):/app \
    composer create-project --prefer-dist laravel/laravel .

# または、Composerがローカルにある場合
composer create-project --prefer-dist laravel/laravel .
```

### Step 3: Dockerファイルの作成

#### docker-compose.yml
```bash
cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  # Nginx Webサーバー
  nginx:
    image: nginx:alpine
    container_name: cr_nginx
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html
      - ./.docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - cr_network

  # PHP-FPM アプリケーション
  app:
    build:
      context: .
      dockerfile: .docker/php/Dockerfile
    container_name: cr_app
    volumes:
      - ./:/var/www/html
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=clash_royale_analytics
      - DB_USERNAME=cr_user
      - DB_PASSWORD=cr_password
    depends_on:
      - mysql
    networks:
      - cr_network

  # MySQL データベース
  mysql:
    image: mysql:8.0
    container_name: cr_mysql
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: clash_royale_analytics
      MYSQL_USER: cr_user
      MYSQL_PASSWORD: cr_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./.docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf
    networks:
      - cr_network

  # phpMyAdmin (オプション)
  phpmyadmin:
    image: phpmyadmin:latest
    container_name: cr_phpmyadmin
    ports:
      - "8080:80"
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: cr_user
      PMA_PASSWORD: cr_password
    depends_on:
      - mysql
    networks:
      - cr_network

  # Redis (キャッシュ・セッション用、オプション)
  redis:
    image: redis:alpine
    container_name: cr_redis
    ports:
      - "6379:6379"
    networks:
      - cr_network

networks:
  cr_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
EOF
```

#### .docker/php/Dockerfile
```bash
mkdir -p .docker/php
cat > .docker/php/Dockerfile << 'EOF'
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
    libzip-dev

# PHP拡張のインストール
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ユーザー作成
RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www

# 権限設定
COPY --chown=www:www . /var/www/html

USER www

EXPOSE 9000

CMD ["php-fpm"]
EOF
```

#### .docker/nginx/default.conf
```bash
mkdir -p .docker/nginx
cat > .docker/nginx/default.conf << 'EOF'
server {
    listen 80;
    index index.php index.html;
    server_name localhost;
    root /var/www/html/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
```

#### .docker/mysql/my.cnf
```bash
mkdir -p .docker/mysql
cat > .docker/mysql/my.cnf << 'EOF'
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

[client]
default-character-set=utf8mb4
EOF
```

### Step 4: 環境変数の設定

```bash
# .env.exampleをコピー
cp .env.example .env

# .envファイルを編集
```

**.env の重要な設定**:
```env
APP_NAME="Clash Royale Analytics"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=clash_royale_analytics
DB_USERNAME=cr_user
DB_PASSWORD=cr_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Google AI API設定
GOOGLE_CLOUD_PROJECT_ID=your_project_id
GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/app/google-cloud-key.json
GOOGLE_AI_API_KEY=your_api_key_here
```

### Step 5: Dockerコンテナの起動

```bash
# コンテナをビルド・起動
docker-compose up -d --build

# コンテナの状態確認
docker-compose ps

# ログ確認
docker-compose logs -f app
```

### Step 6: Laravel初期設定

```bash
# Composerの依存関係をインストール
docker-compose exec app composer install

# アプリケーションキーの生成
docker-compose exec app php artisan key:generate

# ストレージへのシンボリックリンク作成
docker-compose exec app php artisan storage:link

# データベースマイグレーション
docker-compose exec app php artisan migrate

# マイグレーション実行のみ（シーダー不要）
```

### Step 7: 動作確認

ブラウザで以下にアクセス:
- **アプリケーション**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

Laravelのウェルカムページが表示されればOK!

---

## 🔑 Google AI API 設定

### Google Cloud Platform セットアップ

1. **Google Cloud Platform アカウント作成**
   - https://cloud.google.com/ にアクセス
   - アカウント登録・ログイン
   - 無料トライアル (300ドル分のクレジット) が利用可能

2. **プロジェクト作成**
   - Cloud Console にログイン
   - 「プロジェクトを作成」をクリック
   - プロジェクト名を入力 (例: `clash-royale-analytics`)
   - プロジェクトIDをメモ

3. **API 有効化**
   - 「APIとサービス」→ 「ライブラリ」を開く
   - 以下2つのAPIを有効化:
     - **Video Intelligence API**
     - **Gemini API** (Generative AI API)

4. **サービスアカウント作成**
   - 「IAMと管理」→ 「サービスアカウント」を開く
   - 「サービスアカウントを作成」をクリック
   - 名前と説明を入力
   - 「役割」で `Video Intelligence API User` と `AI Platform User` を付与

5. **認証情報の作成**
   - 作成したサービスアカウントをクリック
   - 「キー」タブ → 「キーを追加」→ 「JSONを作成」
   - ダウンロードされたJSONファイルを `storage/app/google-cloud-key.json` に保存

6. **環境変数の設定**
   - `.env` ファイルに以下を追加:
   ```env
   GOOGLE_CLOUD_PROJECT_ID=your_project_id
   GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/app/google-cloud-key.json
   GOOGLE_AI_API_KEY=your_api_key_here
   ```

**注意**: 
- JSONキーファイルは `.gitignore` に追加してください
- 本番環境では環境変数で認証情報を設定することを推奨します

---

## 📦 追加パッケージのインストール

### 開発に便利なパッケージ

```bash
# Laravel IDE Helper (コード補完)
docker-compose exec app composer require --dev barryvdh/laravel-ide-helper

# Laravel Debugbar (デバッグツール)
docker-compose exec app composer require --dev barryvdh/laravel-debugbar

# PHPUnit (テスト)
docker-compose exec app composer require --dev phpunit/phpunit

# PHP CS Fixer (コード整形)
docker-compose exec app composer require --dev friendsofphp/php-cs-fixer

# Larastan (静的解析)
docker-compose exec app composer require --dev nunomaduro/larastan
```

### フロントエンド

```bash
# Node.jsパッケージのインストール
docker-compose exec app npm install

# Vite起動
docker-compose exec app npm run dev

# または、本番ビルド
docker-compose exec app npm run build
```

---

## 🛠 よく使うコマンド

### Dockerコマンド

```bash
# コンテナ起動
docker-compose up -d

# コンテナ停止
docker-compose down

# コンテナ再起動
docker-compose restart

# ログ確認
docker-compose logs -f [service_name]

# コンテナに入る
docker-compose exec app bash
docker-compose exec mysql mysql -u cr_user -p
```

### Laravelコマンド

```bash
# Artisanコマンド一覧
docker-compose exec app php artisan list

# マイグレーション
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:fresh --seed

# キャッシュクリア
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# 新規コントローラー作成
docker-compose exec app php artisan make:controller PlayerController

# 新規モデル作成 (マイグレーション同時生成)
docker-compose exec app php artisan make:model Player -m

# 新規サービス作成
docker-compose exec app php artisan make:class Services/BattleAnalysisService

# テスト実行
docker-compose exec app php artisan test
```

---

## 🧪 テスト環境のセットアップ

### テスト用データベース設定

**.env.testing**:
```env
APP_ENV=testing
DB_DATABASE=clash_royale_analytics_test
```

**phpunit.xml**:
```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="clash_royale_analytics_test"/>
```

### テスト実行

```bash
# 全テスト実行
docker-compose exec app php artisan test

# 特定のテストファイル実行
docker-compose exec app php artisan test --filter=PlayerTest

# カバレッジレポート生成
docker-compose exec app php artisan test --coverage
```

---

## ⚙️ IDEの設定

### Cursor / VS Code

#### 推奨拡張機能
- PHP Intelephense
- Laravel Extension Pack
- Docker
- GitLens
- Prettier

#### settings.json
```json
{
  "editor.formatOnSave": true,
  "php.validate.executablePath": "/usr/bin/php",
  "intelephense.files.exclude": [
    "**/vendor/**",
    "**/node_modules/**"
  ]
}
```

### PHPStorm

1. **Interpreter設定**
   - Settings → PHP → CLI Interpreter
   - Docker Compose を選択
   - サービス: `app`

2. **Database接続**
   - Database → + → MySQL
   - Host: localhost, Port: 3306
   - User: cr_user, Password: cr_password

---

## 🐛 トラブルシューティング

### 問題: ポートが既に使用されている

```bash
# ポート使用状況確認
# Windows
netstat -ano | findstr :8000

# Mac/Linux
lsof -i :8000

# docker-compose.ymlのポート番号を変更
ports:
  - "8001:80"  # 8000 → 8001に変更
```

### 問題: 権限エラー

```bash
# storageとbootstrap/cacheに書き込み権限を付与
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www:www storage bootstrap/cache
```

### 問題: データベース接続エラー

```bash
# MySQLコンテナが起動しているか確認
docker-compose ps

# データベースが存在するか確認
docker-compose exec mysql mysql -u cr_user -pcr_password -e "SHOW DATABASES;"

# .envのDB設定を再確認
DB_HOST=mysql  # localhostではなくmysql
```

### 問題: Composerが遅い

```bash
# Composerの並列ダウンロードを有効化
docker-compose exec app composer config --global process-timeout 2000
docker-compose exec app composer config --global repos.packagist composer https://packagist.jp
```

---

## 📚 次のステップ

環境構築が完了したら:

1. [PROJECT_OVERVIEW.md](./PROJECT_OVERVIEW.md) で機能要件を確認
2. [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) でDB設計を理解
3. [CODING_STANDARDS.md](./CODING_STANDARDS.md) でコーディング規約を確認
4. 実装開始!

---

**最終更新**: 2026-01-06
