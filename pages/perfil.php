<?php
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Asegurarse de que el usuario haya iniciado sesión antes de permitir el acceso a esta página
$u = need_login();
$error = '';

// Procesar el formulario de actualización de perfil si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $name = trim((string) ($_POST['nombre'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));

    // Validar los datos del formulario: nombre y correo electrónico
    if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Los datos ingresados no son válidos.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ?');
        $stmt->execute([$email, $u['id']]);

        if ($stmt->fetch()) {
            $error = 'No fue posible guardar esos datos.';
        } else {
            $update = $pdo->prepare('UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?');
            $update->execute([$name, $email, $u['id']]);

            flash('Perfil actualizado correctamente.');
            go('perfil.php');
        }
    }
}

$pageTitle = 'Mi perfil - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de perfil -->
<main class="container auth-container">
    <h2>Mi perfil</h2>
    <p>Rol asignado: <strong><?= e($u['rol']) ?></strong></p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="perfil.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

        <div class="mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-control" id="nombre" name="nombre" value="<?= e((string) ($_POST['nombre'] ?? $u['nombre'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control" id="email" type="email" name="email" value="<?= e((string) ($_POST['email'] ?? $u['email'])) ?>" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar perfil</button>
            <a class="btn btn-secondary" href="cambiar_password.php">Cambiar contraseña</a>
        </div>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>