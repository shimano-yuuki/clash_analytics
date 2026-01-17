'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { videoApi } from '@/lib/api';
import type { Video, VideoAnalysis } from '@/types';

export default function VideoDetailPage() {
  const params = useParams();
  const router = useRouter();
  const videoId = params.id as string;

  const [video, setVideo] = useState<Video | null>(null);
  const [analysis, setAnalysis] = useState<VideoAnalysis | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [analyzing, setAnalyzing] = useState(false);

  useEffect(() => {
    const fetchVideo = async () => {
      try {
        const response = await videoApi.getById(videoId);
        setVideo(response.data);
        if (response.data.analysis) {
          setAnalysis(response.data.analysis);
        }
      } catch (err) {
        setError('動画情報の取得に失敗しました');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchVideo();
  }, [videoId]);

  const handleAnalyze = async () => {
    setAnalyzing(true);
    try {
      await videoApi.analyze(videoId);
      // 解析開始後、動画情報を再取得
      const response = await videoApi.getById(videoId);
      setVideo(response.data);
      if (response.data.analysis) {
        setAnalysis(response.data.analysis);
      }
    } catch (err) {
      setError('解析の開始に失敗しました');
      console.error(err);
    } finally {
      setAnalyzing(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-xl">読み込み中...</div>
      </div>
    );
  }

  if (error || !video) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-red-600 text-xl">{error || '動画が見つかりません'}</div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-6">
        <button
          onClick={() => router.back()}
          className="text-blue-600 hover:underline mb-4"
        >
          ← 戻る
        </button>
        <h1 className="text-3xl font-bold">{video.title || video.filename}</h1>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* 動画プレーヤー */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold mb-4">動画</h2>
          <video
            controls
            className="w-full rounded-lg"
            src={`http://localhost:8000/storage/${video.file_path}`}
          >
            お使いのブラウザは動画タグをサポートしていません。
          </video>
        </div>

        {/* 動画情報 */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold mb-4">情報</h2>
          <dl className="space-y-2">
            <div>
              <dt className="text-sm font-medium text-gray-600">ファイル名</dt>
              <dd className="text-gray-900">{video.filename}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-600">ファイルサイズ</dt>
              <dd className="text-gray-900">{(video.file_size / 1024 / 1024).toFixed(2)} MB</dd>
            </div>
            {video.duration && (
              <div>
                <dt className="text-sm font-medium text-gray-600">再生時間</dt>
                <dd className="text-gray-900">{video.duration}秒</dd>
              </div>
            )}
            <div>
              <dt className="text-sm font-medium text-gray-600">アップロード日時</dt>
              <dd className="text-gray-900">
                {new Date(video.uploaded_at).toLocaleString('ja-JP')}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-600">ステータス</dt>
              <dd className="text-gray-900">{video.status}</dd>
            </div>
          </dl>

          {video.status !== 'processing' && (
            <button
              onClick={handleAnalyze}
              disabled={analyzing}
              className="mt-6 w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition disabled:opacity-50"
            >
              {analyzing ? '解析中...' : '解析を開始'}
            </button>
          )}
        </div>
      </div>

      {/* 解析結果 */}
      {analysis && (
        <div className="mt-6 bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold mb-4">解析結果</h2>
          <div className="space-y-4">
            {analysis.elixir_analysis && (
              <div>
                <h3 className="font-bold text-lg mb-2">エリクサー分析</h3>
                <p>平均エリクサー: {analysis.elixir_analysis.average_elixir}</p>
                <p>効率: {analysis.elixir_analysis.elixir_efficiency}%</p>
              </div>
            )}
            {analysis.cost_analysis && (
              <div>
                <h3 className="font-bold text-lg mb-2">コスト分析</h3>
                <p>デッキコスト: {analysis.cost_analysis.deck_cost}</p>
                <p>平均カードコスト: {analysis.cost_analysis.average_card_cost}</p>
              </div>
            )}
            {analysis.timing_analysis && (
              <div>
                <h3 className="font-bold text-lg mb-2">タイミング分析</h3>
                <p>攻撃タイミング: {analysis.timing_analysis.attack_timings.length}回</p>
              </div>
            )}
            {analysis.risk_analysis && (
              <div>
                <h3 className="font-bold text-lg mb-2">リスク分析</h3>
                <p>リスクスコア: {analysis.risk_analysis.overall_risk_score}</p>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
