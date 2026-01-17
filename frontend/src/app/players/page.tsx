'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { playerApi } from '@/lib/api';
import type { Player, Battle } from '@/types';

export default function PlayersPage() {
    const router = useRouter();
    const [playerTag, setPlayerTag] = useState('');
    const [player, setPlayer] = useState<Player | null>(null);
    const [battles, setBattles] = useState<Battle[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleGetPlayer = async () => {
        if (!playerTag.trim()) {
            setError('プレイヤータグを入力してください');
            return;
        }

        setLoading(true);
        setError(null);

        try {
            // プレイヤー情報を取得
            const playerResponse = await playerApi.getPlayer(playerTag);
            setPlayer(playerResponse.data);

            // バトルログを取得
            const battlesResponse = await playerApi.getBattles(playerTag);
            setBattles(battlesResponse.data.data || battlesResponse.data);
        } catch (err: any) {
            const errorMessage = err.response?.data?.error 
                || err.response?.data?.message 
                || err.message 
                || 'プレイヤー情報の取得に失敗しました';
            setError(errorMessage);
            console.error('Player fetch error:', err);
            if (err.response?.data) {
                console.error('Error details:', err.response.data);
            }
        } finally {
            setLoading(false);
        }
    };

    const handleAnalyzeBattle = async (battleIndex: number) => {
        if (!playerTag.trim()) {
            setError('プレイヤータグを入力してください');
            return;
        }

        try {
            const response = await playerApi.getBattleForAnalysis(playerTag, battleIndex);
            // バトル分析ページに遷移
            router.push(`/battles/${battleIndex}?tag=${encodeURIComponent(playerTag)}`);
        } catch (err: any) {
            setError('バトル分析の開始に失敗しました');
            console.error(err);
        }
    };

    return (
        <div className="container mx-auto px-4 py-8 max-w-6xl">
            <h1 className="text-3xl font-bold mb-8">プレイヤー検索</h1>

            {/* プレイヤータグ入力フォーム */}
            <div className="bg-white rounded-lg shadow p-6 mb-8">
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        if (!loading && playerTag.trim()) {
                            handleGetPlayer();
                        }
                    }}
                >
                    <div className="flex gap-4">
                        <div className="flex-1">
                            <label htmlFor="playerTag" className="block text-sm font-medium text-gray-700 mb-2">
                                プレイヤータグ
                            </label>
                            <input
                                type="text"
                                id="playerTag"
                                name="playerTag"
                                value={playerTag}
                                onChange={(e) => {
                                    const value = e.target.value;
                                    setPlayerTag(value);
                                    setError(null);
                                }}
                                placeholder="#ABC123"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                                autoComplete="off"
                                autoFocus
                            />
                            <p className="mt-1 text-sm text-gray-500">
                                プレイヤータグは # から始まる文字列です（例: #ABC123）
                            </p>
                            {playerTag && (
                                <p className="mt-1 text-xs text-gray-400">
                                    入力値: {playerTag}
                                </p>
                            )}
                        </div>
                        <div className="flex items-end">
                            <button
                                type="submit"
                                disabled={loading || !playerTag.trim()}
                                className="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px]"
                            >
                                {loading ? '取得中...' : '検索'}
                            </button>
                        </div>
                    </div>
                </form>

                {error && (
                    <div className="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {error}
                    </div>
                )}
            </div>

            {/* プレイヤー情報 */}
            {player && (
                <div className="bg-white rounded-lg shadow p-6 mb-8">
                    <h2 className="text-2xl font-bold mb-4">プレイヤー情報</h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p className="text-sm text-gray-600">名前</p>
                            <p className="text-lg font-semibold">{player.name}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-600">タグ</p>
                            <p className="text-lg font-semibold">{player.tag}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-600">トロフィー</p>
                            <p className="text-lg font-semibold">{player.trophies}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-600">レベル</p>
                            <p className="text-lg font-semibold">{player.expLevel}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-600">勝利数</p>
                            <p className="text-lg font-semibold">{player.wins}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-600">敗北数</p>
                            <p className="text-lg font-semibold">{player.losses}</p>
                        </div>
                    </div>
                </div>
            )}

            {/* バトルログ */}
            {battles.length > 0 && (
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-2xl font-bold mb-4">バトルログ</h2>
                    <div className="space-y-4">
                        {battles.map((battle) => (
                            <div
                                key={battle.index}
                                className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition"
                            >
                                <div className="flex justify-between items-start mb-3">
                                    <div>
                                        <p className="font-semibold text-lg">{battle.game_mode}</p>
                                        <p className="text-sm text-gray-600">
                                            {new Date(battle.battle_time).toLocaleString('ja-JP')}
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <span
                                            className={`inline-block px-3 py-1 rounded-full text-sm font-medium ${battle.result === 'win'
                                                ? 'bg-green-100 text-green-800'
                                                : battle.result === 'loss'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-gray-100 text-gray-800'
                                                }`}
                                        >
                                            {battle.result === 'win'
                                                ? '勝利'
                                                : battle.result === 'loss'
                                                    ? '敗北'
                                                    : '引き分け'}
                                        </span>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <p className="text-sm font-medium text-gray-700 mb-1">あなたのデッキ</p>
                                        <p className="text-sm text-gray-600">
                                            平均コスト: {battle.player_deck_cost} エリクサー
                                        </p>
                                        <div className="flex flex-wrap gap-1 mt-1">
                                            {battle.player_deck.slice(0, 4).map((card, idx) => (
                                                <span
                                                    key={idx}
                                                    className="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded"
                                                >
                                                    {card.name} ({card.elixir_cost})
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-gray-700 mb-1">相手のデッキ</p>
                                        <p className="text-sm text-gray-600">
                                            平均コスト: {battle.opponent_deck_cost} エリクサー
                                        </p>
                                        <div className="flex flex-wrap gap-1 mt-1">
                                            {battle.opponent_deck.slice(0, 4).map((card, idx) => (
                                                <span
                                                    key={idx}
                                                    className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded"
                                                >
                                                    {card.name} ({card.elixir_cost})
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                <button
                                    onClick={() => handleAnalyzeBattle(battle.index)}
                                    className="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition"
                                >
                                    このバトルを分析
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
