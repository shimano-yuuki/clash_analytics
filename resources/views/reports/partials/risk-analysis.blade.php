@if($analysis)
<div class="space-y-6">
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('Risk Analysis') }}</h4>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 mb-1">{{ __('Overall Risk Score') }}</p>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format(($analysis->risk_score ?? 0) * 100, 1) }}%</p>
            <p class="text-xs text-gray-500 mt-2">
                {{ __('Lower score indicates lower risk plays.') }}
            </p>
        </div>

        @if(isset($analysis->risk_summary))
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-sm text-blue-800">{{ $analysis->risk_summary }}</p>
            </div>
        @endif

        @if(isset($analysis->high_risk_plays) && count($analysis->high_risk_plays) > 0)
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-3">{{ __('High Risk Plays') }}</h5>
                <div class="space-y-4">
                    @foreach($analysis->high_risk_plays as $play)
                        <div class="bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h6 class="text-sm font-medium text-red-800">
                                            {{ $play->timestamp ?? 'N/A' }} - {{ $play->play_description ?? 'N/A' }}
                                        </h6>
                                        @if(isset($play->risk_level))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ ucfirst($play->risk_level) }} Risk
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-red-700">
                                        {{ $play->risk_description ?? 'N/A' }}
                                    </p>
                                    @if(isset($play->recommendation))
                                        <div class="mt-3 bg-white rounded p-3">
                                            <p class="text-xs font-medium text-gray-700 mb-1">{{ __('Recommendation') }}:</p>
                                            <p class="text-sm text-gray-600">{{ $play->recommendation }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-800">
                    {{ __('No high-risk plays detected. Great job!') }}
                </p>
            </div>
        @endif
    </div>
</div>
@else
<p class="text-gray-500 text-center py-8">{{ __('Risk analysis data is not available.') }}</p>
@endif
