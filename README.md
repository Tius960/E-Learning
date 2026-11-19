<div align="center">

# 🎓 Ruang Kelas

**Platform e-learning sederhana untuk mengelola course, lesson, dan video pembelajaran.**

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Status](https://img.shields.io/badge/Status-Demo-success?style=for-the-badge)

</div>

---

## ✨ Fitur Utama

| Untuk | Fitur |
|---|---|
| 👨‍🏫 **Admin** | Kelola course dan lesson, atur urutan lesson, terbitkan atau sembunyikan course |
| 🎬 **Admin** | Upload video lesson dengan **progress bar** langsung di browser |
| 🎒 **Siswa** | Lihat daftar course yang sudah terbit dan belajar lewat video |
| 🔒 **Keamanan** | Video bersifat privat: hanya bisa diputar oleh pengguna yang sudah login dan hanya jika course diterbitkan |

## 🧰 Teknologi

| Teknologi | Peran |
|---|---|
| **Laravel 12 + PHP** | MVC, routing, autentikasi, validasi, dan penyimpanan file dalam satu framework |
| **MySQL** | Data pengguna, role, course, lesson, dan status publikasi secara relasional |
| **Blade** | Render halaman server-side tanpa framework frontend tambahan |
| **CSS + JavaScript (XHR)** | Tampilan responsif dan progress upload tanpa dependensi tambahan |
| **Private local storage** | Video demo disimpan di server dan dibatasi lewat autentikasi serta status publikasi |

> 🎨 Mockup di folder `design/` adalah konsep awal. Implementasi akhir menyederhanakan beberapa elemen visual dan alur sesuai kebutuhan aplikasi.

## 📋 Persyaratan

- PHP **8.2+** dengan ekstensi `pdo_mysql`, `fileinfo`, dan `mbstring`
- Composer
- MySQL (disarankan **Laragon** untuk pengembangan lokal)

## 🚀 Instalasi Cepat

**1. Siapkan database**
Jalankan MySQL di Laragon, lalu buat database bernama `elearning`.

**2. Install dependensi dan buat konfigurasi**

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

**3. Sesuaikan `.env`** (jika pengaturan Laragon-mu berbeda)
Nilai awal memakai `127.0.0.1:3306`, database `elearning`, user `root`, tanpa password.

**4. Buat tabel dan akun demo**

```powershell
php artisan migrate --seed
```

**5. Jalankan aplikasi**

```powershell
php artisan serve
```

Buka **http://127.0.0.1:8000** 🎉

> ⚠️ Jangan bagikan atau commit file `.env`. Isinya konfigurasi lokal dan kunci aplikasi.

## 🔑 Akun Demo

| Role | Email | Password |
|---|---|---|
| 👨‍🏫 Admin | `admin@elearning.test` | `admin123` |
| 🎒 Siswa | `student@elearning.test` | `password` |

Akun dibuat oleh seeder. Aplikasi demo tidak menyediakan pendaftaran mandiri.

## 📌 Asumsi

- Hanya ada **dua role**: admin mengelola materi, siswa melihat course yang sudah terbit.
- Belum ada enrolment per course, jadi semua siswa yang login dapat melihat seluruh course terbit.
- Data contoh dan akun demo ditujukan untuk pengembangan lokal.

## ⚙️ Batasan Sistem

- **Format upload:** MP4, WebM, MOV, dan MKV, maksimal **500 MB**. Atur `upload_max_filesize` (minimal `512M`) dan `post_max_size` (misalnya `520M`) di `php.ini` Laragon, lalu restart server web.
- **Penyimpanan:** upload langsung ke satu server (private storage lokal). Belum ada object storage, CDN, konversi video, atau antrean.
- **Status video:** `uploading`, `ready`, atau `failed`. Belum ada pemulihan otomatis untuk upload yang terputus.
- **Sesi login:** berlaku 8 jam.
- **Belum tersedia:** reset password, pendaftaran siswa, enrolment, dan pelacakan progres belajar.

## 🛡️ Sebelum Dipakai di Produksi

- [ ] Ganti semua password akun demo
- [ ] Set `APP_DEBUG=false`
- [ ] Gunakan HTTPS
- [ ] Gunakan konfigurasi penyimpanan dan server yang sesuai kebutuhan
