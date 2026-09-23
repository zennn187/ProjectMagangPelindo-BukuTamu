
@php($splashDuration = $duration ?? 5)
@php($splashLogo = file_exists(public_path('images/pelindo-logo.png')) ? asset('images/pelindo-logo.png') : null)

<div id="bukutamu-splash" class="bukutamu-splash" role="status" aria-label="Memuat Buku Tamu Pelindo">
    <div class="bukutamu-splash-inner">
        <div class="bukutamu-splash-logo">
            @if ($splashLogo)
                <img src="{{ $splashLogo }}" alt="Logo Pelindo" class="bukutamu-splash-logo-img" />
            @else
                <svg class="bukutamu-splash-logo-img" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                    {{-- pita belakang (biru muda) --}}
                    <path fill="#5B9BD5" d="M38 12c26-10 56 2 66 28 8 22-2 46-24 56l-9-19c14-7 21-23 15-38-7-17-26-24-42-17z"/>
                    {{-- pita depan (biru tua) --}}
                    <path fill="#1F6FB2" d="M34 30l22 9v51c9 3 19 2 26-3l10 18c-13 9-31 11-47 6-7-2-11-8-11-15z"/>
                    {{-- batang & kotak aksen --}}
                    <path fill="#1F6FB2" d="M30 26h20v64H30z"/>
                    <rect fill="#5B9BD5" x="30" y="94" width="20" height="16" rx="2"/>
                </svg>
                @endif
        </div>

        @if (! $splashLogo)
            <p class="bukutamu-splash-wordmark">PELINDO</p>
        @endif
        <div class="bukutamu-splash-divider" aria-hidden="true"></div>
        <p class="bukutamu-splash-region">Regional 1 Dumai</p>
        <p class="bukutamu-splash-app">Buku Tamu Digital</p>

        <div class="bukutamu-splash-progress" aria-hidden="true">
            <span class="bukutamu-splash-progress-bar"></span>
        </div>
    </div>
</div>

<style>
    .bukutamu-splash {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f7fa; /* bank-bg */
        font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
        opacity: 0;
        animation: bukutamu-in 400ms ease forwards;
    }
    .bukutamu-splash-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0 24px;
    }
    .bukutamu-splash-logo {
        opacity: 0;
        transform: scale(0.94);
        animation: bukutamu-pop 700ms cubic-bezier(0.22, 1, 0.36, 1) 150ms forwards;
    }
    .bukutamu-splash-logo-img {
        width: clamp(150px, 34vw, 210px);
        height: auto;
        display: block;
    }
    .bukutamu-splash-wordmark {
        margin-top: 18px;
        font-size: clamp(1.5rem, 5.5vw, 2rem);
        font-weight: 800;
        letter-spacing: 0.32em;
        margin-right: -0.32em; /* kompensasi tracking agar tetap center */
        color: #1f6fb2;
        opacity: 0;
        animation: bukutamu-rise 600ms ease 500ms forwards;
    }
    .bukutamu-splash-divider {
        width: 56px;
        height: 2px;
        background: #d7dee8;
        margin: 20px 0 16px;
        opacity: 0;
        animation: bukutamu-rise 600ms ease 700ms forwards;
    }
    .bukutamu-splash-region {
        font-size: clamp(0.85rem, 3vw, 1rem);
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #343c6a; /* bank-navy */
        opacity: 0;
        animation: bukutamu-rise 600ms ease 850ms forwards;
    }
    .bukutamu-splash-app {
        margin-top: 4px;
        font-size: clamp(0.72rem, 2.4vw, 0.85rem);
        font-weight: 500;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #718ebf; /* bank-gray */
        opacity: 0;
        animation: bukutamu-rise 600ms ease 1000ms forwards;
    }
    .bukutamu-splash-progress {
        width: min(200px, 56vw);
        height: 3px;
        margin-top: 34px;
        border-radius: 9999px;
        background: #e6eff5; /* bank-light */
        overflow: hidden;
        opacity: 0;
        animation: bukutamu-rise 600ms ease 1200ms forwards;
    }
    .bukutamu-splash-progress-bar {
        display: block;
        height: 100%;
        width: 0%;
        border-radius: inherit;
        background: #1814f3; /* bank-blue */
        animation: bukutamu-progress {{ $splashDuration }}s linear forwards;
    }
    .bukutamu-splash.done {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 500ms ease, visibility 500ms ease;
    }
    @keyframes bukutamu-in { to { opacity: 1; } }
    @keyframes bukutamu-pop { to { opacity: 1; transform: scale(1); } }
    @keyframes bukutamu-rise { to { opacity: 1; } }
    @keyframes bukutamu-progress { to { width: 100%; } }
    @media (prefers-reduced-motion: reduce) {
        .bukutamu-splash *, .bukutamu-splash *::before, .bukutamu-splash *::after {
            animation-duration: 0.001s !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>

<script>
    (function () {
        var root = document.getElementById('bukutamu-splash');
        if (!root) return;

        var prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        var finished = false;
        function finish() {
            if (finished) return;
            finished = true;
            root.classList.add('done');
            window.setTimeout(function () {
                document.body.style.overflow = prevOverflow;
                root.remove();
            }, 550);
        }

        window.setTimeout(finish, {{ $splashDuration }} * 1000);
    })();
</script>
