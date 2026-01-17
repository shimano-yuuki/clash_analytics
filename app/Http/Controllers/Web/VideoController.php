<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        // TODO: 実際のデータを取得
        $videos = collect([]);

        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        return view('videos.upload');
    }

    public function store(Request $request)
    {
        // TODO: 動画アップロード処理
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:524288', // 500MB
            'title' => 'nullable|string|max:255',
        ]);

        // 実際の処理は後で実装
        return redirect()->route('videos.index')->with('success', __('Video uploaded successfully. Analysis will start shortly.'));
    }

    public function show($id)
    {
        // TODO: 実際のデータを取得
        $video = (object)[
            'id' => $id,
            'file_name' => 'sample_video.mp4',
            'file_path' => 'videos/sample.mp4',
            'status' => 'completed',
            'created_at' => now(),
        ];

        $analysis = null;
        if ($video->status === 'completed') {
            // TODO: 解析結果を取得
            $analysis = (object)[
                'elixir_analysis' => (object)[
                    'elixir_efficiency' => 0.75,
                    'elixir_waste_count' => 3,
                ],
                'cost_analysis' => (object)[
                    'cost_balance_score' => 0.80,
                ],
                'risk_analysis' => (object)[
                    'risk_score' => 0.65,
                ],
                'timeline_data' => [],
            ];
        }

        return view('videos.show', compact('video', 'analysis'));
    }

    public function analyze($id)
    {
        // TODO: 解析開始処理
        return redirect()->route('videos.show', $id)->with('success', __('Analysis started. Please check back later.'));
    }

    public function destroy($id)
    {
        // TODO: 動画削除処理
        return redirect()->route('videos.index')->with('success', __('Video deleted successfully.'));
    }
}
