@props([
    'data',
    'ariaLabel' => 'Jumlah kunjungan per minggu',
    'activeWeek' => null,
])

@php
    $items = collect($data)->values();
    $monthDate = isset($items[0]['date'])
        ? \Illuminate\Support\Carbon::parse($items[0]['date'])
        : now()->startOfMonth();
    $monthEnd = $monthDate->copy()->endOfMonth();
    $weekCount = $monthEnd->day > 28 ? 5 : 4;
    $activeWeek = max(1, min($weekCount, (int) ($activeWeek ?? ceil(now()->day / 7))));
    $chartWidth = 760;
    $left = 44;
    $rows = 12;
    $columns = 5;
    $dotGap = 8;
    $dotsPerVisit = $columns;
    $tubeTop = 34;
    $baseline = 170;
    $weeks = collect(range(1, $weekCount))->mapWithKeys(function (int $week) use ($items) {
        return [$week => $items->filter(function ($item) use ($week) {
            $day = (int) ($item['day'] ?? \Illuminate\Support\Carbon::parse($item['date'])->day);

            return (int) ceil($day / 7) === $week;
        })->values()];
    });
@endphp

@push('styles')
    <style>
        .visit-particle-chart {
            --chart-dot-idle: #d5deea;
            --chart-dot-active: #1814f3;
            --chart-tube: #f5f7fa;
            --chart-tube-border: #e6eff5;
        }

        html[data-theme="dark"] .operator-shell .visit-particle-chart {
            --chart-dot-idle: #334155;
            --chart-dot-active: #818cf8;
            --chart-tube: #17243a;
            --chart-tube-border: #34445c;
        }

        .visit-particle-week {
            transition: background-color 160ms ease, color 160ms ease, box-shadow 160ms ease;
        }

        .visit-particle-week.is-selected {
            background-color: #1814f3;
            color: #fff;
            box-shadow: 0 2px 8px rgb(24 20 243 / 20%);
        }

        html[data-theme="dark"] .operator-shell .visit-particle-week.is-selected {
            background-color: #818cf8;
            color: #0b1220;
        }

        .visit-particle-dot.is-active {
            transform-box: fill-box;
            transform-origin: center;
            animation: visit-particle-appear 420ms cubic-bezier(.2, .75, .3, 1) both;
            animation-delay: var(--particle-delay, 0ms);
        }

        @keyframes visit-particle-appear {
            from { opacity: 0; transform: scale(.35); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            .visit-particle-dot.is-active { animation: none; }
        }
    </style>
@endpush

<figure
    class="visit-particle-chart overflow-hidden rounded-2xl border border-bank-light bg-white p-4 text-bank-navy shadow-sm sm:p-5"
    data-active-week="{{ $activeWeek }}"
    data-month="{{ $monthDate->format('Y-m') }}"
    aria-label="{{ $ariaLabel }}"
>
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <figcaption class="text-sm font-semibold text-bank-navy">Aktivitas Bulan Ini</figcaption>
            <p class="mt-1 text-xs text-bank-gray">{{ $monthDate->translatedFormat('F Y') }} · jumlah kunjungan per hari</p>
            <p class="mt-1 text-[11px] text-bank-gray">Setiap kunjungan menambah 5 titik (1 baris; skala penuh 12/hari)</p>
        </div>
        <div class="text-right">
            <p class="visit-particle-total text-2xl font-semibold leading-none text-bank-navy" aria-live="polite">
                {{ number_format((int) $weeks[$activeWeek]->sum('total')) }}
            </p>
            <p class="visit-particle-range mt-1 text-[11px] text-bank-gray">
                {{ $monthDate->copy()->day(($activeWeek - 1) * 7 + 1)->translatedFormat('j M') }}
                – {{ $monthDate->copy()->day(min($activeWeek * 7, $monthEnd->day))->translatedFormat('j M') }}
            </p>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="inline-flex rounded-xl bg-bank-bg p-1" role="group" aria-label="Pilih minggu pada bulan ini">
            @foreach($weeks as $week => $weekItems)
                @php
                    $rangeStart = ($week - 1) * 7 + 1;
                    $rangeEnd = min($week * 7, $monthEnd->day);
                    $rangeLabel = $monthDate->copy()->day($rangeStart)->translatedFormat('j M')
                        .'–'.$monthDate->copy()->day($rangeEnd)->translatedFormat('j M');
                @endphp
                <button
                    type="button"
                    class="visit-particle-week rounded-lg px-3 py-1.5 text-xs font-semibold text-bank-gray hover:bg-bank-light/70 focus-visible:outline focus-visible:outline-2 focus-visible:outline-bank-blue {{ $week === $activeWeek ? 'is-selected' : '' }}"
                    data-week="{{ $week }}"
                    data-total="{{ (int) $weekItems->sum('total') }}"
                    data-range="{{ $rangeLabel }}"
                    aria-pressed="{{ $week === $activeWeek ? 'true' : 'false' }}"
                    aria-label="Minggu W{{ $week }}, tanggal {{ $rangeLabel }}"
                >W{{ $week }}</button>
            @endforeach
        </div>
        <p class="visit-particle-week-label text-xs font-medium text-bank-gray" aria-live="polite">Minggu W{{ $activeWeek }}</p>
    </div>

    <div class="overflow-x-auto rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-300" tabindex="0" aria-label="Grafik dapat digeser mendatar">
        <svg
            class="block h-auto w-full"
            style="min-width: 560px"
            viewBox="0 0 {{ $chartWidth }} 230"
            role="img"
            aria-label="{{ $ariaLabel }} untuk minggu W{{ $activeWeek }}"
            xmlns="http://www.w3.org/2000/svg"
        >
            @foreach($weeks as $week => $weekItems)
                @php
                    $weekCount = max(1, $weekItems->count());
                    $step = ($chartWidth - ($left * 2)) / $weekCount;
                @endphp
                <g class="visit-particle-week-data" data-week="{{ $week }}" @if($week !== $activeWeek) style="display:none" @endif>
                    @foreach($weekItems as $index => $item)
                        @php
                            $value = max(0, (int) ($item['total'] ?? 0));
                            $center = $left + ($index * $step) + ($step / 2);
                            $tubeWidth = min(58, $step * 0.62);
                            $tubeX = $center - ($tubeWidth / 2);
                            $fillCount = min($value * $dotsPerVisit, $rows * $columns);
                            $dots = [];
                            for ($row = 0; $row < $rows; $row++) {
                                for ($column = 0; $column < $columns; $column++) {
                                    $dotIndex = $row * $columns + $column;
                                    $dots[] = [
                                        'x' => $center - (($columns - 1) * $dotGap / 2) + ($column * $dotGap),
                                        'y' => $baseline - ($row * $dotGap),
                                        'active' => $dotIndex < $fillCount,
                                        'delay' => min(540, $dotIndex * 9),
                                    ];
                                }
                            }
                            $date = \Illuminate\Support\Carbon::parse($item['date']);
                        @endphp
                        <g data-date="{{ $date->toDateString() }}" data-value="{{ $value }}">
                            <title>{{ $date->translatedFormat('j M') }}, {{ $item['label'] }}: {{ $value }} kunjungan</title>
                            <rect
                                x="{{ $tubeX }}"
                                y="{{ $tubeTop }}"
                                width="{{ $tubeWidth }}"
                                height="{{ $baseline - $tubeTop + 8 }}"
                                rx="16"
                                fill="var(--chart-tube)"
                                fill-opacity="0.025"
                                stroke="var(--chart-tube-border)"
                                stroke-opacity="0.9"
                            />
                            @foreach($dots as $dot)
                                <circle
                                    class="visit-particle-dot{{ $dot['active'] ? ' is-active' : '' }}"
                                    style="--particle-delay: {{ $dot['delay'] }}ms"
                                    cx="{{ $dot['x'] }}"
                                    cy="{{ $dot['y'] }}"
                                    r="2.3"
                                    fill="{{ $dot['active'] ? 'var(--chart-dot-active)' : 'var(--chart-dot-idle)' }}"
                                    fill-opacity="{{ $dot['active'] ? '1' : '0.82' }}"
                                />
                            @endforeach
                            <text x="{{ $center }}" y="25" text-anchor="middle" fill="currentColor" font-size="12" font-weight="600">{{ $value }}</text>
                            <text x="{{ $center }}" y="202" text-anchor="middle" fill="currentColor" font-size="12">{{ $item['label'] }}</text>
                            <text x="{{ $center }}" y="218" text-anchor="middle" fill="currentColor" fill-opacity="0.7" font-size="10">{{ $date->format('j') }}</text>
                        </g>
                    @endforeach
                </g>
            @endforeach
        </svg>
    </div>
</figure>

@push('scripts')
    <script>
        document.querySelectorAll('.visit-particle-chart:not([data-switch-ready])').forEach((chart) => {
            chart.dataset.switchReady = 'true';
            const buttons = chart.querySelectorAll('.visit-particle-week');
            const groups = chart.querySelectorAll('.visit-particle-week-data');
            const total = chart.querySelector('.visit-particle-total');
            const range = chart.querySelector('.visit-particle-range');
            const weekLabel = chart.querySelector('.visit-particle-week-label');
            const svg = chart.querySelector('svg');
            const storageKey = `bukutamu-dashboard-chart-week:${chart.dataset.month}`;

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const selectedWeek = button.dataset.week;

                    buttons.forEach((option) => {
                        const selected = option === button;
                        option.setAttribute('aria-pressed', selected ? 'true' : 'false');
                        option.classList.toggle('is-selected', selected);
                    });

                    groups.forEach((group) => {
                        const selected = group.dataset.week === selectedWeek;
                        group.style.display = selected ? '' : 'none';

                        if (selected) {
                            const dots = group.querySelectorAll('.visit-particle-dot.is-active');
                            dots.forEach((dot) => dot.classList.remove('is-active'));
                            void group.getBoundingClientRect();
                            dots.forEach((dot) => dot.classList.add('is-active'));
                        }
                    });

                    total.textContent = Number(button.dataset.total).toLocaleString('id-ID');
                    range.textContent = button.dataset.range;
                    weekLabel.textContent = `Minggu W${selectedWeek}`;
                    svg.setAttribute('aria-label', `${svg.getAttribute('aria-label').split(' untuk minggu ')[0]} untuk minggu W${selectedWeek}`);

                    const url = new URL(window.location.href);
                    url.searchParams.set('chart_week', selectedWeek);
                    window.history.replaceState({}, '', url);

                    try {
                        window.localStorage.setItem(storageKey, selectedWeek);
                    } catch (error) {
                        console.warn('Pilihan minggu chart tidak dapat disimpan.', error);
                    }
                });
            });

            try {
                const savedWeek = window.localStorage.getItem(storageKey);
                const savedButton = Array.from(buttons).find((button) => button.dataset.week === savedWeek);
                const requestedWeek = new URL(window.location.href).searchParams.get('chart_week');
                const hasValidRequestedWeek = Array.from(buttons).some((button) => button.dataset.week === requestedWeek);

                if (!hasValidRequestedWeek && savedButton && savedWeek !== chart.dataset.activeWeek) {
                    savedButton.click();
                }
            } catch (error) {
                console.warn('Pilihan minggu chart tidak dapat dibaca.', error);
            }
        });
    </script>
@endpush
