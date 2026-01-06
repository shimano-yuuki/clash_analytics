# データベース設計

## 🎯 設計方針

- **正規化**: 第3正規形までを基本とする
- **パフォーマンス**: 適切なインデックスを設定
- **拡張性**: 将来の機能追加を考慮
- **命名規則**: スネークケース、複数形テーブル名

## 📊 ER図 (Entity Relationship Diagram)

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│   players    │───────│   battles    │───────│    decks     │
│              │ 1   * │              │ 1   1 │              │
│  - id        │       │  - id        │       │  - id        │
│  - tag       │       │  - player_id │       │  - hash      │
│  - name      │       │  - deck_id   │       │  - cards     │
└──────────────┘       └──────────────┘       └──────────────┘
                              │                       │
                              │                       │
                              │                  ┌────┴────┐
                              │                  │         │
                       ┌──────┴──────┐    ┌─────▼─────┐  │
                       │   reports   │    │   cards   │  │
                       │             │    │           │  │
                       │  - id       │    │  - id     │◄─┘
                       │  - player_id│    │  - name   │
                       └─────────────┘    └───────────┘
                              │
                       ┌──────┴──────┐
                       │  player_    │
                       │ statistics  │
                       │             │
                       │  - id       │
                       │  - player_id│
                       └─────────────┘
```

## 📋 テーブル定義

### 1. players (プレイヤー)

プレイヤーの基本情報を保存

```sql
CREATE TABLE players (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(20) UNIQUE NOT NULL COMMENT 'プレイヤータグ (#2PP)',
    name VARCHAR(100) NOT NULL COMMENT 'プレイヤー名',
    exp_level TINYINT UNSIGNED NOT NULL COMMENT '経験レベル',
    trophies INT UNSIGNED NOT NULL COMMENT '現在のトロフィー',
    best_trophies INT UNSIGNED NOT NULL COMMENT '最高トロフィー',
    wins INT UNSIGNED DEFAULT 0 COMMENT '総勝利数',
    losses INT UNSIGNED DEFAULT 0 COMMENT '総敗北数',
    battle_count INT UNSIGNED DEFAULT 0 COMMENT '総バトル数',
    three_crown_wins INT UNSIGNED DEFAULT 0 COMMENT '3クラウン勝利数',
    arena_id INT UNSIGNED COMMENT 'アリーナID',
    arena_name VARCHAR(100) COMMENT 'アリーナ名',
    clan_tag VARCHAR(20) COMMENT 'クランタグ',
    clan_name VARCHAR(100) COMMENT 'クラン名',
    current_deck JSON COMMENT '現在使用中のデッキ',
    last_fetched_at TIMESTAMP NULL COMMENT '最終取得日時',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tag (tag),
    INDEX idx_trophies (trophies),
    INDEX idx_last_fetched (last_fetched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**カラム説明**:
- `tag`: Clash Royaleのプレイヤー識別子 (例: #2PP)
- `current_deck`: JSON形式でカードIDとレベルを保存
- `last_fetched_at`: API最終取得時刻、データ更新タイミングの判断に使用

---

### 2. battles (バトル記録)

個々のバトルの詳細情報

```sql
CREATE TABLE battles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL COMMENT 'プレイヤーID',
    battle_time VARCHAR(30) NOT NULL COMMENT 'バトル時刻 (ISO 8601)',
    type VARCHAR(50) NOT NULL COMMENT 'バトルタイプ (PvP, challenge等)',
    game_mode VARCHAR(50) NOT NULL COMMENT 'ゲームモード (Ladder, 2v2等)',
    deck_id BIGINT UNSIGNED COMMENT '使用デッキID',
    opponent_deck_id BIGINT UNSIGNED COMMENT '相手デッキID',
    is_win BOOLEAN NOT NULL COMMENT '勝利フラグ',
    is_draw BOOLEAN DEFAULT FALSE COMMENT '引き分けフラグ',
    trophy_change INT NULL COMMENT 'トロフィー変動 (+30, -25等)',
    crowns TINYINT UNSIGNED NOT NULL COMMENT '獲得クラウン数',
    opponent_crowns TINYINT UNSIGNED NOT NULL COMMENT '相手クラウン数',
    arena_id INT UNSIGNED COMMENT 'アリーナID',
    arena_name VARCHAR(100) COMMENT 'アリーナ名',
    opponent_tag VARCHAR(20) COMMENT '相手プレイヤータグ',
    opponent_name VARCHAR(100) COMMENT '相手プレイヤー名',
    opponent_trophies INT UNSIGNED COMMENT '相手トロフィー',
    elixir_leaked INT UNSIGNED COMMENT '無駄にしたエリクサー',
    battle_duration_seconds INT UNSIGNED COMMENT 'バトル時間(秒)',
    raw_data JSON COMMENT 'API生データ (将来の分析用)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (deck_id) REFERENCES decks(id) ON DELETE SET NULL,
    FOREIGN KEY (opponent_deck_id) REFERENCES decks(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_battle (player_id, battle_time),
    INDEX idx_player_id (player_id),
    INDEX idx_battle_time (battle_time),
    INDEX idx_is_win (is_win),
    INDEX idx_game_mode (game_mode),
    INDEX idx_player_time (player_id, battle_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**カラム説明**:
- `battle_time`: ユニークキーとして使用、重複バトルを防ぐ
- `trophy_change`: Ladderモードのみ有効、その他はNULL
- `raw_data`: 将来的な分析のため、API生データを保存

---

### 3. decks (デッキ)

使用されたデッキの組み合わせ

```sql
CREATE TABLE decks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hash VARCHAR(64) UNIQUE NOT NULL COMMENT 'デッキのハッシュ値 (カードIDソート後)',
    cards JSON NOT NULL COMMENT 'カード配列 [{"id": 26000000, "name": "Knight", "level": 14}]',
    average_elixir DECIMAL(3,2) NOT NULL COMMENT '平均エリクサーコスト',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_hash (hash),
    INDEX idx_avg_elixir (average_elixir)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**カラム説明**:
- `hash`: カードIDをソートして生成したハッシュ、同一デッキの重複を防ぐ
- `cards`: JSON形式 `[{"id": 26000000, "name": "Knight", "level": 14}, ...]`
- `average_elixir`: 8枚カードの平均エリクサーコスト

**ハッシュ生成例** (PHP):
```php
$cardIds = array_map(fn($card) => $card['id'], $cards);
sort($cardIds);
$hash = md5(implode(',', $cardIds));
```

---

### 4. cards (カード)

Clash Royaleの全カード情報 (マスターデータ)

```sql
CREATE TABLE cards (
    id INT UNSIGNED PRIMARY KEY COMMENT 'Clash Royale カードID',
    name VARCHAR(100) NOT NULL COMMENT 'カード名',
    max_level TINYINT UNSIGNED NOT NULL COMMENT '最大レベル',
    rarity VARCHAR(20) NOT NULL COMMENT 'レアリティ (common, rare, epic, legendary)',
    elixir_cost TINYINT UNSIGNED NOT NULL COMMENT 'エリクサーコスト',
    type VARCHAR(20) NOT NULL COMMENT 'タイプ (troop, spell, building)',
    icon_url VARCHAR(255) COMMENT 'アイコン画像URL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_rarity (rarity),
    INDEX idx_elixir (elixir_cost)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**初期データ投入**: Seederで実装
```php
// database/seeders/CardSeeder.php
DB::table('cards')->insert([
    ['id' => 26000000, 'name' => 'Knight', 'rarity' => 'common', 'elixir_cost' => 3, ...],
    ['id' => 26000001, 'name' => 'Archers', 'rarity' => 'common', 'elixir_cost' => 3, ...],
    // ...
]);
```

---

### 5. reports (レポート)

生成されたレポート

```sql
CREATE TABLE reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL COMMENT 'プレイヤーID',
    type VARCHAR(20) NOT NULL COMMENT 'レポートタイプ (daily, weekly, monthly, custom)',
    period_start DATE NOT NULL COMMENT '集計期間開始',
    period_end DATE NOT NULL COMMENT '集計期間終了',
    total_battles INT UNSIGNED NOT NULL COMMENT '総バトル数',
    wins INT UNSIGNED NOT NULL COMMENT '勝利数',
    losses INT UNSIGNED NOT NULL COMMENT '敗北数',
    draws INT UNSIGNED NOT NULL COMMENT '引き分け数',
    win_rate DECIMAL(5,4) NOT NULL COMMENT '勝率 (0.0000 - 1.0000)',
    trophy_change INT NOT NULL COMMENT 'トロフィー変動',
    average_trophy_change DECIMAL(6,2) COMMENT '平均トロフィー変動',
    three_crown_wins INT UNSIGNED COMMENT '3クラウン勝利数',
    total_crowns INT UNSIGNED COMMENT '総獲得クラウン数',
    statistics JSON COMMENT '詳細統計データ',
    deck_analysis JSON COMMENT 'デッキ分析データ',
    card_analysis JSON COMMENT 'カード分析データ',
    opponent_analysis JSON COMMENT '対戦相手分析データ',
    time_analysis JSON COMMENT '時間帯分析データ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    
    INDEX idx_player_id (player_id),
    INDEX idx_type (type),
    INDEX idx_period (period_start, period_end),
    INDEX idx_player_period (player_id, period_start, period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**JSON カラムの構造例**:

**statistics**:
```json
{
  "win_streak": {
    "current": 3,
    "best": 8
  },
  "loss_streak": {
    "current": 0,
    "worst": 5
  },
  "hourly_distribution": {
    "00": 2,
    "01": 0,
    "20": 5
  }
}
```

**deck_analysis**:
```json
{
  "most_used": {
    "deck_id": 123,
    "usage_count": 50
  },
  "best_performing": {
    "deck_id": 456,
    "win_rate": 0.8
  },
  "deck_win_rates": [
    {
      "deck_id": 123,
      "battles": 50,
      "wins": 35,
      "win_rate": 0.7
    }
  ]
}
```

---

### 6. player_statistics (プレイヤー統計)

定期的に計算されるプレイヤー統計のスナップショット

```sql
CREATE TABLE player_statistics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL COMMENT 'プレイヤーID',
    period VARCHAR(10) NOT NULL COMMENT '期間 (7d, 30d, 90d, all)',
    calculated_at TIMESTAMP NOT NULL COMMENT '計算日時',
    total_battles INT UNSIGNED NOT NULL,
    wins INT UNSIGNED NOT NULL,
    losses INT UNSIGNED NOT NULL,
    draws INT UNSIGNED NOT NULL,
    win_rate DECIMAL(5,4) NOT NULL,
    average_trophy_change DECIMAL(6,2),
    favorite_deck_id BIGINT UNSIGNED COMMENT 'よく使うデッキ',
    best_deck_id BIGINT UNSIGNED COMMENT '勝率が高いデッキ',
    most_used_card_id INT UNSIGNED COMMENT 'よく使うカード',
    statistics_data JSON COMMENT '詳細統計データ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (favorite_deck_id) REFERENCES decks(id) ON DELETE SET NULL,
    FOREIGN KEY (best_deck_id) REFERENCES decks(id) ON DELETE SET NULL,
    FOREIGN KEY (most_used_card_id) REFERENCES cards(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_player_period (player_id, period, calculated_at),
    INDEX idx_player_id (player_id),
    INDEX idx_period (period)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**使用例**:
- 毎日深夜に7日/30日/90日/全期間の統計を計算
- ダッシュボード表示時に最新の統計を取得

---

## 🔍 インデックス戦略

### 頻繁に使うクエリ

1. **プレイヤーのバトル一覧取得**
```sql
SELECT * FROM battles 
WHERE player_id = ? 
ORDER BY battle_time DESC;
```
→ インデックス: `idx_player_time (player_id, battle_time)`

2. **期間指定でのバトル取得**
```sql
SELECT * FROM battles 
WHERE player_id = ? 
AND battle_time BETWEEN ? AND ?;
```
→ インデックス: `idx_player_time (player_id, battle_time)`

3. **デッキ使用統計**
```sql
SELECT deck_id, COUNT(*), SUM(is_win) 
FROM battles 
WHERE player_id = ? 
GROUP BY deck_id;
```
→ インデックス: `idx_player_id`, カバリングインデックスも検討

---

## 📊 データサイズ見積もり

### 想定
- ユーザー数: 1,000人
- 1人あたりバトル数: 1,000件
- 保存期間: 1年

### 計算
```
battles テーブル:
- 1レコード ≈ 500 bytes
- 1,000人 × 1,000件 × 500 bytes = 500 MB

decks テーブル:
- 重複を考慮し、ユニークデッキ数 ≈ 5,000
- 1レコード ≈ 300 bytes
- 5,000 × 300 bytes = 1.5 MB

players テーブル:
- 1,000人 × 1KB = 1 MB

合計: 約 502 MB (1年分)
```

---

## 🚀 マイグレーション作成順序

```bash
# 1. players テーブル
php artisan make:migration create_players_table

# 2. cards テーブル (マスターデータ)
php artisan make:migration create_cards_table

# 3. decks テーブル
php artisan make:migration create_decks_table

# 4. battles テーブル (playersとdecksに依存)
php artisan make:migration create_battles_table

# 5. reports テーブル (playersに依存)
php artisan make:migration create_reports_table

# 6. player_statistics テーブル
php artisan make:migration create_player_statistics_table
```

---

## 🧪 サンプルクエリ

### プレイヤーの勝率計算
```sql
SELECT 
    p.name,
    COUNT(b.id) as total_battles,
    SUM(b.is_win) as wins,
    ROUND(SUM(b.is_win) / COUNT(b.id) * 100, 2) as win_rate
FROM players p
LEFT JOIN battles b ON p.id = b.player_id
WHERE p.id = 1
GROUP BY p.id;
```

### よく使うデッキTOP5
```sql
SELECT 
    d.id,
    d.cards,
    COUNT(b.id) as usage_count,
    SUM(b.is_win) as wins,
    ROUND(SUM(b.is_win) / COUNT(b.id) * 100, 2) as win_rate
FROM battles b
JOIN decks d ON b.deck_id = d.id
WHERE b.player_id = 1
GROUP BY d.id
ORDER BY usage_count DESC
LIMIT 5;
```

### 時間帯別バトル数
```sql
SELECT 
    HOUR(STR_TO_DATE(battle_time, '%Y-%m-%dT%H:%i:%s')) as hour,
    COUNT(*) as battle_count,
    SUM(is_win) as wins
FROM battles
WHERE player_id = 1
GROUP BY hour
ORDER BY hour;
```

---

**最終更新**: 2026-01-06
