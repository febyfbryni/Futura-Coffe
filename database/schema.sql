CREATE DATABASE IF NOT EXISTS futura_coffee;
USE futura_coffee;

CREATE TABLE IF NOT EXISTS tb_user (
 id_user INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role VARCHAR(20) NOT NULL DEFAULT 'admin',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tb_menu (
 id_menu INT AUTO_INCREMENT PRIMARY KEY,
 nama_menu VARCHAR(100) NOT NULL,
 kategori VARCHAR(50) NOT NULL,
 harga DECIMAL(12,2) NOT NULL,
 gambar VARCHAR(255) DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tb_transaksi (
 id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
 id_user INT NOT NULL,
 tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
 total_harga DECIMAL(12,2) NOT NULL,
 metode_bayar ENUM('Cash','QRIS') NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_user) REFERENCES tb_user(id_user)
);

CREATE TABLE IF NOT EXISTS tb_detail_transaksi (
 id_detail INT AUTO_INCREMENT PRIMARY KEY,
 id_transaksi INT NOT NULL,
 id_menu INT NOT NULL,
 jumlah INT NOT NULL,
 harga DECIMAL(12,2) NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL,
 FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi),
 FOREIGN KEY (id_menu) REFERENCES tb_menu(id_menu)
);

-- ===== DATA AWAL (aman dijalankan berulang) =====
-- Login: username admin / password admin123 (segera ganti setelah login)
INSERT IGNORE INTO tb_user (nama, username, password, role)
VALUES ('Administrator', 'admin', '$2y$12$IRKanwGw5Q0aAxJEBX8mxOEmT5t7dMruoD23bpnYJ8LK2ZrX2PXPm', 'admin');

INSERT INTO tb_menu (nama_menu, kategori, harga, gambar)
SELECT * FROM (
  SELECT 'Iced Coffe Futura' AS nama_menu, 'Minuman Kopi' AS kategori, 13000 AS harga, 'menu1.jpg' AS gambar
  UNION ALL SELECT 'Iced Coffe Aren' AS nama_menu, 'Minuman Kopi' AS kategori, 15000 AS harga, 'menu2.jpg' AS gambar
  UNION ALL SELECT 'Iced Coffe Vanilla' AS nama_menu, 'Minuman Kopi' AS kategori, 15000 AS harga, 'menu3.jpg' AS gambar
  UNION ALL SELECT 'Iced Coffe Pandan' AS nama_menu, 'Minuman Kopi' AS kategori, 15000 AS harga, 'menu4.jpg' AS gambar
  UNION ALL SELECT 'Iced Coffe Caramel' AS nama_menu, 'Minuman Kopi' AS kategori, 15000 AS harga, 'menu5.jpg' AS gambar
  UNION ALL SELECT 'Butterscotch Coffe' AS nama_menu, 'Minuman Kopi' AS kategori, 15000 AS harga, 'menu6.jpg' AS gambar
  UNION ALL SELECT 'Hot Coffe Creamy' AS nama_menu, 'Minuman Kopi' AS kategori, 10000 AS harga, 'menu7.jpg' AS gambar
  UNION ALL SELECT 'Americano' AS nama_menu, 'Minuman Kopi' AS kategori, 10000 AS harga, 'menu8.jpg' AS gambar
  UNION ALL SELECT 'Matcha Creamy' AS nama_menu, 'Minuman Non Kopi' AS kategori, 15000 AS harga, 'menu9.jpg' AS gambar
  UNION ALL SELECT 'ChocoBerry' AS nama_menu, 'Minuman Non Kopi' AS kategori, 15000 AS harga, 'menu10.jpg' AS gambar
  UNION ALL SELECT 'Strawberry Mojito Ice' AS nama_menu, 'Minuman Non Kopi' AS kategori, 15000 AS harga, 'menu11.jpg' AS gambar
  UNION ALL SELECT 'Green Tea' AS nama_menu, 'Minuman Non Kopi' AS kategori, 10000 AS harga, 'menu12.jpg' AS gambar
  UNION ALL SELECT 'Breezy Lemon Tea' AS nama_menu, 'Minuman Non Kopi' AS kategori, 10000 AS harga, 'menu13.jpg' AS gambar
  UNION ALL SELECT 'Red Velvet' AS nama_menu, 'Minuman Non Kopi' AS kategori, 10000 AS harga, 'menu14.jpg' AS gambar
  UNION ALL SELECT 'Taro' AS nama_menu, 'Minuman Non Kopi' AS kategori, 10000 AS harga, 'menu15.jpg' AS gambar
  UNION ALL SELECT 'Chocolate' AS nama_menu, 'Minuman Non Kopi' AS kategori, 10000 AS harga, 'menu16.jpg' AS gambar
) AS seed WHERE NOT EXISTS (SELECT 1 FROM tb_menu);
