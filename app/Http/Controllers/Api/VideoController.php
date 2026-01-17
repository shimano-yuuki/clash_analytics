<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * 動画一覧を取得
     */
    public function index(): JsonResponse
    {
        // TODO: 実際のデータを取得
        $videos = [];

        return response()->json([
            'data' => $videos,
            'message' => 'Videos retrieved successfully.',
        ]);
    }

    /**
     * 動画をアップロード
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:524288', // 500MB
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // TODO: 実際のアップロード処理
        // $file = $request->file('video');
        // $title = $request->input('title');

        // 一時的なレスポンス
        $video = [
            'id' => 1,
            'title' => $request->input('title', $request->file('video')->getClientOriginalName()),
            'filename' => $request->file('video')->getClientOriginalName(),
            'file_path' => 'videos/temp.mp4',
            'file_size' => $request->file('video')->getSize(),
            'status' => 'pending',
            'uploaded_at' => now()->toISOString(),
        ];

        return response()->json([
            'data' => $video,
            'message' => 'Video uploaded successfully. Analysis will start shortly.',
        ], 201);
    }

    /**
     * 動画詳細を取得
     */
    public function show(string $id): JsonResponse
    {
        // TODO: 実際のデータを取得
        $video = [
            'id' => (int) $id,
            'title' => 'Sample Video',
            'filename' => 'sample_video.mp4',
            'file_path' => 'videos/sample.mp4',
            'file_size' => 1024000,
            'duration' => 180,
            'status' => 'completed',
            'uploaded_at' => now()->subDays(1)->toISOString(),
        ];

        $analysis = null;
        if ($video['status'] === 'completed') {
            // TODO: 解析結果を取得
            $analysis = [
                'id' => 1,
                'video_id' => (int) $id,
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
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ];
        }

        $video['analysis'] = $analysis;

        return response()->json([
            'data' => $video,
            'message' => 'Video retrieved successfully.',
        ]);
    }

    /**
     * 動画解析を開始
     */
    public function analyze(string $id): JsonResponse
    {
        // TODO: 実際の解析開始処理

        return response()->json([
            'message' => 'Analysis started. Please check back later.',
            'video_id' => (int) $id,
            'status' => 'processing',
        ]);
    }

    /**
     * 動画を削除
     */
    public function destroy(string $id): JsonResponse
    {
        // TODO: 実際の削除処理

        return response()->json([
            'message' => 'Video deleted successfully.',
            'video_id' => (int) $id,
        ]);
    }
}
