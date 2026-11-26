# Test Report — Phase 4

Tanggal: 26 November 2026 (Asia/Jakarta)  
Branch/HEAD: `main` / `026c170 pergantian logo pelindo`  
Perubahan seluruhnya masih lokal; tidak ada commit, push, migration production, atau deployment.

## Ringkasan status

| Pemeriksaan | Status | Bukti/catatan |
|---|---|---|
| `composer validate --no-check-publish` | PASS | `composer.json is valid`. |
| `php artisan route:list --except-vendor --json` | PASS | Route map berhasil dibuat; middleware auth/verified/role dan route publik ditinjau. |
| `npm run build` | PASS | Vite 7.3.6, 112 modules; app JS 114.73 KB (41.25 KB gzip), Lenis chunk 18.71 KB (5.45 KB gzip), CSS 100.53 KB (17.31 KB gzip), badge JS 38.51 KB (15.63 KB gzip). |
| `php artisan view:cache` | PASS | Semua Blade templates berhasil dikompilasi. |
| `php -l` changed PHP files | PASS | `PhotoService.php`, `PhotoServiceTest.php`, `VisitWhatsAppFlowTest.php`. |
| Pint pada changed PHP files | PASS | `vendor/bin/pint --test ...` lulus setelah `PhotoService.php` diformat. |
| `node --check` JS entry files | PASS | `resources/js/app.js` dan `resources/js/badge.js`. |
| `php artisan test tests/Unit/PhotoServiceTest.php` | PASS | 2 tes, 6 assertions. |
| Full `php artisan test` | BLOCKED | 5 tes PASS; 49 tes berhenti pada bootstrap database `SQLSTATE could not find driver` untuk SQLite `:memory:`. Tidak ada assertion failure aplikasi yang teramati pada output; PDO SQLite tidak tersedia. |
| `git diff --check` | PASS | Tidak ada whitespace error. |
| Lighthouse mobile/desktop | NOT TESTED | Tidak ada binary Lighthouse/browser automation lokal; tidak mengakses production. |
| Browser E2E / screen reader / camera | NOT TESTED | Environment browser dan sesi login sah tidak tersedia untuk pengujian ini. |
| Aiven connection/migration status | NOT TESTED | Tidak membaca credential atau mengakses DB production. |
| Vercel deployment/runtime | NOT TESTED | Tidak ada deploy/log production yang diperiksa. |

## Cakupan flow

| Flow | Status | Bukti yang ada |
|---|---|---|
| Landing dan buka form kiosk | NOT TESTED (browser) | Route/view ditinjau; `KioskNavigationTest` lulus. |
| Daftar pegawai async | NOT TESTED (runtime) | Implementasi controller/cache/throttle dan JS ditinjau. |
| Validasi/pengiriman kiosk + badge QR | NOT TESTED (E2E) | Controller/FormRequest/token ditinjau; Feature test yang memerlukan DB blocked. `KioskSubmissionTest` yang memakai mock lulus. |
| Kamera kiosk | NOT TESTED (perangkat) | Pemuatan diminta setelah form dibuka dan stream dibersihkan pada `pagehide`; izin/retake belum dicoba di browser nyata. |
| Form Gate dan kamera | NOT TESTED (browser) | Route/FormRequest/controller ditinjau; tes persistensi blocked. |
| Scan masuk/keluar, GateLog, aktivitas hari ini | NOT TESTED (operator runtime) | Route/controller/service ditinjau; `GateReportTest` dan status-history tests blocked SQLite. |
| Login/role | NOT TESTED (browser) | Middleware/route/controller ditinjau; feature auth tests blocked SQLite. |
| Dashboard/status/foto privat | NOT TESTED (operator runtime) | Route/Gate checks, pagination dan response ditinjau; tes feature blocked SQLite. |
| Laporan/filter/export | NOT TESTED (admin runtime) | Controller/export/route ditinjau; tes laporan blocked SQLite. |
| Retensi/backup/restore | NOT TESTED (database test copy) | Controller/service/schema statis ditinjau; restore sengaja tidak diarahkan ke production dan tes SQLite blocked. |

## Lighthouse: baseline dan pengukuran

Baseline screenshot pengguna pada dashboard desktop (`/dashboard?chart_week=5`): Performance **55**, Accessibility **80**, Best Practices **100**, SEO **82**. Ini bukan pengukuran yang dilakukan saat audit dan tidak merepresentasikan landing page.

| Kategori | Sebelum (screenshot lama) | Sesudah | Target |
|---|---:|---:|---:|
| Performance | 55 (dashboard desktop) | NOT TESTED | 90–100 |
| Accessibility | 80 (dashboard desktop) | NOT TESTED | 95–100 |
| Best Practices | 100 (dashboard desktop) | NOT TESTED | 100 |
| SEO | 82 (dashboard desktop) | NOT TESTED | 95–100 |

LCP, INP, CLS, FCP, TBT, request trace, dan resource network: NOT TESTED. Optimasi bundle dan ukuran wordmark adalah bukti build/resource lokal, bukan pengganti Lighthouse.

## Isolasi dan keamanan

- PHPUnit diarahkan ke SQLite `:memory:` oleh `phpunit.xml`. Tidak ada perubahan `.env`, kredensial, konfigurasi DB, atau koneksi ke MySQL Aiven.
- Tidak ada submission visitor ke production, scan kode, retensi, backup/restore, atau aksi mutasi bisnis yang dijalankan.
- Route publik dan terproteksi ditinjau statis. Pengujian guest/receptionist/admin terhadap response HTTP belum dapat diklaim PASS karena Feature tests database-blocked dan tidak ada browser session.
- Pemeriksaan CSP, HSTS/cookie/session production, secrets rotation, status migration, dan logging Vercel masih memerlukan bukti/akses operator.

## Langkah agar tes BLOCKED dapat dijalankan

Aktifkan PDO SQLite pada PHP CLI yang dipakai repo, lalu ulangi full `php artisan test`. Jalankan hanya terhadap konfigurasi `phpunit.xml` (SQLite in-memory); jangan mengganti target ke MySQL production.
