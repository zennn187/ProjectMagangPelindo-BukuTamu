<?php

// Entry point Vercel serverless untuk Laravel.
//
// Paksa semua driver/store ke nilai aman TANPA syarat (tidak pakai cek VERCEL env),
// supaya dijamin berjalan terlepas dari apakah vercel-php runtime sudah set
// env VERCEL atau belum. Ini juga mengatasi bug ArgumentCountError yang muncul
// ketika salah satu env var bernilai kosong "" sehingga driver() memanggil
// $this->createDriver() tanpa argumen.

$force = [
    'SESSION_DRIVER'         => 'cookie',
    'CACHE_STORE'            => 'array',
    'QUEUE_CONNECTION'       => 'sync',
    'LOG_CHANNEL'            => 'stderr',
    'LOG_STACK'              => 'stderr',
    'APP_CONFIG_CACHE'       => '/tmp/config.php',
    'APP_EVENTS_CACHE'       => '/tmp/events.php',
    'APP_PACKAGES_CACHE'     => '/tmp/packages.php',
    'APP_ROUTES_CACHE'       => '/tmp/routes.php',
    'APP_SERVICES_CACHE'     => '/tmp/services.php',
    'VIEW_COMPILED_PATH'     => '/tmp/views',
    'APP_MAINTENANCE_DRIVER' => 'file',
];
foreach ($force as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key]    = $value;
    $_SERVER[$key] = $value;
}

// Pastikan trusted proxies terset
if (!getenv('APP_TRUSTED_PROXIES') || getenv('APP_TRUSTED_PROXIES') === '') {
    putenv('APP_TRUSTED_PROXIES=*');
    $_ENV['APP_TRUSTED_PROXIES']    = '*';
    $_SERVER['APP_TRUSTED_PROXIES'] = '*';
}

// Buat semua folder /tmp yang dibutuhkan Laravel agar tidak ada error read-only
foreach ([
    '/tmp/storage',
    '/tmp/storage/logs',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/views',
    '/tmp/sessions',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

require __DIR__ . '/../public/index.php';
