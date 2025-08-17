function getParam(name){
  const url = new URL(window.location.href);
  return url.searchParams.get(name);
}

async function loadProducts(){
  const res = await fetch('products.json?ts=' + Date.now());
  const data = await res.json();
  return data.map(p => {
    if (p.resim && !p.resimler) p.resimler = [p.resim];
    if (!p.resimler) p.resimler = [];
    return p;
  });
}

async function init(){
  const id = parseInt(getParam('id'));
  const products = await loadProducts();
  const p = products.find(x => parseInt(x.id) === id);
  const holder = document.getElementById('detail');
  if (!p){ holder.textContent = 'Ürün bulunamadı.'; return; }

  let html = '<h1>'+ (p.isim||'') +'</h1>';
  if (p.resimler && p.resimler.length){
    html += '<div class="gallery">';
    p.resimler.forEach(src => {
      html += '<img src="'+src+'" alt="">';
    });
    html += '</div>';
  }
  html += '<p><strong>Kategori:</strong> '+ (p.kategori||'') +'</p>';
  html += '<p><strong>Açıklama:</strong> '+ (p.aciklama||'') +'</p>';
  html += '<p><strong>Malzeme:</strong> '+ (p.malzeme||'') +'</p>';
  html += '<p><strong>Renk:</strong> '+ (p.renk||'') +'</p>';
  holder.innerHTML = html;
}

init();