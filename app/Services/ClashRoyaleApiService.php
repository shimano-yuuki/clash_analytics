<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ClashRoyaleApiService
{
    private string $baseUrl = 'https://api.clashroyale.com/v1';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.clashroyale.api_key');
        
        if (empty($this->apiKey)) {
            throw new Exception('Clash Royale API key is not configured');
        }
    }

    /**
     * プレイヤータグからプレイヤー情報を取得
     * 
     * @param string $playerTag プレイヤータグ（例: #ABC123）
     * @return array
     */
    public function getPlayer(string $playerTag): array
    {
        $tag = $this->formatTag($playerTag);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/players/{$tag}");

            if ($response->successful()) {
                return $response->json();
            }

            // 詳細なエラー情報を取得
            $statusCode = $response->status();
            $errorBody = $response->body();
            $errorJson = $response->json();
            
            $errorMessage = 'Failed to fetch player data';
            if (isset($errorJson['message'])) {
                $errorMessage = $errorJson['message'];
            } elseif (!empty($errorBody)) {
                $errorMessage = $errorBody;
            }

            Log::error("Clash Royale API Error (getPlayer): Status {$statusCode} - {$errorMessage}");
            throw new Exception("Clash Royale API Error [{$statusCode}]: {$errorMessage}");
        } catch (Exception $e) {
            Log::error('Clash Royale API Error (getPlayer): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * プレイヤーのバトルログを取得
     * 
     * @param string $playerTag プレイヤータグ
     * @return array
     */
    public function getPlayerBattles(string $playerTag): array
    {
        $tag = $this->formatTag($playerTag);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/players/{$tag}/battlelog");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to fetch battle log: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Clash Royale API Error (getPlayerBattles): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 特定のバトル情報を取得（バトルログから）
     * 
     * @param string $playerTag プレイヤータグ
     * @param int $index バトルログのインデックス（0が最新）
     * @return array|null
     */
    public function getBattleByIndex(string $playerTag, int $index = 0): ?array
    {
        $battles = $this->getPlayerBattles($playerTag);
        
        if (isset($battles[$index])) {
            return $battles[$index];
        }

        return null;
    }

    /**
     * タグをURLエンコード形式に変換（#を%23に変換）
     * 
     * @param string $tag プレイヤータグ
     * @return string
     */
    private function formatTag(string $tag): string
    {
        // #を%23にエンコード
        return str_replace('#', '%23', $tag);
    }

    /**
     * バトル情報からデッキ情報を抽出
     * 
     * @param array $battle バトル情報
     * @param bool $isPlayer プレイヤー側かどうか（true: プレイヤー、false: 相手）
     * @return array
     */
    public function extractDeck(array $battle, bool $isPlayer = true): array
    {
        $team = $isPlayer ? ($battle['team'] ?? []) : ($battle['opponent'] ?? []);
        
        $deck = [];
        if (isset($team[0]['cards'])) {
            foreach ($team[0]['cards'] as $card) {
                $deck[] = [
                    'id' => $card['id'] ?? null,
                    'name' => $card['name'] ?? null,
                    'level' => $card['level'] ?? null,
                    'elixir_cost' => $card['elixir'] ?? null,
                ];
            }
        }

        return $deck;
    }

    /**
     * デッキの平均エリクサーコストを計算
     * 
     * @param array $deck デッキ情報
     * @return float
     */
    public function calculateDeckCost(array $deck): float
    {
        if (empty($deck)) {
            return 0;
        }

        $totalElixir = 0;
        foreach ($deck as $card) {
            $totalElixir += $card['elixir_cost'] ?? 0;
        }

        return round($totalElixir / count($deck), 2);
    }
}
