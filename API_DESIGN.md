# API 設計

## 🎯 API設計方針

- **RESTful**: REST原則に従ったAPI設計
- **JSON形式**: レスポンスはすべてJSON
- **ステータスコード**: HTTP標準ステータスコードを使用
- **バージョニング**: APIバージョンをURLに含める (`/api/v1/`)
- **ページネーション**: 大量データは必ずページネーション
- **レート制限**: API呼び出し頻度制限を実装

## 📡 Clash Royale API 連携

### エンドポイント一覧

#### プレイヤー情報取得
```
GET https://api.clashroyale.com/v1/players/{playerTag}
```

**リクエストヘッダー**:
```
Authorization: Bearer {API_TOKEN}
```

**レスポンス例**:
```json
{
  "tag": "#2PP",
  "name": "PlayerName",
  "expLevel": 14,
  "trophies": 5000,
  "bestTrophies": 5200,
  "wins": 1000,
  "losses": 800,
  "battleCount": 2000,
  "threeCrownWins": 300,
  "cards": [...],
  "currentDeck": [...]
}
```

#### バトルログ取得
```
GET https://api.clashroyale.com/v1/players/{playerTag}/battlelog
```

**レスポンス例**:
```json
[
  {
    "type": "PvP",
    "battleTime": "20240106T123045.000Z",
    "isLadderTournament": false,
    "arena": {
      "id": 54000015,
      "name": "Legendary Arena"
    },
    "gameMode": {
      "id": 72000006,
      "name": "Ladder"
    },
    "team": [
      {
        "tag": "#2PP",
        "name": "PlayerName",
        "startingTrophies": 5000,
        "trophyChange": 30,
        "crowns": 3,
        "cards": [...]
      }
    ],
    "opponent": [...]
  }
]
```

#### カード情報取得
```
GET https://api.clashroyale.com/v1/cards
```

## 🔌 自アプリケーションAPI

### ベースURL
```
開発環境: http://localhost:8000/api/v1
本番環境: https://your-domain.com/api/v1
```

### 共通レスポンス形式

#### 成功レスポンス
```json
{
  "success": true,
  "data": { ... },
  "message": "Success message",
  "meta": {
    "timestamp": "2024-01-06T12:30:45Z"
  }
}
```

#### エラーレスポンス
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": [
      {
        "field": "player_tag",
        "message": "The player tag format is invalid."
      }
    ]
  },
  "meta": {
    "timestamp": "2024-01-06T12:30:45Z"
  }
}
```

### エンドポイント一覧

---

## 👤 Players (プレイヤー)

### 1. プレイヤー一覧取得
```
GET /api/v1/players
```

**クエリパラメータ**:
- `page` (integer): ページ番号 (デフォルト: 1)
- `per_page` (integer): 1ページあたりの件数 (デフォルト: 15, 最大: 100)
- `sort` (string): ソート項目 (`trophies`, `name`, `created_at`)
- `order` (string): ソート順 (`asc`, `desc`)

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tag": "#2PP",
      "name": "PlayerName",
      "trophies": 5000,
      "level": 14,
      "wins": 1000,
      "losses": 800,
      "last_fetched_at": "2024-01-06T12:30:45Z",
      "created_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### 2. プレイヤー詳細取得
```
GET /api/v1/players/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tag": "#2PP",
    "name": "PlayerName",
    "trophies": 5000,
    "best_trophies": 5200,
    "level": 14,
    "wins": 1000,
    "losses": 800,
    "three_crown_wins": 300,
    "battle_count": 2000,
    "win_rate": 0.556,
    "current_deck": [...],
    "statistics": {
      "recent_win_rate": 0.65,
      "average_trophy_change": 15,
      "most_used_cards": [...]
    },
    "last_fetched_at": "2024-01-06T12:30:45Z"
  }
}
```

### 3. プレイヤー検索
```
GET /api/v1/players/search
```

**クエリパラメータ**:
- `tag` (string, required): プレイヤータグ

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "tag": "#2PP",
    "name": "PlayerName",
    "trophies": 5000,
    "level": 14,
    "exists_in_db": false
  }
}
```

### 4. プレイヤー登録
```
POST /api/v1/players
```

**リクエストボディ**:
```json
{
  "tag": "#2PP"
}
```

**バリデーション**:
- `tag`: 必須、文字列、正規表現 `/^#[0-9A-Z]+$/`

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tag": "#2PP",
    "name": "PlayerName",
    "trophies": 5000,
    "message": "Player registered successfully"
  }
}
```

### 5. プレイヤーデータ更新
```
PUT /api/v1/players/{id}/refresh
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tag": "#2PP",
    "updated_fields": ["trophies", "wins", "losses"],
    "last_fetched_at": "2024-01-06T12:30:45Z"
  },
  "message": "Player data refreshed successfully"
}
```

### 6. プレイヤー削除
```
DELETE /api/v1/players/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "message": "Player deleted successfully"
}
```

---

## ⚔️ Battles (バトル)

### 1. バトルログ取得
```
GET /api/v1/players/{playerId}/battles
```

**クエリパラメータ**:
- `page` (integer): ページ番号
- `per_page` (integer): 1ページあたりの件数
- `from_date` (date): 開始日 (YYYY-MM-DD)
- `to_date` (date): 終了日 (YYYY-MM-DD)
- `game_mode` (string): ゲームモード (`Ladder`, `Challenge`, etc.)

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "battle_time": "2024-01-06T12:30:45Z",
      "type": "PvP",
      "game_mode": "Ladder",
      "is_win": true,
      "trophy_change": 30,
      "crowns": 3,
      "opponent_crowns": 1,
      "deck": [...],
      "opponent_deck": [...],
      "arena_name": "Legendary Arena"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 500
  }
}
```

### 2. バトル詳細取得
```
GET /api/v1/battles/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "player": { ... },
    "battle_time": "2024-01-06T12:30:45Z",
    "type": "PvP",
    "game_mode": "Ladder",
    "is_win": true,
    "trophy_change": 30,
    "crowns": 3,
    "opponent_crowns": 1,
    "deck": [
      {
        "id": 1,
        "name": "Knight",
        "level": 14,
        "max_level": 14
      }
    ],
    "opponent": {
      "tag": "#ABC",
      "name": "Opponent",
      "trophies": 4900,
      "deck": [...]
    },
    "arena": {
      "id": 54000015,
      "name": "Legendary Arena"
    }
  }
}
```

### 3. バトルログ手動更新
```
POST /api/v1/players/{playerId}/battles/fetch
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "fetched_count": 25,
    "new_battles": 10,
    "updated_battles": 0
  },
  "message": "Battle log fetched successfully"
}
```

---

## 📊 Reports (レポート)

### 1. レポート一覧取得
```
GET /api/v1/players/{playerId}/reports
```

**クエリパラメータ**:
- `page` (integer): ページ番号
- `type` (string): レポートタイプ (`daily`, `weekly`, `monthly`, `custom`)

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "daily",
      "period_start": "2024-01-06",
      "period_end": "2024-01-06",
      "total_battles": 10,
      "wins": 7,
      "losses": 3,
      "win_rate": 0.7,
      "trophy_change": 150,
      "created_at": "2024-01-06T23:59:59Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 30
  }
}
```

### 2. レポート詳細取得
```
GET /api/v1/reports/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "player": { ... },
    "type": "daily",
    "period_start": "2024-01-06",
    "period_end": "2024-01-06",
    "statistics": {
      "total_battles": 10,
      "wins": 7,
      "losses": 3,
      "draws": 0,
      "win_rate": 0.7,
      "average_trophy_change": 15,
      "total_trophy_change": 150,
      "three_crown_wins": 3,
      "crowns_earned": 25,
      "crowns_lost": 12
    },
    "deck_analysis": {
      "most_used_deck": [...],
      "best_performing_deck": [...],
      "deck_win_rates": [...]
    },
    "card_analysis": {
      "most_used_cards": [...],
      "best_performing_cards": [...]
    },
    "opponent_analysis": {
      "average_opponent_trophies": 5000,
      "common_opponent_decks": [...]
    },
    "time_analysis": {
      "hourly_distribution": [...],
      "best_performance_time": "20:00-22:00"
    },
    "created_at": "2024-01-06T23:59:59Z"
  }
}
```

### 3. レポート生成
```
POST /api/v1/players/{playerId}/reports
```

**リクエストボディ**:
```json
{
  "type": "custom",
  "period_start": "2024-01-01",
  "period_end": "2024-01-06"
}
```

**バリデーション**:
- `type`: 必須、in:daily,weekly,monthly,custom
- `period_start`: type=customの場合必須、date
- `period_end`: type=customの場合必須、date、period_start以降

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 10,
    "type": "custom",
    "period_start": "2024-01-01",
    "period_end": "2024-01-06",
    "statistics": { ... }
  },
  "message": "Report generated successfully"
}
```

---

## 📈 Statistics (統計)

### 1. プレイヤー統計取得
```
GET /api/v1/players/{playerId}/statistics
```

**クエリパラメータ**:
- `period` (string): 期間 (`7d`, `30d`, `90d`, `all`)

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "period": "30d",
    "total_battles": 150,
    "wins": 95,
    "losses": 50,
    "draws": 5,
    "win_rate": 0.633,
    "win_streak": {
      "current": 3,
      "best": 8
    },
    "trophy_stats": {
      "starting_trophies": 4800,
      "current_trophies": 5000,
      "change": 200,
      "peak": 5100,
      "lowest": 4750
    },
    "deck_stats": {
      "total_decks_used": 5,
      "favorite_deck": [...],
      "best_deck": [...]
    },
    "card_stats": {
      "most_used_card": "Knight",
      "best_performing_card": "Hog Rider"
    }
  }
}
```

### 2. デッキ統計取得
```
GET /api/v1/decks/statistics
```

**クエリパラメータ**:
- `player_id` (integer): プレイヤーID
- `period` (string): 期間

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "deck": [...],
      "usage_count": 50,
      "win_count": 35,
      "loss_count": 15,
      "win_rate": 0.7,
      "average_trophy_change": 18
    }
  ]
}
```

---

## 🚨 エラーコード

| コード | 説明 |
|--------|------|
| `VALIDATION_ERROR` | バリデーションエラー |
| `NOT_FOUND` | リソースが見つからない |
| `UNAUTHORIZED` | 認証エラー |
| `FORBIDDEN` | 権限エラー |
| `API_ERROR` | Clash Royale APIエラー |
| `RATE_LIMIT_EXCEEDED` | レート制限超過 |
| `SERVER_ERROR` | サーバーエラー |

## 🔒 レート制限

- **一般ユーザー**: 100リクエスト/時間
- **認証済みユーザー**: 500リクエスト/時間

レスポンスヘッダー:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1704542400
```

## 📝 実装例

### Laravel Controller
```php
class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::query()
            ->when($request->sort, function ($query, $sort) {
                $order = $request->order ?? 'desc';
                $query->orderBy($sort, $order);
            })
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => PlayerResource::collection($players),
            'meta' => [
                'current_page' => $players->currentPage(),
                'per_page' => $players->perPage(),
                'total' => $players->total(),
                'last_page' => $players->lastPage(),
            ],
        ]);
    }
}
```

---

**最終更新**: 2026-01-06
