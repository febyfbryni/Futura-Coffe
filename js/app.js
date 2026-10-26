const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const openM = id => $('#' + id).classList.add('open');
document.addEventListener('click', e => {
  const o = e.target.closest('[data-open]'); if (o) openM(o.dataset.open);
  if (e.target.closest('[data-close]') || e.target.classList.contains('modal')) e.target.closest('.modal').classList.remove('open');
});
const tp = $('#togglePw'); if (tp) tp.onclick = () => { const p = $('#pw'); p.type = p.type === 'password' ? 'text' : 'password'; };

// Data Menu
if ($('#m-menu')) {
  const f = id => $('#' + id);
  f('btnTambah').onclick = () => {
    f('mm-title').textContent = 'Tambah Menu'; f('mm-aksi').value = 'tambah'; f('mm-id').value = '';
    ['mm-nama', 'mm-kat', 'mm-harga'].forEach(i => f(i).value = ''); f('mm-img').hidden = true; openM('m-menu');
  };
  $$('.btn-edit').forEach(b => b.onclick = () => {
    f('mm-title').textContent = 'Edit Menu'; f('mm-aksi').value = 'edit'; f('mm-id').value = b.dataset.id;
    f('mm-nama').value = b.dataset.nama; f('mm-kat').value = b.dataset.kat; f('mm-harga').value = b.dataset.harga;
    f('mm-img').src = b.dataset.img; f('mm-img').hidden = false; openM('m-menu');
  });
  $$('.btn-hapus').forEach(b => b.onclick = () => { f('mh-id').value = b.dataset.id; openM('m-hapus'); });
}

// Transaksi
const trx = $('#trx');
if (trx) {
  const menus = JSON.parse(trx.dataset.menus), cart = {};
  const byId = Object.fromEntries(menus.map(m => [m.id, m]));
  const draw = () => {
    const k = $('#cariMenu').value.toLowerCase();
    $('#listMenu').innerHTML = menus.filter(m => m.nama.toLowerCase().includes(k)).map(m =>
      `<div class="li"><img class="thumb" src="${esc(m.img)}" alt=""><span class="nm">${esc(m.nama)}</span><span class="muted">${rp(m.harga)}</span><button type="button" class="add" data-id="${m.id}">+</button></div>`).join('');
  };
  const drawCart = () => {
    let total = 0;
    $('#cartBody').innerHTML = Object.entries(cart).map(([id, q]) => {
      const m = byId[id], sub = q * m.harga; total += sub;
      return `<tr><td>${esc(m.nama)}</td><td class="qty"><button type="button" data-d="-1" data-id="${id}">-</button> ${q} <button type="button" data-d="1" data-id="${id}">+</button></td><td>${rp(m.harga)}</td><td>${rp(sub)}</td><td><button type="button" class="ico red" data-del="${id}">&times;</button></td></tr>`;
    }).join('') || '<tr><td colspan="5" class="muted center">Belum ada pesanan.</td></tr>';
    $('#cartTotal').textContent = rp(total); $('#cartInput').value = JSON.stringify(cart);
  };
  $('#cariMenu').oninput = draw;
  trx.addEventListener('click', e => {
    const a = e.target.closest('.add'), d = e.target.closest('[data-d]'), x = e.target.closest('[data-del]');
    if (a) cart[a.dataset.id] = (cart[a.dataset.id] || 0) + 1;
    else if (d) { const id = d.dataset.id; cart[id] += +d.dataset.d; if (cart[id] < 1) delete cart[id]; }
    else if (x) delete cart[x.dataset.del]; else return;
    drawCart();
  });
  $('#btnReset').onclick = () => { Object.keys(cart).forEach(k => delete cart[k]); drawCart(); };
  $('#formTrx').onsubmit = e => { if (!Object.keys(cart).length) { e.preventDefault(); alert('Pilih menu terlebih dahulu.'); } };
  draw(); drawCart();
}
