# Sistem Informasi Pengajuan Surat Mahasiswa

Aplikasi web berbasis **Laravel + MySQL** untuk mengajukan surat secara online: Mahasiswa mengajukan surat dan mengunggah dokumen persyaratan; Admin/Petugas memeriksa, memverifikasi, memproses, dan mengunggah surat hasil.

---

## Daftar Isi

1. [Analisis Sistem](#1-analisis-sistem)
2. [Kebutuhan](#2-kebutuhan)
3. [Instalasi](#3-instalasi)
4. [Konfigurasi `.env`](#4-konfigurasi-env)
5. [Menjalankan Aplikasi](#5-menjalankan-aplikasi)
6. [Akun Testing](#6-akun-testing)
7. [Struktur Project](#7-struktur-project)
8. [Alur Status Pengajuan](#8-alur-status-pengajuan)
9. [Keamanan File Upload](#9-keamanan-file-upload)
10. [Daftar Route](#10-daftar-route)
11. [Skenario Testing](#11-skenario-testing)
12. [Menjalankan Test Otomatis](#12-menjalankan-test-otomatis)

---

## 1. Analisis Sistem

### Aktor

| Role | Keterangan |
|---|---|
| **Mahasiswa** | Pengguna yang mengajukan surat, memantau status, memperbaiki pengajuan, dan mengunduh surat selesai. |
| **Admin/Petugas** | Pihak yang memeriksa, menyetujui, meminta perbaikan, menolak, memproses, mengunggah surat hasil, mengelola jenis surat/persyaratan, dan melihat laporan. |

### Use Case Utama

- **Mahasiswa**: login, melihat jenis surat + persyaratan, membuat pengajuan, mengunggah dokumen, memantau status, memperbaiki pengajuan, melihat riwayat, mengunduh surat selesai.
- **Admin**: login, melihat semua pengajuan (dengan filter), memeriksa dokumen, menyetujui / meminta perbaikan / menolak, memproses surat, mengunggah surat hasil, mengelola jenis surat & persyaratan, melihat laporan.

### Alur Bisnis

```
Diajukan → Diperiksa → Diproses → Selesai
Diajukan → Diperiksa → Perlu Perbaikan → (mahasiswa kirim ulang) → Diajukan → Diperiksa → ...
Diajukan → Diperiksa → Ditolak
```

### Aturan Bisnis

1. NIM unik untuk mahasiswa; Admin boleh tanpa NIM.
2. Setiap pengajuan mendapat nomor unik otomatis berformat `SP-YYYY-NNNN` (contoh: `SP-2026-0001`).
3. Mahasiswa hanya boleh mengubah pengajuan miliknya sendiri dan hanya jika berstatus **Perlu Perbaikan**.
4. Admin wajib mengisi catatan saat meminta perbaikan dan alasan saat menolak.
5. Jenis surat yang tidak aktif tidak dapat dipilih untuk pengajuan baru.
6. Dokumen hanya boleh diakses pemilik pengajuan dan Admin.
7. Mahasiswa baru dapat mengunduh surat hasil setelah status **Selesai**.
8. Setiap perubahan status tercatat pada `riwayat_statuses` lengkap dengan pelaku, waktu, dan keterangan.

---

## 2. Kebutuhan

- PHP >= 8.3 (dikembangkan & diuji pada PHP 8.5)
- Composer
- MySQL / MariaDB (dikembangkan & diuji pada MariaDB 10.4 XAMPP)
- Node.js & npm (untuk build aset CSS/JS — Tailwind CSS v4)

---

## 3. Instalasi

```bash
cd Tugas-app
composer install
cp .env.example .env          # lalu sesuaikan konfigurasi database (lihat bagian 4)
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

> **Catatan `storage:link`:** aplikasi ini **tidak memerlukan** `php artisan storage:link`. Seluruh dokumen dan surat hasil disimpan pada disk privat (`storage/app/private`) dan hanya dapat diakses melalui route download yang diperiksa authorization.

## 4. Konfigurasi `.env`

Bagian yang wajib disesuaikan:

```env
APP_NAME="Sistem Informasi Pengajuan Surat"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengajuan_surat
DB_USERNAME=root
DB_PASSWORD=
```

Buat database terlebih dahulu:

```sql
CREATE DATABASE pengajuan_surat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 5. Menjalankan Aplikasi

```bash
php artisan serve
```

Buka <http://localhost:8000>. Saat pertama kali, Anda akan diarahkan ke halaman login.

## 6. Akun Testing

Password semua akun bawaan: **`password`**

| Role | Email | Keterangan |
|---|---|---|
| Admin | `admin@kampus.ac.id` | Admin/Akademik |
| Mahasiswa | `budi@mahasiswa.ac.id` | NIM 20210001 — Teknik Informatika |
| Mahasiswa | `siti@mahasiswa.ac.id` | NIM 20210002 — Sistem Informasi |
| Mahasiswa | `andi@mahasiswa.ac.id` | NIM 20220003 — Manajemen |

Data awal juga memuat 4 jenis surat (Surat Aktif Kuliah, Surat Keterangan Mahasiswa, Surat Pengantar Penelitian, Surat Izin) lengkap dengan persyaratannya.

## 7. Struktur Project

```
app/
├── Enums/PengajuanStatus.php          # Enum status pengajuan
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php   # Login & logout
│   │   ├── Mahasiswa/                 # Dashboard, Surat, Pengajuan, Profile
│   │   └── Admin/                     # Dashboard, Pengajuan, JenisSurat, Persyaratan, Laporan
│   ├── Middleware/EnsureRole.php       # Middleware role (role:mahasiswa / role:admin)
│   └── Requests/                      # Form Request validasi (Store/Update Pengajuan, Verifikasi, dsb.)
├── Models/                            # User, JenisSurat, Persyaratan, Pengajuan, DokumenPengajuan, RiwayatStatus
├── Policies/PengajuanPolicy.php       # Otorisasi akses pengajuan & file
├── Services/PengajuanService.php      # Business logic: buat pengajuan, perbaiki, status, riwayat, nomor unik
database/
├── factories/                         # User, JenisSurat, Pengajuan
├── migrations/                        # Seluruh tabel + FK + index + unique
└── seeders/                           # JenisSuratSeeder + akun admin & mahasiswa
resources/views/
├── layouts/app.blade.php              # Layout sidebar responsif
├── auth/, mahasiswa/, admin/          # Halaman per role
├── components/status-badge.blade.php  # Badge status
└── errors/404.blade.php, 403.blade.php
routes/web.php                         # Route dipisah per role
storage/app/private/                   # Disk PRIVAT: dokumen/ dan surat/
tests/Feature/                         # 43 test Pest (auth, mahasiswa, admin workflow, jenis surat)
```

### Database

| Tabel | Keterangan |
|---|---|
| `users` | Satu tabel untuk dua role (`role`: mahasiswa/admin); NIM unik, boleh NULL untuk admin |
| `jenis_surats` | Jenis surat + deskripsi + status aktif |
| `persyaratans` | Daftar persyaratan per jenis surat (+ flag `wajib`) |
| `pengajuans` | Data pengajuan: nomor unik, FK user & jenis surat, status, catatan admin, file surat hasil, field tambahan Surat Pengantar Penelitian |
| `dokumen_pengajuans` | File dokumen per pengajuan per persyaratan (unique per kombinasi) |
| `riwayat_statuses` | Log perubahan status: pengajuan, pelaku (user), status, keterangan, waktu |

## 8. Alur Status Pengajuan

| Dari | Aksi Admin | Ke | Keterangan |
|---|---|---|---|
| Diajukan | Tandai "Diperiksa" | Diperiksa | Pemeriksaan dimulai |
| Diperiksa | Setujui | Diproses | `processed_by` dicatat |
| Diperiksa | Perlu Perbaikan (catatan wajib) | Perlu Perbaikan | Catatan terlihat oleh mahasiswa |
| Diperiksa | Tolak (alasan wajib) | Ditolak | Alasan terlihat oleh mahasiswa |
| Perlu Perbaikan | Mahasiswa kirim ulang | Diajukan | Data/dokumen diperbaiki, catatan dibersihkan |
| Diproses | Unggah surat hasil (PDF) | Selesai | `completed_at` dicatat, mahasiswa bisa mengunduh |

Setiap transisi menulis baris baru ke `riwayat_statuses`.

## 9. Keamanan File Upload

- Format diizinkan: PDF, JPG, JPEG, PNG (surat hasil: PDF saja); ukuran maksimal 2 MB.
- Nama file diubah menjadi unik (UUID) — nama asli tidak dipakai dan tidak disimpan di folder publik.
- File disimpan pada disk **lokal privat** `storage/app/private` (`dokumen/`, `surat/`), dengan `serve => false` pada `config/filesystems.php` sehingga **tidak** dapat diakses langsung lewat URL `/storage/...`.
- Akses file hanya melalui route download dengan pemeriksaan `PengajuanPolicy` (pemilik pengajuan dan Admin).
- Surat hasil hanya dapat diunduh pemilik ketika status **Selesai**.

## 10. Daftar Route

Jalankan `php artisan route:list` untuk daftar lengkap. Ringkasnya:

| Method | URI | Keterangan |
|---|---|---|
| GET/POST | `/login` | Login |
| POST | `/logout` | Logout |
| GET | `/mahasiswa/dashboard` | Dashboard mahasiswa |
| GET | `/mahasiswa/surat` | Daftar jenis surat aktif |
| GET | `/mahasiswa/surat/{jenisSurat}` | Detail jenis surat + persyaratan |
| GET | `/mahasiswa/pengajuan` | Riwayat pengajuan (+filter/search) |
| GET | `/mahasiswa/pengajuan/buat/{jenisSurat}` | Form pengajuan |
| POST | `/mahasiswa/pengajuan` | Kirim pengajuan |
| GET | `/mahasiswa/pengajuan/{pengajuan}` | Detail pengajuan |
| GET/PUT | `/mahasiswa/pengajuan/{pengajuan}/perbaiki` | Perbaikan pengajuan |
| GET | `/mahasiswa/pengajuan/{pengajuan}/dokumen/{dokumen}` | Download dokumen |
| GET | `/mahasiswa/pengajuan/{pengajuan}/surat` | Download surat hasil (setelah Selesai) |
| GET | `/mahasiswa/profil` | Profil |
| GET | `/admin/dashboard` | Dashboard admin |
| GET | `/admin/pengajuan` | Semua pengajuan (+filter status/jenis/tanggal/NIM) |
| GET | `/admin/pengajuan/{pengajuan}` | Detail & verifikasi |
| POST | `/admin/pengajuan/{pengajuan}/periksa` | Tandai Diperiksa |
| POST | `/admin/pengajuan/{pengajuan}/verifikasi` | Setujui / Perlu Perbaikan / Tolak |
| POST | `/admin/pengajuan/{pengajuan}/selesai` | Unggah surat hasil → Selesai |
| GET | `/admin/pengajuan/{pengajuan}/dokumen/{dokumen}` | Download dokumen (pemeriksaan) |
| GET/POST/PUT/DELETE | `/admin/jenis-surat...` | CRUD jenis surat |
| POST/PUT/DELETE | `/admin/jenis-surat/{jenisSurat}/persyaratan...` | Kelola persyaratan |
| GET | `/admin/laporan` | Laporan (periode + jenis surat + status) |

## 11. Skenario Testing

### A. Mahasiswa

| # | Skenario | Langkah | Ekspektasi |
|---|---|---|---|
| A1 | Login | Login dengan akun mahasiswa | Berhasil, masuk dashboard mahasiswa |
| A2 | Lihat jenis surat | Menu "Ajukan Surat" | Hanya jenis surat **aktif** yang tampil |
| A3 | Lihat persyaratan | Klik "Lihat & Ajukan" | Daftar persyaratan tampil (wajib/opsional) |
| A4 | Buat pengajuan | Isi formulir + unggah dokumen wajib, kirim | Sukses; nomor `SP-2026-XXXX`; status **Diajukan**; ada di riwayat |
| A5 | Validasi dokumen | Kirim tanpa dokumen wajib | Muncul pesan error: dokumen wajib diunggah |
| A6 | Validasi format file | Unggah `.exe`/`.docx` | Ditolak: hanya PDF/JPG/JPEG/PNG (maks 2 MB) |
| A7 | Pantau status | Buka detail pengajuan | Timeline riwayat status tampil lengkap (tanggal, status, keterangan, admin) |
| A8 | Perbaiki pengajuan | Admin minta perbaikan → mahasiswa buka "Perbaiki Pengajuan", ubah data/ganti dokumen, kirim ulang | Status kembali **Diajukan**; catatan admin dibersihkan; riwayat tercatat |
| A9 | Edit hanya saat perlu perbaikan | Coba akses form perbaiki saat status lain | Ditolak (403) |
| A10 | Download surat | Setelah status **Selesai**, klik "Unduh Surat (PDF)" | File PDF terunduh |
| A11 | Akses data orang lain | Buka `/mahasiswa/pengajuan/{id}` milik mahasiswa lain | 403 Forbidden |

### B. Admin/Petugas

| # | Skenario | Langkah | Ekspektasi |
|---|---|---|---|
| B1 | Login | Login dengan akun admin | Masuk dashboard admin |
| B2 | Lihat pengajuan | Menu "Pengajuan Surat" | Seluruh pengajuan tampil; filter status/jenis/tanggal/NIM berfungsi |
| B3 | Periksa | Buka pengajuan **Diajukan**, klik "Tandai Diperiksa" | Status **Diperiksa**, riwayat tercatat |
| B4 | Setujui | Klik "Setujui" | Status **Diproses**, `processed_by` = admin |
| B5 | Minta perbaikan | Klik "Perlu Perbaikan" tanpa catatan | Error: catatan wajib. Dengan catatan → status **Perlu Perbaikan** + catatan tersimpan |
| B6 | Tolak | Klik "Tolak" tanpa alasan | Error: alasan wajib. Dengan alasan → status **Ditolak** |
| B7 | Periksa dokumen | Klik "Lihat / Unduh" pada tiap dokumen | File terunduh untuk pemeriksaan |
| B8 | Selesai | Pada pengajuan **Diproses**, unggah PDF surat hasil | Status **Selesai**, `completed_at` terisi |
| B9 | Kelola jenis surat | Tambah/ubah/nonaktifkan/hapus jenis surat | Tersimpan; jenis nonaktif hilang dari daftar mahasiswa; jenis bekas pengajuan tidak bisa dihapus |
| B10 | Kelola persyaratan | Tambah/edit/hapus persyaratan per jenis surat | Tersimpan dan tampil di form pengajuan |
| B11 | Laporan | Buka Laporan, filter periode/jenis/status | Total, selesai, diproses, perlu perbaikan, ditolak tampil sesuai filter |
| B12 | Akses route mahasiswa | Login admin, buka `/mahasiswa/dashboard` | Dialihkan ke dashboard admin |

### C. Sistem

| # | Skenario | Ekspektasi |
|---|---|---|
| C1 | Nomor pengajuan unik | Dua pengajuan mendapat nomor berbeda `SP-YYYY-NNNN` |
| C2 | File privat | `/storage/dokumen/...` **404** — file tidak bisa diakses tanpa authorization |
| C3 | Gate kebijakan | Download dokumen/surat hanya untuk pemilik & Admin |
| C4 | Riwayat | Setiap perubahan status selalu menambah baris `riwayat_statuses` |
| C5 | Role middleware | Mahasiswa ↔ Admin saling diblokir antar area |
| C6 | 404/403 | Halaman error tampil rapi, tanpa bocoran info sensitif |

## 12. Menjalankan Test Otomatis

```bash
php artisan test --compact
```

43 test (118 assertions) mencakup: autentikasi, pembuatan pengajuan + upload, validasi, otorisasi pemilik data, alur workflow admin (periksa → verifikasi → selesai), perbaikan, penolakan, CRUD jenis surat/persyaratan, dan proteksi file privat.
