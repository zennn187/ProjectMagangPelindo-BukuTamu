# Audit Proyek Buku Tamu Digital — Phase 1

Tanggal audit: 26 November 2026 (Asia/Jakarta)  
Branch/HEAD: `main` / `026c170 pergantian logo pelindo`  
Metode: pemeriksaan source code, routes, migrations, configuration, tests, Git state, dan validasi lokal yang tidak mengubah database.

## 1. Arsitektur aktual

- Backend: PHP 8.2 dan Laravel 12, Eloquent ORM, MySQL sebagai target produksi menurut konfigurasi/panduan proyek. Ekspor menggunakan Laravel Excel; QR memakai `simplesoftwareio/simple-qrcode`.
- Frontend: Blade, Tailwind CSS, Vite, Alpine.js, vanilla JavaScript, Axios, Anime.js/Lenis. Blade tetap menjadi renderer utama; tidak ditemukan React atau Shadcn dependency.
- Deployment: Vercel dengan `api/index.php`, runtime `vercel-php@0.7.3`, build Vite ke `public/build`, dan rewrite sisa route ke Laravel. `vercel.json` memasang header `nosniff`, `DENY`, Referrer Policy, Permissions Policy, dan HSTS.
- Penyimpanan foto baru: BLOB pada `visit_photos`, MIME type disimpan bersamanya; endpoint foto di `/visits/{visit}/photo` berada di middleware auth/verified dan memeriksa Gate role.
- Transisi status: `VisitStatusService` menggunakan transaksi dan row lock, memeriksa status asal, menyimpan perubahan, dan menulis `VisitStatusHistory`.
- Sumber kebenaran data produksi dan deployment tidak dapat diperiksa dari checkout: tidak ada akses/status Aiven atau Vercel yang digunakan dalam audit ini.

## 2. Struktur dan hubungan utama

| Area | Route/controller | View/service/model dan data |
|---|---|---|
| Kiosk | `/`, `/kiosk/employees`, `POST /kiosk`; `KioskController` | `kiosk/index`, `kiosk/badge`, `StoreKioskVisitRequest`, `PhotoService`, `Visit`, `VisitPhoto`, status awal `pending` |
| Gate publik | `/gate/form`, `POST /gate/form`, `/gate/pass/{token}`; `GateController` | `gate/form`, `gate/pass`, `StoreGateVisitRequest`; pendaftaran membuat status `active`, QR checkout, `GateLog` arah masuk dan riwayat awal |
| Scanner | `/gate/scanner`, `POST /gate/scanner`, `/gate/scanner/activities` | View scanner; scan masuk/keluar memakai `VisitStatusService`, event dicatat di `GateLog`; endpoint dan view memakai auth/verified serta authorization receptionist/admin |
| Operator | `/dashboard`, `/dashboard/latest`, `/visits/{visit}/*` | `VisitController`, view reception; halaman utama menggunakan eager loading pegawai, `withExists(photo)`, pagination; foto tidak ikut query daftar |
| Laporan Gate | `/gate-reports`, `/gate-reports/latest` | `GateReportController`, view admin gate; sumber data kunjungan dengan `source=gate`; laporan latest mengembalikan detail identitas hanya di route terautentikasi |
| Admin | `/admin/*` | Middleware `auth`, `verified`, `role:admin`; pengguna, pegawai, blacklist, laporan/ekspor, retensi, backup/restore, dan riwayat status |
| Autentikasi | `/login`, reset/verifikasi password, `POST /logout` | Tidak ada route registrasi publik dalam route list aktual; logout memakai POST |

Migrations memuat tabel `visits`, `gate_logs`, `visit_photos`, `visit_status_histories`, `retention_settings`, dan index kunjungan. Index yang terlihat untuk `visits` mencakup `source`, `status`, `check_in_time`, `created_at`, dan pasangan sumber/status serta sumber/check-in. Status migration pada Aiven belum diperiksa.

## 3. Fitur yang diverifikasi dari kode

- Daftar pegawai kiosk diambil asynchronous, dicache satu jam, dan route dibatasi throttle.
- Kiosk memvalidasi form melalui FormRequest, menyimpan visit/foto/riwayat dalam transaksi, membuat token QR, dan mencegah submit ganda di sisi klien.
- Gate menyimpan check-in awal serta GateLog dalam transaksi; scanner mendukung token masuk dan checkout satu kali, mencatat event, dan menyediakan aktivitas dari database.
- Dashboard memiliki filter tanggal/search, pagination antrean harian dan antrean terbuka, agregasi chart bulanan, endpoint latest terpisah, serta status update melalui service.
- Admin-only route mencakup ekspor laporan, operasi retensi, backup dan restore. Retensi memerlukan konfirmasi eksplisit dan restore memvalidasi arsip, lalu melewati record yang sudah ada.
- PWA hanya precache halaman offline dan ikon; navigasi mencoba jaringan tanpa menyimpan response navigasi ke cache. Tidak ditemukan aturan cache foto privat pada service worker.
- Wordmark terbaru berada di `public/images/logo_pelindo_transparan_HD.png`; pada baseline ukurannya 1,272,525 byte, lalu di PHASE 2 di-downsample ke 1024×724 dengan transparansi yang sama dan ukuran 42,359 byte. Gambar hero sekitar 51 KB; foto layanan/section masing-masing sekitar 18–22 KB.

## 4. Temuan dan prioritas

### P0 — Critical security/data integrity

Tidak ada isu P0 yang dapat dipastikan dari inspeksi statis ini. Database production, deployment runtime, dan sesi user production tidak diperiksa.

### P1 — Perbaikan besar untuk keamanan/operasional

1. **Validasi payload foto belum membatasi format raster secara tegas.** `StoreKioskVisitRequest` dan `StoreGateVisitRequest` menerima string base64; `PhotoService` menerima subtype `image/*`, tidak memeriksa signature terhadap MIME yang diklaim, dan pada decoder tidak tersedia/gagal dapat mengembalikan binary mentah. Endpoint privat kemudian menyajikan MIME yang disimpan sebagai response inline. Ini membuka risiko konten aktif/jenis file yang tidak diharapkan tersimpan dan disajikan pada origin aplikasi. UI menyebut JPG/PNG dan kamera membuat JPEG. Rekomendasi: izinkan format raster yang dibutuhkan, periksa konten sesungguhnya, dan tolak SVG serta payload yang tidak dapat didekode; uji tanpa database.
2. **Status migration audit/retensi dan Vercel belum diketahui.** Kode menggunakan tabel audit/retensi; handoff menyebut status Aiven belum terkonfirmasi. Rekomendasi: operator memeriksa migration terhadap target Aiven yang benar sebelum memakai fitur terkait; jangan jalankan migration dari sesi ini.
3. **Backup memuat PII/foto dan restore adalah operasi integritas tinggi.** Route dan controller membatasi operasi ke admin, namun restore belum diuji di database salinan dalam environment ini. Validasi restore terisolasi diperlukan sebelum mengandalkan backup pemulihan.

### P2 — Kualitas, responsivitas, dan pengalaman pengguna

1. **Lenis masuk ke entry bundle global.** `resources/js/app.js` mengimpor Lenis secara statis walaupun hanya menginisialisasikannya pada halaman dengan `data-lenis`. Ini berpotensi menambah JS unduhan/parse di login dan halaman operator. Rekomendasi: pertimbangkan dynamic import pada halaman yang mengaktifkan Lenis, ukur bundle/build dan perilaku reduced-motion.
2. **Landing memiliki CSS dan script inline cukup besar serta banyak gambar section.** Gambar section tidak terlihat memakai `loading="lazy"`/`decoding="async"`; dimensi eksplisit dan prioritas gambar hero perlu ditinjau pada hasil render/browser. Rekomendasi: lazy-load hanya gambar di bawah fold dan pertahankan prioritas hero.
3. **Logo wordmark berukuran 1.27 MB.** Ukuran file tinggi untuk aset identitas yang mungkin tampil di splash/layout. Rekomendasi: audit dimensi/format dan siapkan optimasi raster yang mempertahankan tampilan/alpha sebelum mengganti referensi; jangan mengubah logo tanpa verifikasi visual.
4. **Kamera kiosk diminta saat script halaman dijalankan, sebelum pengguna membuka form.** `startCamera()` dipanggil segera; gate meminta kamera hanya setelah tombol pengguna, tetapi tidak membersihkan stream jika pengguna meninggalkan halaman saat kamera aktif. Rekomendasi: mulai kamera setelah pengguna membuka/memerlukan langkah foto dan hentikan stream saat form ditutup/navigasi.
5. **Scanner mencoba memulai kamera otomatis saat `window.load`.** Ini menambah kerja awal dan dapat memunculkan prompt izin saat scanner dibuka. Rekomendasi: ukur terhadap workflow petugas dan pertimbangkan aktivasi eksplisit, tanpa mengurangi fungsi scan.
6. **Aksesibilitas label kiosk perlu pass rinci.** Sejumlah `<label>` tidak terhubung eksplisit dengan `for`/id; perlu validasi browser/screen reader serta audit kontrast/keyboard semua flow sebelum perbaikan.
7. **Permintaan aktivitas scanner dipoll tiap 10 detik.** Endpoint menggunakan filter tanggal dan mengembalikan detail aktivitas yang dibutuhkan UI; migration `gate_logs` perlu ditinjau untuk index timestamp/query plan pada skala data operasional. Belum ada query plan atau volume database aktual.
8. **Tanggal laporan dibaca melalui `$request->date()` tanpa FormRequest/validasi rentang eksplisit pada controller.** Periksa input tanggal invalid, rentang sangat lebar, dan batas beban ekspor sebelum mengandalkan laporan publik admin.
9. **Vercel response headers belum menyetel Content-Security-Policy.** `vercel.json` memiliki HSTS, `nosniff`, frame/referrer/permissions policy. CSP adalah defense-in-depth; karena halaman memakai inline script/style dan CDN eksternal, policy harus dirancang dan diuji bertahap agar tidak memblokir flow.

### P3 — Optimasi tambahan

- Hindari menginisialisasi handler global yang tidak dipakai pada halaman jika pengukuran menunjukkan biaya nyata.
- Tambahkan dimensi gambar eksplisit dan strategi font lokal/remote berdasarkan hasil network trace.
- Tinjau cache browser asset statis dan header keamanan tambahan dengan uji kompatibilitas yang sesuai, tanpa melemahkan perlindungan yang ada.

## 5. File/area penting yang ditinjau

`composer.json`, `package.json`, `vite.config.js`, `tailwind.config.js`, `vercel.json`, `api/index.php`, `routes/web.php`, `routes/auth.php`, `KioskController`, `GateController`, `VisitController`, controller admin, model `Visit`/`GateLog`/`VisitPhoto`/`VisitStatusHistory`, `PhotoService`, `VisitStatusService`, `VisitBackupService`, FormRequest kiosk/gate, migrations, tests, layout Blade, halaman kiosk/gate/dashboard, CSS/JS, service worker, manifest, serta aset landing/logo.

## 6. Cakupan runtime dan Lighthouse

Lampiran Lighthouse dari pengguna menunjukkan skor **Performance 55, Accessibility 80, Best Practices 100, SEO 82**. URL pada screenshot adalah `/dashboard?chart_week=5`, sehingga ini adalah baseline dashboard (bukan landing page); angka tersebut merupakan hasil terdahulu dan bukan pengukuran yang dijalankan pada audit ini. Screenshot tidak memuat LCP, INP, CLS, FCP, TBT atau rincian resource.

Tidak ada binary Lighthouse lokal atau browser automation yang tersedia pada PATH/dependency checkout. Sesuai batasan instruksi, tidak ada permintaan ke URL production dan tidak ada dashboard authenticated dibuka. Oleh sebab itu:

| Halaman | Audit source | Runtime/browser | Lighthouse mobile/desktop |
|---|---|---|---|
| Landing `/` | Ditinjau | NOT TESTED | NOT TESTED |
| Form kiosk | Ditinjau | NOT TESTED | NOT TESTED |
| Gate publik `/gate/form` | Ditinjau | NOT TESTED | NOT TESTED |
| Login | Route/view ditinjau | NOT TESTED | NOT TESTED |
| Dashboard `/dashboard` | Ditinjau; screenshot baseline lama tersedia | NOT TESTED (tanpa sesi sah) | NOT TESTED ulang |
| Scanner `/gate/scanner` | Ditinjau | NOT TESTED (tanpa sesi sah) | NOT TESTED |
| Laporan | Ditinjau | NOT TESTED (tanpa sesi sah) | NOT TESTED |
| Admin | Route/controller/view ditinjau | NOT TESTED (tanpa sesi sah) | NOT TESTED |

## 7. Validasi aktual Phase 1

- `git status --short`: hanya `M HANDOFF-CODEX-ANTIGRAVITY.md`, perubahan lokal sebelumnya dipertahankan.
- `git status -sb`: `main...origin/main`; HEAD sama dengan `origin/main` pada `026c170`.
- `git log -5 --oneline`: `026c170`, `d85da5c`, `a34203d`, `7ad8484`, `71e12f9`.
- `git diff` memperlihatkan perubahan handoff lokal; `git diff --cached` kosong.
- `composer validate --no-check-publish`: PASS, `composer.json is valid`.
- `php artisan route:list --except-vendor --json`: PASS; route map dikonfirmasi termasuk middleware yang tercantum di atas.
- Runtime lokal: PHP 8.2.35, Node 24.14.1, npm 11.11.0. Extension `pdo_sqlite`/GD tidak tampak di daftar module yang diperiksa; `pdo_mysql` tersedia. PHPUnit dikonfigurasi eksplisit untuk SQLite `:memory:`.
- `php artisan view:cache`: PASS. `npm run build`: PASS (baseline app JS 132.12 KB/45.72 KB gzip, CSS 100.53 KB/17.31 KB gzip, badge 38.51 KB/15.63 KB gzip).
- `php artisan test`: 3 PASS, 49 BLOCKED ketika SQLite test DB bootstrap memberi `could not find driver`; tidak ada assertion failure yang membuktikan cacat aplikasi. Konfigurasi target database test adalah SQLite `:memory:` dari `phpunit.xml`. Tidak ada koneksi atau query ke Aiven.
- `node --check resources/js/app.js`, `node --check resources/js/badge.js`, `git diff --check`: PASS.

## 10. Tindak lanjut PHASE 2

Perbaikan lokal sudah diterapkan dan dirinci dalam `PERFORMANCE-REPORT.md`: dynamic import Lenis, pemuatan gambar bawah fold, akses kamera kiosk setelah form dibuka dan pembersihan stream saat meninggalkan halaman, serta pengetatan tipe/signature foto JPEG/PNG dengan tes unit. Build terbaru menunjukkan app JS 114.73 KB/41.25 KB gzip dan Lenis menjadi chunk 18.71 KB/5.45 KB gzip. Perubahan ini belum diuji di browser/Lighthouse dan belum dideploy.

## 8. Risiko dan hal yang belum terkonfirmasi

- Deployment yang sedang aktif dan commit yang dilayani Vercel belum diverifikasi.
- Migration `visit_status_histories`/`retention_settings` di Aiven belum terverifikasi. Jangan menggunakan status lokal sebagai bukti production.
- Kredensial Aiven yang sempat terlihat pada screenshot lama belum diketahui status rotasinya; lakukan rotasi melalui prosedur operasional bila belum, jangan menaruh nilainya di laporan/source control.
- Plan/biaya/status Aiven, query plan, volume tabel, log production, session browser, dan keberhasilan WhatsApp aktual belum diverifikasi.
- Restore hanya layak dipercaya setelah diuji pada database test/salinan terisolasi.
- Ukuran/aspect ratio skeleton dibanding halaman nyata dan skor mobile belum diukur.

## 9. Rekomendasi urutan implementasi

1. Tangani validasi dan penyajian foto dengan tes unit tanpa database. (Dilakukan di PHASE 2.)
2. Tunda kamera kiosk sampai form dibuka dan bersihkan stream saat navigasi. (Dilakukan di PHASE 2.)
3. Pecah pemuatan Lenis menjadi non-critical, lazy-load gambar bawah fold, dan optimasi resolusi wordmark. (Dilakukan di PHASE 2.)
4. Periksa input tanggal laporan, polling gate/index yang didukung schema, dan budget ekspor memakai validasi lokal tanpa production.
5. Rancang CSP defense-in-depth, uji report-only dahulu, kemudian audit aksesibilitas dan browser/network mobile/desktop saat environment tersedia.
6. Minta izin/akses operator untuk pemeriksaan Vercel, migration Aiven, dan Lighthouse production sebelum menyatakan deployment siap.
