@props([
    'data',
    'maxValue' => null,
    'ariaLabel' => 'Grafik kunjungan',
])

@php
    $items = collect($data)->values();
    $itemCount = $items->count();
    $chartWidth = max(640, ($itemCount * 30) + 56);
    $plotLeft = 48;
    $plotRight = $chartWidth - 16;
    $plotTop = 12;
    $plotHeight = 112;
    $baseline = $plotTop + $plotHeight;
    $valueMax = max(1, (int) ($maxValue ?? $items->max('value') ?? 0));
    $bandWidth = ($plotRight - $plotLeft) / max(1, $itemCount);
    $barWidth = min(44, max(8, $bandWidth * 0.56));
    $labelEvery = max(1, (int) ceil($itemCount / 20));
    $gridValues = collect([0, (int) ceil($valueMax / 2), $valueMax])->unique()->values();
    $gradientId = 'visit-chart-gradient-'.\Illuminate\Support\Str::uuid();
@endphp

<figure class="w-full" aria-label="{{ $ariaLabel }}">
    <div class="overflow-x-auto rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-bank-blue" tabindex="0" aria-label="Grafik dapat digeser mendatar untuk melihat semua tanggal">
        <svg
            class="mx-auto block"
            width="{{ $chartWidth }}"
            height="176"
            viewBox="0 0 {{ $chartWidth }} 176"
            role="img"
            aria-label="{{ $ariaLabel }}. Sumbu vertikal menunjukkan jumlah kunjungan."
            xmlns="http://www.w3.org/2000/svg"
        >
            <defs>
                <linearGradient id="{{ $gradientId }}" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#818cf8" />
                    <stop offset="100%" stop-color="#1814F3" />
                </linearGradient>
            </defs>

            @foreach($gridValues as $tick)
                @php
                    $y = $baseline - (($tick / $valueMax) * $plotHeight);
                @endphp
                <line x1="{{ $plotLeft }}" y1="{{ $y }}" x2="{{ $plotRight }}" y2="{{ $y }}" stroke="#E6EFF5" stroke-dasharray="{{ $tick === 0 ? '0' : '4 5' }}" />
                <text x="{{ $plotLeft - 10 }}" y="{{ $y + 4 }}" text-anchor="end" fill="#718EBF" font-size="11">{{ $tick }}</text>
            @endforeach

            @foreach($items as $index => $item)
                @php
                    $value = max(0, (int) ($item['value'] ?? 0));
                    $barHeight = $value === 0 ? 2 : max(3, ($value / $valueMax) * $plotHeight);
                    $x = $plotLeft + ($index * $bandWidth) + (($bandWidth - $barWidth) / 2);
                    $y = $baseline - $barHeight;
                    $label = (string) ($item['label'] ?? '');
                    $tooltip = trim((string) ($item['date'] ?? $label)).': '.$value.' kunjungan';
                @endphp
                <g>
                    <title>{{ $tooltip }}</title>
                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="{{ min(10, $barWidth / 2) }}" fill="url(#{{ $gradientId }})" />
                    @if($itemCount <= 14)
                        <text x="{{ $x + ($barWidth / 2) }}" y="{{ max(12, $y - 7) }}" text-anchor="middle" fill="#343C6A" font-size="11" font-weight="600">{{ $value }}</text>
                    @endif
                    @if($index % $labelEvery === 0 || $index === $itemCount - 1)
                        <text x="{{ $plotLeft + ($index * $bandWidth) + ($bandWidth / 2) }}" y="{{ $baseline + 22 }}" text-anchor="middle" fill="#718EBF" font-size="11">{{ $label }}</text>
                    @endif
                </g>
            @endforeach
        </svg>
    </div>
</figure>
