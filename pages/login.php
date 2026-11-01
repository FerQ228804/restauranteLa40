<?php
// Declarar el tipo estricto para mejorar la seguridad y la consistencia del código
declare(strict_types=1);

// Definir la ruta base y cargar los archivos de configuración y bootstrap
define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Condición para redirigir al usuario si ya ha iniciado sesión
if (me()) {
    go(BASE_PATH . 'index.php');
}
// Inicializar variables para manejar errores y almacenar el correo electrónico ingresado
$error = '';
$emailInput = '';

// Manejo del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $emailInput = strtolower(trim((string) ($_POST['email'] ?? '')));
    $pass = (string) ($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT id, password_hash, activo FROM usuarios WHERE email = ?');
    $stmt->execute([$emailInput]);
    $u = $stmt->fetch();

    if ($u && (int) $u['activo'] === 1 && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $u['id'];
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();

        flash('Sesión iniciada correctamente.');
        go(BASE_PATH . 'index.php');
    }

    $error = 'Correo o contraseña incorrectos, o cuenta inactiva.';
}

$pageTitle = 'Iniciar sesión - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de inicio de sesión -->
<main class="container auth-container">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

        <div class="mb-3">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control" id="email" name="email" type="email" maxlength="190" value="<?= e($emailInput) ?>" autocomplete="username" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Contraseña</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Entrar</button>
            <a href="recuperar.php">¿Olvidaste la contraseña?</a>
        </div>

        <?php if (registration_open()): ?>
            <p class="mt-3 mb-0">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        <?php endif; ?>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>