<?php
$title = 'Data Menu'; $active = 'menu';
require_once __DIR__ . '/../includes/header.php';
$dir = __DIR__ . '/../images/menu/';
function simpan_gambar($old = null) {
    global $dir;
    if (empty($_FILES['gambar']['name']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) return $old;
    $info = @getimagesize($_FILES['gambar']['tmp_name']);
    $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2] ?? 0] ?? null;
    if (!$ext || $_FILES['gambar']['size'] > 2 * 1024 * 1024) throw new Exception('Gambar harus JPG/PNG/WEBP maksimal 2 MB.');
    $nama = 'm' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['gambar']['tmp_name'], $dir . $nama);
    if ($old && is_file($dir . $old)) @unlink($dir . $old);
    return $nama;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? ''; $id = (int)($_POST['id_menu'] ?? 0);
    $nama = trim($_POST['nama_menu'] ?? ''); $kat = $_POST['kategori'] ?? ''; $harga = (float)($_POST['harga'] ?? 0);
    try {
        if ($aksi === 'hapus') {
            $m = row("SELECT gambar FROM tb_menu WHERE id_menu = ?", 'i', [$id]);
            q("DELETE FROM tb_menu WHERE id_menu = ?", 'i', [$id]);
            if ($m && $m['gambar'] && is_file($dir . $m['gambar'])) @unlink($dir . $m['gambar']);
            $_SESSION['flash'] = 'Menu berhasil dihapus.';
        } elseif (in_array($aksi, ['tambah', 'edit'])) {
            if ($nama === '' || !in_array($kat, ['Minuman Kopi', 'Minuman Non Kopi']) || $harga <= 0) throw new Exception('Data menu belum lengkap.');
            if ($aksi === 'tambah') {
                q("INSERT INTO tb_menu (nama_menu, kategori, harga, gambar) VALUES (?,?,?,?)", 'ssds', [$nama, $kat, $harga, simpan_gambar()]);
                $_SESSION['flash'] = 'Menu berhasil ditambahkan.';
            } else {
                $old = row("SELECT gambar FROM tb_menu WHERE id_menu = ?", 'i', [$id])['gambar'] ?? null;
                q("UPDATE tb_menu SET nama_menu=?, kategori=?, harga=?, gambar=? WHERE id_menu=?", 'ssdsi', [$nama, $kat, $harga, simpan_gambar($old), $id]);
                $_SESSION['flash'] = 'Menu berhasil diperbarui.';
            }
        }
    } catch (mysqli_sql_exception $ex) {
        $_SESSION['flash'] = 'Menu tidak bisa dihapus karena sudah dipakai di transaksi.';
    } catch (Exception $ex) { $_SESSION['flash'] = $ex->getMessage(); }
    redirect('/pages/data-menu.php');
}
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
$cari = trim($_GET['q'] ?? '');
$data = rows("SELECT * FROM tb_menu WHERE nama_menu LIKE ? ORDER BY id_menu", 's', ["%$cari%"]);
?>
<div class="topbar"><h1>Data Menu</h1><button class="btn" id="btnTambah">+ Tambah Menu</button></div>
<?php if ($flash): ?><div class="alert info"><?= e($flash) ?></div><?php endif; ?>
<form method="get" class="search"><input class="field" name="q" placeholder="Cari menu..." value="<?= e($cari) ?>"></form>
<div class="card"><div class="scroll"><table><thead><tr><th>No</th><th>Gambar</th><th>Nama Menu</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data as $i => $m): ?>
  <tr><td><?= $i + 1 ?></td><td><img class="thumb" src="<?= menu_img($m['gambar']) ?>" alt=""></td><td><b><?= e($m['nama_menu']) ?></b></td><td><?= e($m['kategori']) ?></td><td><?= rupiah($m['harga']) ?></td>
  <td class="aksi">
    <button class="ico green btn-edit" data-id="<?= $m['id_menu'] ?>" data-nama="<?= e($m['nama_menu']) ?>" data-kat="<?= e($m['kategori']) ?>" data-harga="<?= (int)$m['harga'] ?>" data-img="<?= menu_img($m['gambar']) ?>" aria-label="Edit">&#9998;</button>
    <button class="ico red btn-hapus" data-id="<?= $m['id_menu'] ?>" aria-label="Hapus">&#128465;</button></td></tr>
<?php endforeach; if (!$data): ?><tr><td colspan="6" class="muted center">Menu tidak ditemukan.</td></tr><?php endif; ?>
</tbody></table></div></div>

<div class="modal" id="m-menu"><form class="box" method="post" enctype="multipart/form-data">
  <h3 id="mm-title">Tambah Menu</h3><?= csrf_field() ?>
  <input type="hidden" name="aksi" value="tambah" id="mm-aksi"><input type="hidden" name="id_menu" id="mm-id">
  <img id="mm-img" class="thumb big" alt="" hidden>
  <label>Nama Menu<input class="field" name="nama_menu" id="mm-nama" placeholder="Contoh: Kopi Susu" required></label>
  <label>Kategori<select class="field" name="kategori" id="mm-kat" required><option value="">Pilih Kategori</option><option>Minuman Kopi</option><option>Minuman Non Kopi</option></select></label>
  <label>Harga<input class="field" type="number" min="1" name="harga" id="mm-harga" placeholder="Rp 0" required></label>
  <label>Gambar<input class="field" type="file" name="gambar" accept="image/jpeg,image/png,image/webp"></label>
  <div class="row-btn"><button type="button" class="btn light" data-close>Batal</button><button class="btn">Simpan</button></div>
</form></div>
<div class="modal" id="m-hapus"><form class="box center" method="post">
  <h3>Hapus Menu</h3><p class="muted">Apakah Anda yakin ingin menghapus menu ini?</p>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id_menu" id="mh-id">
  <div class="row-btn"><button type="button" class="btn light" data-close>Batal</button><button class="btn danger">Hapus</button></div>
</form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
