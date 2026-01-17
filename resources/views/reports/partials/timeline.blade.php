@if(isset($timeline) && count($timeline) > 0)
<div class="space-y-6">
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('Play Timeline') }}</h4>
        
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-purple-200"></div>
            
            <!-- Timeline Items -->
            <div class="space-y-6">
                @foreach($timeline as $item)
                    <div class="relative flex items-start" data-timestamp="{{ isset($item->timestamp) ? str_replace(':', '', $item->timestamp) : '' }}">
                        <!-- Timeline Dot -->
                        <div class="absolute left-6 flex h-4 w-4 items-center justify-center">
                            <div class="h-3 w-3 rounded-full bg-purple-600 ring-4 ring-white"></div>
                        </div>
                        
                        <!-- Content -->
                        <div class="ml-12 flex-1 rounded-lg bg-gray-50 p-4 hover:bg-purple-50 transition cursor-pointer" onclick="seekToTime('{{ $item->timestamp ?? '' }}')">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-purple-600">{{ $item->timestamp ?? 'N/A' }}</span>
                                @if(isset($item->risk_level))
                                    @php
                                        $riskColors = [
                                            'low' => 'bg-green-100 text-green-800',
                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                            'high' => 'bg-red-100 text-red-800',
                                        ];
                                        $riskColor = $riskColors[$item->risk_level] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $riskColor }}">
                                        {{ ucfirst($item->risk_level) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-900 mb-2">{{ $item->description ?? 'N/A' }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                @if(isset($item->elixir))
                                    <span class="mr-4">⚗️ {{ $item->elixir }} {{ __('Elixir') }}</span>
                                @endif
                                @if(isset($item->cards_played) && count($item->cards_played) > 0)
                                    <span>🃏 {{ implode(', ', $item->cards_played) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@else
<p class="text-gray-500 text-center py-8">{{ __('Timeline data is not available.') }}</p>
@endif

@push('scripts')
<script>
function seekToTime(timestamp) {
    // Convert timestamp (MM:SS) to seconds
    const parts = timestamp.split(':');
    if (parts.length === 2) {
        const seconds = parseInt(parts[0]) * 60 + parseInt(parts[1]);
        const videoPlayer = document.querySelector('video');
        if (videoPlayer) {
            videoPlayer.currentTime = seconds;
            videoPlayer.play();
        }
    }
}

// Make timeline data available globally for video player sync
@if(isset($timeline))
window.timelineData = @json($timeline);
@endif
</script>
@endpush
