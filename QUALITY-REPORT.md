# Quality, Accessibility, dan SEO — Phase 3

Tanggal: 26 November 2026 (Asia/Jakarta)  
Audit dilakukan melalui source code dan route configuration. Browser, screen reader, authenticated session, dan production belum diuji.

## Aksesibilitas yang diperbaiki

- Label nama, telepon, instansi, pegawai tujuan, dan keperluan pada form kiosk kini terhubung ke field melalui `for`/`id`; telepon memakai `type=tel`, `autocomplete`, dan `inputmode` yang sesuai.
- Kelompok pilihan jenis kunjungan dan preferensi penyerahan memiliki nama aksesibel melalui `role=group`/`aria-labelledby`.
- Preview kamera memiliki nama aksesibel dan gambar hasil capture memiliki alt text yang menjelaskan isinya.
- Label filter pencarian/tanggal dashboard dan rentang tanggal laporan terhubung ke input masing-masing.
- Form Gate sudah menghubungkan label input identitas utama; pilihan foto diberi label melalui deskripsi dan status kamera/validasi tetap menggunakan teks.
- Tidak ada token warna, palet, layout, identitas logo, atau komposisi UI yang diubah. Aset wordmark yang sama di-downsample untuk mengurangi byte; pratinjau diperiksa dan transparansi dipertahankan.

## SEO dan crawling

- Landing `/` memiliki deskripsi Bahasa Indonesia dan canonical ke route kiosk. Alias `/welcome` dan `/landing` menyajikan view terpisah dan belum diberi canonical di audit ini; hubungan/duplikasi kontennya perlu dipastikan sebelum mengubah indexing.
- Form `/gate/form` diberi deskripsi dan `noindex,nofollow` karena merupakan flow pendaftaran operasional yang diakses melalui QR.
- Login/halaman guest, layout aplikasi Breeze, dan layout operator diberi `noindex,nofollow`; route laporan/admin/dashboard tetap dilindungi middleware aktual.
- `public/robots.txt` saat ini mengizinkan crawler umum. `robots.txt` bukan kontrol akses; halaman privat tetap memerlukan authorization server-side.
- Privacy page memiliki title dan heading utama; tidak ditambahkan canonical/domain karena host canonical production belum diverifikasi.

## Pemeriksaan dan batas validasi

- Heading utama ditemukan pada kiosk, gate form, login, badge/pass, dan dashboard melalui layout operator. Hierarki setiap state/modal belum diverifikasi visual atau dengan screen reader.
- Focus state dan reduced-motion utility/pola ditemukan di CSS dan interaksi tertentu. Belum dilakukan keyboard walkthrough semua modal, dropdown, kamera, popover aktivitas, pagination, serta form errors.
- Kontras seluruh state terang/gelap belum diukur dengan axe/DevTools. Lighthouse Accessibility 80 pada screenshot lama adalah baseline dashboard lama, bukan hasil sesudah.
- Halaman laporan/admin, login, dashboard, dan scanner belum diuji dengan sesi browser sah. Tidak ada klaim aksesibilitas 95+ atau SEO 95+.

## Temuan yang masih perlu ditangani

1. Kamera Gate adalah interaksi custom; tombol mulai/capture/retake perlu diuji dengan keyboard, pembaca layar, dan browser permission denial.
2. Label “Foto Tamu” pada form Gate berupa instruksi kelompok tombol/kamera, bukan label native; evaluasi alur screen reader ketika browser tersedia.
3. Alias `/welcome` dan `/landing` serta metadata public privacy belum diuji terhadap index/canonical production.
4. Screenshot mobile/desktop dan tes viewport tidak tersedia. Perubahan label/metadata tidak mengubah visual yang diharapkan, tetapi harus diperiksa saat browser tersedia.
