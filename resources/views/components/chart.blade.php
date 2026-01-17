@props(['id', 'type' => 'line', 'data', 'options' => []])

<div class="bg-white p-6 rounded-lg shadow-sm">
    <canvas id="{{ $id }}"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id }}');
    if (ctx) {
        new Chart(ctx, {
            type: '{{ $type }}',
            data: @json($data ?? []),
            options: @json($options ?? {})
        });
    }
});
</script>
@endpush
