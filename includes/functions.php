<?php
declare(strict_types=1);

/**
 * Escapa cadenas de texto de forma segura para prevenir ataques XSS en vistas HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirige a una URL y detiene la ejecución del script inmediatamente.
 */
function go(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Genera o recupera el token CSRF de la sesión actual.
 */
function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Verifica la validez del token CSRF enviado mediante una petición POST.
 */
function check_csrf(): void
{
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals(csrf(), $token)) {
        http_response_code(419);
        exit('Solicitud no válida o expirada. Recarga la página e intenta de nuevo.');
    }
}

/**
 * Almacena un mensaje temporal (Flash) en la sesión para mostrarlo en la siguiente vista.
 */
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type'    => $type
    ];
}

/**
 * Obtiene los datos del usuario autenticado (con caché estática para evitar consultas duplicadas).
 */
function me(): ?array
{
    global $pdo;
    static $cachedUser = null;
    static $loaded = false;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    if ($loaded) {
        return $cachedUser;
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT u.id, u.nombre, u.email, u.activo, r.nombre AS rol 
             FROM usuarios u 
             JOIN roles r ON r.id = u.rol_id 
             WHERE u.id = ?'
        );
        $stmt->execute([(int) $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user || (int) $user['activo'] !== 1) {
            // Solo cerramos la sesión del usuario sin destruir el carrito ni el token CSRF
            unset($_SESSION['user_id'], $_SESSION['created_at'], $_SESSION['last_activity']);
            $loaded = true;
            return null;
        }

        $cachedUser = $user;
        $loaded = true;
        return $cachedUser;
    } catch (PDOException $e) {
        error_log('Error en me(): ' . $e->getMessage());
        return null;
    }
}

/**
 * Exige que el usuario haya iniciado sesión; de lo contrario, redirige a pages/login.php.
 */
function need_login(): array
{
    $user = me();
    if (!$user) {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        flash('Inicia sesión para continuar.', 'warning');
        go($basePath . 'pages/login.php');
    }
    return $user;
}

/**
 * Verifica si el usuario autenticado posee un permiso específico según su rol.
 */
function can(string $permissionKey): bool
{
    global $pdo;
    static $permissions = null;

    if (empty($_SESSION['user_id'])) {
        return false;
    }

    if ($permissions === null) {
        try {
            $stmt = $pdo->prepare(
                'SELECT p.clave 
                 FROM usuarios u 
                 JOIN rol_permisos rp ON rp.rol_id = u.rol_id 
                 JOIN permisos p ON p.id = rp.permiso_id 
                 WHERE u.id = ? AND u.activo = 1'
            );
            $stmt->execute([(int) $_SESSION['user_id']]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log('Error en can(): ' . $e->getMessage());
            $permissions = [];
        }
    }

    return in_array('*', $permissions, true) || in_array($permissionKey, $permissions, true);
}

/**
 * Exige un permiso específico para acceder a la vista o ejecutar una operación.
 */
function need_permission(string $permissionKey): void
{
    need_login();
    if (!can($permissionKey)) {
        http_response_code(403);
        exit('Acceso denegado: no tienes permiso para realizar esta operación.');
    }
}

/**
 * Verifica si el registro público de usuarios está habilitado en la tabla de configuración.
 */
function registration_open(): bool
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT valor FROM configuracion WHERE clave = 'registro_abierto'");
        return $stmt->fetchColumn() === '1';
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Envía correos electrónicos en texto plano codificando el asunto en UTF-8 Base64.
 */
function send_mail(string $to, string $subject, string $body): bool
{
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = 'From: ' . MAIL_FROM . "\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $encodedSubject, $body, $headers);
}