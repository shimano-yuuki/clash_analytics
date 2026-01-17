<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /**
     * レポート一覧を取得
     */
    public function index(): JsonResponse
    {
        // TODO: 実際のデータを取得
        $reports = [];

        return response()->json([
            'data' => $reports,
            'message' => 'Reports retrieved successfully.',
        ]);
    }

    /**
     * レポート詳細を取得
     */
    public function show(string $videoId): JsonResponse
    {
        // TODO: 実際のデータを取得
        $video = [
            'id' => (int) $videoId,
            'title' => 'Sample Video',
            'filename' => 'sample_video.mp4',
        ];

        $report = [
            'id' => 1,
            'video_id' => (int) $videoId,
            'video' => $video,
            'summary' => 'This is a sample report summary.',
            'generated_at' => now()->toISOString(),
            'analysis' => [
                'elixir_analysis' => [
                    'average_elixir' => 7.5,
                    'elixir_efficiency' => 75,
                    'peak_usage_times' => [],
                    'recommendations' => [],
                ],
                'cost_analysis' => [
                    'deck_cost' => 3.8,
                    'average_card_cost' => 3.8,
                    'cost_distribution' => [],
                    'recommendations' => [],
                ],
                'timing_analysis' => [
                    'attack_timings' => [],
                    'defensive_timings' => [],
                    'optimal_moments' => [],
                    'recommendations' => [],
                ],
                'risk_analysis' => [
                    'risk_factors' => [],
                    'overall_risk_score' => 65,
                    'recommendations' => [],
                ],
            ],
        ];

        return response()->json([
            'data' => $report,
            'message' => 'Report retrieved successfully.',
        ]);
    }
}
