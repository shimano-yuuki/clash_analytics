# API 設計

## 🎯 API設計方針

- **RESTful**: REST原則に従ったAPI設計
- **JSON形式**: レスポンスはすべてJSON
- **ステータスコード**: HTTP標準ステータスコードを使用
- **バージョニング**: APIバージョンをURLに含める (`/api/v1/`)
- **ページネーション**: 大量データは必ずページネーション
- **レート制限**: API呼び出し頻度制限を実装

## 📡 Google AI API 連携

### 使用API

#### Video Intelligence API
動画の内容認識、オブジェクト検出、テキスト検出を行う

**ベースURL**:
```
https://videointelligence.googleapis.com/v1/videos:annotate
```

**主な機能**:
- 動画内容認識
- ラベル検出
- シーン変更検出
- テキスト検出(OCR)
- オブジェクト追跡

#### Gemini API
動画の詳細分析と自然言語での説明生成を行う

**ベースURL**:
```
https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent
```

**主な機能**:
- 動画フレームの詳細分析
- ゲーム画面の認識(カード、エリクサー、タイマー等)
- 自然言語での説明生成
- リスク分析のテキスト生成

**認証**:
```
Authorization: Bearer {API_KEY}
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

## 🎬 Videos (動画)

### 1. 動画一覧取得
```
GET /api/v1/videos
```

**クエリパラメータ**:
- `page` (integer): ページ番号 (デフォルト: 1)
- `per_page` (integer): 1ページあたりの件数 (デフォルト: 15, 最大: 100)
- `sort` (string): ソート項目 (`created_at`, `file_name`, `status`)
- `order` (string): ソート順 (`asc`, `desc`)
- `status` (string): フィルタ (`uploaded`, `analyzing`, `completed`, `failed`)

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "file_name": "clash_royale_gameplay_001.mp4",
      "file_size": 52428800,
      "file_path": "videos/2024/01/clash_royale_gameplay_001.mp4",
      "status": "completed",
      "duration_seconds": 180,
      "created_at": "2024-01-06T12:30:45Z",
      "analysis": {
        "status": "completed",
        "completed_at": "2024-01-06T12:35:20Z"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

### 2. 動画詳細取得
```
GET /api/v1/videos/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "file_name": "clash_royale_gameplay_001.mp4",
    "file_size": 52428800,
    "file_path": "videos/2024/01/clash_royale_gameplay_001.mp4",
    "status": "completed",
    "duration_seconds": 180,
    "created_at": "2024-01-06T12:30:45Z",
    "analysis": {
      "status": "completed",
      "started_at": "2024-01-06T12:31:00Z",
      "completed_at": "2024-01-06T12:35:20Z",
      "report_id": 10
    }
  }
}
```

### 3. 動画アップロード
```
POST /api/v1/videos/upload
```

**Content-Type**: `multipart/form-data`

**リクエストボディ**:
- `video` (file, required): 動画ファイル (MP4, MOV, AVI, WebM)
- `title` (string, optional): 動画のタイトル

**バリデーション**:
- `video`: 必須、ファイル、MIMEタイプ: `video/mp4`, `video/quicktime`, `video/x-msvideo`, `video/webm`
- `video`: 最大サイズ: 500MB
- `title`: 文字列、最大255文字

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "file_name": "clash_royale_gameplay_001.mp4",
    "status": "uploaded",
    "message": "Video uploaded successfully. Analysis will start shortly."
  }
}
```

### 4. 動画削除
```
DELETE /api/v1/videos/{id}
```

**レスポンス例**:
```json
{
  "success": true,
  "message": "Video deleted successfully"
}
```

---

## 🔍 Video Analysis (動画解析)

### 1. 動画解析開始
```
POST /api/v1/videos/{videoId}/analyze
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "video_id": 1,
    "analysis_id": 5,
    "status": "analyzing",
    "message": "Analysis started. Please check status later."
  }
}
```

### 2. 解析ステータス取得
```
GET /api/v1/videos/{videoId}/analysis/status
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "video_id": 1,
    "analysis_id": 5,
    "status": "analyzing",
    "progress": 65,
    "started_at": "2024-01-06T12:31:00Z",
    "estimated_completion": "2024-01-06T12:35:00Z"
  }
}
```

### 3. 解析結果取得
```
GET /api/v1/videos/{videoId}/analysis
```

**レスポンス例**:
```json
{
  "success": true,
  "data": {
    "video_id": 1,
    "analysis_id": 5,
    "status": "completed",
    "elixir_analysis": {
      "average_elixir_usage": 7.5,
      "elixir_waste_count": 3,
      "elixir_waste_timestamps": ["00:45", "01:23", "02:10"],
      "overload_count": 5,
      "overload_timestamps": ["00:12", "00:45", "01:30", "02:05", "02:45"]
    },
    "cost_analysis": {
      "average_deck_cost": 3.8,
      "high_cost_card_usage": 12,
      "low_cost_card_usage": 28,
      "cost_balance_score": 0.75
    },
    "timing_analysis": {
      "attack_timings": [
        {
          "timestamp": "00:30",
          "elixir_status": "full",
          "outcome": "success",
          "risk_level": "low"
        },
        {
          "timestamp": "01:45",
          "elixir_status": "low",
          "outcome": "failed",
          "risk_level": "high"
        }
      ]
    },
    "risk_analysis": {
      "high_risk_plays": [
        {
          "timestamp": "01:45",
          "play_description": "エリクサー不足状態での攻撃",
          "risk_level": "high",
          "risk_description": "エリクサーが2の状態でコスト6のカードを使用したため、防御が手薄になりました"
        }
      ],
      "risk_score": 0.65
    },
    "timeline": [
      {
        "timestamp": "00:00",
        "description": "ゲーム開始",
        "elixir": 10,
        "cards_played": []
      },
      {
        "timestamp": "00:15",
        "description": "Knightを使用",
        "elixir": 7,
        "cards_played": ["Knight"]
      }
    ],
    "created_at": "2024-01-06T12:35:20Z"
  }
}
```

---

## 📊 Reports (レポート)

### 1. レポート一覧取得
```
GET /api/v1/videos/{videoId}/reports
```

**クエリパラメータ**:
- `page` (integer): ページ番号

**レスポンス例**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "video_id": 1,
      "video": {
        "id": 1,
        "file_name": "clash_royale_gameplay_001.mp4"
      },
      "analysis_summary": {
        "elixir_efficiency": 0.75,
        "cost_balance": 0.80,
        "risk_score": 0.65
      },
      "created_at": "2024-01-06T12:35:20Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 1
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
    "video_id": 1,
    "video": {
      "id": 1,
      "file_name": "clash_royale_gameplay_001.mp4",
      "duration_seconds": 180
    },
    "elixir_analysis": {
      "average_elixir_usage": 7.5,
      "elixir_waste_count": 3,
      "elixir_efficiency": 0.75,
      "overload_count": 5,
      "waste_timestamps": [
        {
          "timestamp": "00:45",
          "waste_amount": 2,
          "description": "エリクサーが満タンの状態で攻撃を見送った"
        }
      ]
    },
    "cost_analysis": {
      "average_deck_cost": 3.8,
      "high_cost_card_usage": 12,
      "low_cost_card_usage": 28,
      "cost_balance_score": 0.80,
      "card_usage_distribution": {
        "1-3": 28,
        "4-6": 45,
        "7-10": 12
      }
    },
    "timing_analysis": {
      "attack_timings": [
        {
          "timestamp": "00:30",
          "elixir_status": "full",
          "outcome": "success",
          "risk_level": "low",
          "description": "エリクサー満タン状態での攻撃で成功"
        },
        {
          "timestamp": "01:45",
          "elixir_status": "low",
          "outcome": "failed",
          "risk_level": "high",
          "description": "エリクサー不足状態での攻撃で失敗"
        }
      ],
      "optimal_timing_count": 8,
      "poor_timing_count": 3
    },
    "risk_analysis": {
      "high_risk_plays": [
        {
          "timestamp": "01:45",
          "play_description": "エリクサー不足状態での攻撃",
          "risk_level": "high",
          "risk_description": "エリクサーが2の状態でコスト6のカードを使用したため、防御が手薄になりました",
          "recommendation": "このタイミングでは防御に専念し、エリクサーが回復してから攻撃することを推奨します"
        },
        {
          "timestamp": "02:30",
          "play_description": "0分30秒でこういうプレイをした場合のリスク",
          "risk_level": "medium",
          "risk_description": "...",
          "recommendation": "..."
        }
      ],
      "risk_score": 0.65,
      "risk_summary": "全体として中程度のリスクが見られます。エリクサー管理を改善することで、リスクを低減できます。"
    },
    "timeline": [
      {
        "timestamp": "00:00",
        "description": "ゲーム開始",
        "elixir": 10,
        "cards_played": [],
        "risk_level": null
      },
      {
        "timestamp": "00:15",
        "description": "Knightを使用",
        "elixir": 7,
        "cards_played": ["Knight"],
        "risk_level": "low"
      }
    ],
    "recommendations": [
      {
        "category": "elixir",
        "priority": "high",
        "description": "エリクサーの無駄遣いを減らすことで、より効率的なプレイが可能になります"
      },
      {
        "category": "timing",
        "priority": "medium",
        "description": "攻撃タイミングを改善することで、成功率を向上できます"
      }
    ],
    "created_at": "2024-01-06T12:35:20Z"
  }
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
| `API_ERROR` | Google AI APIエラー |
| `RATE_LIMIT_EXCEEDED` | レート制限超過 |
| `FILE_TOO_LARGE` | ファイルサイズ制限超過 |
| `UNSUPPORTED_FILE_FORMAT` | サポートされていないファイル形式 |
| `ANALYSIS_FAILED` | 動画解析失敗 |
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
