# アプリケーション起動ガイド

## 🚀 起動コマンド

### 1. Dockerコンテナの起動

```bash
# コンテナを起動（初回のみビルド）
docker-compose up -d

# または、強制的に再ビルドして起動
docker-compose up -d --build
```

### 2. 起動確認

```bash
# コンテナの状態を確認
docker-compose ps

# ログを確認
docker-compose logs app
```

### 3. Next.js開発サーバーの起動（Hot Reload有効）

```bash
# Dockerコンテナ内でNext.js開発サーバーを起動（自動起動している場合）
docker-compose up -d nextjs

# または、既に起動しているコンテナ内で実行
docker-compose exec nextjs npm run dev

# または、フォアグラウンドで起動（ログを確認したい場合）
docker-compose exec app npm run dev
```

### 4. アプリケーションアクセス

- **フロントエンド（Next.js）**: http://localhost:3000
- **API（Laravel）**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Vite HMR**: http://localhost:5173

## 🔄 よく使うコマンド

### 停止・再起動

```bash
# コンテナを停止
docker-compose down

# コンテナを再起動
docker-compose restart

# 特定のコンテナを再起動
docker-compose restart app
```

### アセットのビルド

```bash
# プロダクション用にビルド（Vite開発サーバー不要）
docker-compose exec app npm run build

# 開発サーバーを停止してビルドしたい場合
# まずVite開発サーバーを停止（コンテナを再起動）
docker-compose restart app
docker-compose exec app npm run build
```

### ログの確認

```bash
# すべてのコンテナのログ
docker-compose logs

# 特定のコンテナのログ
docker-compose logs app
docker-compose logs nginx
docker-compose logs mysql

# リアルタイムでログを追跡
docker-compose logs -f app
```

### Laravelコマンド

```bash
# Artisanコマンド実行例
docker-compose exec app php artisan migrate
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear
```

## 📝 開発フロー

### 通常の開発（Hot Reload有効）

1. Dockerコンテナを起動
   ```bash
   docker-compose up -d
   ```

2. Vite開発サーバーを起動
   ```bash
   docker-compose exec -d app npm run dev
   ```

3. ブラウザで http://localhost:3000 を開く（Next.jsフロントエンド）

4. React/TypeScriptファイルを編集すると自動的に反映されます（Hot Reload）

### 本番ビルド

1. アセットをビルド
   ```bash
   docker-compose exec app npm run build
   ```

2. これでVite開発サーバーなしでも動作します

## ⚠️ トラブルシューティング

### Next.js開発サーバーが起動しない場合

```bash
# Next.jsコンテナの状態を確認
docker-compose ps nextjs

# Next.jsコンテナ内のNode.jsバージョンを確認
docker-compose exec nextjs node --version

# 依存関係を再インストール
docker-compose exec nextjs npm install

# 開発サーバーを起動
docker-compose exec nextjs npm run dev

# ログを確認
docker-compose logs -f nextjs
```

### コンテナが起動しない場合

```bash
# ログを確認
docker-compose logs

# コンテナを再ビルド
docker-compose up -d --build

# コンテナの状態を確認
docker-compose ps
```
