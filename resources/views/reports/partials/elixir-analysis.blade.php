@if($analysis)
<div class="space-y-6">
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('Elixir Management Analysis') }}</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Average Elixir Usage') }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($analysis->average_elixir_usage ?? 0, 1) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Elixir Efficiency') }}</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format(($analysis->elixir_efficiency ?? 0) * 100, 1) }}%</p>
            </div>
        </div>

        @if(isset($analysis->elixir_waste_count) && $analysis->elixir_waste_count > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h5 class="text-sm font-medium text-yellow-800">
                            {{ __('Elixir Waste Detected') }}
                        </h5>
                        <p class="mt-1 text-sm text-yellow-700">
                            {{ __('Number of waste events') }}: {{ $analysis->elixir_waste_count }}
                        </p>
                    </div>
                </div>
            </div>

            @if(isset($analysis->elixir_waste_timestamps) && count($analysis->elixir_waste_timestamps) > 0)
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Waste Timestamps') }}:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($analysis->elixir_waste_timestamps as $timestamp)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">{{ $timestamp }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if(isset($analysis->overload_count) && $analysis->overload_count > 0)
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h5 class="text-sm font-medium text-green-800">
                            {{ __('Overload Status') }}
                        </h5>
                        <p class="mt-1 text-sm text-green-700">
                            {{ __('Number of overload events') }}: {{ $analysis->overload_count }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@else
<p class="text-gray-500 text-center py-8">{{ __('Elixir analysis data is not available.') }}</p>
@endif
