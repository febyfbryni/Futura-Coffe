</main>
</div>
<div class="modal" id="m-logout"><div class="box center">
  <h3>Logout</h3><p class="muted">Apakah Anda yakin ingin keluar dari sistem?</p>
  <form method="post" action="<?= BASE_URL ?>/pages/logout.php" class="row-btn">
    <?= csrf_field() ?><button type="button" class="btn light" data-close>Batal</button><button class="btn">Logout</button>
  </form>
</div></div>
<script src="<?= asset("js/app.js") ?>"></script>
</body>
</html>
