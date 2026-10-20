<?php

// Entry point Vercel serverless untuk Laravel.
//
// Filesystem Vercel itu read-only kecuali /tmp, dan tiap function invocation
// itu fresh — jadi driver yang butuh tulis persisten (database/file) akan
// bikin HTTP 500 kalau env-nya belum diset. Paksa default yang aman di sini;
// nilai ini tetap bisa dioverride lewat Environment Variables di dashboard.
if (getenv('VERCEL')) {
    foreach ([
        'SESSION_DRIVER'   => 'cookie',
        'CACHE_STORE'      => 'array',
        'QUEUE_CONNECTION' => 'sync',
        'LOG_CHANNEL'      => 'stderr',
        'APP_TRUSTED_PROXIES' => '*',
    ] as $key => $fallback) {
        if (getenv($key) === false || getenv($key) === '') {
            putenv("{$key}={$fallback}");
            $_ENV[$key] = $fallback;
            $_SERVER[$key] = $fallback;
        }
    }

    // Pastikan Laravel menulis cache/compiled view ke /tmp yang writable.
    foreach ([
        'VIEW_COMPILED_PATH' => '/tmp/views',
        'SESSION_FILES'      => '/tmp/sessions',
    ] as $key => $dir) {
        if (! is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
    if (getenv('VIEW_COMPILED_PATH') === false) {
        putenv('VIEW_COMPILED_PATH=/tmp/views');
        $_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
        $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';
    }
}

require __DIR__ . '/../public/index.php';
