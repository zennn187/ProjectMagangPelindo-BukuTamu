@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi antrean buku tamu" class="flex flex-wrap items-center justify-between gap-3">
        <p class="hidden text-xs text-bank-gray sm:block">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </p>

        <div class="flex items-center gap-1.5" aria-label="Halaman antrean">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Halaman sebelumnya" class="grid h-9 w-9 place-items-center rounded-full bg-bank-bg text-bank-gray/50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.5 18-6-6 6-6"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="grid h-9 w-9 place-items-center rounded-full border border-bank-light bg-white text-bank-navy transition hover:border-bank-blue/40 hover:bg-bank-bg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.5 18-6-6 6-6"/>
                    </svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="px-1.5 text-sm text-bank-gray">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="grid h-9 min-w-9 place-items-center rounded-full bg-bank-blue px-3 text-xs font-semibold text-white shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="grid h-9 min-w-9 place-items-center rounded-full border border-transparent px-3 text-xs font-medium text-bank-gray transition hover:border-bank-light hover:bg-bank-bg hover:text-bank-navy focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="grid h-9 w-9 place-items-center rounded-full border border-bank-light bg-white text-bank-navy transition hover:border-bank-blue/40 hover:bg-bank-bg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/>
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Halaman berikutnya" class="grid h-9 w-9 place-items-center rounded-full bg-bank-bg text-bank-gray/50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
