@if($analysis && isset($analysis->attack_timings) && count($analysis->attack_timings) > 0)
<div class="space-y-6">
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('Attack Timing Analysis') }}</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Optimal Timings') }}</p>
                <p class="text-2xl font-bold text-green-600">{{ $analysis->optimal_timing_count ?? 0 }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">{{ __('Poor Timings') }}</p>
                <p class="text-2xl font-bold text-red-600">{{ $analysis->poor_timing_count ?? 0 }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Timestamp') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Elixir Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Outcome') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Risk Level') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($analysis->attack_timings as $timing)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $timing->timestamp ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst($timing->elixir_status ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $outcome = $timing->outcome ?? 'unknown';
                                    $outcomeColor = $outcome === 'success' ? 'text-green-600' : ($outcome === 'failed' ? 'text-red-600' : 'text-gray-600');
                                @endphp
                                <span class="text-sm font-medium {{ $outcomeColor }}">
                                    {{ ucfirst($outcome) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $riskLevel = $timing->risk_level ?? 'medium';
                                    $riskColors = [
                                        'low' => 'bg-green-100 text-green-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'high' => 'bg-red-100 text-red-800',
                                    ];
                                    $riskColor = $riskColors[$riskLevel] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $riskColor }}">
                                    {{ ucfirst($riskLevel) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $timing->description ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<p class="text-gray-500 text-center py-8">{{ __('Timing analysis data is not available.') }}</p>
@endif
