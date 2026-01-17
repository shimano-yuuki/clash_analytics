import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import Link from 'next/link'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Clash Royale Analytics',
  description: 'Clash Royaleプレイ動画を解析して詳細なレポートを提供',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="ja">
      <body className={inter.className}>
        <nav className="bg-white shadow-lg">
          <div className="container mx-auto px-4">
            <div className="flex justify-between items-center py-4">
              <Link href="/" className="text-2xl font-bold text-purple-600">
                🎮 Clash Royale Analytics
              </Link>
              <div className="space-x-4">
                <Link
                  href="/dashboard"
                  className="text-gray-700 hover:text-purple-600 transition"
                >
                  ダッシュボード
                </Link>
                <Link
                  href="/videos"
                  className="text-gray-700 hover:text-purple-600 transition"
                >
                  動画一覧
                </Link>
                <Link
                  href="/players"
                  className="text-gray-700 hover:text-purple-600 transition"
                >
                  プレイヤー検索
                </Link>
                <Link
                  href="/videos/upload"
                  className="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition"
                >
                  アップロード
                </Link>
              </div>
            </div>
          </div>
        </nav>
        <main>{children}</main>
        <footer className="bg-gray-100 mt-12 py-6">
          <div className="container mx-auto px-4 text-center text-gray-600">
            <p>&copy; 2026 Clash Royale Analytics. All rights reserved.</p>
          </div>
        </footer>
      </body>
    </html>
  )
}
