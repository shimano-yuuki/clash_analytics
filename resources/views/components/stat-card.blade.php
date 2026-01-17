@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'purple' => 'bg-purple-500',
        'yellow' => 'bg-yellow-500',
        'red' => 'bg-red-500',
    ];
    $colorClass = $colorClasses[$color] ?? 'bg-blue-500';
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 {{ $colorClass }} rounded-md p-3">
                <span class="text-2xl">{{ $icon ?? '📊' }}</span>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="text-2xl font-semibold text-gray-900">
                        {{ number_format($value ?? 0) }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
