@php($skeletonWeekCount = now()->daysInMonth > 28 ? 5 : 4)

<div class="flex min-h-screen">
    <aside class="fixed inset-y-0 left-0 hidden w-64 flex-col border-r border-bank-light bg-white xl:flex">
        <div class="flex min-h-[104px] items-center gap-3 border-b border-bank-light/70 px-5 py-5">
            <div class="h-12 w-12 animate-pulse rounded-xl bg-bank-light"></div>
            <div class="space-y-2">
                <div class="h-3 w-20 animate-pulse rounded-full bg-bank-light"></div>
                <div class="h-2 w-28 animate-pulse rounded-full bg-bank-light"></div>
            </div>
        </div>
        <nav class="flex-1 overflow-hidden py-4" aria-hidden="true">
            @foreach(range(1, 11) as $item)
                <div class="flex h-12 items-center pl-6">
                    <div class="h-3 animate-pulse rounded-full bg-bank-light {{ $item % 3 === 0 ? 'w-28' : ($item % 2 === 0 ? 'w-36' : 'w-32') }}"></div>
                </div>
            @endforeach
        </nav>
        <div class="mt-auto shrink-0 border-t border-bank-light p-4">
            <div class="mb-4 h-10 animate-pulse rounded-xl bg-bank-bg"></div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 animate-pulse rounded-full bg-bank-light"></div>
                <div class="space-y-2">
                    <div class="h-3 w-24 animate-pulse rounded-full bg-bank-light"></div>
                    <div class="h-2 w-14 animate-pulse rounded-full bg-bank-light"></div>
                </div>
            </div>
            <div class="h-11 animate-pulse rounded-xl bg-bank-bg"></div>
        </div>
    </aside>

    <div class="min-w-0 flex-1 xl:ml-64">
        <div class="flex h-14 items-center gap-3 border-b border-bank-light bg-white px-4 xl:hidden">
            <div class="h-8 w-8 animate-pulse rounded-lg bg-bank-light"></div>
            <div class="h-3 w-28 animate-pulse rounded-full bg-bank-light"></div>
        </div>

        <main class="min-h-screen p-4 sm:p-6 2xl:p-8">
            <header class="mb-6 flex items-center justify-between gap-3">
                <div class="h-8 w-40 animate-pulse rounded-lg bg-bank-light"></div>
                <div class="h-10 w-32 animate-pulse rounded-xl bg-bank-blue/15"></div>
            </header>

            <div class="mb-6">
                <div class="h-4 w-72 max-w-full animate-pulse rounded-full bg-bank-light"></div>
            </div>

            <section class="mb-6 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 2xl:grid-cols-5" aria-hidden="true">
                @foreach(range(1, 5) as $card)
                    <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-bank-light/70">
                        <div class="h-11 w-11 animate-pulse rounded-full bg-bank-bg"></div>
                        <div class="mt-4 h-3 w-24 animate-pulse rounded-full bg-bank-light"></div>
                        <div class="mt-1 h-7 w-10 animate-pulse rounded-lg bg-bank-light"></div>
                    </div>
                @endforeach
            </section>

            <section class="mb-6 grid gap-4 sm:gap-5 lg:grid-cols-3" aria-hidden="true">
                <div class="overflow-hidden rounded-2xl border border-bank-light bg-white p-4 text-bank-navy shadow-sm sm:p-5 lg:col-span-2">
                    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-2">
                            <div class="h-4 w-36 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="h-3 w-40 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-2.5 w-56 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                        <div class="space-y-2 text-right">
                            <div class="ml-auto h-7 w-12 animate-pulse rounded-lg bg-bank-light"></div>
                            <div class="ml-auto h-3 w-20 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                    </div>
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div class="flex gap-1 rounded-xl bg-bank-bg p-1">
                            @foreach(range(1, $skeletonWeekCount) as $tab)
                                <div class="h-7 w-10 animate-pulse rounded-lg bg-bank-light"></div>
                            @endforeach
                        </div>
                        <div class="h-3 w-20 animate-pulse rounded-full bg-bank-light"></div>
                    </div>
                    <div class="overflow-hidden rounded-lg">
                        <div class="flex h-[170px] min-w-[560px] items-end justify-around px-3 pb-2">
                            @foreach(range(1, 7) as $day)
                                <div class="flex h-full flex-col items-center justify-end gap-2">
                                    <div class="h-3 w-5 animate-pulse rounded-full bg-bank-light"></div>
                                    <div class="grid h-[138px] w-[58px] grid-cols-5 content-end justify-items-center gap-1 rounded-2xl border border-bank-light bg-bank-bg p-2">
                                        @foreach(range(1, 60) as $dot)
                                            <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-bank-light"></div>
                                        @endforeach
                                    </div>
                                    <div class="h-3 w-7 animate-pulse rounded-full bg-bank-light"></div>
                                    <div class="h-2.5 w-4 animate-pulse rounded-full bg-bank-bg"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-bank-light/70 sm:p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="h-4 w-36 animate-pulse rounded-full bg-bank-light"></div>
                        <div class="h-3 w-12 animate-pulse rounded-full bg-bank-bg"></div>
                    </div>
                    <div class="space-y-4">
                        @foreach(range(1, 5) as $row)
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <div class="h-3 w-3/5 animate-pulse rounded-full bg-bank-light"></div>
                                    <div class="h-2.5 w-2/5 animate-pulse rounded-full bg-bank-bg"></div>
                                </div>
                                <div class="h-2.5 w-10 animate-pulse rounded-full bg-bank-bg"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mb-6 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-bank-light/70" aria-hidden="true">
                <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-5 sm:px-6">
                    <div class="space-y-2">
                        <div class="h-4 w-56 animate-pulse rounded-full bg-bank-light"></div>
                        <div class="h-3 w-72 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                    </div>
                    <div class="h-4 w-28 animate-pulse rounded-full bg-bank-light"></div>
                </div>
                <div class="grid grid-cols-3 gap-2 px-5 pb-4 sm:gap-3 sm:px-6">
                    @foreach(range(1, 3) as $stat)
                        <div class="rounded-xl bg-bank-bg px-3 py-2.5">
                            <div class="h-3 w-20 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="mt-1 h-5 w-8 animate-pulse rounded-full bg-bank-light"></div>
                        </div>
                    @endforeach
                </div>
                @foreach(range(1, 5) as $row)
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-bank-light px-5 py-4 sm:px-6">
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-40 max-w-[60%] animate-pulse rounded-full bg-bank-light"></div>
                                <div class="h-5 w-24 animate-pulse rounded-full bg-bank-bg"></div>
                            </div>
                            <div class="h-3 w-64 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-52 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                        <div class="h-10 w-28 animate-pulse rounded-xl bg-bank-blue/15"></div>
                    </div>
                @endforeach
                <div class="flex justify-center border-t border-bank-light px-5 py-3">
                    <div class="h-9 w-32 animate-pulse rounded-full bg-bank-bg"></div>
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-bank-light/70" aria-hidden="true">
                <div class="flex items-center justify-between gap-4 px-5 py-5 sm:px-6">
                    <div class="h-4 w-36 animate-pulse rounded-full bg-bank-light"></div>
                    <div class="h-3 w-16 animate-pulse rounded-full bg-bank-bg"></div>
                </div>
                <div class="px-5 pb-5 sm:px-6">
                    <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_14rem_auto]">
                        <div class="h-[42px] animate-pulse rounded-xl bg-bank-bg"></div>
                        <div class="h-[42px] animate-pulse rounded-xl bg-bank-bg"></div>
                        <div class="flex gap-2">
                            <div class="h-[42px] w-20 animate-pulse rounded-xl bg-bank-blue/15"></div>
                            <div class="h-[42px] w-20 animate-pulse rounded-xl bg-bank-bg"></div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <div class="min-w-[760px]">
                    <div class="grid grid-cols-[2rem_minmax(9rem,1.3fr)_3rem_5rem_3rem_5rem_8rem_5rem] gap-3 bg-bank-bg px-4 py-3">
                        @foreach(range(1, 8) as $column)
                            <div class="h-3 animate-pulse rounded-full bg-bank-light"></div>
                        @endforeach
                    </div>
                    @foreach(range(1, 5) as $row)
                        <div class="grid grid-cols-[2rem_minmax(9rem,1.3fr)_3rem_5rem_3rem_5rem_8rem_5rem] items-center gap-3 border-t border-bank-light px-4 py-4">
                            <div class="h-3 w-4 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="space-y-2">
                                <div class="h-3 w-36 max-w-full animate-pulse rounded-full bg-bank-light"></div>
                                <div class="h-2.5 w-28 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                                <div class="h-2.5 w-24 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                            </div>
                            <div class="h-12 w-12 animate-pulse rounded-lg bg-bank-bg"></div>
                            <div class="h-3 w-16 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-10 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-5 w-16 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-28 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-7 w-12 animate-pulse rounded-lg bg-bank-blue/15"></div>
                        </div>
                    @endforeach
                    </div>
                </div>
                <div class="flex justify-end border-t border-bank-light px-5 py-3">
                    <div class="h-9 w-36 animate-pulse rounded-full bg-bank-bg"></div>
                </div>
            </section>

            <span class="sr-only">Memuat data dashboard…</span>
        </main>
    </div>
</div>
