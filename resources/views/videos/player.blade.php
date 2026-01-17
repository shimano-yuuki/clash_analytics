@props(['videoUrl', 'videoId'])

<div class="bg-black rounded-lg overflow-hidden mb-6">
    <video 
        id="video-player-{{ $videoId ?? 'default' }}" 
        class="w-full" 
        controls 
        preload="metadata"
        data-video-id="{{ $videoId ?? '' }}"
    >
        <source src="{{ $videoUrl }}" type="video/mp4">
        {{ __('Your browser does not support the video tag.') }}
    </video>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoId = {{ $videoId ?? 'null' }};
    const player = document.getElementById('video-player-' + (videoId || 'default'));
    
    if (player && window.timelineData) {
        // タイムラインと同期する処理
        player.addEventListener('timeupdate', function() {
            const currentTime = Math.floor(player.currentTime);
            // タイムラインの該当箇所をハイライトする処理
            highlightTimelineAt(currentTime);
        });
    }
});

function highlightTimelineAt(seconds) {
    // タイムライン要素を取得してハイライト
    const timelineItems = document.querySelectorAll('[data-timestamp]');
    timelineItems.forEach(item => {
        const timestamp = parseInt(item.dataset.timestamp);
        if (Math.abs(timestamp - seconds) < 1) {
            item.classList.add('bg-purple-100', 'border-purple-500');
        } else {
            item.classList.remove('bg-purple-100', 'border-purple-500');
        }
    });
}
</script>
@endpush
