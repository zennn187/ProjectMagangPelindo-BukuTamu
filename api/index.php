<?php

// Entry point Vercel serverless untuk Laravel.
//
// Filesystem Vercel itu read-only kecuali /tmp, dan tiap function invocation
// itu fresh — jadi driver yang butuh tulis persisten (database/file) akan
// bikin HTTP 500 kalau env-nya masih nilai lokal. Di sini nilai WAJIB
// serverless dipaksa langsung (env dashboard yang salah ikut ditimpa),
// supaya copy-paste .env lokal tidak lagi bikin Error Rate 100%.
if (getenv('VERCEL') || getenv('VERCEL_ENV')) {
    $force = [
        'SESSION_DRIVER'   => 'cookie',
        'CACHE_STORE'      => 'array',
        'QUEUE_CONNECTION' => 'sync',
        'LOG_CHANNEL'      => 'stderr',
        'LOG_STACK'        => 'stderr',
        'APP_CONFIG_CACHE'  => '/tmp/config.php',
        'APP_EVENTS_CACHE'  => '/tmp/events.php',
        'APP_PACKAGES_CACHE' => '/tmp/packages.php',
        'APP_ROUTES_CACHE'  => '/tmp/routes.php',
        'APP_SERVICES_CACHE' => '/tmp/services.php',
    ];
    foreach ($force as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
    if (getenv('APP_TRUSTED_PROXIES') === false || getenv('APP_TRUSTED_PROXIES') === '') {
        putenv('APP_TRUSTED_PROXIES=*');
        $_ENV['APP_TRUSTED_PROXIES'] = '*';
        $_SERVER['APP_TRUSTED_PROXIES'] = '*';
    }

    // Pastikan Laravel menulis cache/compiled view ke /tmp yang writable.
    foreach (['/tmp/storage', '/tmp/storage/logs', '/tmp/storage/framework', '/tmp/storage/framework/views', '/tmp/storage/framework/sessions', '/tmp/views', '/tmp/sessions'] as $dir) {
        if (! is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
    putenv('VIEW_COMPILED_PATH=/tmp/views');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';
}

require __DIR__ . '/../public/index.php';
