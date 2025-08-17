<?php
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if ($u === ADMIN_USER && $p === ADMIN_PASS) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Hatalı kullanıcı adı veya şifre.';
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Girişi</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f7f7f8; }
    .card { background:#fff; padding:24px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); width:100%; max-width:380px; }
    .card h1 { margin:0 0 12px; font-size:22px; }
    .form-group { margin-bottom:12px; }
    label { display:block; margin-bottom:6px; font-weight:600; }
    input { width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; }
    button { width:100%; padding:12px; border:none; background:black; color:white; border-radius:12px; font-weight:700; cursor:pointer; }
    .error { color:#b00020; margin-bottom:10px; }
  </style>
</head>
<body>
  <form class="card" method="post">
    <h1>Admin Girişi</h1>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="form-group">
      <label>Kullanıcı Adı</label>
      <input name="username" required>
    </div>
    <div class="form-group">
      <label>Şifre</label>
      <input name="password" type="password" required>
    </div>
    <button type="submit">Giriş yap</button>
  </form>
</body>
</html>
