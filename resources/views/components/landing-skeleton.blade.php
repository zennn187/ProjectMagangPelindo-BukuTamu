<div class="page-loading-card">
    <div class="fixed inset-x-0 top-4 z-[121] mx-auto flex h-[68px] w-full max-w-6xl items-center justify-between gap-3 rounded-2xl border border-white/15 bg-bank-navy/70 px-5 py-3.5 shadow-lg sm:gap-4">
        <div class="flex items-center gap-2.5">
            <div class="skeleton skeleton-on-dark h-10 w-10 rounded-xl"></div>
            <div class="space-y-1.5">
                <div class="skeleton skeleton-on-dark h-3 w-14"></div>
                <div class="skeleton skeleton-on-dark h-2.5 w-24"></div>
            </div>
        </div>
        <div class="hidden items-center gap-8 md:flex">
            <div class="skeleton skeleton-on-dark h-3 w-12"></div>
            <div class="skeleton skeleton-on-dark h-3 w-14"></div>
            <div class="skeleton skeleton-on-dark h-3 w-8"></div>
            <div class="skeleton skeleton-on-dark h-3 w-20"></div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="skeleton skeleton-on-dark h-10 w-10 rounded-xl"></div>
            <div class="skeleton skeleton-on-dark h-9 w-10 rounded-xl sm:w-24"></div>
            <div class="skeleton h-9 w-24 rounded-xl sm:w-28"></div>
        </div>
    </div>

    <section class="relative flex min-h-[92vh] items-center justify-center overflow-hidden bg-bank-navy">
        <div class="absolute inset-0 bg-gradient-to-b from-bank-navy/75 via-bank-navy/55 to-bank-bg"></div>
        <div class="relative z-10 mx-auto w-full max-w-[616px] px-5 pb-24 pt-28 text-center">
            <div class="skeleton skeleton-on-dark mx-auto mb-6 h-7 w-[290px] max-w-full rounded-full"></div>
            <div class="mx-auto max-w-3xl">
                <div class="skeleton skeleton-on-dark mx-auto h-[45px] w-[88%] max-w-[560px] sm:h-12"></div>
                <div class="skeleton skeleton-on-dark mx-auto h-[45px] w-[78%] max-w-[420px] sm:h-12"></div>
            </div>
            <div class="mx-auto mt-4 max-w-xl">
                <div class="space-y-1.5 sm:hidden">
                    @foreach(range(1, 6) as $line)
                        <div class="skeleton skeleton-on-dark mx-auto h-3.5 {{ $line === 6 ? 'w-3/4' : 'w-full' }}"></div>
                    @endforeach
                </div>
                <div class="hidden space-y-3 sm:block">
                    <div class="skeleton skeleton-on-dark mx-auto h-4 w-full"></div>
                    <div class="skeleton skeleton-on-dark mx-auto h-4 w-[96%]"></div>
                    <div class="skeleton skeleton-on-dark mx-auto h-4 w-[72%]"></div>
                </div>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <div class="skeleton h-11 w-56 rounded-xl"></div>
                <div class="skeleton skeleton-on-dark h-11 w-52 rounded-xl"></div>
            </div>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-2">
                <div class="skeleton skeleton-on-dark h-[33px] w-24 rounded-full sm:h-8"></div>
                <div class="skeleton skeleton-on-dark h-3 w-2 rounded-full"></div>
                <div class="skeleton skeleton-on-dark h-[33px] w-24 rounded-full sm:h-8"></div>
                <div class="skeleton skeleton-on-dark h-3 w-2 rounded-full"></div>
                <div class="skeleton skeleton-on-dark h-[33px] w-36 rounded-full sm:h-8"></div>
                <div class="skeleton skeleton-on-dark h-3 w-2 rounded-full"></div>
                <div class="skeleton skeleton-on-dark h-[33px] w-28 rounded-full sm:h-8"></div>
            </div>
        </div>
        <div class="absolute bottom-6 left-1/2 grid h-10 w-10 -translate-x-1/2 place-items-center rounded-full border border-white/25">
            <div class="skeleton skeleton-on-dark h-2 w-2 rounded-full"></div>
        </div>
    </section>

    <section class="border-b border-bank-light bg-white/90">
        <div class="mx-auto grid max-w-5xl grid-cols-2 gap-4 px-5 py-10 sm:gap-5 md:grid-cols-4 md:py-12">
            @foreach(range(1, 4) as $card)
                <div class="rounded-2xl border border-bank-light/60 bg-white/80 p-4 shadow-sm">
                    <div class="mx-auto {{ $card === 4 ? 'h-[56px] sm:h-8' : 'h-8 sm:h-9' }} w-20 animate-pulse rounded-lg bg-bank-light"></div>
                    <div class="mx-auto mt-2 h-3.5 w-4/5 animate-pulse rounded-full bg-bank-bg"></div>
                    <div class="mx-auto mt-1.5 h-3.5 w-3/5 animate-pulse rounded-full bg-bank-bg sm:mt-1"></div>
                    @if ($card === 2)
                        <div class="mx-auto mt-1.5 h-3.5 w-2/5 animate-pulse rounded-full bg-bank-bg sm:hidden"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-bank-bg py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-5 text-center">
            <div class="mx-auto h-4 w-16 animate-pulse rounded-full bg-bank-light"></div>
            <div class="mx-auto mt-3 h-[26px] w-72 max-w-full animate-pulse rounded-lg bg-bank-light sm:h-[33px]"></div>
            <div class="mx-auto mt-3 h-[71px] w-full max-w-xl sm:h-auto">
                <div class="h-4 w-full animate-pulse rounded-full bg-bank-light"></div>
                <div class="mt-1 h-3 w-4/5 animate-pulse rounded-full bg-bank-light"></div>
                <div class="mt-1 h-3 w-3/5 animate-pulse rounded-full bg-bank-light"></div>
                <div class="mt-1 h-3 w-2/5 animate-pulse rounded-full bg-bank-light sm:hidden"></div>
            </div>
            <div class="mt-10 grid gap-6 text-left sm:grid-cols-3">
                @foreach(range(1, 3) as $card)
                    <div class="overflow-hidden rounded-2xl border border-bank-light/60 bg-white shadow-sm">
                        <div class="h-56 animate-pulse bg-bank-light sm:h-72"></div>
                        <div class="p-5">
                            <div class="h-6 w-3/4 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="mt-1 space-y-1">
                                <div class="h-3.5 w-full animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="h-3.5 w-4/5 animate-pulse rounded-full bg-bank-bg"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-5">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <div class="h-4 w-16 animate-pulse rounded-full bg-bank-light"></div>
                    <div class="mt-3 flex h-[53px] flex-col justify-between sm:h-[66px]">
                        <div class="h-6 w-full max-w-md animate-pulse rounded-lg bg-bank-light sm:h-8"></div>
                        <div class="h-6 w-4/5 max-w-sm animate-pulse rounded-lg bg-bank-light sm:h-8"></div>
                    </div>
                    <div class="mt-4 h-[143px] max-w-md sm:h-auto">
                        <div class="space-y-[5px] sm:hidden">
                            @foreach(range(1, 7) as $line)
                                <div class="h-4 {{ $line === 7 ? 'w-4/5' : 'w-full' }} animate-pulse rounded-full bg-bank-bg"></div>
                            @endforeach
                        </div>
                        <div class="hidden space-y-3 sm:block">
                            <div class="h-4 w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-4 w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-4 w-4/5 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                    </div>
                </div>
                <div class="h-64 animate-pulse rounded-2xl bg-bank-light sm:h-80 lg:ml-auto lg:max-w-md"></div>
            </div>
            <div class="mt-8 grid items-center gap-10 lg:grid-cols-2">
                <div class="h-56 animate-pulse rounded-2xl bg-bank-light sm:h-72 lg:order-2 lg:max-w-md"></div>
                <div class="lg:order-1 lg:pl-6">
                    <div class="h-[114px] space-y-2 sm:h-auto">
                        <div class="space-y-2 sm:hidden">
                            @foreach(range(1, 5) as $line)
                                <div class="h-4 {{ $line === 5 ? 'w-3/4' : 'w-full' }} animate-pulse rounded-full bg-bank-bg"></div>
                            @endforeach
                        </div>
                        <div class="hidden space-y-3 sm:block">
                            <div class="h-3 w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-3/4 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-3">
                @foreach(range(1, 3) as $card)
                    <div class="flex min-h-[105px] items-start gap-4 rounded-2xl border border-bank-light/60 bg-white p-5">
                        <div class="h-11 w-11 shrink-0 animate-pulse rounded-xl bg-bank-bg"></div>
                        <div class="flex-1">
                            <div class="h-5 w-3/4 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="mt-1 space-y-1">
                                <div class="h-4 w-full animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="h-4 w-4/5 animate-pulse rounded-full bg-bank-bg"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-bank-navy py-16 sm:py-20">
        <div class="mx-auto grid max-w-6xl items-start gap-12 px-5 lg:grid-cols-2">
        <div>
            <div class="h-4 w-12 animate-pulse rounded-full bg-white/20"></div>
            <div class="mt-3 flex h-[53px] flex-col justify-between sm:h-[66px]">
                <div class="h-6 w-full max-w-sm animate-pulse rounded-lg bg-white/20 sm:h-8"></div>
                <div class="h-6 w-4/5 max-w-xs animate-pulse rounded-lg bg-white/20 sm:h-8"></div>
            </div>
            <div class="mt-4 h-[68px] max-w-sm space-y-2 sm:h-[46px]">
                <div class="h-4 w-full animate-pulse rounded-full bg-white/10"></div>
                <div class="h-4 w-4/5 animate-pulse rounded-full bg-white/10"></div>
                <div class="h-4 w-3/5 animate-pulse rounded-full bg-white/10 sm:hidden"></div>
            </div>
            <ul class="mt-8 space-y-6">
                @foreach(range(1, 2) as $item)
                    <li class="flex h-[63px] items-start gap-3">
                        <div class="mt-0.5 h-6 w-6 shrink-0 animate-pulse rounded-full bg-white/20"></div>
                        <div class="w-full max-w-xs">
                            <div class="h-5 w-3/4 animate-pulse rounded-full bg-white/20"></div>
                            <div class="mt-1 space-y-2">
                                <div class="h-3.5 w-full animate-pulse rounded-full bg-white/10"></div>
                                <div class="h-3.5 w-4/5 animate-pulse rounded-full bg-white/10"></div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-[24px] bg-white p-3 shadow-sm">
            @foreach(range(1, 4) as $item)
                <div class="{{ $item === 1 ? 'h-[170px] sm:h-[111px]' : ($item === 4 ? 'h-[77px] sm:h-[56px]' : 'h-[77px] sm:h-[57px]') }} flex flex-col justify-center gap-2 border-b border-bank-light px-4 last:border-b-0">
                    <div class="flex h-[68px] flex-col justify-center sm:h-4 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="h-5 w-4/5 animate-pulse rounded-full bg-bank-light sm:h-4"></div>
                            <div class="h-4 w-4 shrink-0 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                            <div class="h-5 w-3/5 animate-pulse rounded-full bg-bank-light sm:hidden"></div>
                    </div>
                    @if ($item === 1)
                        <div class="h-[94px] space-y-2 sm:h-auto">
                                <div class="h-3.5 w-full animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="h-3.5 w-4/5 animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="h-3.5 w-3/5 animate-pulse rounded-full bg-bank-bg sm:hidden"></div>
                                <div class="h-3.5 w-2/5 animate-pulse rounded-full bg-bank-bg sm:hidden"></div>
                        </div>
                    @endif
                </div>
            @endforeach
            </div>
        </div>
    </section>

    <section class="bg-bank-bg px-4 py-10 text-center sm:px-6 sm:py-16">
        <div class="mx-auto w-full max-w-xl rounded-[28px] border border-bank-light bg-white/90 px-6 py-10 shadow-sm sm:px-10 sm:py-12">
            <div class="mx-auto h-6 w-28 animate-pulse rounded-full bg-bank-bg"></div>
            <div class="mx-auto mt-5 h-8 w-64 max-w-full animate-pulse rounded-lg bg-bank-light"></div>
            <div class="mx-auto mt-4 h-3 w-full max-w-md animate-pulse rounded-full bg-bank-bg"></div>
            <div class="mx-auto mt-2 h-3 w-4/5 max-w-sm animate-pulse rounded-full bg-bank-bg"></div>
            <div class="mx-auto mt-8 h-12 w-56 max-w-full animate-pulse rounded-xl bg-bank-blue/15"></div>
        </div>
    </section>

    <footer class="border-t border-white/10 bg-[#071631]">
        <div class="mx-auto grid max-w-6xl gap-9 px-5 py-12 sm:px-8 md:grid-cols-4 md:gap-10 md:py-14">
            <div class="space-y-3 md:col-span-2">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 animate-pulse rounded-xl bg-white/20"></div>
                    <div class="space-y-2">
                        <div class="h-3 w-16 animate-pulse rounded-full bg-white/20"></div>
                        <div class="h-2.5 w-24 animate-pulse rounded-full bg-white/10"></div>
                    </div>
                </div>
                <div class="h-3 w-full max-w-md animate-pulse rounded-full bg-white/10"></div>
                <div class="h-3 w-4/5 max-w-sm animate-pulse rounded-full bg-white/10"></div>
            </div>
            @foreach(range(1, 2) as $column)
                <div class="space-y-4">
                    <div class="h-4 w-24 animate-pulse rounded-full bg-white/20"></div>
                    @foreach(range(1, 3) as $link)
                        <div class="h-3 w-32 animate-pulse rounded-full bg-white/10"></div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </footer>
</div>
