<div class="flex min-h-screen">
    <aside class="fixed inset-y-0 left-0 hidden w-64 flex-col border-r border-bank-light bg-white xl:flex">
        <div class="flex min-h-[104px] items-center gap-3 border-b border-bank-light/70 px-5 py-5">
            <div class="h-12 w-12 animate-pulse rounded-xl bg-bank-light"></div>
            <div class="space-y-2">
                <div class="h-3 w-20 animate-pulse rounded-full bg-bank-light"></div>
                <div class="h-2 w-28 animate-pulse rounded-full bg-bank-light"></div>
            </div>
        </div>
        <nav class="flex-1 space-y-5 px-5 py-7" aria-hidden="true">
            @foreach(range(1, 9) as $item)
                <div class="h-3 animate-pulse rounded-full bg-bank-light {{ $item === 1 ? 'w-28' : ($item % 2 === 0 ? 'w-36' : 'w-32') }}"></div>
            @endforeach
        </nav>
        <div class="space-y-4 border-t border-bank-light p-5">
            <div class="h-10 animate-pulse rounded-xl bg-bank-bg"></div>
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

        <main class="space-y-5 p-4 sm:p-6 2xl:p-8">
            <header class="flex items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="h-7 w-36 animate-pulse rounded-lg bg-bank-light"></div>
                    <div class="h-3 w-60 max-w-full animate-pulse rounded-full bg-bank-light"></div>
                </div>
                <div class="h-10 w-28 animate-pulse rounded-xl bg-bank-blue/15"></div>
            </header>

            <section class="grid grid-cols-2 gap-3 md:grid-cols-3 2xl:grid-cols-5" aria-hidden="true">
                @foreach(range(1, 5) as $card)
                    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-bank-light/70 sm:p-5">
                        <div class="h-9 w-9 animate-pulse rounded-full bg-bank-bg"></div>
                        <div class="mt-4 h-3 w-24 animate-pulse rounded-full bg-bank-light"></div>
                        <div class="mt-2 h-6 w-10 animate-pulse rounded-lg bg-bank-light"></div>
                    </div>
                @endforeach
            </section>

            <section class="grid gap-4 lg:grid-cols-3" aria-hidden="true">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-bank-light/70 sm:p-6 lg:col-span-2">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <div class="h-4 w-32 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="h-3 w-48 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                        <div class="h-5 w-10 animate-pulse rounded-full bg-bank-light"></div>
                    </div>
                    <div class="mt-5 flex gap-2">
                        @foreach(range(1, 5) as $tab)
                            <div class="h-8 w-10 animate-pulse rounded-full bg-bank-bg"></div>
                        @endforeach
                    </div>
                    <div class="mt-5 h-52 animate-pulse rounded-2xl bg-bank-bg"></div>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-bank-light/70 sm:p-6 lg:col-span-1">
                    <div class="h-4 w-36 animate-pulse rounded-full bg-bank-light"></div>
                    <div class="mt-5 space-y-5">
                        @foreach(range(1, 4) as $row)
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 animate-pulse rounded-full bg-bank-bg"></div>
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

            <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-bank-light/70" aria-hidden="true">
                <div class="flex items-center justify-between gap-4 px-5 py-5 sm:px-6">
                    <div class="space-y-2">
                        <div class="h-4 w-56 animate-pulse rounded-full bg-bank-light"></div>
                        <div class="h-3 w-72 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                    </div>
                    <div class="h-3 w-24 animate-pulse rounded-full bg-bank-light"></div>
                </div>
                <div class="grid grid-cols-3 gap-3 px-5 pb-4 sm:px-6">
                    @foreach(range(1, 3) as $stat)
                        <div class="rounded-xl bg-bank-bg p-3">
                            <div class="h-2.5 w-20 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="mt-2 h-4 w-8 animate-pulse rounded-full bg-bank-light"></div>
                        </div>
                    @endforeach
                </div>
                @foreach(range(1, 3) as $row)
                    <div class="flex items-center justify-between gap-4 border-t border-bank-light px-5 py-4 sm:px-6">
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="h-3 w-40 max-w-full animate-pulse rounded-full bg-bank-light"></div>
                            <div class="h-2.5 w-64 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-2.5 w-52 max-w-full animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                        <div class="h-10 w-28 animate-pulse rounded-xl bg-bank-blue/15"></div>
                    </div>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-bank-light/70" aria-hidden="true">
                <div class="flex items-center justify-between px-5 py-5 sm:px-6">
                    <div class="h-4 w-36 animate-pulse rounded-full bg-bank-light"></div>
                    <div class="h-3 w-20 animate-pulse rounded-full bg-bank-bg"></div>
                </div>
                <div class="space-y-4 px-5 pb-5 sm:px-6">
                    @foreach(range(1, 4) as $row)
                        <div class="flex items-center gap-4 border-t border-bank-light pt-4">
                            <div class="h-9 w-9 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 flex-1 animate-pulse rounded-full bg-bank-light"></div>
                            <div class="h-3 w-24 animate-pulse rounded-full bg-bank-bg"></div>
                            <div class="h-3 w-16 animate-pulse rounded-full bg-bank-bg"></div>
                        </div>
                    @endforeach
                </div>
            </section>

            <span class="sr-only">Memuat data dashboard…</span>
        </main>
    </div>
</div>
