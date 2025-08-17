<?php
// config.php
session_start();

// Basit admin bilgileri (isteğe göre değiştirin)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '1234'); // Demo amaçlı. Canlıda .env/hashed kullanın.

define('PRODUCTS_JSON', __DIR__ . '/products.json');
define('IMAGES_DIR', __DIR__ . '/images');

function load_products() {
    $raw = file_get_contents(PRODUCTS_JSON);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = [];
    return $data;
}

function save_products($arr) {
    // Güzel formatla yaz
    file_put_contents(PRODUCTS_JSON, json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

function ensure_logged_in() {
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        header('Location: admin-login.php');
        exit;
    }
}

function normalize_item($item) {
    // Eski kayıtlarda 'resim' tekil olabilir. Hepsini 'resimler' listesine dönüştürelim.
    if (isset($item['resim']) && !isset($item['resimler'])) {
        $item['resimler'] = [$item['resim']];
        unset($item['resim']);
    }
    if (!isset($item['resimler'])) $item['resimler'] = [];
    return $item;
}
?>