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
        // 419 = CSRF token tidak cocok dengan session (session kedaluwarsa, cookie
        // session hilang/mati, atau halaman lama dikirim ulang). Default Laravel
        // menampilkan "Page Expired" yang buntu, jadi arahkan kembali ke halaman
        // yang relevan beserta pesan + token yang baru agar user bisa coba lagi.
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($exception->getStatusCode() !== 419) {
                return null;
            }

            $csrfDiagnostics = null;

            // Sediakan hanya indikator aman untuk mendiagnosis 419 di Vercel.
            // Jangan pernah mengirim atau mencatat nilai token, cookie, atau APP_KEY.
            if ($request->is('kiosk') && $request->isMethod('post')) {
                $submittedToken = $request->header('X-CSRF-TOKEN')
                    ?? $request->input('_token');
                $sessionToken = $request->hasSession()
                    ? $request->session()->token()
                    : null;
                $rawCookieHeader = (string) $request->server->get('HTTP_COOKIE', '');
                $rawCookieNames = array_filter(array_map(
                    static fn (string $cookie): string => trim(explode('=', $cookie, 2)[0]),
                    explode(';', $rawCookieHeader)
                ));
                $sessionCookieName = (string) config('session.cookie');

                $csrfDiagnostics = [
                    'session_available' => $request->hasSession(),
                    'incoming_cookie_header_present' => $rawCookieHeader !== '',
                    'incoming_cookie_names' => array_values($rawCookieNames),
                    'expected_session_cookie_name' => $sessionCookieName,
                    'incoming_session_cookie_present' => in_array($sessionCookieName, $rawCookieNames, true),
                    'session_cookie_readable' => $request->cookies->has($sessionCookieName),
                    'submitted_token_present' => is_string($submittedToken) && $submittedToken !== '',
                    'session_token_present' => is_string($sessionToken) && $sessionToken !== '',
                    'tokens_match' => is_string($submittedToken)
                        && is_string($sessionToken)
                        && hash_equals($sessionToken, $submittedToken),
                ];
            }

            if ($request->expectsJson()) {
                $body = [
                    'message' => 'Sesi Anda telah berakhir. Muat ulang halaman lalu coba lagi.',
                ];

                if ($csrfDiagnostics !== null) {
                    $body['csrf_diagnostic'] = $csrfDiagnostics;
                }

                return response()->json($body, 419);
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

