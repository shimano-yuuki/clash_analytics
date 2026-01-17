'use client';

import { useEffect, useState } from 'react';
import { videoApi } from '@/lib/api';
import type { Video } from '@/types';
import Link from 'next/link';

export default function VideosPage() {
  const [videos, setVideos] = useState<Video[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchVideos = async () => {
      try {
        const response = await videoApi.getAll();
        setVideos(response.data.data || response.data);
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
        return '完了';
      case 'processing':
        return '処理中';
      case 'failed':
        return '失敗';
      default:
        return '待機中';
    }
  };

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
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">動画一覧</h1>
        <Link
          href="/videos/upload"
          className="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition"
        >
          動画をアップロード
        </Link>
      </div>

      {videos.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {videos.map((video) => (
            <div
              key={video.id}
              className="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
            >
              <Link href={`/videos/${video.id}`}>
                <h2 className="text-xl font-bold mb-2 truncate">
                  {video.title || video.filename}
                </h2>
                <div className="space-y-2 text-sm text-gray-600">
                  <p>ファイルサイズ: {(video.file_size / 1024 / 1024).toFixed(2)} MB</p>
                  {video.duration && <p>再生時間: {video.duration}秒</p>}
                  <p>
                    アップロード日時:{' '}
                    {new Date(video.uploaded_at).toLocaleString('ja-JP')}
                  </p>
                  <span
                    className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${getStatusBadgeColor(
                      video.status
                    )}`}
                  >
                    {getStatusText(video.status)}
                  </span>
                </div>
              </Link>
            </div>
          ))}
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow p-12 text-center">
          <p className="text-gray-500 text-lg mb-4">動画がまだありません</p>
          <Link
            href="/videos/upload"
            className="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-block"
          >
            最初の動画をアップロード
          </Link>
        </div>
      )}
    </div>
  );
}
