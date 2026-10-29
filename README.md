# AksiBersama — Platform Kegiatan Sosial & Relawan

AksiBersama adalah aplikasi web berbasis **PHP Native dan MySQL** untuk mempertemukan kegiatan sosial dengan calon relawan. Proyek ini dibuat sebagai fondasi portofolio yang dapat dijalankan secara lokal menggunakan XAMPP.

## Fitur utama

### Pengunjung
- Landing page responsif dengan ringkasan kegiatan.
- Daftar dan detail kegiatan berdasarkan kategori.
- Form pendaftaran relawan tanpa perlu membuat akun.
- Validasi data dan konfirmasi pendaftaran.
- Informasi tanggal, lokasi, kuota, dan persyaratan kegiatan.

### Admin
- Login admin menggunakan `password_hash()` / `password_verify()`.
- Dashboard statistik kegiatan dan pendaftaran.
- CRUD kegiatan: tambah, lihat, ubah, hapus.
- Status publikasi: draft atau dipublikasikan.
- Melihat pendaftaran relawan dan memperbarui status: menunggu, diterima, ditolak, hadir.
- Pencarian tabel di sisi browser.

### Keamanan dasar
- PDO prepared statements untuk query dinamis.
- Token CSRF pada formulir POST.
- Escape output dengan `htmlspecialchars`.
- Validasi input server-side.
- Session regeneration saat login.
- Pembatasan akses halaman admin.

## Teknologi
- PHP 8.1+ (disarankan PHP 8.2+)
- MySQL / MariaDB
- HTML5, CSS3, JavaScript
- PDO
- Google Fonts (memerlukan koneksi internet untuk memuat font; aplikasi tetap berfungsi tanpa font tersebut)

## Instalasi di XAMPP

1. Ekstrak folder `AksiBersama` ke `C:\\xampp\\htdocs\\AksiBersama`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Import file `database/aksibersama.sql`.
5. Periksa pengaturan database di `config/config.php`. Default XAMPP biasanya menggunakan host `127.0.0.1`, user `root`, dan password kosong.
6. Buka `http://localhost/AksiBersama/`.

Jika folder aplikasi diubah namanya atau dipasang di subfolder berbeda, sesuaikan `BASE_URL` di `config/config.php`. Contoh untuk folder `AksiBersama`: `define('BASE_URL', '/AksiBersama');`. Jika mengakses melalui root virtual host, biarkan kosong.

## Akun demo admin

- Email: `admin@aksibersama.test`
- Kata sandi: `AksiAdmin2026!`

Jika akun demo gagal masuk, buat hash baru menggunakan PHP:

```bash
php -r "echo password_hash('AksiAdmin2026!', PASSWORD_DEFAULT), PHP_EOL;"
```

Salin hash yang dihasilkan dan jalankan query berikut di phpMyAdmin:

```sql
UPDATE admins
SET password_hash = 'PASTE_HASH_DI_SINI'
WHERE email = 'admin@aksibersama.test';
```

**Keamanan:** akun demo hanya untuk pengembangan lokal. Ganti kredensial sebelum aplikasi dipublikasikan. Jangan gunakan data pribadi nyata dalam lingkungan demo.

## Struktur folder

```text
AksiBersama/
├── admin/
│   ├── _layout.php
│   ├── _footer.php
│   ├── index.php
│   ├── events.php
│   └── registrations.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── config/config.php
├── database/aksibersama.sql
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── index.php
├── event.php
├── register.php
├── registration-success.php
├── login.php
├── logout.php
└── README.md
```

## Catatan pengembangan
- Formulir saat ini tidak mengirim email otomatis. Admin dapat meninjau status pendaftaran melalui dashboard.
- URL gambar kegiatan bersifat opsional dan memakai URL publik; unggah file lokal belum diimplementasikan.
- Peta, notifikasi WhatsApp, verifikasi email, ekspor laporan PDF/Excel, dan reset kata sandi dapat ditambahkan pada tahap berikutnya.
- Sebelum deploy publik, gunakan HTTPS, konfigurasi secret dan database yang aman, nonaktifkan tampilan error, serta ganti akun demo.

## Deskripsi singkat
AksiBersama adalah platform kegiatan sosial berbasis PHP Native dan MySQL yang membantu masyarakat menemukan kegiatan kerelawanan, mendaftar sebagai relawan, serta memantau status pendaftaran. Admin dapat mengelola kegiatan, kuota, publikasi, dan data relawan melalui dashboard yang responsif.
