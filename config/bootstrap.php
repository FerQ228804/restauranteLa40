<?php
// Declarar tipos estrictos para mejorar la seguridad y consistencia del código
declare(strict_types=1);

// Require el archivo de configuración principal
require_once __DIR__ . '/config.php';


// Configuración de la sesión para mejorar la seguridad y consistencia
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_name('la40_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Iniciar la sesión si aún no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Expiración de sesión por inactividad o tiempo absoluto
if (isset($_SESSION['user_id'])) {
    $now = time();
    if (($now - (int)($_SESSION['last_activity'] ?? $now)) > IDLE_TIMEOUT || 
        ($now - (int)($_SESSION['created_at'] ?? $now)) > ABS_TIMEOUT) {
        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'La sesión expiró. Inicia sesión nuevamente.'];
    } else {
        $_SESSION['last_activity'] = $now;
    }
}

require_once __DIR__ . '/../includes/functions.php';