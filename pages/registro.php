<?php
// Declarar el tipo estricto para mejorar la seguridad y la consistencia del código
declare(strict_types=1);

define('BASE_PATH', '../');
require_once __DIR__ . '/../config/bootstrap.php';

// Redirigir al usuario a la página principal si ya ha iniciado sesión
if (me()) {
    go(BASE_PATH . 'index.php');
}

// Verificar si el registro público está habilitado
if (!registration_open()) {
    http_response_code(403);
    exit('El administrador ha deshabilitado el registro público.');
}

// Inicializar variables para manejar errores y almacenar datos del formulario
$error = '';
$name = '';
$email = '';

// Procesar el formulario de registro si se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $name = trim((string) ($_POST['nombre'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $pass = (string) ($_POST['password'] ?? '');

    // Validar los datos del formulario: nombre, correo electrónico y contraseña
    if (
        mb_strlen($name) < 2 ||
        mb_strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($pass) < 10
    ) {
        $error = 'Revisa los datos ingresados. La contraseña debe tener mínimo 10 caracteres.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'No fue posible crear una cuenta con esos datos.';
        } else {
            $roleId = $pdo->query("SELECT id FROM roles WHERE nombre = 'usuario'")->fetchColumn();
            $insert = $pdo->prepare(
                'INSERT INTO usuarios (nombre, email, password_hash, rol_id) VALUES (?, ?, ?, ?)'
            );
            $insert->execute([
                $name,
                $email,
                password_hash($pass, PASSWORD_DEFAULT),
                (int) $roleId
            ]);

            flash('Cuenta creada con éxito. Ahora puedes iniciar sesión.');
            go('login.php');
        }
    }
}

// Establecer el título de la página y cargar el encabezado
$pageTitle = 'Crear cuenta - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal de la página de registro -->
<main class="container auth-container">
    <h2>Crear cuenta</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="registro.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

        <div class="mb-3">
            <label class="form-label" for="nombre">Nombre completo</label>
            <input class="form-control" id="nombre" name="nombre" maxlength="100" value="<?= e($name) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control" id="email" type="email" name="email" maxlength="190" value="<?= e($email) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Contraseña (mínimo 10 caracteres)</label>
            <input class="form-control" id="password" type="password" name="password" minlength="10" maxlength="200" autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrarme</button>
        <p class="mt-3 mb-0">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>