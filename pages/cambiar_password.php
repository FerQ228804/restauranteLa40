<?php
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Asegurarse de que el usuario haya iniciado sesión antes de permitir el acceso a esta página
$u = need_login();
$error = '';

// Procesar el formulario de cambio de contraseña si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $old = (string) ($_POST['actual'] ?? '');
    $new = (string) ($_POST['nuevo'] ?? '');
    $confirm = (string) ($_POST['confirmar'] ?? '');

    $stmt = $pdo->prepare('SELECT password_hash FROM usuarios WHERE id = ?');
    $stmt->execute([$u['id']]);
    $hash = (string) $stmt->fetchColumn();

    // Validar la contraseña actual, la longitud de la nueva contraseña y la coincidencia de las contraseñas nuevas
    if (!password_verify($old, $hash)) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($new) < 10) {
        $error = 'Usa al menos 10 caracteres para la nueva contraseña.';
    } elseif ($new !== $confirm) {
        $error = 'Las contraseñas nuevas no coinciden.';
    } else {
        $update = $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?');
        $update->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);

        // Regenerar el ID de sesión para mejorar la seguridad después de cambiar la contraseña
        session_regenerate_id(true);
        flash('Contraseña actualizada correctamente.');
        go('perfil.php');
    }
}

$pageTitle = 'Cambiar contraseña - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de cambio de contraseña -->
<main class="container auth-container">
    <h2>Cambiar contraseña</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="cambiar_password.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

        <div class="mb-3">
            <label class="form-label" for="actual">Contraseña actual</label>
            <input class="form-control" id="actual" type="password" name="actual" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="nuevo">Nueva contraseña (mínimo 10 caracteres)</label>
            <input class="form-control" id="nuevo" type="password" name="nuevo" minlength="10" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="confirmar">Confirmar nueva contraseña</label>
            <input class="form-control" id="confirmar" type="password" name="confirmar" minlength="10" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
            <a class="btn btn-secondary" href="perfil.php">Cancelar</a>
        </div>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>