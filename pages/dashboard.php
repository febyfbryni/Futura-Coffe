<?php
$title = 'Dashboard'; $active = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
$totMenu = row("SELECT COUNT(*) c FROM tb_menu")['c'];
$totTrx = row("SELECT COUNT(*) c FROM tb_transaksi")['c'];
$totPend = row("SELECT COALESCE(SUM(total_harga),0) c FROM tb_transaksi")['c'];
$terbaru = rows("SELECT t.id_transaksi, t.tanggal, t.total_harga, GROUP_CONCAT(m.nama_menu SEPARATOR ', ') menu, SUM(d.jumlah) jumlah
  FROM tb_transaksi t JOIN tb_detail_transaksi d ON d.id_transaksi = t.id_transaksi JOIN tb_menu m ON m.id_menu = d.id_menu
  GROUP BY t.id_transaksi ORDER BY t.tanggal DESC, t.id_transaksi DESC LIMIT 5");
$perHari = array_column(rows("SELECT DATE(tanggal) d, SUM(total_harga) s FROM tb_transaksi WHERE tanggal >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(tanggal)"), 's', 'd');
$chart = []; for ($i = 6; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-$i day")); $chart[$d] = (float)($perHari[$d] ?? 0); }
$max = max($chart) ?: 1;
?>
<div class="topbar"><div><h1>Dashboard</h1><p class="muted small">Selamat datang di sistem pencatatan transaksi Kedai Kopi Futura</p></div>
  <div class="chip"><?= e($_SESSION['nama']) ?></div></div>
<div class="stats">
  <div class="card stat"><span class="muted small">Total Menu</span><b><?= $totMenu ?></b></div>
  <div class="card stat"><span class="muted small">Total Transaksi</span><b><?= $totTrx ?></b></div>
  <div class="card stat"><span class="muted small">Total Pendapatan</span><b><?= rupiah($totPend) ?></b></div>
</div>
<div class="grid2">
  <div class="card"><h3>Transaksi Terbaru</h3>
    <div class="scroll"><table><thead><tr><th>No</th><th>Tanggal</th><th>Menu</th><th>Jumlah</th><th>Total</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($terbaru as $i => $t): ?>
      <tr><td><?= $i + 1 ?></td><td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td><td><?= e($t['menu']) ?></td><td><?= $t['jumlah'] ?></td><td><?= rupiah($t['total_harga']) ?></td><td><span class="badge">Lunas</span></td></tr>
    <?php endforeach; if (!$terbaru): ?><tr><td colspan="6" class="muted center">Belum ada transaksi.</td></tr><?php endif; ?>
    </tbody></table></div></div>
  <div class="card"><h3>Pendapatan 7 Hari Terakhir</h3>
    <div class="bars"><?php foreach ($chart as $d => $v): ?>
      <div class="bar" title="<?= rupiah($v) ?>"><i style="height:<?= round($v / $max * 100) ?>%"></i><span><?= date('d M', strtotime($d)) ?></span></div>
    <?php endforeach; ?></div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
