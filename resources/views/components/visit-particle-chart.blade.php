@props([
    'data',
    'ariaLabel' => 'Jumlah kunjungan dalam grafik partikel',
])

@php
    $items = collect($data)->values();
    $itemCount = max(1, $items->count());
    $maxValue = max(1, (int) $items->max('value'));
    $total = (int) $items->sum('value');
    $chartWidth = 760;
    $left = 64;
    $step = ($chartWidth - ($left * 2)) / $itemCount;
    $rows = 12;
    $columns = 5;
    $dotGap = 8;
    $tubeTop = 24;
    $baseline = 166;
@endphp

<figure class="overflow-hidden rounded-2xl border border-white/10 bg-[#111318] p-4 text-slate-100 sm:p-5" aria-label="{{ $ariaLabel }}">
    <div class="mb-3 flex items-start justify-between gap-4">
        <div>
            <figcaption class="text-sm font-semibold text-white">Aktivitas 7 Hari</figcaption>
            <p class="mt-1 text-xs text-slate-400">Jumlah kunjungan per hari</p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-semibold leading-none text-white">{{ number_format($total) }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Total kunjungan</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-300" tabindex="0" aria-label="Grafik dapat digeser mendatar">
        <svg
            class="block h-auto w-full"
            style="min-width: 560px"
            viewBox="0 0 {{ $chartWidth }} 212"
            role="img"
            aria-label="{{ $ariaLabel }}"
            xmlns="http://www.w3.org/2000/svg"
        >
            @foreach($items as $index => $item)
                @php
                    $value = max(0, (int) ($item['value'] ?? 0));
                    $center = $left + ($index * $step) + ($step / 2);
                    $tubeWidth = min(58, $step * 0.58);
                    $tubeX = $center - ($tubeWidth / 2);
                    $fillCount = (int) round(($value / $maxValue) * $rows * $columns);
                    $day = (string) ($item['label'] ?? '');
                    $dots = [];
                    for ($row = 0; $row < $rows; $row++) {
                        for ($column = 0; $column < $columns; $column++) {
                            $dotIndex = ($rows - 1 - $row) * $columns + $column;
                            $dots[] = [
                                'x' => $center - (($columns - 1) * $dotGap / 2) + ($column * $dotGap),
                                'y' => $baseline - ($row * $dotGap),
                                'active' => $dotIndex < $fillCount,
                            ];
                        }
                    }
                @endphp
                <g>
                    <title>{{ $day }}: {{ $value }} kunjungan</title>
                    <rect
                        x="{{ $tubeX }}"
                        y="{{ $tubeTop }}"
                        width="{{ $tubeWidth }}"
                        height="{{ $baseline - $tubeTop + 8 }}"
                        rx="16"
                        fill="#ffffff"
                        fill-opacity="0.025"
                        stroke="#ffffff"
                        stroke-opacity="0.12"
                    />
                    @foreach($dots as $dot)
                        <circle
                            cx="{{ $dot['x'] }}"
                            cy="{{ $dot['y'] }}"
                            r="2.3"
                            fill="{{ $dot['active'] ? '#a5b4fc' : '#34363d' }}"
                            fill-opacity="{{ $dot['active'] ? '0.95' : '0.7' }}"
                        />
                    @endforeach
                    <text x="{{ $center }}" y="198" text-anchor="middle" fill="#aab0bd" font-size="12">{{ $day }}</text>
                </g>
            @endforeach
        </svg>
    </div>
</figure>
