# データベース設計

## 🎯 設計方針

- **正規化**: 第3正規形までを基本とする
- **パフォーマンス**: 適切なインデックスを設定
- **拡張性**: 将来の機能追加を考慮
- **命名規則**: スネークケース、複数形テーブル名

## 📊 ER図 (Entity Relationship Diagram)

```
┌──────────────┐       ┌─────────────────┐       ┌──────────────┐
│   videos     │───────│ video_analyses  │───────│  reports     │
│              │ 1   1 │                 │ 1   1 │              │
│  - id        │       │  - id           │       │  - id        │
│  - file_name │       │  - video_id     │       │  - video_id  │
│  - file_path │       │  - status       │       │  - analysis_ │
│  - status    │       │  - elixir_data  │       │    id        │
└──────────────┘       └─────────────────┘       └──────────────┘
                              │
                              │
                       ┌──────┴──────┐
                       │  analysis_  │
                       │  timelines  │
                       │             │
                       │  - id       │
                       │  - analysis_│
                       │    id       │
                       └─────────────┘
```

## 📋 テーブル定義

### 1. videos (動画)

アップロードされた動画ファイルの情報

```sql
CREATE TABLE videos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL COMMENT 'ファイル名',
    original_file_name VARCHAR(255) NOT NULL COMMENT '元のファイル名',
    file_path VARCHAR(500) NOT NULL COMMENT '保存パス',
    file_size BIGINT UNSIGNED NOT NULL COMMENT 'ファイルサイズ(バイト)',
    mime_type VARCHAR(100) NOT NULL COMMENT 'MIMEタイプ',
    duration_seconds INT UNSIGNED COMMENT '動画時間(秒)',
    width INT UNSIGNED COMMENT '動画幅(px)',
    height INT UNSIGNED COMMENT '動画高さ(px)',
    status ENUM('uploaded', 'analyzing', 'completed', 'failed') DEFAULT 'uploaded' COMMENT '状態',
    error_message TEXT NULL COMMENT 'エラーメッセージ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**カラム説明**:
- `file_path`: 動画ファイルの保存先パス
- `status`: 動画の処理状態
  - `uploaded`: アップロード完了、解析待ち
  - `analyzing`: 解析中
  - `completed`: 解析完了
  - `failed`: 解析失敗

---

### 2. video_analyses (動画解析結果)

動画解析の詳細な結果データ

```sql
CREATE TABLE video_analyses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id BIGINT UNSIGNED NOT NULL COMMENT '動画ID',
    status ENUM('pending', 'analyzing', 'completed', 'failed') DEFAULT 'pending' COMMENT '解析状態',
    progress TINYINT UNSIGNED DEFAULT 0 COMMENT '進捗率(0-100)',
    started_at TIMESTAMP NULL COMMENT '解析開始時刻',
    completed_at TIMESTAMP NULL COMMENT '解析完了時刻',
    
    -- エリクサー分析データ
    elixir_analysis JSON COMMENT 'エリクサー分析結果',
    
    -- コスト分析データ
    cost_analysis JSON COMMENT 'コスト分析結果',
    
    -- タイミング分析データ
    timing_analysis JSON COMMENT 'タイミング分析結果',
    
    -- リスク分析データ
    risk_analysis JSON COMMENT 'リスク分析結果',
    
    -- タイムライン情報
    timeline_data JSON COMMENT 'タイムライン情報',
    
    -- Google AI API生データ
    raw_ai_response JSON COMMENT 'AI API生レスポンス',
    
    error_message TEXT NULL COMMENT 'エラーメッセージ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_video_analysis (video_id),
    INDEX idx_status (status),
    INDEX idx_video_id (video_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**JSON カラムの構造例**:

**elixir_analysis**:
```json
{
  "average_elixir_usage": 7.5,
  "elixir_waste_count": 3,
  "elixir_waste_timestamps": ["00:45", "01:23", "02:10"],
  "elixir_efficiency": 0.75,
  "overload_count": 5,
  "overload_timestamps": ["00:12", "00:45", "01:30", "02:05", "02:45"],
  "waste_details": [
    {
      "timestamp": "00:45",
      "waste_amount": 2,
      "description": "エリクサーが満タンの状態で攻撃を見送った"
    }
  ]
}
```

**cost_analysis**:
```json
{
  "average_deck_cost": 3.8,
  "high_cost_card_usage": 12,
  "low_cost_card_usage": 28,
  "cost_balance_score": 0.80,
  "card_usage_distribution": {
    "1-3": 28,
    "4-6": 45,
    "7-10": 12
  }
}
```

**timing_analysis**:
```json
{
  "attack_timings": [
    {
      "timestamp": "00:30",
      "elixir_status": "full",
      "outcome": "success",
      "risk_level": "low",
      "description": "エリクサー満タン状態での攻撃で成功"
    }
  ],
  "optimal_timing_count": 8,
  "poor_timing_count": 3
}
```

**risk_analysis**:
```json
{
  "high_risk_plays": [
    {
      "timestamp": "01:45",
      "play_description": "エリクサー不足状態での攻撃",
      "risk_level": "high",
      "risk_description": "エリクサーが2の状態でコスト6のカードを使用したため、防御が手薄になりました",
      "recommendation": "このタイミングでは防御に専念し、エリクサーが回復してから攻撃することを推奨します"
    }
  ],
  "risk_score": 0.65,
  "risk_summary": "全体として中程度のリスクが見られます。エリクサー管理を改善することで、リスクを低減できます。"
}
```

**timeline_data**:
```json
[
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
]
```

---

### 3. reports (レポート)

動画解析結果を基に生成されたレポート

```sql
CREATE TABLE reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id BIGINT UNSIGNED NOT NULL COMMENT '動画ID',
    analysis_id BIGINT UNSIGNED NOT NULL COMMENT '解析ID',
    elixir_efficiency DECIMAL(5,4) COMMENT 'エリクサー効率 (0.0000 - 1.0000)',
    cost_balance_score DECIMAL(5,4) COMMENT 'コストバランススコア (0.0000 - 1.0000)',
    risk_score DECIMAL(5,4) COMMENT 'リスクスコア (0.0000 - 1.0000)',
    summary TEXT COMMENT 'レポートサマリー',
    recommendations JSON COMMENT '推奨事項',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (analysis_id) REFERENCES video_analyses(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_video_report (video_id, analysis_id),
    INDEX idx_video_id (video_id),
    INDEX idx_analysis_id (analysis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**JSON カラムの構造例**:

**recommendations**:
```json
[
  {
    "category": "elixir",
    "priority": "high",
    "description": "エリクサーの無駄遣いを減らすことで、より効率的なプレイが可能になります",
    "specific_timestamps": ["00:45", "01:23"]
  },
  {
    "category": "timing",
    "priority": "medium",
    "description": "攻撃タイミングを改善することで、成功率を向上できます",
    "specific_timestamps": ["01:45"]
  }
]
```

---

## 🔍 インデックス戦略

### 頻繁に使うクエリ

1. **動画一覧取得**
```sql
SELECT * FROM videos 
WHERE status = 'completed'
ORDER BY created_at DESC;
```
→ インデックス: `idx_status`, `idx_created_at`

2. **動画解析結果取得**
```sql
SELECT * FROM video_analyses 
WHERE video_id = ? 
AND status = 'completed';
```
→ インデックス: `idx_video_id`, `idx_status`

3. **レポート取得**
```sql
SELECT r.*, v.file_name, va.status 
FROM reports r
JOIN videos v ON r.video_id = v.id
JOIN video_analyses va ON r.analysis_id = va.id
WHERE r.video_id = ?;
```
→ インデックス: `idx_video_id`, `idx_analysis_id`

---

## 📊 データサイズ見積もり

### 想定
- ユーザー数: 1,000人
- 1人あたり動画数: 10本/月
- 動画ファイル: 平均50MB/本
- 保存期間: 1年

### 計算
```
videos テーブル:
- 1レコード ≈ 500 bytes
- 1,000人 × 10本/月 × 12ヶ月 = 120,000本
- 120,000本 × 500 bytes = 60 MB

動画ファイルストレージ:
- 120,000本 × 50 MB = 6,000 GB = 6 TB

video_analyses テーブル:
- 1レコード ≈ 10 KB (JSONデータ含む)
- 120,000本 × 10 KB = 1,200 MB = 1.2 GB

reports テーブル:
- 1レコード ≈ 2 KB
- 120,000本 × 2 KB = 240 MB

合計(DB): 約 1.5 GB (1年分)
合計(ストレージ): 約 6 TB (動画ファイル)
```

---

## 🚀 マイグレーション作成順序

```bash
# 1. videos テーブル
php artisan make:migration create_videos_table

# 2. video_analyses テーブル (videosに依存)
php artisan make:migration create_video_analyses_table

# 3. reports テーブル (videosとvideo_analysesに依存)
php artisan make:migration create_reports_table
```

---

## 🧪 サンプルクエリ

### 動画解析状態の確認
```sql
SELECT 
    v.file_name,
    v.status as video_status,
    va.status as analysis_status,
    va.progress,
    va.completed_at
FROM videos v
LEFT JOIN video_analyses va ON v.id = va.video_id
WHERE v.id = 1;
```

### リスクスコアの高い動画TOP5
```sql
SELECT 
    v.file_name,
    r.risk_score,
    r.elixir_efficiency,
    r.cost_balance_score
FROM reports r
JOIN videos v ON r.video_id = v.id
ORDER BY r.risk_score DESC
LIMIT 5;
```

### エリクサー効率の平均値
```sql
SELECT 
    AVG(r.elixir_efficiency) as avg_elixir_efficiency,
    AVG(r.cost_balance_score) as avg_cost_balance,
    AVG(r.risk_score) as avg_risk_score
FROM reports r
WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

**最終更新**: 2026-01-06
