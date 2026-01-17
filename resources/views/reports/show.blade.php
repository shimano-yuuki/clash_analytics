@extends('layouts.app')

@section('title', __('Report Details'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Report Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Analysis Report') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Video') }}: {{ $video->file_name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ __('Generated') }}: {{ $report->created_at ? $report->created_at->format('Y/m/d H:i') : 'N/A' }}
                        </p>
                    </div>
                    <a href="{{ route('videos.show', $video->id ?? 1) }}" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                        {{ __('Back to Video') }}
                    </a>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-stat-card 
                        title="{{ __('Elixir Efficiency') }}" 
                        :value="($report->elixir_efficiency ?? 0) * 100" 
                        icon="⚗️"
                        color="blue"
                    />
                    <x-stat-card 
                        title="{{ __('Cost Balance') }}" 
                        :value="($report->cost_balance_score ?? 0) * 100" 
                        icon="💰"
                        color="green"
                    />
                    <x-stat-card 
                        title="{{ __('Risk Score') }}" 
                        :value="($report->risk_score ?? 0) * 100" 
                        icon="⚠️"
                        color="yellow"
                    />
                </div>
            </div>
        </div>

        <!-- Analysis Sections -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Detailed Analysis') }}</h3>

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
                    @include('reports.partials.elixir-analysis', ['analysis' => $report->elixir_analysis ?? null])
                </div>
                <div id="tab-cost" class="tab-content hidden">
                    @include('reports.partials.cost-analysis', ['analysis' => $report->cost_analysis ?? null])
                </div>
                <div id="tab-timing" class="tab-content hidden">
                    @include('reports.partials.timing-analysis', ['analysis' => $report->timing_analysis ?? null])
                </div>
                <div id="tab-risk" class="tab-content hidden">
                    @include('reports.partials.risk-analysis', ['analysis' => $report->risk_analysis ?? null])
                </div>
                <div id="tab-timeline" class="tab-content hidden">
                    @include('reports.partials.timeline', ['timeline' => $report->timeline_data ?? []])
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        @if(isset($report->recommendations) && count($report->recommendations) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Recommendations') }}</h3>
                    <div class="space-y-4">
                        @foreach($report->recommendations as $recommendation)
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">
                                            {{ $recommendation->category ?? __('General') }} - 
                                            <span class="capitalize">{{ $recommendation->priority ?? 'medium' }}</span>
                                        </h4>
                                        <p class="mt-2 text-sm text-blue-700">
                                            {{ $recommendation->description ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
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
