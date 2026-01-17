// 動画関連の型定義
export interface Video {
  id: number;
  title: string;
  filename: string;
  file_path: string;
  file_size: number;
  duration?: number;
  uploaded_at: string;
  status: 'pending' | 'processing' | 'completed' | 'failed';
}

// 動画解析結果の型定義
export interface VideoAnalysis {
  id: number;
  video_id: number;
  elixir_analysis?: ElixirAnalysis;
  cost_analysis?: CostAnalysis;
  timing_analysis?: TimingAnalysis;
  risk_analysis?: RiskAnalysis;
  created_at: string;
  updated_at: string;
}

export interface ElixirAnalysis {
  average_elixir: number;
  elixir_efficiency: number;
  peak_usage_times: Array<{ time: number; elixir: number }>;
  recommendations: string[];
}

export interface CostAnalysis {
  deck_cost: number;
  average_card_cost: number;
  cost_distribution: Array<{ cost: number; count: number }>;
  recommendations: string[];
}

export interface TimingAnalysis {
  attack_timings: Array<{ time: number; event: string }>;
  defensive_timings: Array<{ time: number; event: string }>;
  optimal_moments: Array<{ time: number; reason: string }>;
  recommendations: string[];
}

export interface RiskAnalysis {
  risk_factors: Array<{ time: number; factor: string; severity: 'low' | 'medium' | 'high' }>;
  overall_risk_score: number;
  recommendations: string[];
}

// レポートの型定義
export interface Report {
  id: number;
  video_id: number;
  video?: Video;
  analysis?: VideoAnalysis;
  generated_at: string;
  summary: string;
}

// ダッシュボード統計の型定義
export interface DashboardStats {
  total_videos: number;
  total_reports: number;
  processing_videos: number;
  recent_videos: Video[];
}
