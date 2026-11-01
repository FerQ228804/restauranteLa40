<?php
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Procesar el token de recuperación de contraseña y validar su existencia y vigencia
$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$uid = 0;

// Validar el token de recuperación: debe ser un hash SHA-256 válido (64 caracteres hexadecimales)
if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $stmt = $pdo->prepare(
        'SELECT usuario_id FROM tokens_recuperacion WHERE token_hash = ? AND expira_en > NOW()'
    );
    $stmt->execute([hash('sha256', $token)]);
    $uid = (int) ($stmt->fetchColumn() ?: 0);
}

// Procesar el formulario de restablecimiento de contraseña si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $pass = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirmar'] ?? '');

    /* Validar la existencia del usuario asociado al token, la longitud
    de la nueva contraseña y la coincidencia de las contraseñas nuevas */
    if (!$uid) {
        $error = 'El enlace no es válido o ha expirado.';
    } elseif (strlen($pass) < 10) {
        $error = 'La contraseña debe tener al menos 10 caracteres.';
    } elseif ($pass !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($pass, PASSWORD_DEFAULT), $uid]);
        $pdo->prepare('DELETE FROM tokens_recuperacion WHERE usuario_id = ?')
            ->execute([$uid]);

        flash('Contraseña actualizada con éxito. Inicia sesión.');
        go('login.php');
    }
}

$pageTitle = 'Restablecer contraseña - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de restablecimiento de contraseña -->
<main class="container auth-container">
    <h2>Restablecer contraseña</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!$uid): ?>
        <p>El enlace no es válido o ha vencido. <a href="recuperar.php">Solicita uno nuevo aquí</a>.</p>
    <?php else: ?>
        <form method="post" action="restablecer.php">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="mb-3">
                <label class="form-label" for="password">Nueva contraseña (mínimo 10 caracteres)</label>
                <input class="form-control" id="password" type="password" name="password" minlength="10" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="confirmar">Confirmar contraseña</label>
                <input class="form-control" id="confirmar" type="password" name="confirmar" minlength="10" required>
            </div>

            <button type="submit" class="btn btn-primary">Guardar nueva contraseña</button>
        </form>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>