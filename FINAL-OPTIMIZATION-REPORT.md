# Final Optimization Report — Buku Tamu Digital Pelindo Regional 1 Dumai

Tanggal: 26 November 2026 (Asia/Jakarta)  
Branch/HEAD: `main` / `026c170 pergantian logo pelindo`  
Status: PHASE 1–4 lokal telah dijalankan; PHASE 5 deployment/Lighthouse belum dapat diverifikasi. Perubahan belum di-commit/push/deploy.

## Ringkasan

Audit source dan schema, implementasi perbaikan lokal, validasi build/style, serta source-level quality pass telah dilakukan tanpa perubahan arsitektur, database, alur status, atau identitas visual. Perbaikan memotong beban JS global, menunda akses kamera sampai flow dipakai, lazy-load foto halaman bawah, mengurangi ukuran file wordmark sambil mempertahankan transparansi/nama aset, menolak payload foto SVG/MIME mismatch, menghubungkan label form, dan memberi metadata SEO dasar.

## Perubahan dan file

- Source/performa/aksesibilitas: `resources/js/app.js`, `resources/views/kiosk/index.blade.php`, `resources/views/gate/form.blade.php`, `resources/views/reception/dashboard.blade.php`, `resources/views/admin/reports/index.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/operator.blade.php`.
- Validasi keamanan foto: `app/Services/PhotoService.php`.
- Tes: `tests/Unit/PhotoServiceTest.php` (baru), `tests/Feature/VisitWhatsAppFlowTest.php` (fixture foto menjadi PNG valid).
- Aset sama: `public/images/logo_pelindo_transparan_HD.png`, 8192×5792/1,272,525 byte → 1024×724/42,359 byte; alpha/transparansi diperiksa dan pratinjau visual ditinjau.
- Laporan fase: `AUDIT-REPORT.md`, `PERFORMANCE-REPORT.md`, `QUALITY-REPORT.md`, `TEST-REPORT.md`.

Perubahan lokal yang sudah ada sebelum pekerjaan ini pada `HANDOFF-CODEX-ANTIGRAVITY.md` dipertahankan dan tidak ditimpa. Tidak ada dependency baru atau migration.

## Hasil performa lokal

| Artefak | Sebelum | Sesudah | Perubahan |
|---|---:|---:|---:|
| App JS | 132.12 KB / 45.72 KB gzip | 114.73 KB / 41.25 KB gzip | -17.39 KB / -4.47 KB gzip |
| Lenis | Tergabung dengan app | Chunk dinamis 18.71 KB / 5.45 KB gzip | Tidak diunduh halaman tanpa `data-lenis` |
| CSS | 100.53 KB / 17.31 KB gzip | 100.53 KB / 17.31 KB gzip | Tetap |
| Badge JS | 38.51 KB / 15.63 KB gzip | 38.51 KB / 15.63 KB gzip | Tetap |
| Wordmark PNG | 1,272,525 byte | 42,359 byte | Sekitar -96.7% |

Ukuran Vite adalah bukti build lokal; Lighthouse/Core Web Vitals belum diukur ulang.

## Lighthouse

Baseline screenshot lama berasal dari dashboard desktop, bukan landing page. Angka itu bukan hasil audit ulang.

| Kategori | Sebelum | Sesudah | Target |
|---|---:|---:|---:|
| Performance | 55 | NOT TESTED | 90–100 |
| Accessibility | 80 | NOT TESTED | 95–100 |
| Best Practices | 100 | NOT TESTED | 100 |
| SEO | 82 | NOT TESTED | 95–100 |

LCP/INP/CLS/FCP/TBT dan network trace belum tersedia. Lighthouse tidak terpasang di environment dan tidak ada browser automation. Tidak ada angka yang diperkirakan atau diklaim.

## Hasil testing

- PASS: `composer validate`, route listing, Vite build, Blade view cache, PHP lint, Pint, Node syntax check, dan `git diff --check`.
- PASS: tes unit `PhotoServiceTest`, 2 tests / 6 assertions, termasuk penolakan SVG/mismatch dan penerimaan PNG ber-signature cocok.
- BLOCKED: full Laravel suite menampilkan 5 PASS dan 49 tes terhenti saat SQLite in-memory bootstrap dengan `could not find driver`; `pdo_sqlite` tidak tersedia. Tidak ada bukti assertion failure aplikasi dari run tersebut.
- NOT TESTED: seluruh browser E2E, kamera pada perangkat, keyboard/screen reader, browser matrix, Lighthouse mobile/desktop, serta alur dengan sesi auth yang sah.
- NOT TESTED: Aiven migration state/connectivity, Vercel runtime/environment/logs, WhatsApp production, restore pada database salinan, dan route/privacy behavior production.

Bukti rinci ada di `TEST-REPORT.md`.

## Keamanan dan integritas

- Source menunjukkan middleware `auth`/`verified` dan authorization role untuk area operator/admin; foto hanya melalui endpoint terproteksi. Pemeriksaan ini statis dan bukan penetration test.
- `PhotoService` sekarang menerima JPEG/PNG dengan signature yang cocok dan menolak SVG. Runtime lokal tidak memiliki GD; konten JPEG/PNG yang valid tetap dapat disimpan tanpa proses decode/re-encode penuh, sehingga format signature lolos belum sama dengan bukti decode penuh pada setiap runtime.
- Vercel headers saat ini mencakup HSTS, `nosniff`, frame deny, Referrer Policy, Permissions Policy. Content-Security-Policy belum dikonfigurasi; perlu policy kompatibel dengan inline script/style/CDN dan pengujian report-only.
- Tidak ada kredensial dibaca atau dicatat. Tidak ada koneksi ke production, perubahan schema/config DB, retensi, backup, restore, push, atau deploy.

## Deployment readiness (PHASE 5)

| Pemeriksaan | Status |
|---|---|
| Config routing/Vercel dan build lokal | PASS (source/build) |
| Build Vite | PASS |
| Environment variables Vercel dan secret rotation | NOT TESTED |
| Database Aiven dan migration status | NOT TESTED |
| Production commit dan deployment | NOT TESTED |
| Public flow setelah deployment | NOT TESTED |
| Authenticated dashboard/scanner/admin | NOT TESTED (perlu sesi sah) |
| Lighthouse production | NOT TESTED |

PHASE 5 belum bisa dinyatakan siap karena deployment aktif dan status migration tidak diverifikasi dan Lighthouse/browser tool tidak tersedia; instruksi juga mensyaratkan izin sebelum pengujian production. Setelah operator melakukan deployment perubahan lokal dan memberi izin serta URL/akun sesi yang sah tanpa mengirim password melalui chat/source, pengujian dapat diulang pada landing mobile/desktop dan halaman internal sesuai role.

## Risiko tersisa dan rekomendasi

1. Aktifkan PDO SQLite pada PHP CLI, lalu ulangi full suite menggunakan konfigurasi SQLite in-memory.
2. Uji browser flow kamera kiosk/Gate dan aksesibilitas semua modal/dropdown sebelum merge.
3. Ukur Lighthouse mobile/desktop pada URL yang sama seperti baseline; lampiran baseline berasal dari dashboard desktop sehingga siapkan baseline landing yang terpisah.
4. Verifikasi migration di Aiven pada target yang benar dan status deployment/Runtime Logs di Vercel sebelum menyebut production siap.
5. Review batas tanggal/rentang ekspor laporan dan query plan GateLog; perubahan indeks memerlukan migration dan rencana penerapan yang ditinjau terpisah.
6. Rancang CSP report-only sebelum enforcement.
7. Uji restore backup di database test/salinan, tidak di database production.
