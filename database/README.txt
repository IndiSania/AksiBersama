DATABASE AksiBersama

1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Pilih tab Import.
3. Pilih file aksibersama.sql, lalu klik Import.
4. File SQL membuat database `aksibersama`, tabel, akun admin, dan data demo.

PENTING:
- Jika Anda ingin menjalankan ulang instalasi, file SQL akan menghapus tabel dengan nama yang sama terlebih dahulu.
- Untuk instalasi baru, jalankan file SQL sekali saja.
- Ubah kata sandi admin setelah masuk ke aplikasi. Jika hash akun demo tidak berfungsi pada lingkungan Anda, buat hash baru dengan PHP:
  php -r "echo password_hash('KataSandiBaruAnda', PASSWORD_DEFAULT), PHP_EOL;"
  Lalu perbarui kolom password_hash milik admin di phpMyAdmin.
