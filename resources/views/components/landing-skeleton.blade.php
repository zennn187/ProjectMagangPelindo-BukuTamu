<div class="page-loading-card">
    <div class="mx-auto mt-2 flex w-full max-w-6xl items-center justify-between gap-4 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 sm:px-5">
        <div class="flex items-center gap-3">
            <div class="skeleton skeleton-on-dark h-10 w-10 rounded-xl"></div>
            <div>
                <div class="skeleton skeleton-on-dark h-3 w-16"></div>
                <div class="skeleton skeleton-on-dark mt-2 h-2 w-20"></div>
            </div>
        </div>
        <div class="hidden items-center gap-6 md:flex">
            <div class="skeleton skeleton-on-dark h-2.5 w-12"></div>
            <div class="skeleton skeleton-on-dark h-2.5 w-14"></div>
            <div class="skeleton skeleton-on-dark h-2.5 w-8"></div>
            <div class="skeleton skeleton-on-dark h-2.5 w-20"></div>
        </div>
        <div class="flex items-center gap-2">
            <div class="skeleton skeleton-on-dark hidden h-9 w-20 rounded-xl sm:block"></div>
            <div class="skeleton h-9 w-24 rounded-xl"></div>
        </div>
    </div>
    <section class="mx-auto flex min-h-[calc(100vh-6rem)] max-w-4xl flex-col items-center justify-center px-2 pb-10 pt-16 text-center sm:px-6 sm:pt-8">
        <div class="skeleton skeleton-on-dark h-6 w-60 max-w-full rounded-full"></div>
        <div class="mt-7 w-full space-y-3">
            <div class="skeleton skeleton-on-dark mx-auto h-9 w-4/5 max-w-xl sm:h-12"></div>
            <div class="skeleton skeleton-on-dark mx-auto h-9 w-3/5 max-w-md sm:h-12"></div>
        </div>
        <div class="mt-6 w-full max-w-xl space-y-2.5">
            <div class="skeleton skeleton-on-dark mx-auto h-3 w-full"></div>
            <div class="skeleton skeleton-on-dark mx-auto h-3 w-11/12"></div>
            <div class="skeleton skeleton-on-dark mx-auto h-3 w-3/4"></div>
        </div>
        <div class="mt-8 flex w-full flex-wrap justify-center gap-3">
            <div class="skeleton h-11 w-44 rounded-xl"></div>
            <div class="skeleton skeleton-on-dark h-11 w-40 rounded-xl"></div>
        </div>
        <div class="mt-8 flex flex-wrap justify-center gap-2">
            @foreach(range(1, 4) as $step)
                <div class="skeleton skeleton-on-dark h-7 w-24 rounded-full"></div>
            @endforeach
        </div>
        <p class="mt-8 text-xs font-medium text-white/75">Menyiapkan halaman kunjungan…</p>
        <div class="mt-10 grid h-10 w-10 place-items-center rounded-full border border-white/25">
            <div class="skeleton skeleton-on-dark h-2 w-2 rounded-full"></div>
        </div>
    </section>
</div>
