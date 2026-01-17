@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Dashboard') }}</h2>
                <p class="text-gray-600">{{ __('Welcome to Clash Royale Analytics Platform') }}</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-stat-card 
                title="{{ __('Total Videos') }}" 
                value="{{ $totalVideos ?? 0 }}" 
                icon="📹"
                color="blue"
            />
            <x-stat-card 
                title="{{ __('Completed Analyses') }}" 
                value="{{ $completedAnalyses ?? 0 }}" 
                icon="✅"
                color="green"
            />
            <x-stat-card 
                title="{{ __('Reports Generated') }}" 
                value="{{ $reportsCount ?? 0 }}" 
                icon="📊"
                color="purple"
            />
        </div>

        <!-- Recent Videos -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Recent Videos') }}</h3>
                @if(isset($recentVideos) && $recentVideos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('File Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Uploaded') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentVideos as $video)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ Str::limit($video->file_name ?? 'N/A', 30) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'uploaded' => 'bg-yellow-100 text-yellow-800',
                                                    'analyzing' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                ];
                                                $status = $video->status ?? 'uploaded';
                                                $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $video->created_at ? $video->created_at->format('Y/m/d H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('videos.show', $video->id ?? 1) }}" class="text-purple-600 hover:text-purple-900">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">{{ __('No videos uploaded yet.') }}</p>
                    <div class="text-center">
                        <a href="{{ route('videos.upload') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition">
                            {{ __('Upload Your First Video') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('videos.upload') }}" class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <span class="mr-2">📤</span>
                        {{ __('Upload Video') }}
                    </a>
                    <a href="{{ route('videos.index') }}" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <span class="mr-2">📹</span>
                        {{ __('View All Videos') }}
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <span class="mr-2">📊</span>
                        {{ __('View Reports') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
