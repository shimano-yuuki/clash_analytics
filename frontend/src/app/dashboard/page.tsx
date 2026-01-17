'use client';

import { useEffect, useState } from 'react';
import { dashboardApi } from '@/lib/api';
import type { DashboardStats } from '@/types';

export default function DashboardPage() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await dashboardApi.getStats();
        setStats(response.data);
      } catch (err) {
        setError('統計情報の取得に失敗しました');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-xl">読み込み中...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-red-600 text-xl">{error}</div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">ダッシュボード</h1>

      {/* 統計カード */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-gray-600 text-sm font-medium mb-2">総動画数</h2>
          <p className="text-3xl font-bold text-purple-600">
            {stats?.total_videos || 0}
          </p>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-gray-600 text-sm font-medium mb-2">総レポート数</h2>
          <p className="text-3xl font-bold text-blue-600">
            {stats?.total_reports || 0}
          </p>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-gray-600 text-sm font-medium mb-2">処理中</h2>
          <p className="text-3xl font-bold text-orange-600">
            {stats?.processing_videos || 0}
          </p>
        </div>
      </div>

      {/* 最近の動画 */}
      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-xl font-bold mb-4">最近の動画</h2>
        {stats?.recent_videos && stats.recent_videos.length > 0 ? (
          <ul className="space-y-2">
            {stats.recent_videos.map((video) => (
              <li key={video.id} className="border-b pb-2">
                <a
                  href={`/videos/${video.id}`}
                  className="text-blue-600 hover:underline"
                >
                  {video.title || video.filename}
                </a>
                <span className="text-gray-500 text-sm ml-4">
                  {new Date(video.uploaded_at).toLocaleDateString('ja-JP')}
                </span>
              </li>
            ))}
          </ul>
        ) : (
          <p className="text-gray-500">動画がまだありません</p>
        )}
      </div>
    </div>
  );
}
