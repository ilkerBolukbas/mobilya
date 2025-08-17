<?php
require_once __DIR__ . '/config.php';
ensure_logged_in();

$products = array_map('normalize_item', load_products());
$actionMsg = '';

// CREATE / UPDATE / DELETE işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create' || $action === 'update') {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
        $isim = trim($_POST['isim'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $malzeme = trim($_POST['malzeme'] ?? '');
        $renk = trim($_POST['renk'] ?? '');

        // resimler: mevcut + yüklenen
        $resimler = [];
        if (!empty($_POST['existing_images'])) {
            foreach ($_POST['existing_images'] as $img) {
                if ($img !== '') $resimler[] = $img;
            }
        }

        // Yeni yüklenenler
        if (!empty($_FILES['resimler']['name'][0])) {
            for ($i=0; $i<count($_FILES['resimler']['name']); $i++) {
                if ($_FILES['resimler']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['resimler']['tmp_name'][$i];
                    $ext = pathinfo($_FILES['resimler']['name'][$i], PATHINFO_EXTENSION);
                    $new = 'images/' . uniqid('urun_') . '.' . strtolower($ext);
                    move_uploaded_file($tmp, __DIR__ . '/' . $new);
                    $resimler[] = $new;
                }
            }
        }

        if ($action === 'create') {
            // yeni id
            $maxId = -1;
            foreach ($products as $p) { if (isset($p['id']) && $p['id'] > $maxId) $maxId = $p['id']; }
            $newItem = [
                'id' => $maxId + 1,
                'isim' => $isim,
                'kategori' => $kategori,
                'aciklama' => $aciklama,
                'malzeme' => $malzeme,
                'renk' => $renk,
                'resimler' => $resimler
            ];
            $products[] = $newItem;
            save_products($products);
            $actionMsg = 'Ürün eklendi.';
        } else {
            // update
            foreach ($products as &$p) {
                if (isset($p['id']) && $p['id'] == $id) {
                    $p['isim'] = $isim;
                    $p['kategori'] = $kategori;
                    $p['aciklama'] = $aciklama;
                    $p['malzeme'] = $malzeme;
                    $p['renk'] = $renk;
                    $p['resimler'] = $resimler;
                    break;
                }
            }
            unset($p);
            save_products($products);
            $actionMsg = 'Ürün güncellendi.';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $products = array_values(array_filter($products, function($p) use ($id) { return intval($p['id']) !== $id; }));
        save_products($products);
        $actionMsg = 'Ürün silindi.';
    }
}

$products = array_map('normalize_item', load_products());
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body{background:#f6f7fb;padding:20px;font-family:system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .btn{padding:10px 14px;border:none;border-radius:12px;background:black;color:white;font-weight:700;cursor:pointer;}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .card{background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:16px;}
    .card h2{margin:0 0 12px;font-size:20px;}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    label{font-weight:600;margin-bottom:6px;display:block;}
    input, textarea, select{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;}
    table{width:100%;border-collapse:collapse;}
    th, td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;}
    .thumbs{display:flex;gap:8px;flex-wrap:wrap;}
    .thumbs img{width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #eee;}
    .msg{margin-bottom:10px;color:green;}
    @media(max-width:900px){.grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="topbar">
    <h1>Admin Panel</h1>
    <a class="btn" href="logout.php">Çıkış</a>
  </div>
  <?php if ($actionMsg): ?><div class="msg"><?= htmlspecialchars($actionMsg) ?></div><?php endif; ?>

  <div class="grid">
    <div class="card">
      <h2>Ürün Ekle/Güncelle</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create" id="actionField">
        <div class="form-grid">
          <div>
            <label>Ürün ID (güncelleme için)</label>
            <input name="id" id="idField" placeholder="(güncellemede doldurun)">
          </div>
          <div>
            <label>Kategori</label>
            <input name="kategori" id="kategoriField" required placeholder="ör: masa, tezgah, banyo-dolabi">
          </div>
          <div>
            <label>İsim</label>
            <input name="isim" id="isimField" required>
          </div>
          <div>
            <label>Renk</label>
            <input name="renk" id="renkField">
          </div>
          <div style="grid-column:1/3">
            <label>Malzeme</label>
            <input name="malzeme" id="malzemeField">
          </div>
          <div style="grid-column:1/3">
            <label>Açıklama</label>
            <textarea name="aciklama" id="aciklamaField" rows="3"></textarea>
          </div>
          <div style="grid-column:1/3">
            <label>Resimler (çoklu seçilebilir)</label>
            <input type="file" name="resimler[]" multiple accept="image/*">
          </div>
          <div id="existingImages" style="grid-column:1/3"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px;">
          <button class="btn" type="submit" onclick="document.getElementById('actionField').value='create'">Ekle</button>
          <button class="btn" type="submit" onclick="document.getElementById('actionField').value='update'">Güncelle</button>
        </div>
      </form>
    </div>

    <div class="card">
      <h2>Ürünler</h2>
      <table>
        <thead><tr><th>ID</th><th>İsim</th><th>Kategori</th><th>Görseller</th><th>İşlem</th></tr></thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= intval($p['id'] ?? -1) ?></td>
              <td><?= htmlspecialchars($p['isim'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['kategori'] ?? '') ?></td>
              <td class="thumbs">
                <?php foreach (($p['resimler'] ?? []) as $img): ?>
                  <img src="<?= htmlspecialchars($img) ?>" alt="">
                <?php endforeach; ?>
              </td>
              <td>
                <button class="btn" onclick='fillForm(<?= json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>)'>Düzenle</button>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Silinsin mi?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= intval($p['id'] ?? -1) ?>">
                  <button class="btn" type="submit">Sil</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

<script>
function fillForm(p){
  document.getElementById('idField').value = p.id ?? '';
  document.getElementById('isimField').value = p.isim ?? '';
  document.getElementById('kategoriField').value = p.kategori ?? '';
  document.getElementById('aciklamaField').value = p.aciklama ?? '';
  document.getElementById('malzemeField').value = p.malzeme ?? '';
  document.getElementById('renkField').value = p.renk ?? '';

  const holder = document.getElementById('existingImages');
  holder.innerHTML = '';
  const title = document.createElement('div');
  title.innerHTML = '<label>Mevcut Resimler</label>';
  holder.appendChild(title);

  const imgs = p.resimler || (p.resim ? [p.resim] : []);
  imgs.forEach((src) => {
    const row = document.createElement('div');
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '8px';
    row.style.marginBottom = '6px';
    row.innerHTML = '<img src="'+src+'" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #eee">' +
                    '<input type="text" name="existing_images[]" value="'+src+'" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:10px">' +
                    '<small>(Düzenlemek istiyorsanız yolu değiştirebilirsiniz. Kaldırmak için değeri boş bırakın.)</small>';
    holder.appendChild(row);
  });
}
</script>
</body>
</html>
