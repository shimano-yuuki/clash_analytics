@extends('layouts.app')

@section('title', __('Video Details'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Video Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $video->file_name ?? 'Video' }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Uploaded') }}: {{ $video->created_at ? $video->created_at->format('Y/m/d H:i') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        @php
                            $status = $video->status ?? 'uploaded';
                            $statusColors = [
                                'uploaded' => 'bg-yellow-100 text-yellow-800',
                                'analyzing' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                            ];
                            $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $color }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                </div>

                @if($status === 'uploaded')
                    <div class="mt-4">
                        <form action="{{ route('videos.analyze', $video->id ?? 1) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                {{ __('Start Analysis') }}
                            </button>
                        </form>
                    </div>
                @endif

                @if($status === 'analyzing')
                    <div class="mt-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <p class="text-sm text-blue-800">
                                {{ __('Analysis in progress. This may take several minutes...') }}
                            </p>
                            <div class="mt-2 bg-blue-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                     style="width: {{ $analysis->progress ?? 0 }}%">
                                </div>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">{{ $analysis->progress ?? 0 }}%</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($status === 'completed' && isset($video->file_path))
            <!-- Video Player -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Video Player') }}</h3>
                    <x-video-player 
                        :videoUrl="asset('storage/' . $video->file_path)" 
                        :videoId="$video->id ?? 1" 
                    />
                </div>
            </div>

            <!-- Analysis Results -->
            @if(isset($analysis))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Analysis Results') }}</h3>
                        
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <x-stat-card 
                                title="{{ __('Elixir Efficiency') }}" 
                                :value="($analysis->elixir_analysis->elixir_efficiency ?? 0) * 100" 
                                icon="⚗️"
                                color="blue"
                            />
                            <x-stat-card 
                                title="{{ __('Cost Balance') }}" 
                                :value="($analysis->cost_analysis->cost_balance_score ?? 0) * 100" 
                                icon="💰"
                                color="green"
                            />
                            <x-stat-card 
                                title="{{ __('Risk Score') }}" 
                                :value="($analysis->risk_analysis->risk_score ?? 0) * 100" 
                                icon="⚠️"
                                color="yellow"
                            />
                        </div>

                        <!-- Analysis Tabs -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button onclick="showTab('elixir')" class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 border-purple-500 font-medium text-sm text-purple-600">
                                    {{ __('Elixir Analysis') }}
                                </button>
                                <button onclick="showTab('cost')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    {{ __('Cost Analysis') }}
                                </button>
                                <button onclick="showTab('timing')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    {{ __('Timing Analysis') }}
                                </button>
                                <button onclick="showTab('risk')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    {{ __('Risk Analysis') }}
                                </button>
                                <button onclick="showTab('timeline')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    {{ __('Timeline') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Contents -->
                        <div id="tab-elixir" class="tab-content">
                            @include('reports.partials.elixir-analysis', ['analysis' => $analysis->elixir_analysis ?? null])
                        </div>
                        <div id="tab-cost" class="tab-content hidden">
                            @include('reports.partials.cost-analysis', ['analysis' => $analysis->cost_analysis ?? null])
                        </div>
                        <div id="tab-timing" class="tab-content hidden">
                            @include('reports.partials.timing-analysis', ['analysis' => $analysis->timing_analysis ?? null])
                        </div>
                        <div id="tab-risk" class="tab-content hidden">
                            @include('reports.partials.risk-analysis', ['analysis' => $analysis->risk_analysis ?? null])
                        </div>
                        <div id="tab-timeline" class="tab-content hidden">
                            @include('reports.partials.timeline', ['timeline' => $analysis->timeline_data ?? []])
                        </div>

                        <!-- View Full Report Link -->
                        <div class="mt-6 text-center">
                            <a href="{{ route('reports.show', $video->id ?? 1) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                {{ __('View Full Report') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Add active class to clicked button
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-purple-500', 'text-purple-600');
}
</script>
@endpush
