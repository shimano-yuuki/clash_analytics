<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClashRoyaleApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class PlayerController extends Controller
{
    private ClashRoyaleApiService $clashRoyaleApi;

    public function __construct(ClashRoyaleApiService $clashRoyaleApi)
    {
        $this->clashRoyaleApi = $clashRoyaleApi;
    }

    /**
     * プレイヤー情報を取得
     */
    public function getPlayer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string|regex:/^#?[A-Z0-9]+$/i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = $request->input('tag');
            $playerData = $this->clashRoyaleApi->getPlayer($tag);

            return response()->json([
                'data' => $playerData,
                'message' => 'Player data retrieved successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch player data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * プレイヤーのバトルログを取得
     */
    public function getBattles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string|regex:/^#?[A-Z0-9]+$/i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = $request->input('tag');
            $battles = $this->clashRoyaleApi->getPlayerBattles($tag);

            // バトル情報を整形
            $formattedBattles = [];
            foreach ($battles as $index => $battle) {
                $playerDeck = $this->clashRoyaleApi->extractDeck($battle, true);
                $opponentDeck = $this->clashRoyaleApi->extractDeck($battle, false);
                
                $formattedBattles[] = [
                    'index' => $index,
                    'battle_time' => $battle['battleTime'] ?? null,
                    'game_mode' => $battle['gameMode']['name'] ?? null,
                    'type' => $battle['type'] ?? null,
                    'result' => $this->determineResult($battle),
                    'player_deck' => $playerDeck,
                    'player_deck_cost' => $this->clashRoyaleApi->calculateDeckCost($playerDeck),
                    'opponent_deck' => $opponentDeck,
                    'opponent_deck_cost' => $this->clashRoyaleApi->calculateDeckCost($opponentDeck),
                    'raw_data' => $battle, // 分析用に生データも含める
                ];
            }

            return response()->json([
                'data' => $formattedBattles,
                'message' => 'Battle log retrieved successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch battle log',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 特定のバトルを取得して分析用データを作成
     */
    public function getBattleForAnalysis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string|regex:/^#?[A-Z0-9]+$/i',
            'battle_index' => 'nullable|integer|min:0|max:24', // 最新25件まで
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = $request->input('tag');
            $battleIndex = $request->input('battle_index', 0);
            
            $battle = $this->clashRoyaleApi->getBattleByIndex($tag, $battleIndex);

            if (!$battle) {
                return response()->json([
                    'message' => 'Battle not found',
                ], 404);
            }

            $playerDeck = $this->clashRoyaleApi->extractDeck($battle, true);
            $opponentDeck = $this->clashRoyaleApi->extractDeck($battle, false);

            $battleData = [
                'battle_time' => $battle['battleTime'] ?? null,
                'game_mode' => $battle['gameMode']['name'] ?? null,
                'type' => $battle['type'] ?? null,
                'result' => $this->determineResult($battle),
                'player_deck' => $playerDeck,
                'player_deck_cost' => $this->clashRoyaleApi->calculateDeckCost($playerDeck),
                'opponent_deck' => $opponentDeck,
                'opponent_deck_cost' => $this->clashRoyaleApi->calculateDeckCost($opponentDeck),
                'battle_raw_data' => $battle,
            ];

            // TODO: このバトルデータをデータベースに保存して分析を実行

            return response()->json([
                'data' => $battleData,
                'message' => 'Battle data retrieved successfully. Ready for analysis.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch battle data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * バトルの結果を判定（勝ち/負け/引き分け）
     */
    private function determineResult(array $battle): string
    {
        if (!isset($battle['team'][0]) || !isset($battle['opponent'][0])) {
            return 'unknown';
        }

        $playerCrowns = $battle['team'][0]['crowns'] ?? 0;
        $opponentCrowns = $battle['opponent'][0]['crowns'] ?? 0;

        if ($playerCrowns > $opponentCrowns) {
            return 'win';
        } elseif ($playerCrowns < $opponentCrowns) {
            return 'loss';
        } else {
            return 'draw';
        }
    }
}
