# Ruang Kelas

Aplikasi e-learning Laravel untuk admin dan siswa: pengelolaan course dan lesson, pengurutan lesson, upload video dengan progress, serta pemutaran video privat.

## Requirement

- PHP 8.2+ dengan ekstensi `pdo_mysql`, `fileinfo`, dan `mbstring`
- Composer
- MySQL (Laragon direkomendasikan untuk pengembangan lokal)

## Teknologi dan alasan

- **Laravel 12 dan PHP** — struktur MVC, routing, autentikasi, validasi, dan penyimpanan file tersedia dalam satu framework.
- **MySQL** — menyimpan data pengguna, role, course, lesson, dan status publikasi secara relasional.
- **Blade** — merender halaman server-side tanpa framework frontend tambahan.
- **CSS dan JavaScript bawaan browser (XHR)** — tampilan responsif dan progress upload tanpa dependensi frontend tambahan.
- **Private local storage** — menyimpan video demo di server dan membatasi pemutaran melalui autentikasi serta status publikasi.

## Catatan desain

Mockup pada folder `design/` merupakan konsep awal; implementasi akhir menyederhanakan beberapa elemen visual dan alur sesuai kebutuhan aplikasi.

## Instalasi dan konfigurasi

1. Jalankan MySQL di Laragon dan buat database `elearning`.
2. Dari folder proyek, jalankan:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Sesuaikan koneksi database di `.env` bila pengaturan Laragon berbeda. Nilai awal pada template memakai `127.0.0.1:3306`, database `elearning`, dan user `root` tanpa password.
4. Buat tabel dan akun demo:

   ```powershell
   php artisan migrate --seed
   ```

Jangan bagikan atau commit `.env`; berkas tersebut berisi konfigurasi lokal dan kunci aplikasi.

## Menjalankan

```powershell
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun demo

- Admin: `admin@elearning.test` / `password`
- Siswa: `student@elearning.test` / `password`

Akun dibuat oleh seeder; aplikasi demo tidak menyediakan pendaftaran mandiri.

## Asumsi

- Hanya ada dua role: admin mengelola materi, siswa melihat course yang sudah diterbitkan.
- Belum ada pendaftaran/enrolment per course; semua siswa yang login dapat melihat seluruh course terbit.
- Data contoh dan akun demo ditujukan untuk pengembangan lokal.

## Batasan sistem

- Upload menerima MP4, WebM, MOV, dan MKV hingga 500 MB. Sesuaikan `upload_max_filesize` (minimal `512M`) dan `post_max_size` (misalnya `520M`) pada `php.ini` Laragon, lalu restart server web.
- Upload dilakukan langsung ke satu server; file disimpan di private storage lokal. Tidak ada object storage, CDN, konversi video, atau pemrosesan antrean.
- Status video sederhana: `uploading`, `ready`, atau `failed`. Tidak ada pemulihan otomatis untuk upload terputus.
- Session login berlaku 8 jam. Reset password, pendaftaran siswa, enrolment, dan pelacakan progres belajar belum tersedia.
- Sebelum digunakan di produksi, ganti akun demo, gunakan `APP_DEBUG=false`, HTTPS, serta konfigurasi penyimpanan dan server yang sesuai.
