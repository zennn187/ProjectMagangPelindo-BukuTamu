# Buku Tamu Pelindo — Ringkasan Akun & URL

> Simpan dokumen ini di tempat aman. Jangan bagikan ke publik.
> Segera ubah password bawaan sebelum digunakan di produksi.

---

## 🌐 Alamat (URL)

| Keperluan | Alamat |
|-----------|--------|
| Halaman Kiosk (form tamu publik) | `http://127.0.0.1:8000/` |
| Halaman Login (Admin & Resepsionis) | `http://127.0.0.1:8000/login` |
| Dashboard (setelah login) | `http://127.0.0.1:8000/dashboard` |

> Buka lewat `localhost`/`127.0.0.1` agar kamera kiosk berfungsi.

---

## 👤 AKUN ADMIN (Super Admin / SDM & Umum)

| Field | Nilai |
|-------|-------|
| Email | `admin@pelindo.id` |
| Password | `password` |
| Role | `admin` |

### Akses penuh (menu di sidebar kiri):
| Menu | URL |
|------|-----|
| Dashboard | `/dashboard` |
| Pegawai (CRUD) | `http://127.0.0.1:8000/admin/employees` |
| Daftar Hitam | `http://127.0.0.1:8000/admin/blacklists` |
| Pengguna | `http://127.0.0.1:8000/admin/users` |
| Laporan + Unduh Excel | `http://127.0.0.1:8000/admin/reports` |

---

## 🧑‍💼 AKUN RESEPSIONIS (Operator Harian / Security / Lobi)

| Field | Nilai |
|-------|-------|
| Email | `receptionist@pelindo.id` |
| Password | `password` |
| Role | Receptionist |

### Akses:
- **Dashboard Kunjungan** di `/dashboard` (daftar tamu, tombol Check-in / Check-out / Tolak).
- **Tidak** punya menu admin; buka `/admin/*` → mendapat **403**.

---

## 🚀 Cara Menjalankan (ingat)

```powershell
cd "D:\Project Magang Pelindo\BukuTamu"
php artisan serve
```

Lalu buka `http://127.0.0.1:8000`.

---

## 🛠️ Perintah bantuan

| Fungsi | Perintah |
|--------|----------|
| Reset DB ke awal (akun di atas + contoh pegawai) | `php artisan migrate:fresh --seed` |
| Buat akun baru (`php artisan tinker`) | `User::updateOrCreate(['email'=>'x@y.id'], ['name'=>'...','password'=>'...','role'=>'receptionist']);` |
| Lihat log error | `Get-Content storage\logs\laravel.log -Tail 30` |

---

## 🩺 Kalau Muncul "Page Expired" (kode 419)

**Artinya:** token keamanan (CSRF) yang dikirim form tidak cocok dengan session di server. Laravel sengaja menolak request seperti itu supaya tidak ada form palsu.

Penyebab yang paling sering:

| Penyebab | Keterangan |
|----------|------------|
| Halaman login terbuka > 120 menit | `SESSION_LIFETIME=120`; session di tabel `sessions` sudah dianggap kedaluwarsa |
| Cookie session hilang / berubah | Browser dibersihkan, mode Incognito, extension pemblokir cookie, atau pindah host |
| Bergantian alamat | Buka di `127.0.0.1:8000` lalu submit dari URL tunnel (atau sebaliknya) → cookie host asal tidak ikut terkirim |
| MySQL / XAMPP berhenti | `SESSION_DRIVER=database`, jadi token tidak bisa disimpan/dibaca saat MySQL mati |
| Session dibersihkan | `php artisan migrate:fresh`, truncate tabel `sessions`, atau ganti `APP_KEY` |
| Halaman lama dari cache browser | Form yang dikirim masih memakai token dari halaman versi lama |

**Cara mengatasi (dari yang paling cepat):**

1. Tekan **Ctrl + F5** di halaman login lalu login ulang.
   Sejak perbaikan terakhir, 419 **tidak lagi** berhenti di halaman "Page Expired": otomatis dialihkan kembali ke halaman login dan muncul pesan *"Sesi Anda telah berakhir (halaman kedaluwarsa)"* dengan email tetap terisi.
2. Pastikan MySQL jalan: `Test-NetConnection 127.0.0.1 -Port 3306` (hasil `True` = jalan).
3. Gunakan **satu alamat saja**, mis. selalu `http://127.0.0.1:8000/login`. Kalau memakai tunnel, jangan dicampur dengan alamat lokal.
4. Kalau masih gagal: hapus cookie lama untuk `127.0.0.1` di browser, lalu buka ulang `http://127.0.0.1:8000/login`.
5. Opsional, bersihkan session kedaluwarsa:
   ```powershell
   php artisan tinker --execute="DB::table('sessions')->where('last_activity', '<', now()->subHours(2)->timestamp)->delete();"
   ```

**Catatan teknis (agar tidak terulang):**

- `app/Providers/AppServiceProvider.php` tidak lagi memaksa URL ke `APP_URL` saat diakses dari `localhost`/`127.0.0.1`, dan sekarang menghormati header `X-Forwarded-Host`/`X-Forwarded-Proto` dari tunnel. Dulu form login bisa "dilempar" ke URL tunnel yang sudah mati, sehingga cookie session tidak terkirim dan muncul 419.
- `bootstrap/app.php` menangani error 419: request HTML dialihkan ke halaman sebelumnya (host harus sama) atau ke halaman login, request JSON menerima `419` + pesan. Halaman cadangan ada di `resources/views/errors/419.blade.php`.
- `APP_URL` cukup diisi alamat lokal (`http://127.0.0.1:8000`). Jangan menaruh URL dev tunnel di `APP_URL`/`ASSET_URL`, karena URL tunnel bersifat sementara dan berganti setiap sesi.