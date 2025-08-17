async function loadProducts(){
  const res = await fetch('products.json?ts=' + Date.now());
  const data = await res.json();
  return data.map(p => {
    if (p.resim && !p.resimler) p.resimler = [p.resim];
    if (!p.resimler) p.resimler = [];
    return p;
  });
}

function uniqueCategories(items){
  const set = new Set(items.map(i => i.kategori).filter(Boolean));
  return Array.from(set);
}

function renderFilters(cats){
  const fixed = ['tümü','masa','tezgah','banyo-dolabi'];
  const all = Array.from(new Set(['tümü', ...fixed, ...cats]));
  const select = document.getElementById('filterSelect');
  select.innerHTML='';
  all.forEach(cat => {
    const opt = document.createElement('option');
    opt.value = cat;
    opt.textContent = cat.replace('-', ' ');
    select.appendChild(opt);
  });
  select.value = 'tümü';
  select.onchange = () => applyFilter(select.value);
}

let PRODUCTS = [];
async function init(){
  PRODUCTS = await loadProducts();
  renderFilters(uniqueCategories(PRODUCTS));
  applyFilter('tümü');
}

function applyFilter(cat){
  const grid = document.getElementById('grid');
  grid.innerHTML = '';
  const list = (cat==='tümü') ? PRODUCTS : PRODUCTS.filter(p => (p.kategori||'').toLowerCase() === cat.toLowerCase());
  list.forEach(p => {
    const card = document.createElement('div');
    card.className='card';
    card.onclick = () => { window.location.href = 'detay.html?id='+encodeURIComponent(p.id); };
    const img = document.createElement('img');
    const src = (p.resimler && p.resimler[0]) ? p.resimler[0] : 'images/ban1.jpg';
    img.src = src;
    img.alt = p.isim || '';
    const info = document.createElement('div');
    info.className='p';
    info.innerHTML = '<div class="name">'+ (p.isim||'') +'</div>' +
                     '<div class="muted">Kategori: '+ (p.kategori||'-') +'</div>' +
                     '<div class="muted">'+ (p.aciklama||'') +'</div>';
    card.appendChild(img);
    card.appendChild(info);
    grid.appendChild(card);
  });
}

init();