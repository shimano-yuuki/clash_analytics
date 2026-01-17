@if($analysis)
<div class="space-y-6">
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('Deck Cost Analysis') }}</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Average Deck Cost') }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($analysis->average_deck_cost ?? 0, 2) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('High Cost Cards') }}</p>
                <p class="text-2xl font-bold text-orange-600">{{ $analysis->high_cost_card_usage ?? 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Low Cost Cards') }}</p>
                <p class="text-2xl font-bold text-green-600">{{ $analysis->low_cost_card_usage ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 mb-1">{{ __('Cost Balance Score') }}</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format(($analysis->cost_balance_score ?? 0) * 100, 1) }}%</p>
            <p class="text-xs text-gray-500 mt-2">
                {{ __('Higher score indicates better balance between high and low cost cards.') }}
            </p>
        </div>

        @if(isset($analysis->card_usage_distribution))
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-3">{{ __('Card Usage Distribution') }}</h5>
                <div class="space-y-2">
                    @foreach($analysis->card_usage_distribution as $range => $count)
                        <div class="flex items-center">
                            <span class="w-24 text-sm text-gray-600">{{ $range }} コスト</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-4 mr-2">
                                <div class="bg-purple-600 h-4 rounded-full" style="width: {{ ($count / max(array_values((array)$analysis->card_usage_distribution))) * 100 }}%"></div>
                            </div>
                            <span class="w-12 text-sm text-gray-900 text-right">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@else
<p class="text-gray-500 text-center py-8">{{ __('Cost analysis data is not available.') }}</p>
@endif
