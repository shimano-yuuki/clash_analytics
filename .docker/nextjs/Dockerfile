FROM node:20-alpine

# 作業ディレクトリ
WORKDIR /app

# 依存関係をインストール
COPY package*.json ./
RUN npm ci

# アプリケーションコードをコピー
COPY . .

# 開発サーバーを起動（開発環境）
CMD ["npm", "run", "dev"]

# 本番環境の場合は以下を使用
# RUN npm run build
# CMD ["npm", "start"]
