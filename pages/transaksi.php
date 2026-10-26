<?php
$title = 'Transaksi'; $active = 'transaksi';
require_once __DIR__ . '/../includes/header.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $cart = json_decode($_POST['cart'] ?? '[]', true) ?: [];
    $metode = in_array($_POST['metode'] ?? '', ['Cash', 'QRIS']) ? $_POST['metode'] : 'Cash';
    $tgl = DateTime::createFromFormat('Y-m-d', $_POST['tanggal'] ?? '') ?: new DateTime();
    $waktu = $tgl->format('Y-m-d') . ' ' . date('H:i:s');
    $items = []; $total = 0;
    foreach ($cart as $id => $qty) {
        $qty = (int)$qty; $m = row("SELECT id_menu, harga FROM tb_menu WHERE id_menu = ?", 'i', [(int)$id]);
        if ($m && $qty > 0) { $items[] = [$m['id_menu'], $qty, (float)$m['harga']]; $total += $qty * $m['harga']; }
    }
    if (!$items) { $err = 'Pesanan masih kosong.'; }
    else {
        mysqli_begin_transaction($koneksi);
        try {
            q("INSERT INTO tb_transaksi (id_user, tanggal, total_harga, metode_bayar) VALUES (?,?,?,?)", 'isds', [$_SESSION['id_user'], $waktu, $total, $metode]);
            $idT = mysqli_insert_id($koneksi);
            foreach ($items as [$idm, $qty, $h]) q("INSERT INTO tb_detail_transaksi (id_transaksi, id_menu, jumlah, harga, subtotal) VALUES (?,?,?,?,?)", 'iiidd', [$idT, $idm, $qty, $h, $qty * $h]);
            mysqli_commit($koneksi);
            $_SESSION['flash'] = 'Transaksi berhasil disimpan (' . rupiah($total) . ').';
            redirect('/pages/transaksi.php');
        } catch (Throwable $ex) { mysqli_rollback($koneksi); $err = 'Gagal menyimpan transaksi.'; }
    }
}
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
$menus = rows("SELECT id_menu id, nama_menu nama, harga, gambar FROM tb_menu ORDER BY id_menu");
foreach ($menus as &$m) { $m['img'] = menu_img($m['gambar']); $m['harga'] = (int)$m['harga']; unset($m['gambar']); } unset($m);
?>
<div class="topbar"><h1>Pencatatan Transaksi</h1></div>
<?php if ($flash): ?><div class="alert info"><?= e($flash) ?></div><?php endif; if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif; ?>
<div class="grid2 trx" id="trx" data-menus='<?= e(json_encode($menus)) ?>'>
  <div class="card"><div class="between"><h3>Pilih Menu</h3><span class="muted small">Total <?= count($menus) ?> item</span></div>
    <input class="field" id="cariMenu" placeholder="Cari menu...">
    <div id="listMenu" class="list"></div></div>
  <form class="card" method="post" id="formTrx"><h3>Pesanan</h3>
    <?= csrf_field() ?><input type="hidden" name="cart" id="cartInput">
    <label class="small muted">Tanggal<input class="field" type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required></label>
    <div class="scroll"><table><thead><tr><th>Menu</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th><th></th></tr></thead><tbody id="cartBody"></tbody></table></div>
    <div class="between total"><span>Total</span><b id="cartTotal">Rp 0</b></div>
    <p class="small muted label-up">Metode Pembayaran</p>
    <label class="radio"><input type="radio" name="metode" value="Cash" checked> Cash</label>
    <label class="radio"><input type="radio" name="metode" value="QRIS"> QRIS</label>
    <div class="row-btn right"><button type="button" class="btn light" id="btnReset">Reset</button><button class="btn">Simpan Transaksi</button></div>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
