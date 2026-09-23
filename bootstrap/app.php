<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
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
