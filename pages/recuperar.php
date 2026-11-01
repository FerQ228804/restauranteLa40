<?php
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Inicializar variables para manejar mensajes y errores
$msg = '';

// Procesar el formulario de recuperación de contraseña si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $stmt = $pdo->prepare('SELECT id, email FROM usuarios WHERE email = ? AND activo = 1');
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    /* Si se encuentra un usuario activo con el correo proporcionado, generar un 
    token de recuperación y enviarlo por correo electrónico */
    if ($u) {
        $token = bin2hex(random_bytes(32));

        $pdo->prepare('DELETE FROM tokens_recuperacion WHERE usuario_id = ?')->execute([$u['id']]);
        $insert = $pdo->prepare(
            'INSERT INTO tokens_recuperacion (usuario_id, token_hash, expira_en) 
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 60 MINUTE))'
        );
        $insert->execute([$u['id'], hash('sha256', $token)]);

        // Ajuste de ruta apuntando a /pages/restablecer.php
        $link = APP_URL . '/pages/restablecer.php?token=' . $token;
        $body = "Abre este enlace en los próximos 60 minutos para restablecer tu clave:\n" . $link;

        // Intentar enviar el correo electrónico de recuperación y registrar un error si falla
        if (!send_mail($u['email'], 'Recuperación de contraseña - La 40', $body)) {
            error_log('No se pudo enviar email de recuperación a ' . $u['email']);
        }
    }

    $msg = 'Si existe una cuenta activa con ese correo, recibirás instrucciones en breve.';
}

$pageTitle = 'Recuperar contraseña - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de recuperación de contraseña -->
<main class="container auth-container">
    <h2>Recuperar contraseña</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="recuperar.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

        <div class="mb-3">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control" id="email" type="email" name="email" required>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Enviar instrucciones</button>
            <a href="login.php">Volver al inicio de sesión</a>
        </div>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>