'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { videoApi } from '@/lib/api';

export default function VideoUploadPage() {
  const router = useRouter();
  const [file, setFile] = useState<File | null>(null);
  const [title, setTitle] = useState('');
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setFile(e.target.files[0]);
      if (!title) {
        setTitle(e.target.files[0].name);
      }
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!file) {
      setError('ファイルを選択してください');
      return;
    }

    setUploading(true);
    setError(null);

    try {
      const formData = new FormData();
      formData.append('video', file);
      if (title) {
        formData.append('title', title);
      }

      const response = await videoApi.upload(formData);
      router.push(`/videos/${response.data.id}`);
    } catch (err: any) {
      setError(
        err.response?.data?.message || 'アップロードに失敗しました'
      );
      console.error(err);
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="container mx-auto px-4 py-8 max-w-2xl">
      <h1 className="text-3xl font-bold mb-8">動画をアップロード</h1>

      <form onSubmit={handleSubmit} className="bg-white rounded-lg shadow p-6">
        <div className="mb-6">
          <label
            htmlFor="title"
            className="block text-sm font-medium text-gray-700 mb-2"
          >
            タイトル (任意)
          </label>
          <input
            type="text"
            id="title"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="動画のタイトルを入力"
          />
        </div>

        <div className="mb-6">
          <label
            htmlFor="video"
            className="block text-sm font-medium text-gray-700 mb-2"
          >
            動画ファイル
          </label>
          <input
            type="file"
            id="video"
            accept="video/*"
            onChange={handleFileChange}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
          />
          {file && (
            <p className="mt-2 text-sm text-gray-600">
              選択されたファイル: {file.name} ({(file.size / 1024 / 1024).toFixed(2)} MB)
            </p>
          )}
        </div>

        {error && (
          <div className="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {error}
          </div>
        )}

        <div className="flex space-x-4">
          <button
            type="submit"
            disabled={uploading || !file}
            className="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {uploading ? 'アップロード中...' : 'アップロード'}
          </button>
          <button
            type="button"
            onClick={() => router.back()}
            className="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
          >
            キャンセル
          </button>
        </div>
      </form>
    </div>
  );
}
