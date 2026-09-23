<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->forceRootUrlFromRequest();

        Gate::define('is-admin', fn ($user) => $user->isAdmin());
        Gate::define('is-receptionist-or-admin', fn ($user) => $user->isOperator());
    }

    /**
     * Selaraskan URL hasil route(), url(), dan asset() dengan host + skema yang
     * benar-benar dipakai browser.
     *
     * Sebelumnya host lokal (localhost / 127.0.0.1 tanpa port) dipaksa memakai
     * APP_URL, sehingga saat APP_URL berisi URL dev tunnel yang sudah mati form
     * login bisa "dilempar" ke host lain. Cookie session milik host asal tidak
     * ikut terkirim ke host tujuan, CSRF token tidak ditemukan, dan muncul 419
     * Page Expired. Header X-Forwarded-Host dari tunnel juga dulu diabaikan.
     */
    protected function forceRootUrlFromRequest(): void
    {
        $host = $this->requestHost();

        if ($host === null) {
            // Konteks CLI (queue worker, scheduler, artisan tinker): tidak ada request
            // HTTP, jadi pakai APP_URL apa adanya beserta skemanya.
            if ($appUrl = config('app.url')) {
                URL::forceRootUrl($appUrl);
                URL::forceScheme(parse_url($appUrl, PHP_URL_SCHEME) ?: 'http');
            }

            return;
        }

        $scheme = $this->requestScheme();

        URL::forceRootUrl($scheme.'://'.$host);
        URL::forceScheme($scheme);
    }

    /**
     * Host yang dipakai browser. Utamakan X-Forwarded-Host dari tunnel/proxy
     * (VS Code port forwarding bisa menulis ulang header Host ke 127.0.0.1),
     * dan kembalikan null bila bukan request HTTP.
     */
    protected function requestHost(): ?string
    {
        if (! request()->server->has('HTTP_HOST')) {
            return null;
        }

        $forwardedHost = trim(Str::before((string) request()->header('x-forwarded-host'), ','));

        return $forwardedHost !== '' ? $forwardedHost : (request()->getHttpHost() ?: null);
    }

    /**
     * Skema (http/https) request. X-Forwarded-Proto bisa berisi daftar seperti
     * "https,http" bila request melewati beberapa proxy.
     */
    protected function requestScheme(): string
    {
        $forwardedScheme = strtolower((string) request()->header('x-forwarded-proto'));

        if (str_contains($forwardedScheme, 'https')) {
            return 'https';
        }

        if (str_contains($forwardedScheme, 'http')) {
            return 'http';
        }

        return request()->getScheme();
    }
}
