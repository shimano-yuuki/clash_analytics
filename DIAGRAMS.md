# システム図・フロー図

このドキュメントでは、Clash Royale Analytics Platformのシステム全体を理解するための各種図を提供します。

## 📊 システムアーキテクチャ図

```mermaid
graph TB
    subgraph "クライアント層"
        A[Webブラウザ]
    end
    
    subgraph "アプリケーション層"
        B[Nginx]
        C[Laravel Application]
        D[PHP-FPM]
    end
    
    subgraph "ビジネスロジック層"
        E[VideoController]
        F[VideoAnalysisService]
        G[GoogleAiApiService]
        H[ReportGenerationService]
    end
    
    subgraph "データ層"
        I[(MySQL Database)]
        J[File Storage]
    end
    
    subgraph "外部API"
        K[Google Video Intelligence API]
        L[Google Gemini API]
    end
    
    A -->|HTTP/HTTPS| B
    B --> C
    C --> D
    D --> E
    E --> F
    F --> G
    F --> H
    G --> K
    G --> L
    F --> I
    E --> J
    H --> I
    
    style A fill:#e1f5ff
    style K fill:#fff4e1
    style L fill:#fff4e1
    style I fill:#e8f5e9
    style J fill:#fce4ec
```

## 🔄 動画アップロードフロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant W as Webブラウザ
    participant C as VideoController
    participant VS as VideoStorageService
    participant DB as Database
    participant FS as File Storage
    
    U->>W: 動画ファイルを選択
    W->>C: POST /api/v1/videos/upload
    C->>C: バリデーション(ファイル形式・サイズ)
    alt バリデーション成功
        C->>VS: 動画ファイル保存
        VS->>FS: ファイルをストレージに保存
        FS-->>VS: 保存完了
        VS-->>C: 保存パス返却
        C->>DB: videosテーブルにレコード作成
        DB-->>C: レコード作成完了
        C-->>W: アップロード成功レスポンス
        W-->>U: アップロード完了表示
        Note over C,DB: 非同期で解析ジョブをキューに追加
    else バリデーション失敗
        C-->>W: エラーレスポンス
        W-->>U: エラーメッセージ表示
    end
```

## 🤖 動画解析フロー

```mermaid
sequenceDiagram
    participant Q as Queue Worker
    participant VA as VideoAnalysisService
    participant GA as GoogleAiApiService
    participant VI as Video Intelligence API
    participant GEM as Gemini API
    participant EA as ElixirAnalysisService
    participant TA as TimingAnalysisService
    participant RA as RiskAnalysisService
    participant DB as Database
    
    Q->>VA: 動画解析ジョブ開始
    VA->>DB: video_analysesレコード作成(status=analyzing)
    
    VA->>GA: 動画ファイル取得
    GA->>VI: 動画解析リクエスト(OCR・オブジェクト検出)
    VI-->>GA: 解析結果(テキスト・オブジェクト情報)
    
    GA->>GEM: 解析結果を元に詳細分析リクエスト
    GEM-->>GA: 詳細分析結果(エリクサー・タイミング・リスク)
    
    GA-->>VA: 統合解析結果
    
    VA->>EA: エリクサー分析実行
    EA-->>VA: エリクサー分析結果
    
    VA->>TA: タイミング分析実行
    TA-->>VA: タイミング分析結果
    
    VA->>RA: リスク分析実行
    RA-->>VA: リスク分析結果
    
    VA->>DB: video_analysesレコード更新(解析結果保存)
    VA->>DB: reportsレコード作成
    VA->>DB: videosレコード更新(status=completed)
    
    VA-->>Q: 解析完了
```

## 📈 レポート生成・表示フロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant W as Webブラウザ
    participant C as ReportController
    participant RGS as ReportGenerationService
    participant DB as Database
    
    U->>W: レポート表示リクエスト
    W->>C: GET /api/v1/reports/{id}
    
    C->>DB: reportsテーブルからレポート取得
    DB-->>C: レポートデータ
    
    C->>DB: video_analysesテーブルから解析データ取得
    DB-->>C: 解析データ
    
    C->>RGS: レポートデータ整形
    RGS->>RGS: エリクサー効率計算
    RGS->>RGS: リスクスコア計算
    RGS->>RGS: タイムライン生成
    RGS-->>C: 整形済みレポートデータ
    
    C-->>W: JSONレスポンス
    W->>W: チャート・グラフ描画
    W->>W: タイムライン表示
    W->>W: リスク分析ハイライト表示
    W-->>U: レポート表示完了
```

## 🗄️ データベースER図 (詳細版)

```mermaid
erDiagram
    VIDEOS ||--o| VIDEO_ANALYSES : has
    VIDEOS ||--o{ REPORTS : generates
    VIDEO_ANALYSES ||--|| REPORTS : creates
    
    VIDEOS {
        bigint id PK
        varchar file_name
        varchar original_file_name
        varchar file_path
        bigint file_size
        varchar mime_type
        int duration_seconds
        int width
        int height
        enum status
        text error_message
        timestamp created_at
        timestamp updated_at
    }
    
    VIDEO_ANALYSES {
        bigint id PK
        bigint video_id FK
        enum status
        tinyint progress
        timestamp started_at
        timestamp completed_at
        json elixir_analysis
        json cost_analysis
        json timing_analysis
        json risk_analysis
        json timeline_data
        json raw_ai_response
        text error_message
        timestamp created_at
        timestamp updated_at
    }
    
    REPORTS {
        bigint id PK
        bigint video_id FK
        bigint analysis_id FK
        decimal elixir_efficiency
        decimal cost_balance_score
        decimal risk_score
        text summary
        json recommendations
        timestamp created_at
    }
```

## 🔀 データフロー図

```mermaid
flowchart TD
    Start([動画アップロード]) --> Upload[動画ファイル保存]
    Upload --> DB1[(videosテーブル)]
    DB1 --> Queue[解析ジョブをキューに追加]
    
    Queue --> AI1[Google Video Intelligence API]
    Queue --> AI2[Google Gemini API]
    
    AI1 --> Parse1[OCR: エリクサー数値抽出]
    AI1 --> Parse2[オブジェクト検出: カード認識]
    
    AI2 --> Parse3[ゲーム画面解析]
    AI2 --> Parse4[自然言語説明生成]
    
    Parse1 --> Analysis[解析サービス層]
    Parse2 --> Analysis
    Parse3 --> Analysis
    Parse4 --> Analysis
    
    Analysis --> EA[エリクサー分析]
    Analysis --> CA[コスト分析]
    Analysis --> TA[タイミング分析]
    Analysis --> RA[リスク分析]
    
    EA --> DB2[(video_analysesテーブル)]
    CA --> DB2
    TA --> DB2
    RA --> DB2
    
    DB2 --> Report[レポート生成]
    Report --> DB3[(reportsテーブル)]
    
    DB3 --> Display[レポート表示]
    Display --> End([ユーザー閲覧])
    
    style Start fill:#e1f5ff
    style End fill:#e1f5ff
    style AI1 fill:#fff4e1
    style AI2 fill:#fff4e1
    style DB1 fill:#e8f5e9
    style DB2 fill:#e8f5e9
    style DB3 fill:#e8f5e9
```

## ⚙️ コンポーネント構成図

```mermaid
graph LR
    subgraph "Presentation Layer"
        UI[Blade Templates]
        API[REST API]
    end
    
    subgraph "Application Layer"
        VC[VideoController]
        RC[ReportController]
        VAC[VideoAnalysisController]
    end
    
    subgraph "Service Layer"
        VAS[VideoStorageService]
        VANS[VideoAnalysisService]
        GAIS[GoogleAiApiService]
        EAS[ElixirAnalysisService]
        CAS[CostAnalysisService]
        TAS[TimingAnalysisService]
        RAS[RiskAnalysisService]
        RGS[ReportGenerationService]
    end
    
    subgraph "Repository Layer"
        VR[VideoRepository]
        VAR[VideoAnalysisRepository]
        RR[ReportRepository]
    end
    
    subgraph "Data Layer"
        DB[(MySQL)]
        FS[File Storage]
    end
    
    UI --> VC
    UI --> RC
    API --> VC
    API --> RC
    API --> VAC
    
    VC --> VAS
    VC --> VR
    VAC --> VANS
    RC --> RGS
    
    VAS --> FS
    VANS --> GAIS
    VANS --> EAS
    VANS --> CAS
    VANS --> TAS
    VANS --> RAS
    VANS --> VAR
    
    RGS --> RR
    EAS --> VAR
    CAS --> VAR
    TAS --> VAR
    RAS --> VAR
    
    VR --> DB
    VAR --> DB
    RR --> DB
    
    style UI fill:#e1f5ff
    style API fill:#e1f5ff
    style GAIS fill:#fff4e1
    style DB fill:#e8f5e9
    style FS fill:#fce4ec
```

## 🔐 認証・認可フロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant W as Webブラウザ
    participant A as Laravel App
    participant AUTH as 認証ミドルウェア
    participant DB as Database
    
    U->>W: ログイン
    W->>A: POST /login
    A->>DB: ユーザー認証
    DB-->>A: 認証結果
    alt 認証成功
        A->>A: セッション作成
        A-->>W: 認証トークン返却
        W->>W: トークン保存
    else 認証失敗
        A-->>W: エラーレスポンス
    end
    
    U->>W: 動画アップロード
    W->>A: POST /api/v1/videos/upload<br/>(トークン付き)
    A->>AUTH: トークン検証
    AUTH->>DB: ユーザー確認
    DB-->>AUTH: ユーザー情報
    alt トークン有効
        AUTH-->>A: 認証成功
        A->>A: リクエスト処理
        A-->>W: レスポンス返却
    else トークン無効
        AUTH-->>A: 認証失敗
        A-->>W: 401 Unauthorized
    end
```

## 📊 エリクサー分析プロセス詳細

```mermaid
flowchart TD
    Start([動画解析開始]) --> Extract[フレーム単位でエリクサー数値抽出]
    Extract --> Track[エリクサー変動を追跡]
    
    Track --> Check1{エリクサーが満タン?}
    Check1 -->|Yes| Check2{攻撃を見送った?}
    Check1 -->|No| Normal[正常処理]
    
    Check2 -->|Yes| Waste1[エリクサー無駄遣いカウント]
    Check2 -->|No| Normal
    
    Track --> Check3{エリクサー不足?}
    Check3 -->|Yes| Check4{高コストカード使用?}
    Check3 -->|No| Normal
    
    Check4 -->|Yes| Risk1[リスクプレイカウント]
    Check4 -->|No| Normal
    
    Waste1 --> Calc1[エリクサー効率計算]
    Risk1 --> Calc1
    Normal --> Calc1
    
    Calc1 --> Calc2[平均エリクサー使用量計算]
    Calc2 --> Calc3[無駄遣いタイムスタンプ抽出]
    Calc3 --> Store[(DB保存)]
    Store --> End([分析完了])
    
    style Start fill:#e1f5ff
    style End fill:#e1f5ff
    style Waste1 fill:#ffcdd2
    style Risk1 fill:#ffcdd2
    style Store fill:#e8f5e9
```

## 🎯 リスク分析プロセス詳細

```mermaid
flowchart TD
    Start([タイミング分析完了]) --> Collect[全プレイタイミング収集]
    
    Collect --> Analyze1[エリクサー状況分析]
    Collect --> Analyze2[カードコスト分析]
    Collect --> Analyze3[ゲーム状況分析]
    
    Analyze1 --> Check1{エリクサー不足で高コストカード?}
    Analyze2 --> Check2{コストバランス悪い?}
    Analyze3 --> Check3{防御が手薄?}
    
    Check1 -->|Yes| Risk1[高リスク判定]
    Check1 -->|No| Safe1[安全判定]
    
    Check2 -->|Yes| Risk2[中リスク判定]
    Check2 -->|No| Safe2[安全判定]
    
    Check3 -->|Yes| Risk3[高リスク判定]
    Check3 -->|No| Safe3[安全判定]
    
    Risk1 --> Score[リスクスコア計算]
    Risk2 --> Score
    Risk3 --> Score
    Safe1 --> Score
    Safe2 --> Score
    Safe3 --> Score
    
    Score --> Gen[推奨事項生成]
    Gen --> Desc[リスク説明文生成]
    Desc --> Store[(DB保存)]
    Store --> End([リスク分析完了])
    
    style Start fill:#e1f5ff
    style End fill:#e1f5ff
    style Risk1 fill:#ffcdd2
    style Risk2 fill:#ffe0b2
    style Risk3 fill:#ffcdd2
    style Store fill:#e8f5e9
```

## 🚀 システム全体フロー (統合図)

```mermaid
stateDiagram-v2
    [*] --> Upload: ユーザーが動画アップロード
    
    Upload --> Validating: ファイル検証中
    Validating --> Uploaded: 検証成功
    Validating --> Failed: 検証失敗
    
    Uploaded --> Queued: キューに追加
    Queued --> Analyzing: 解析開始
    
    Analyzing --> Processing: Video Intelligence API実行
    Processing --> Processing: Gemini API実行
    Processing --> Processing: 各種分析サービス実行
    
    Processing --> Completed: 解析完了
    Processing --> Failed: 解析失敗
    
    Completed --> ReportGenerated: レポート生成
    ReportGenerated --> Displayed: レポート表示
    
    Failed --> [*]
    Displayed --> [*]
    
    note right of Analyzing
        エリクサー分析
        コスト分析
        タイミング分析
        リスク分析
    end note
```

---

**最終更新**: 2026-01-06
