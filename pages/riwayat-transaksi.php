<?php
$title = 'Riwayat Transaksi'; $active = 'riwayat';
require_once __DIR__ . '/../includes/header.php';
$valid = fn($d, $def) => DateTime::createFromFormat('Y-m-d', $d ?? '') ? $d : $def;
$dari = $valid($_GET['dari'] ?? '', date('Y-m-01')); $sampai = $valid($_GET['sampai'] ?? '', date('Y-m-d'));
$cari = trim($_GET['q'] ?? '');
$where = "DATE(t.tanggal) BETWEEN ? AND ? AND EXISTS (SELECT 1 FROM tb_detail_transaksi d2 JOIN tb_menu m2 ON m2.id_menu = d2.id_menu WHERE d2.id_transaksi = t.id_transaksi AND m2.nama_menu LIKE ?)";
$par = [$dari, $sampai, "%$cari%"];
$total = row("SELECT COUNT(*) c FROM tb_transaksi t WHERE $where", 'sss', $par)['c'];
$per = 10; $pages = max(1, (int)ceil($total / $per)); $p = min(max(1, (int)($_GET['p'] ?? 1)), $pages); $off = ($p - 1) * $per;
$data = rows("SELECT t.id_transaksi, t.tanggal, t.total_harga, t.metode_bayar, GROUP_CONCAT(m.nama_menu SEPARATOR ', ') menu, SUM(d.jumlah) jumlah
  FROM tb_transaksi t JOIN tb_detail_transaksi d ON d.id_transaksi = t.id_transaksi JOIN tb_menu m ON m.id_menu = d.id_menu
  WHERE $where GROUP BY t.id_transaksi ORDER BY t.tanggal DESC, t.id_transaksi DESC LIMIT $per OFFSET $off", 'sss', $par);
$qs = fn($pg) => '?' . http_build_query(['dari' => $dari, 'sampai' => $sampai, 'q' => $cari, 'p' => $pg]);
?>
<div class="topbar"><h1>Riwayat Transaksi</h1></div>
<form method="get" class="filter">
  <input class="field" type="date" name="dari" value="<?= e($dari) ?>"><span class="muted">-</span>
  <input class="field" type="date" name="sampai" value="<?= e($sampai) ?>">
  <input class="field grow" name="q" placeholder="Cari menu..." value="<?= e($cari) ?>"><button class="btn">Cari</button>
</form>
<div class="card"><div class="scroll"><table><thead><tr><th>No</th><th>Tanggal</th><th>Menu</th><th>Jumlah</th><th>Total</th><th>Pembayaran</th><th>Status</th></tr></thead><tbody>
<?php foreach ($data as $i => $t): ?>
  <tr><td><?= $off + $i + 1 ?></td><td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td><td><?= e($t['menu']) ?></td><td><?= $t['jumlah'] ?></td><td><?= rupiah($t['total_harga']) ?></td><td><?= e($t['metode_bayar']) ?></td><td><span class="badge">Lunas</span></td></tr>
<?php endforeach; if (!$data): ?><tr><td colspan="7" class="muted center">Tidak ada transaksi pada rentang ini.</td></tr><?php endif; ?>
</tbody></table></div>
<?php if ($pages > 1): ?><div class="pager">
  <?php if ($p > 1): ?><a href="<?= $qs($p - 1) ?>">&lsaquo;</a><?php endif; ?>
  <?php for ($i = 1; $i <= $pages; $i++): ?><a href="<?= $qs($i) ?>" class="<?= $i === $p ? 'on' : '' ?>"><?= $i ?></a><?php endfor; ?>
  <?php if ($p < $pages): ?><a href="<?= $qs($p + 1) ?>">&rsaquo;</a><?php endif; ?></div><?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
