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
Aiven **mewajibkan SSL**. Laravel-mu (config/database.php) sudah mendukung
`MYSQL_ATTR_SSL_CA`. Dua cara:

### Cara mudah (tanpa file CA — cukup untuk testing/magang):
Di Vercel Environment Variables, TAMBAHKAN:
```
MYSQL_ATTR_SSL_CA=
```
(kosongkan — PDO tetap konek SSL tapi tanpa verifikasi CA. Aman untuk project magang.)

### Cara ketat (produksi sungguhan):
Upload `ca.pem` ke `storage/certs/aiven-ca.pem`, commit, lalu isi:
```
MYSQL_ATTR_SSL_CA=/var/task/storage/certs/aiven-ca.pem
```

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
MYSQL_ATTR_SSL_CA=                           ← kosongkan (cara mudah)
APP_URL=https://projectmagangpelindo-bukutamu.vercel.app
APP_DEBUG=false
APP_KEY=base64:s1gEP3Lh3suDy4n+2aG8ZkS14Cm2VGgNa/R+K1R0dLo=  ← dari .env lokalmu
SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
WHATSAPP_ENABLED=false
```

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
- Jangan simpan foto besar di DB (foto kamu sudah di storage — sudah benar).
