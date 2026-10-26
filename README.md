# Futura Coffee

Sistem pencatatan transaksi Kedai Kopi Futura (PHP + MySQL).

## Cara menjalankan
1. Letakkan folder ini di `htdocs` (XAMPP) atau `www` (Laragon). Nama folder bebas.
2. Jalankan Apache dan MySQL.
3. Import `database/schema.sql` lewat phpMyAdmin/HeidiSQL (tabel + akun admin + 16 menu awal; aman diulang).
4. Buka `http://localhost/<nama-folder>/`

## Login awal
- Username: `admin`
- Password: `admin123`

Ganti password setelah login. Buat hash baru dengan `password_hash('passwordbaru', PASSWORD_DEFAULT)` lalu `UPDATE tb_user SET password='...' WHERE username='admin';`

## Fitur
Login (password_verify + session), logout, proteksi semua halaman, dashboard, CRUD menu (upload gambar), pencatatan transaksi (Cash/QRIS), riwayat dengan filter tanggal, pencarian, dan paginasi.

Koneksi database ada di `config/koneksi.php`.
