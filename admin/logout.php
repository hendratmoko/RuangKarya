<?php
require_once dirname(__DIR__) . '/config.php';

// 1. Kosongkan semua data di array $_SESSION
$_SESSION = [];

// 2. Hapus cookie sesi
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Hancurkan sesi di server
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// 4. Pengalihan kembali ke halaman login admin
header('Location: ' . url('admin/login.php'));
exit;