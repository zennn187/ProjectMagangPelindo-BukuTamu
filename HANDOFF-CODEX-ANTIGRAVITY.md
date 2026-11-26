# Handoff Konteks Buku Tamu — Codex / Antigravity

Dokumen ini merangkum konteks produk, keputusan UI, alur penting, pekerjaan terdahulu, dan kondisi perubahan terakhir agar agent baru dapat melanjutkan tanpa membaca seluruh riwayat chat. **Kode dan status Git saat ini tetap menjadi sumber kebenaran**; ringkasan ini tidak berarti semua perubahan sudah di-commit atau di-deploy.

## Instruksi untuk agent berikutnya

1. Baca dokumen ini, lalu cek `git status --short` dan `git diff` sebelum mengubah kode. Jangan menimpa atau membuang edit lokal.
2. Mulai dari permintaan terbaru pengguna dan file yang terkait. Jangan mengulang fitur yang sudah selesai atau memperluas lingkup tanpa alasan.
3. Pertahankan arsitektur Laravel Blade/Vite yang ada. Hindari migrasi ke React atau penambahan library besar kecuali diminta secara eksplisit.
4. Ikuti komponen, route, helper, authorization, gaya, dan pola tes yang telah ada. Jangan mengubah palet atau tampilan halaman lain saat mengerjakan fitur lokal.
5. Untuk perubahan UI, validasi build dan halaman/flow yang tepat. Untuk perubahan data, tambahkan tes terarah dan jangan memakai database produksi sebagai database tes.
6. Jangan menganggap perubahan lokal sudah dipush, dideploy, atau migrasi production sudah dijalankan. Konfirmasi melalui bukti yang tersedia.

## Produk dan stack

- Aplikasi buku tamu operasional untuk **Pelindo Regional 1 Dumai**.
- Backend: PHP 8.2, Laravel 12, Eloquent, MySQL Aiven; deployment aplikasi di Vercel.
- Frontend: Blade, Tailwind CSS, Vite, Alpine.js, vanilla JavaScript; animasi tertentu memakai Anime.js/Lenis.
- Laporan Excel memakai Laravel Excel. Tidak ada React/shadcn setup; chart bergaya particle dibuat dengan Blade/SVG sebagai fallback yang disepakati.
- Bahasa UI: Bahasa Indonesia. Sasaran UX: cepat untuk petugas/tamu, sederhana, elegan, mudah dibaca, tidak terasa “AI slop”, tanpa emoji yang tidak perlu.

## Identitas visual — jangan diubah tanpa permintaan

Palet utama didefinisikan di `tailwind.config.js` sebagai warna `bank`:

| Token | Nilai | Pemakaian umum |
|---|---|---|
| `bank-blue` | `#1814F3` | Aksi utama, tautan, menu aktif |
| `bank-navy` | `#343C6A` | Judul dan teks utama |
| `bank-bg` | `#F5F7FA` | Latar halaman |
| `bank-light` | `#E6EFF5` | Border dan pemisah lembut |
| `bank-gray` | `#718EBF` | Teks sekunder |
| `bank-red` | `#FE5C73` | Aksi/peringatan merah yang memang semantik |
| `bank-green` | `#41D4A8` | Aksen positif |
| `bank-yellow` | `#FFBB38` | Aksen peringatan |

- Font utama: **Figtree**.
- Operator memiliki tema gelap di `resources/css/app.css`: canvas `#0b1220`, surface `#111c2e`, raised `#17243a`, muted surface `#1c2b42`, border `#28384f`, teks `#e7eef9`, teks sekunder `#9aacc4`.
- Utamakan token `bank-*`, utility, dan pola layout yang sudah ada; jangan menambah warna hardcoded baru jika token yang tersedia cukup.
- Preferensi pengguna untuk status di dashboard: **semua status memakai satu warna netral yang konsisten**, bukan warna berbeda untuk setiap status. Jangan mengubah ini menjadi label pelangi. Gunakan warna semantik untuk notifikasi/aksi hanya jika pola lama memang memerlukannya.
- Hindari emoji, gradient/animasi berlebihan, kartu terlalu membulat/berbayang, dan ikon dekoratif yang tidak membantu.
- Hormati mode gelap, keyboard/focus states, kontras, dan `prefers-reduced-motion`.

## Alur produk dan akses

### Tamu / kiosk

- `GET /` (`kiosk`) menampilkan landing page dan form tamu publik.
- `GET /kiosk/employees` mengambil daftar pegawai secara asynchronous; `POST /kiosk` menyimpan pendaftaran, dibatasi throttle.
- Form dibuat agar cepat dipakai, tidak memaksa scroll panjang, dan tidak menutup layar dengan spinner permanen.
- Foto baru diproses/dikompres lalu disimpan sebagai BLOB pada `visit_photos`, bukan mengandalkan filesystem sementara Vercel.
- Halaman badge/QR memakai token. Form publik tidak memerlukan login.

### Gate

- Form publik: `GET/POST /gate/form`; menyimpan kunjungan dengan sumber gate.
- Scanner operator: `/gate/scanner`, di belakang `auth` dan `verified`; scan menghasilkan check-in/checkout dan `GateLog`.
- Aktivitas gate bersumber dari log database (`GateLog`), bukan hanya data lokal browser. Endpoint aktivitas `GET /gate/scanner/activities`.
- Laporan/kedatangan gate: `/gate-reports` dan `/gate-reports/latest`, di belakang autentikasi.
- Jangan mengekspos nomor telepon, identitas, foto, atau data pribadi melalui endpoint publik. Data rinci gate hanya boleh tersedia untuk operator berwenang.

### Operator dan admin

- Dashboard: `/dashboard`; dashboard, endpoint foto, scanner, laporan gate, dan mutasi status berada di grup autentikasi/verified.
- Mutasi kunjungan memakai route di `/visits/{visit}/...` (check-in, check-out, wait, accept-delivery, reject).
- Fitur admin berada di prefix `/admin` dan memakai `role:admin`: pengguna, pegawai, daftar hitam, laporan, pengelolaan data/retensi/backup, riwayat status.
- Registrasi publik akun dinonaktifkan karena default role user berisiko memberikan role admin; admin membuat akun melalui menu pengguna yang terlindungi.
- Logout adalah POST; tampilkan jelas untuk penggunaan perangkat bersama. Jangan menaruh data privat di cache PWA.

## Fitur penting yang sudah dibangun

Ini adalah ringkasan berdasarkan checkpoint/riwayat pekerjaan. Verifikasi implementasi aktual sebelum mengubahnya.

1. **Foto dan dashboard**
   - Foto disimpan di `visit_photos` dan dibaca lewat route foto yang dilindungi auth.
   - Tabel daftar menghindari pemuatan BLOB massal; pola yang digunakan mencakup pemeriksaan keberadaan foto lalu mengambil foto saat diperlukan.
   - Aksi dashboard memakai AJAX/JSON agar tidak reload penuh; notifikasi WhatsApp dijalankan setelah respons bila queue runtime bersifat sync.
   - Status pada dashboard memakai warna netral yang seragam.

2. **Laporan**
   - Laporan memakai database dan mendukung rentang mingguan, bulanan, 6 bulan, setahun, dan rentang tanggal khusus; ekspor Excel mengikuti filter periode.
   - Identitas tamu seperti foto/telepon ditampilkan pada area laporan yang sesuai.
   - Chart laporan dibuat ringkas menggunakan Blade/SVG. Chart dashboard memakai visual titik/particle-inspired SVG; ini bukan React Bits Pro asli.

3. **Antrean dan navigasi**
   - Dashboard memiliki “Antrean Kunjungan Belum Selesai” lintas tanggal untuk status `pending`, `waiting`, dan `active`, dengan pagination dan aksi proses.
   - Chart mingguan memakai minggu kalender W1–W5 (jika bulan memiliki tanggal 29–31), bukan jendela tujuh hari berjalan.
   - Pengguna pernah meminta urutan sidebar: Dashboard, Antrean Kunjungan, Scanner Gate, Pengguna, Riwayat Status, Laporan, Pengelolaan Data, Setting; ada pula menu Kedatangan Tamu/Antrean Gate untuk fungsi gate. Pertahankan urutan dan aturan role yang sedang ada; jangan menampilkan menu admin kepada receptionist.

4. **Gate dan aktivitas**
   - GateLog merekam peristiwa masuk/keluar terpisah.
   - Scanner memiliki lima aktivitas terbaru dan dapat memuat aktivitas hari itu lebih lanjut; cek implementasi pagination/filter tanggal aktual sebelum mengubah.
   - Form gate disederhanakan untuk ponsel; notifikasi kedatangan/selesai seharusnya toast sementara, bukan banner permanen.
   - Pernah terjadi Vercel HTTP 500 karena `compact(): Undefined variable $activityDay` di `GateController`; perbaikan telah masuk baseline sebelum perubahan popover terakhir. Jangan menghapus inisialisasi itu.

5. **Status, audit, retensi, backup**
   - `VisitStatusService` mengubah status dalam transaksi dan mencatat actor, aksi, status lama/baru, catatan, waktu ke `visit_status_histories`.
   - Admin dapat mengatur retensi foto dan kunjungan terpisah, melihat perkiraan data terdampak, mengunduh backup, lalu mengonfirmasi hapus secara manual. Penghapusan bukan otomatis/diam-diam.
   - Backup aplikasi berupa ZIP berisi data kunjungan/foto/log/riwayat terkait, dengan validasi arsip. Restore menggabungkan data yang hilang dan tidak menimpa data kunjungan yang sudah ada.
   - Backup aplikasi berbeda dari snapshot provider Aiven. Arsip mengandung PII/foto dan harus disimpan aman.

6. **Loading, PWA, aksesibilitas**
   - PWA memiliki manifest/icon/offline fallback; halaman sensitif dan foto privat tidak disimpan dalam cache offline.
   - Skeleton landing/dashboard dan splash bermerek Pelindo dengan progress bar pernah dibuat. Skeleton dipilih berdasarkan tujuan navigasi; splash tampil sekali per tab.
   - Beberapa checkpoint mencatat ketidaksamaan tinggi skeleton dengan landing asli yang perlu dibandingkan kembali jika tugas menyentuh loading UI.
   - Reduced motion, focus states, dan indikator koneksi/network/database telah ditambahkan di sejumlah halaman.

## Pekerjaan terbaru yang belum di-commit

Saat dokumen ini dibuat:

- Branch: `main`.
- Commit dasar yang terlihat: `a34203d fix error scan gate`.
- `git status --short` menunjukkan tiga file berubah:
  - `app/Http/Controllers/GateController.php`
  - `resources/views/gate/scanner.blade.php`
  - `tests/Feature/GateReportTest.php`
- Perubahan terbaru memungkinkan nama di bagian **Aktivitas Hari Ini** pada Scan Gate dibuka sebagai kartu detail:
  - Data detail meliputi nama, instansi, telepon, nomor identitas, pegawai tujuan, keperluan, status, check-in/check-out, durasi, serta jenis/waktu aktivitas.
  - Aktivitas awal dirender server-side; aktivitas tambahan dibuat dari response API dengan format yang sama.
  - Nama menjadi tombol aksesibel. Hover mouse membuka kartu; klik/tap mengunci kartu agar dapat dibaca di perangkat sentuh.
  - Kartu dapat ditutup dengan tombol, Escape, atau klik di luar; posisinya menyesuaikan viewport/scroll.
  - Isi detail memakai `textContent`; JSON pada atribut tombol di-escape. Detail berisi PII sehingga endpoint harus tetap terautentikasi/terotorisasi.
- Validasi yang sudah dijalankan untuk perubahan ini:
  - `php -l app\Http\Controllers\GateController.php`: lulus.
  - `php artisan view:cache`: lulus.
  - `npm run build`: lulus.
  - Pemeriksaan sintaks JavaScript inline di `scanner.blade.php`: lulus.
  - `git diff --check`: lulus.
  - `php artisan test tests\Feature\GateReportTest.php`: **tidak dapat berjalan**, karena PHP lokal tidak menemukan driver PDO SQLite (`could not find driver`); 0 assertions. Ini keterbatasan environment, bukan bukti tes lulus.
- Belum ada commit, push, atau deployment yang dikonfirmasi untuk perubahan ini.

## Deployment, database, dan keamanan operasional

- Database produksi adalah MySQL Aiven; deployment aplikasi menggunakan Vercel. Jangan menjalankan migration/command destructive sebelum memastikan koneksi menunjuk ke target yang dimaksud.
- Tabel foto memerlukan migration `2026_11_12_000001_create_visit_photos_table.php`; pengguna pernah menjalankannya di Aiven dan melihat tabel `visit_photos` tersedia.
- Migration audit/retensi `2026_11_13_000001_create_visit_status_histories_and_retention_settings.php` pernah ditambahkan. Status production terakhir dalam catatan tidak cukup pasti: cek `php artisan migrate:status` pada target Aiven yang benar sebelum deploy kode yang memakai tabel tersebut.
- Jika migrasi baru perlu production: buat/konfirmasi snapshot Aiven, verifikasi host/database/SSL, jalankan migration dengan konfigurasi production yang terkontrol, lalu deploy aplikasi. Jangan pernah sertakan password/kredensial dalam log atau dokumen.
- Kredensial Aiven pernah terlihat dalam screenshot di riwayat lama; status rotasi tidak dikonfirmasi. Bila belum dilakukan, rotasi password dan perbarui secret `DB_PASSWORD` di Vercel.
- Ada catatan historis bahwa Free Tier Aiven dijadwalkan dihentikan pada 21 Desember 2026; verifikasi status/plan terkini langsung di console Aiven, jangan menganggap tanggal/biaya masih sama.
- Jangan melakukan deploy, push, migration production, penghapusan data, atau restore tanpa instruksi dan target yang jelas.

## Tes dan cara melanjutkan

- Tes feature terkait gate: `php artisan test tests\Feature\GateReportTest.php` (saat ini terhalang PDO SQLite lokal).
- Validasi UI/asset: `php artisan view:cache`, `npm run build`, `git diff --check`.
- PHP: `php -l <file>`; gunakan Pint sesuai pola repo bila file PHP berubah.
- Sebelum menyimpulkan kegagalan tes, bedakan masalah aplikasi dari keterbatasan driver/environment. Jangan mengubah database produksi untuk membuat tes lewat.
- Setelah perubahan, laporkan file yang diubah, tes yang berhasil/gagal dan alasannya, serta apakah perubahan sudah commit/push/deploy (biasanya belum kecuali ada bukti).

## Prompt awal yang disarankan

> Baca `HANDOFF-CODEX-ANTIGRAVITY.md` terlebih dahulu. Lanjutkan hanya permintaan terbaru saya. Cek `git status --short` dan diff sebelum mengedit; pertahankan semua edit lokal. Ikuti palet, template, alur akses, dan pola yang didokumentasikan. Jangan mengubah warna/tema atau fitur lain di luar scope, jangan menjalankan migration production atau deploy tanpa konfirmasi. Validasi perubahan dengan tes/build yang relevan dan jelaskan hasilnya.
