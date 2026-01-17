<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * ダッシュボード統計情報を取得
     */
    public function getStats(): JsonResponse
    {
        // TODO: 実際のデータを取得
        $stats = [
            'total_videos' => 0,
            'total_reports' => 0,
            'processing_videos' => 0,
            'recent_videos' => [],
        ];

        return response()->json($stats);
    }
}
