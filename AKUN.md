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