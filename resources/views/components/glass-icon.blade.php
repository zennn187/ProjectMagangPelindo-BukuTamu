{{--
    Glass Icon — ikon glassmorphism pastel (gaya Figma "Glass Icons").
    Pemakaian:
        <x-glass-icon name="shield" tone="green" size="md" />
        <x-glass-icon name="camera" tone="orange" size="lg" class="shrink-0" />

    Nama tersedia: user, home, bell, shield, calendar, camera, sliders, chat,
    wallet, briefcase, image, mail, edit, lock, search, cart, trash, folder,
    share, location, check, qr

    Fallback otomatis: jika file `public/images/icons/{name}.svg` ada, file itu
    yang dipakai — hasil ekspor ikon dari Figma bisa menggantikan ikon bawaan
    TANPA mengubah kode (cukup tambah/timpa file SVG dengan nama yang sama).

    Tone: purple | orange | blue | red | green
--}}
@props([
    'name' => 'user',
    'tone' => 'blue',
    'size' => 'md',
    'class' => '',
])

@php
    $sizes = [
        'sm' => ['tile' => 'h-9 w-9',   'pad' => 'p-1.5'],
        'md' => ['tile' => 'h-12 w-12', 'pad' => 'p-2'],
        'lg' => ['tile' => 'h-16 w-16', 'pad' => 'p-3'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    $tones = [
        'purple' => ['#B7A6F7', '#7C3AED'],
        'orange' => ['#FDBA8C', '#F97316'],
        'blue'   => ['#9EC5FE', '#3B82F6'],
        'red'    => ['#FDA4AF', '#EF4444'],
        'green'  => ['#8CE9BE', '#10B981'],
    ];
    [$c1, $c2] = $tones[$tone] ?? $tones['blue'];

    // Fallback file: public/images/icons/{name}.svg (hasil ekspor Figma)
    $customFile = 'images/icons/' . $name . '.svg';
    $useCustom  = is_file(public_path($customFile));

    // ID gradasi unik agar tidak bentrok antar-instance
    $uid = 'gi-' . substr(md5($name . $tone . uniqid('', true)), 0, 8);

    $icons = [
        'user'      => '<circle cx="12" cy="7.5" r="4"/><path d="M4 20.5c0-3.6 3.6-6 8-6s8 2.4 8 6v.5H4z"/>',
        'home'      => '<path d="M12 2.8 2.8 11h2.4v9a1.2 1.2 0 0 0 1.2 1.2h3.8v-6h3.6v6h3.8a1.2 1.2 0 0 0 1.2-1.2v-9h2.4z"/>',
        'bell'      => '<path d="M12 2a6.5 6.5 0 0 0-6.5 6.5v4L3.5 16v1.5h17V16l-2-3.5v-4A6.5 6.5 0 0 0 12 2z"/><path d="M9.4 19a2.6 2.6 0 0 0 5.2 0z"/>',
        'shield'    => '<path d="M12 2 4 5v6.2c0 4.9 3.4 8.9 8 10.8 4.6-1.9 8-5.9 8-10.8V5z"/>',
        'calendar'  => '<rect x="3" y="4.5" width="18" height="17" rx="3"/><rect x="3" y="4.5" width="18" height="7" rx="3" fill="#fff" opacity=".25"/><rect x="7" y="2" width="2.6" height="5" rx="1.3"/><rect x="14.4" y="2" width="2.6" height="5" rx="1.3"/>',
        'camera'    => '<path d="M9 4.5 7.6 6.5H4A2.5 2.5 0 0 0 1.5 9v9A2.5 2.5 0 0 0 4 20.5h16a2.5 2.5 0 0 0 2.5-2.5V9A2.5 2.5 0 0 0 20 6.5h-3.6L15 4.5z"/><circle cx="12" cy="13.2" r="4.4" fill="#fff" opacity=".9"/><circle cx="12" cy="13.2" r="2.4"/>',
        'sliders'   => '<rect x="4.6" y="2.5" width="2.2" height="19" rx="1.1"/><rect x="10.9" y="2.5" width="2.2" height="19" rx="1.1"/><rect x="17.2" y="2.5" width="2.2" height="19" rx="1.1"/><circle cx="5.7" cy="8" r="2.2" fill="#fff" opacity=".9"/><circle cx="12" cy="15.5" r="2.2" fill="#fff" opacity=".9"/><circle cx="18.3" cy="7" r="2.2" fill="#fff" opacity=".9"/>',
        'chat'      => '<path d="M4 3.5h11a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H9.5L5 19v-3.5H4a3 3 0 0 1-3-3v-6a3 3 0 0 1 3-3z"/><path d="M17.6 7.6h2.4a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3h-.6V21l-3.6-2.6h-2.7z" opacity=".55"/>',
        'wallet'    => '<path d="M2.5 6.5A2.5 2.5 0 0 1 5 4h12.5v3H5.2a1.1 1.1 0 0 0 0 2.2h14A2.5 2.5 0 0 1 21.5 12v6a2.5 2.5 0 0 1-2.5 2.5H5A2.5 2.5 0 0 1 2.5 18z"/><circle cx="17" cy="14.8" r="1.7" fill="#fff" opacity=".9"/>',
        'briefcase' => '<rect x="2.5" y="7" width="19" height="13.5" rx="3"/><path d="M9 7V5.5A2.5 2.5 0 0 1 11.5 3h1A2.5 2.5 0 0 1 15 5.5V7" fill="none" stroke-width="2"/><path d="M2.5 12.5h19" stroke="#fff" stroke-width="1.6" opacity=".6"/>',
        'image'     => '<rect x="2.5" y="4" width="19" height="16" rx="3"/><circle cx="8.5" cy="9.5" r="2" fill="#fff" opacity=".9"/><path d="M4 19.5 9.5 13l3.4 3.8 3-2.9 4.1 5.6z" fill="#fff" opacity=".75"/>',
        'mail'      => '<rect x="2" y="4.5" width="20" height="15" rx="3"/><path d="m3.5 7.5 8.5 6 8.5-6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".85"/>',
        'edit'      => '<path d="M3.5 20.5 5 15.8 16.6 4.2a2.3 2.3 0 0 1 3.2 3.2L8.2 19z"/>',
        'lock'      => '<rect x="4.5" y="10" width="15" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3" fill="none" stroke-width="2.2"/><circle cx="12" cy="15.3" r="1.8" fill="#fff" opacity=".95"/>',
        'search'    => '<circle cx="10.5" cy="10.5" r="6.8" fill="none" stroke-width="2.6"/><path d="M15.6 15.6 21 21" fill="none" stroke-width="2.8" stroke-linecap="round"/>',
        'cart'      => '<path d="M2.5 3.5h2.6l2.5 11.2a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.5l1.8-7.3H6.2"/><circle cx="10" cy="20.4" r="1.7"/><circle cx="17.5" cy="20.4" r="1.7"/>',
        'trash'     => '<path d="M4.5 7h15l-1.2 12.3a2 2 0 0 1-2 1.7H7.7a2 2 0 0 1-2-1.7z"/><rect x="8.8" y="3" width="6.4" height="3" rx="1.2"/><path d="M9.8 10.5v6M14.2 10.5v6" stroke="#fff" stroke-width="1.7" stroke-linecap="round" opacity=".85"/>',
        'folder'    => '<path d="M2.5 6A2.5 2.5 0 0 1 5 3.5h4.6a2 2 0 0 1 1.5.7l1.4 1.6H19A2.5 2.5 0 0 1 21.5 8.3v10.2A2.5 2.5 0 0 1 19 21H5a2.5 2.5 0 0 1-2.5-2.5z"/><path d="M2.5 9.5h19" stroke="#fff" stroke-width="1.5" opacity=".5"/>',
        'share'     => '<circle cx="18" cy="5.5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="18.5" r="3"/><path d="m8.7 10.6 6.6-3.7M8.7 13.4l6.6 3.7" stroke-width="2.2"/>',
        'location'  => '<path d="M12 1.8a7.4 7.4 0 0 0-7.4 7.4c0 5.4 7.4 13 7.4 13s7.4-7.6 7.4-13A7.4 7.4 0 0 0 12 1.8z"/><circle cx="12" cy="9.2" r="2.7" fill="#fff" opacity=".92"/>',
        'check'     => '<circle cx="12" cy="12" r="10"/><path d="m7.8 12.4 2.9 2.9 5.5-6.2" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>',
        'qr'        => '<rect x="2.8" y="2.8" width="7.4" height="7.4" rx="1.6"/><rect x="13.8" y="2.8" width="7.4" height="7.4" rx="1.6"/><rect x="2.8" y="13.8" width="7.4" height="7.4" rx="1.6"/><path d="M13.8 13.8h3v3h-3zM18.4 13.8h2.8v2.8h-2.8zM13.8 18.2h2.4v3h-2.4zM17.6 18.2h3.6v3h-3.6z" fill="#fff" opacity=".9"/>',
    ];

    $body = $icons[$name] ?? ($icons['check']);
@endphp

<div {{ $attributes->merge(['class' => $s['tile'].' inline-flex shrink-0 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-bank-light/80']) }}>
    @if($useCustom)
        {{-- Ikon dari file (hasil ekspor Figma) — otomatis dipakai bila ada --}}
        <img src="{{ asset($customFile) }}" alt="{{ $name }}" class="{{ $s['pad'] }} h-full w-full object-contain">
    @else
        <svg viewBox="0 0 24 24" class="{{ $s['pad'] }} h-full w-full" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="{{ $uid }}" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="{{ $c1 }}"/>
                    <stop offset="100%" stop-color="{{ $c2 }}"/>
                </linearGradient>
            </defs>
            <g fill="url(#{{ $uid }})" stroke="url(#{{ $uid }})" stroke-linecap="round" stroke-linejoin="round">
                {!! $body !!}
            </g>
        </svg>
    @endif
</div>