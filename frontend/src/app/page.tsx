'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { videoApi } from '@/lib/api';
import type { Video } from '@/types';

export default function Home() {
  const [videos, setVideos] = useState<Video[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchVideos = async () => {
      try {
        const response = await videoApi.getAll();
        setVideos(response.data.data || response.data || []);
      } catch (err) {
        setError('動画一覧の取得に失敗しました');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchVideos();
  }, []);

  const getStatusBadgeColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 text-green-800';
      case 'processing':
        return 'bg-blue-100 text-blue-800';
      case 'failed':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'completed':
        return '解析完了';
      case 'processing':
        return '解析中';
      case 'failed':
        return '失敗';
      default:
        return '待機中';
    }
  };

  return (
    <main className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-4 py-12">
        {/* ヒーローセクション */}
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold text-gray-900 mb-4">
            🎮 Clash Royale Analytics
          </h1>
          <p className="text-xl text-gray-600 mb-8">
            プレイ動画をアップロードして、AI解析でスキル向上を目指しましょう
          </p>
          <Link
            href="/upload"
            className="inline-block bg-purple-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-700 transition shadow-lg"
          >
            📹 動画をアップロード
          </Link>
        </div>

        {/* 動画一覧 */}
        <div className="mb-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">アップロード済み動画</h2>

          {loading ? (
            <div className="text-center py-12">
              <div className="text-gray-600">読み込み中...</div>
            </div>
          ) : error ? (
            <div className="text-center py-12">
              <div className="text-red-600">{error}</div>
            </div>
          ) : videos.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {videos.map((video) => (
                <Link
                  key={video.id}
                  href={`/videos/${video.id}`}
                  className="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition cursor-pointer"
                >
                  <div className="flex justify-between items-start mb-4">
                    <h3 className="text-lg font-bold text-gray-900 truncate flex-1">
                      {video.title || video.filename}
                    </h3>
                    <span
                      className={`ml-2 px-3 py-1 rounded-full text-xs font-medium ${getStatusBadgeColor(
                        video.status
                      )}`}
                    >
                      {getStatusText(video.status)}
                    </span>
                  </div>
                  <div className="space-y-2 text-sm text-gray-600">
                    {video.duration && (
                      <p>⏱ 再生時間: {video.duration}秒</p>
                    )}
                    <p>📁 ファイルサイズ: {(video.file_size / 1024 / 1024).toFixed(2)} MB</p>
                    <p>
                      📅 アップロード日時:{' '}
                      {new Date(video.uploaded_at).toLocaleString('ja-JP')}
                    </p>
                  </div>
                  <div className="mt-4 pt-4 border-t border-gray-200">
                    <span className="text-purple-600 font-medium text-sm">
                      解析結果を見る →
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-md p-12 text-center">
              <div className="text-6xl mb-4">📹</div>
              <p className="text-gray-600 text-lg mb-6">
                まだ動画がアップロードされていません
              </p>
              <Link
                href="/upload"
                className="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition"
              >
                最初の動画をアップロード
              </Link>
            </div>
          )}
        </div>
      </div>
    </main>
  );
}
