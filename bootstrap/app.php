<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Keep the root cause of production login failures visible in Vercel logs
        // without logging submitted fields such as the email or password.
        $exceptions->report(function (Throwable $exception) {
            $request = request();

            if (! $request->isMethod('POST') || ! $request->is('login')) {
                return;
            }

            $message = $exception->getMessage();

            foreach (['email', 'password', 'password_confirmation'] as $field) {
                $value = $request->input($field);

                if (is_string($value) && $value !== '') {
                    $message = str_replace($value, '[redacted]', $message);
                }
            }

            error_log('Login request exception: '.json_encode([
                'exception_class' => $exception::class,
                'message' => substr($message, 0, 500),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ], JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE));

            return false;
        });

        // 419 = CSRF token tidak cocok dengan session (session kedaluwarsa, cookie
        // session hilang/mati, atau halaman lama dikirim ulang). Default Laravel
        // menampilkan "Page Expired" yang buntu, jadi arahkan kembali ke halaman
        // yang relevan beserta pesan + token yang baru agar user bisa coba lagi.
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($exception->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Muat ulang halaman lalu coba lagi.',
                ], 419);
            }

            $previous = url()->previous(route('login'));

            // Kembali ke halaman sebelumnya hanya bila masih host yang sama dan bukan
            // URL request ini sendiri, agar tidak terlempar ke host lain (mis. URL
            // tunnel yang sudah mati) maupun mengulang POST yang sama.
            $target = ($previous !== url()->current() && parse_url($previous, PHP_URL_HOST) === $request->getHost())
                ? $previous
                : route('login');

            return redirect()
                ->to($target)
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->withErrors([
                    'session' => 'Sesi Anda telah berakhir (halaman kedaluwarsa). Silakan coba lagi.',
                ]);
        });
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
