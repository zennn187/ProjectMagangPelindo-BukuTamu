# Performance dan Security Optimization — Phase 2

Tanggal: 26 November 2026 (Asia/Jakarta)  
Baseline kode: `026c170` pada branch `main`  
Status: perubahan lokal belum di-commit, dipush, atau dideploy.

## Perubahan dan alasan

| File | Perubahan | Alasan/dampak yang diharapkan |
|---|---|---|
| `resources/js/app.js` | Lenis diimpor secara dynamic hanya ketika `body` memakai `data-lenis` dan reduced motion tidak aktif. | Mengurangi JS yang diunduh dan diparse pada login/operator yang tidak memakai smooth scroll; kegagalan chunk kembali ke native scroll. |
| `resources/views/kiosk/index.blade.php` | Stream kamera baru dimulai sesudah pengguna membuka form; stream dihentikan pada `pagehide`. Hero diberi `fetchpriority="high"` dan `decoding="async"`; lima foto section diberi `loading="lazy"`/`decoding="async"`. | Hindari prompt/akses kamera saat pengunjung baru melihat landing; utamakan hero dan tunda gambar di bawah fold tanpa mengubah aset/tampilan. |
| `resources/views/gate/form.blade.php` | Kamera tetap dimulai lewat aksi pengguna; stream juga dibersihkan saat halaman ditinggalkan. | Mencegah kamera aktif tertinggal selama navigasi/penutupan halaman. |
| `public/images/logo_pelindo_transparan_HD.png` | Wordmark downsample dari 8192×5792 menjadi 1024×724 px; alpha/transparansi dipertahankan dan nama/path tetap. | File turun dari 1,272,525 byte ke 42,359 byte (sekitar 96.7%) dengan kepadatan hampir 5× pada ukuran tampil maksimum sekitar 210 px. |
| `app/Services/PhotoService.php` | Hanya menerima data URL JPEG/PNG dan memeriksa signature JPEG/PNG terhadap MIME sebelum simpan. SVG serta MIME/signature mismatch ditolak. | Mencegah konten aktif/format tak terduga disajikan sebagai foto dari endpoint satu origin. Tetap mendukung kamera JPEG dan PNG. |
| `tests/Unit/PhotoServiceTest.php` | Tes unit untuk SVG/mismatch ditolak dan PNG dengan signature cocok diterima. | Menguji batas keamanan tanpa database. |
| `tests/Feature/VisitWhatsAppFlowTest.php` | Fixture foto palsu diganti PNG lokal yang valid; assertion memastikan isi tersimpan. | Menjaga tes alur kiosk tetap sesuai validasi foto yang diperketat. |

Tidak ada dependency baru, perubahan schema, migration, perubahan business status, penggantian identitas logo, atau perubahan token/URL QR.

## Pengukuran build

Build baseline sebelum perubahan, `npm run build`:

- App JS: 132.12 KB (45.72 KB gzip).
- CSS: 100.53 KB (17.31 KB gzip).
- Bundle badge: 38.51 KB (15.63 KB gzip).

Build setelah perubahan Lenis, `npm run build`:

- App JS: 114.73 KB (41.25 KB gzip), turun 17.39 KB raw / 4.47 KB gzip.
- Chunk Lenis terpisah: 18.71 KB (5.45 KB gzip), diminta dinamis pada halaman yang memakai Lenis.
- CSS: 100.53 KB (17.31 KB gzip), tidak berubah.
- Bundle badge: 38.51 KB (15.63 KB gzip), tidak berubah.
- Wordmark public asset: 1,272,525 → 42,359 byte; PNG 1024×724 dan transparansi tetap.

Angka build adalah ukuran artefak bundler, bukan skor Lighthouse atau metrik pengguna. Dampak network aktual belum diukur.

## Hasil validasi fase

- `php artisan test tests/Unit/PhotoServiceTest.php`: PASS, 2 tes / 6 assertions.
- `npm run build`: PASS, Vite 7.3.6; 112 modules transformed.
- `php artisan view:cache`: PASS.
- `php -l` pada `PhotoService.php`, `PhotoServiceTest.php`, dan fixture feature test: PASS.
- `node --check resources/js/app.js`: PASS.
- `git diff --check`: PASS; Git memberi peringatan normal bahwa working copy `PhotoService.php` CRLF akan dinormalisasi menjadi LF pada operasi Git berikutnya.
- Full `php artisan test` baseline sebelum optimasi: 3 PASS, 49 gagal saat migrasi test DB dimulai dengan `could not find driver` pada SQLite `:memory:`. Ini BLOCKED oleh environment; bukan kegagalan assertion aplikasi. Tes menggunakan database in-memory dari `phpunit.xml`, bukan Aiven.
- `php artisan view:cache` dan route listing tidak membuka koneksi ke production.

## Lighthouse/Core Web Vitals

Screenshot sebelumnya menunjukkan dashboard desktop Performance 55, Accessibility 80, Best Practices 100, SEO 82. Nilai tersebut bukan pengukuran ulang. Lighthouse/browser automation tidak tersedia pada environment ini, tidak ada sesi sah untuk dashboard, dan URL production tidak diuji. Jadi tidak ada skor sesudah, LCP, INP, CLS, FCP, TBT, request trace, atau ukuran resource network yang dapat dilaporkan.

Target 90–100/95–100 tetap sasaran, bukan hasil yang dapat diklaim saat ini. Perbandingan harus diulang pada URL dan kondisi yang sama, mobile serta desktop, setelah deployment dan izin pengujian diberikan.

## Risiko tersisa

- Wordmark mempertahankan filename, warna, komposisi, dan transparansi; perubahan resolusi diperiksa melalui metadata serta pratinjau. Pengujian pada halaman browser belum tersedia.
- Style inline besar pada kiosk, remote Bunny Fonts, scanner script pihak ketiga, dan ukuran CSS belum diubah; perlu network trace/browser profiling sebelum modifikasi.
- Validasi signature mengidentifikasi format raster yang dideklarasikan tetapi tidak melakukan decode penuh ketika runtime tidak memiliki GD. Konten selain JPEG/PNG dan signature mismatch ditolak; endpoint foto tetap authenticated/authorized serta mengirim `X-Content-Type-Options: nosniff`.
- Tes feature yang memakai SQLite belum bisa memverifikasi persistensi, Gate, laporan, backup/restore, atau session hingga driver PDO SQLite tersedia.
- Kamera dan pemuatan gambar belum diuji pada perangkat browser nyata.
