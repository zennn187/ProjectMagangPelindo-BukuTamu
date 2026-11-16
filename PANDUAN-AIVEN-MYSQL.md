# Panduan: MySQL Online Gratis via Aiven (untuk Vercel)
# Project: BukuTamu Pelindo | DB lokal: buku_tamu (MySQL 127.0.0.1, ±55 KB)
# File export sudah siap: database/buku_tamu_export.sql

## LANGKAH 1 — Daftar & buat service MySQL (10 menit)
1. Buka https://aiven.io → Sign up (email saja, TANPA kartu kredit).
2. Buat Organization + Project (nama bebas, mis. `bukutamu-pelindo`).
3. Create service:
   - Type: **MySQL**
   - Plan: **Free** (1 CPU / 1 GB RAM / 1 GB storage — DB kamu cuma ±55 KB, muat jauh)
   - Cloud + Region: **Google Cloud / asia-southeast1 (Singapore)** — terdekat ke Vercel & ke Dumai
   - Service name: `bukutamu-mysql`
4. Tunggu status: **Running** (hijau, ±3–5 menit).

## LANGKAH 2 — Siapkan database + user
1. Di halaman service → tab **Databases** → Create database: `buku_tamu`.
2. Tab **Users** → user default `avnadmin` sudah ada — catat passwordnya
   (atau buat user baru `bukutamu` biar rapi).
3. Tab **Overview** → catat 4 nilai ini:
   - Host      : mis. `bukutamu-mysql-xxx.aivencloud.com`
   - Port      : mis. `21354` (CATAT! Bukan 3306 — tiap service Aiven portnya beda)
   - User      : `avnadmin`
   - Password  : (klik Show)
   - SSL Mode  : `required` (wajib — lihat Langkah 4)

## LANGKAH 3 — Import data (pilih SALAH SATU)

### Opsi A — phpMyAdmin lokal (paling mudah)
1. Aiven tidak punya phpMyAdmin bawaan, jadi konekkan phpMyAdmin lokalmu ke Aiven:
   - Hati-hati: butuh edit config + file CA — lanjut ke Opsi B kalau ragu.
2. ATAU pakai tool gratis: **DBeaver** (dbeaver.io) / **MySQL Workbench**:
   - New connection → host/port/user/password dari Langkah 2
   - SSL: aktifkan, masukkan file `ca.pem` (download dari tab Overview Aiven)
   - Konek → buka database `buku_tamu` → Import file:
     `database/buku_tamu_export.sql` (sudah saya siapkan di repo, 55 KB)

### Opsi B — Command line (XAMPP, tanpa install apa-apa)
PowerShell, ganti HOST/PORT/USER/PASSWORD dengan nilai Langkah 2:
```powershell
C:\xampp\mysql\bin\mysql.exe -h HOST_AIVEN -P PORT_AIVEN -u avnadmin -p --ssl-ca="C:\path\ke\ca.pem" buku_tamu < "d:\Project Magang Pelindo\BukuTamu\database\buku_tamu_export.sql"
```
Download `ca.pem` dari dashboard Aiven (Overview → CA certificate → Download).
Ketik password saat diminta. Sukses = tidak ada pesan error.

### Verifikasi import
Jalankan di tool / CLI yang sama:
```sql
SELECT COUNT(*) FROM visits;  -- harusnya 31
SELECT COUNT(*) FROM users;   -- harusnya 2
```

## LANGKAH 4 — SSL: PENTING untuk Laravel di Vercel
Aiven **mewajibkan SSL**. `config/database.php` mendukung `MYSQL_ATTR_SSL_CA`,
dan file CA `database/aiven-ca.pem` sudah ada di repository. Di Vercel
Environment Variables, isi `MYSQL_ATTR_SSL_CA` dengan path file CA pada runtime:
```
MYSQL_ATTR_SSL_CA=/var/task/user/database/aiven-ca.pem
```
Jangan kosongkan variabel ini; pastikan file CA berasal dari Aiven dan ikut
termasuk dalam deployment.

## LANGKAH 5 — Isi Environment Variables di Vercel
Dashboard Vercel → project → Settings → Environment Variables
(halaman yang kemarin sudah kamu buka — Add Environment Variable).
Scope: centang **Production + Preview**. Timpa / tambah:

```
DB_CONNECTION=mysql
DB_HOST=bukutamu-mysql-xxx.aivencloud.com   ← dari Langkah 2, BUKAN 127.0.0.1
DB_PORT=21354                                ← port Aiven, BUKAN 3306
DB_DATABASE=buku_tamu
DB_USERNAME=avnadmin
DB_PASSWORD=(password Langkah 2)
MYSQL_ATTR_SSL_CA=/var/task/user/database/aiven-ca.pem
APP_URL=https://projectmagangpelindo-bukutamu.vercel.app
APP_DEBUG=false
APP_KEY=(isi sebagai secret Vercel; jangan tulis di repo atau kirim lewat chat)
SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
WHATSAPP_ENABLED=false
```

**Penting:** nilai `APP_KEY` yang pernah tertulis di dokumen/repository harus dianggap sudah terekspos. Buat key baru dan simpan langsung sebagai Environment Variable rahasia di Vercel; jangan gunakan ulang key dari riwayat Git. Mengganti key akan mengeluarkan sesi login yang tersimpan.

## LANGKAH 6 — Redeploy & test realtime
1. Vercel → tab **Deployments** → `...` di deploy terbaru → **Redeploy**.
2. Tunggu `Ready`, buka: https://projectmagangpelindo-bukutamu.vercel.app/
3. Test dari 2 HP tanpa local lagi:
   - HP tamu (tanpa login): `/gate/form` → isi 1 tamu dummy → submit sukses
   - Laptop petugas (login receptionist): `/gate/scanner` → kamera auto-start
     → scan QR dari kartu kuning "Testing Scan" → TING-TONG + kotak hijau
     → cek laporan `/gate-reports` → data masuk
4. Kalau masih merah, buka tab **Logs** → copy 10 baris terakhir ke saya.

## CATATAN AIVEN FREE TIER
- Bisa di-power-off otomatis bila lama tanpa aktivitas (ada email notifikasi dulu).
  Nyalakan lagi: dashboard Aiven → service → Power on. Data TIDAK hilang.
- Max 76 koneksi — cukup untuk testing + demo magang.
- Foto tamu dikompres (maksimum sisi 640 px) lalu disimpan sebagai BLOB pada tabel
  `visit_photos` di database yang sama. Foto tidak ikut terbaca saat query daftar
  dashboard dan endpoint fotonya tetap memerlukan login petugas.
- Jika aplikasi production menampilkan error `visit_photos doesn't exist`, tabel
  tersebut belum dibuat di Aiven. Migrasi foto hanya menambahkan tabel baru dan
  tidak menghapus data kunjungan.
- Untuk menjalankan migrasi dari komputer Windows:
  1. Buat backup database Aiven terlebih dahulu.
  2. Simpan nilai koneksi lokal `.env` Anda sementara di tempat aman. Jangan
     kirim atau commit nilai-nilai ini.
  3. Di `.env`, sementara isi `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`,
     `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan nilai **Production**
     dari Vercel → Project → Settings → Environment Variables. Untuk host dan
     port, cocokkan dengan Aiven; log production saat ini menunjukkan
     `mysql-37bc1d-bukutamu-pelindo.b.aivencloud.com:13192`.
  4. Untuk `MYSQL_ATTR_SSL_CA`, pakai path file CA lokal, bukan path runtime
     Vercel. File di repo tersedia di
     `D:\Project Magang Pelindo\BukuTamu\database\aiven-ca.pem`.
  5. Di terminal pada folder proyek, jalankan:
     ```powershell
     php artisan config:clear
     php artisan tinker --execute="dump(config('database.connections.mysql.host'), config('database.connections.mysql.port'), config('database.connections.mysql.database'));"
     php artisan migrate:status
     ```
     Pastikan hasil perintah pertama menunjukkan host dan port Aiven serta
     database `buku_tamu`.      Periksa daftar migrasi pending. Migrasi aplikasi yang
     diharapkan saat ini adalah `2026_11_12_000001_create_visit_photos_table`,
     `2026_11_13_000001_create_visit_status_histories_and_retention_settings`,
     dan `2026_11_13_000002_set_default_user_role_to_receptionist`.
     Jika ada migrasi lain yang tidak dikenal, berhenti dan periksa dahulu.
     Jika `migrate:status` menghasilkan `SQLSTATE[HY000] [1045] Access denied`,
     jangan jalankan migrasi. Pastikan username dan password berasal dari user
     Aiven yang sama (bukan Service URI), perbarui password lokal/Vercel bila
     perlu, lalu ulangi `config:clear` dan `migrate:status`. Jangan kirim password
     ke chat atau commit ke repository.
  6. Jalankan `php artisan migrate --force` hanya setelah target Aiven
     terverifikasi dan migrasi pending sudah diperiksa.
  7. Pulihkan nilai koneksi lokal `.env`, lalu jalankan
     `php artisan config:clear` lagi. Jangan commit `.env`.
- Setelah migrasi Aiven sukses, coba kirim formulir production sekali lagi.
- Foto baru akan persisten. Foto lama yang hanya tersimpan di filesystem sementara
  Vercel tidak dapat dipulihkan setelah file sementara tersebut hilang.

## Backup, pemulihan, dan penghapusan data
Setelah aplikasi terbaru dideploy dan semua migrasi di atas berstatus `Ran`:

1. Login sebagai admin dan buka menu **Pengelolaan Data**.
2. Masa simpan awal untuk foto dan catatan kunjungan masing-masing 365 hari.
   Admin dapat mengubah keduanya secara terpisah (30–3.650 hari). Mengubah
   pengaturan tidak langsung menghapus data.
3. Untuk backup, pilih rentang tanggal lalu klik **Unduh backup ZIP**. Arsip
   berisi kunjungan, foto yang tersedia, log gate, dan riwayat status. Maksimum
   1.000 kunjungan dan 4 MB per file; bagi rentang menjadi beberapa bagian bila
   batas terlampaui. Simpan file dengan aman karena berisi data pribadi.
4. Untuk memulihkan data yang hilang, pilih file ZIP dari fitur backup aplikasi
   dan klik **Pulihkan backup**. Proses menggabungkan catatan yang hilang, tidak
   menimpa catatan yang sudah ada, serta melewati duplikat. Arsip yang rusak atau
   berasal dari format tidak dikenal akan ditolak.
5. Untuk menghapus data yang melewati masa simpan, pastikan backup sudah
   tersimpan, periksa jumlah data kedaluwarsa pada halaman yang sama, lalu ketik
   `HAPUS` dan konfirmasi. Penghapusan dilakukan manual oleh admin, bukan otomatis;
   data hanya dapat dipulihkan bila arsip backup tersedia.
6. Untuk rekap tahunan, menu **Laporan** memiliki ekspor Excel. ZIP backup ditujukan
   untuk pemulihan aplikasi; keduanya tidak menggantikan snapshot provider Aiven.

Di Aiven, halaman service **Backups** menyediakan snapshot untuk pemulihan service
sesuai fitur/plan Aiven. Snapshot tersebut berbeda dari arsip ZIP aplikasi dan
umumnya dipakai melalui proses restore service Aiven, bukan sebagai file Excel
yang bisa diunggah ke aplikasi. Sebelum migrasi atau penghapusan besar, buat
snapshot Aiven bila tersedia dan simpan ZIP aplikasi di lokasi terpisah. Uji
pemulihan dengan hati-hati pada salinan database, bukan dengan menimpa production.

## Akun petugas dan logout pada perangkat bersama
- Pendaftaran akun publik (`/register`) dinonaktifkan. Admin yang sudah masuk
  dapat membuat akun admin/resepsionis melalui menu **Pengguna**.
- Pada dashboard/operator PWA, tekan tombol **Keluar** yang ada di bagian bawah
  sidebar setelah selesai bertugas. Tutup tab/aplikasi setelah logout. Jangan
  mengandalkan menutup aplikasi saja karena sesi browser bisa tetap aktif.
- Jika tombol Keluar tidak terlihat di layar HP, buka menu sidebar terlebih
  dahulu. Form kiosk untuk tamu tetap publik dan tidak memerlukan login.
