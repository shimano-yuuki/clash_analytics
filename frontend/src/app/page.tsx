export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
        <h1 className="text-4xl font-bold text-center mb-8">
          🎮 Clash Royale Analytics
        </h1>
        <p className="text-center text-gray-600 mb-8">
          プレイ動画をアップロードして、AI解析でスキル向上を目指しましょう
        </p>
        <div className="flex justify-center space-x-4">
          <a
            href="/dashboard"
            className="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
          >
            ダッシュボード
          </a>
          <a
            href="/videos"
            className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            動画一覧
          </a>
        </div>
      </div>
    </main>
  )
}
