<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: 実際のデータを取得
        $totalVideos = 0;
        $completedAnalyses = 0;
        $reportsCount = 0;
        $recentVideos = collect([]);

        return view('dashboard.index', compact('totalVideos', 'completedAnalyses', 'reportsCount', 'recentVideos'));
    }
}
