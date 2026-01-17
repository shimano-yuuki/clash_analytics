<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // TODO: 実際のデータを取得
        $reports = collect([]);

        return view('reports.index', compact('reports'));
    }

    public function show($videoId)
    {
        // TODO: 実際のデータを取得
        $video = (object)[
            'id' => $videoId,
            'file_name' => 'sample_video.mp4',
        ];

        $report = (object)[
            'id' => 1,
            'video_id' => $videoId,
            'elixir_efficiency' => 0.75,
            'cost_balance_score' => 0.80,
            'risk_score' => 0.65,
            'elixir_analysis' => (object)[
                'average_elixir_usage' => 7.5,
                'elixir_waste_count' => 3,
                'elixir_efficiency' => 0.75,
            ],
            'cost_analysis' => (object)[
                'average_deck_cost' => 3.8,
                'cost_balance_score' => 0.80,
            ],
            'timing_analysis' => (object)[
                'attack_timings' => [],
            ],
            'risk_analysis' => (object)[
                'high_risk_plays' => [],
                'risk_score' => 0.65,
            ],
            'recommendations' => [],
        ];

        return view('reports.show', compact('video', 'report'));
    }
}
