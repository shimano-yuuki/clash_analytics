'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { videoApi } from '@/lib/api';
import type { Video } from '@/types';
import UploadTab from './components/UploadTab';

// 動画一覧タブコンポーネント
function VideoListTab({ videos, loading, error }: {
    videos: Video[];
    loading: boolean;
    error: string | null;
}) {
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

    if (loading) {
        return (
            <div className="text-center py-12">
                <div className="text-gray-600">読み込み中...</div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="text-center py-12">
                <div className="text-red-600">{error}</div>
            </div>
        );
    }

    if (videos.length === 0) {
        return (
            <div className="bg-white rounded-lg shadow-md p-12 text-center">
                <div className="text-6xl mb-4">📹</div>
                <p className="text-gray-600 text-lg mb-6">
                    まだ動画がアップロードされていません
                </p>
                <p className="text-sm text-gray-500">
                    上の「動画をアップロード」タブから動画をアップロードしてください
                </p>
            </div>
        );
    }

    return (
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
    );
}

export default function Home() {
    const [activeTab, setActiveTab] = useState<'list' | 'upload'>('list');
    const [videos, setVideos] = useState<Video[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchVideos = async () => {
            // リストタブがアクティブな時のみ動画を取得
            if (activeTab !== 'list') return;

            setLoading(true);
            setError(null);

            try {
                const response = await videoApi.getAll();
                const videoData = response.data?.data || response.data || [];

                // 空配列の場合も正常な状態として扱う（「動画がありません」を表示）
                if (Array.isArray(videoData)) {
                    setVideos(videoData);
                    setError(null);
                } else {
                    // データが配列でない場合も空配列として扱う
                    setVideos([]);
                    setError(null);
                }
            } catch (err: any) {
                // ネットワークエラーやサーバーエラーの場合のみエラーとして扱う
                const status = err.response?.status;
                if (status && (status === 404 || status >= 500)) {
                    setError('動画一覧の取得に失敗しました');
                    setVideos([]);
                } else {
                    // その他のエラー（ネットワークエラーなど）も空配列として扱う
                    // これにより「動画がありません」が表示される
                    setVideos([]);
                    setError(null);
                }
                console.error('Video fetch error:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchVideos();
    }, [activeTab]);

    // アップロード成功時に動画一覧を再取得
    const handleUploadSuccess = async () => {
        setActiveTab('list');
        setLoading(true);
        try {
            const response = await videoApi.getAll();
            const videoData = response.data.data || response.data || [];
            setVideos(Array.isArray(videoData) ? videoData : []);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
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
                </div>

                {/* タブナビゲーション */}
                <div className="bg-white rounded-lg shadow-md mb-8">
                    <div className="border-b border-gray-200">
                        <nav className="flex space-x-8 px-6" aria-label="Tabs">
                            <button
                                onClick={() => setActiveTab('list')}
                                className={`
                  py-4 px-1 border-b-2 font-medium text-sm transition
                  ${activeTab === 'list'
                                        ? 'border-purple-500 text-purple-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }
                `}
                            >
                                📹 動画一覧
                            </button>
                            <button
                                onClick={() => setActiveTab('upload')}
                                className={`
                  py-4 px-1 border-b-2 font-medium text-sm transition
                  ${activeTab === 'upload'
                                        ? 'border-purple-500 text-purple-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }
                `}
                            >
                                ⬆️ 動画をアップロード
                            </button>
                        </nav>
                    </div>

                    {/* タブコンテンツ */}
                    <div className="p-6">
                        {activeTab === 'list' ? (
                            <div>
                                <h2 className="text-2xl font-bold text-gray-900 mb-6">アップロード済み動画</h2>
                                <VideoListTab videos={videos} loading={loading} error={error} />
                            </div>
                        ) : (
                            <div>
                                <h2 className="text-2xl font-bold text-gray-900 mb-6">動画をアップロード</h2>
                                <UploadTab onUploadSuccess={handleUploadSuccess} />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </main>
    );
}
