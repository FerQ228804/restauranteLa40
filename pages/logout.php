<?php
//Declarar el tipo estricto para mejorar la seguridad y la consistencia del código
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar que la solicitud sea un POST para cerrar sesión
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
 // Verificar el token CSRF para proteger contra ataques de falsificación de solicitudes
check_csrf();
// Limpiar todas las variables de sesión y destruir la sesión actual
$_SESSION = [];
// Eliminar la cookie de sesión si se está utilizando cookies para la sesión
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'] ?? '',
        $params['secure'],
        $params['httponly']
    );
}
// Destruir la sesión actual y crear una nueva sesión para evitar problemas de seguridad
session_destroy();
session_start();
flash('Sesión cerrada correctamente.', 'info');
go('login.php');