<?php
// Declaración estricta de tipos y definición de la constante BASE_PATH si no está definida
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '');
}

// 1. Inicialización de la sesión y manejo de errores
$u = function_exists('me') ? me() : null;
$notice = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// 2. Cálculo dinámico de unidades y monto total del carrito en sesión
$cantidad_total = 0;
$monto_total = 0.0;

if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cant = (int) ($item['cantidad'] ?? 0);
        $prec = (float) ($item['precio'] ?? 0);
        $cantidad_total += $cant;
        $monto_total += ($cant * $prec);
    }
}

// 3. Identificar página actual para marcar el estado activo en el navbar
$currentScript = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$isHome = ($currentScript === 'index.php');

// 4. Resolvedor automático de rutas optimizado para extensiones .png
$projectRoot = dirname(__DIR__);

// Función para resolver la ruta de un archivo .png en múltiples directorios
if (!function_exists('resolve_png_logo')) {
    function resolve_png_logo(string $baseName, string $projectRoot): ?string
    {
        $directories = ['assets/img/', 'img/', 'assets/images/', 'images/'];
        foreach ($directories as $dir) {
            $relativePath = $dir . $baseName . '.png';
            $fullDiskPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            if (is_file($fullDiskPath)) {
                return BASE_PATH . $relativePath;
            }
        }
        return null;
    }
}

// 5. Resolución de logos multi-resolución para favicon y marca
$logo1x = resolve_png_logo('la40_logo_1x', $projectRoot) ?? resolve_png_logo('logo', $projectRoot);
$logo2x = resolve_png_logo('la40_logo_2x', $projectRoot);
$logo3x = resolve_png_logo('la40_logo_3x', $projectRoot);

// Construir el atributo srcset dinámicamente con los archivos .png encontrados
$srcsetParts = [];
if ($logo1x !== null) {
    $srcsetParts[] = $logo1x . ' 1x';
}
if ($logo2x !== null) {
    $srcsetParts[] = $logo2x . ' 2x';
}
if ($logo3x !== null) {
    $srcsetParts[] = $logo3x . ' 3x';
}
$logoSrcset = implode(', ', $srcsetParts);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= function_exists('e') ? e($pageTitle ?? 'Restaurante La 40 - Sabor Urbano y Tradición') : 'Restaurante La 40' ?></title>

    <!-- Condicional para favicon multi-resolución si se encuentra el logo 1x -->
    <?php if ($logo1x !== null): ?>
        <link rel="icon" type="image/png" href="<?= htmlspecialchars($logo1x, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <!-- Tipografías del Sistema de Diseño: Plus Jakarta Sans & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Bootstrap 5.3.3 y Hoja de Estilos Principal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_PATH ?>assets/css/styles.css">
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <!-- 1. Marca y Logo PNG Transparente Multi-Resolución -->
            <a href="<?= BASE_PATH ?>index.php" class="brand-group" aria-label="Ir al inicio de Restaurante La 40">
                <?php if ($logo1x !== null): ?>
                    <img alt="La 40"
                        class="brand-logo"
                        src="<?= htmlspecialchars($logo1x, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $logoSrcset !== '' ? 'srcset="' . htmlspecialchars($logoSrcset, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                        width="42"
                        height="42"
                        decoding="async"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <span class="brand-logo-fallback" style="display: none;" aria-hidden="true">
                        <span class="material-symbols-outlined">local_fire_department</span>
                    </span>
                <?php else: ?>
                    <span class="brand-logo-fallback" aria-hidden="true">
                        <span class="material-symbols-outlined">local_fire_department</span>
                    </span>
                <?php endif; ?>

                <div class="brand-copy">
                    <span class="brand-title">La 40</span>
                    <span class="brand-subtitle">Sabor colombiano al mejor precio</span>
                </div>
            </a>

            <!-- 2. Navegación Central -->
            <nav class="main-nav" aria-label="Navegación principal">
                <a href="<?= BASE_PATH ?>index.php"
                    class="nav-pill <?= $isHome ? 'is-active' : '' ?>"
                    <?= $isHome ? 'aria-current="page"' : '' ?>
                    data-path="inicio">Inicio</a>
                <a href="<?= BASE_PATH ?>index.php#menu" class="nav-pill" data-path="menu">Menú</a>
                <a href="<?= BASE_PATH ?>index.php#menu" class="nav-pill" data-path="promociones">Promociones</a>
                <a href="<?= BASE_PATH ?>index.php#nosotros" class="nav-pill" data-path="nosotros">Nosotros</a>
                <a href="#contacto" class="nav-pill" data-path="contacto">Contacto</a>
            </nav>

            <!-- 3. Acciones de Usuario, Reserva y Carrito -->
            <div class="header-actions">
                <?php if ($u): ?>
                    <?php if (function_exists('can') && can('usuarios.gestionar')): ?>
                        <a class="action-link hide-mobile" href="<?= BASE_PATH ?>pages/admin/usuarios.php">
                            Admin
                        </a>
                    <?php endif; ?>

                    <form method="post" action="<?= BASE_PATH ?>pages/logout.php" class="d-inline m-0">
                        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                        <button type="submit" class="action-link btn-reset hide-mobile">Cerrar Sesión</button>
                    </form>
                <?php else: ?>
                    <a class="action-link hide-mobile"
                        data-path="iniciar-sesion"
                        href="<?= BASE_PATH ?>pages/login.php">Iniciar Sesión</a>

                    <?php if (!function_exists('registration_open') || registration_open()): ?>
                        <a class="action-reserve"
                            data-path="reservas-y-registro"
                            href="<?= BASE_PATH ?>pages/registro.php">Registro</a>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Botón Píldora del Carrito de Compras -->
                <a class="action-cart-pill"
                    data-path="carrito-de-compras"
                    href="<?= BASE_PATH ?>pages/pedidoCarrito.php"
                    title="Ver mi pedido">
                    <span class="material-symbols-outlined cart-icon">shopping_bag</span>
                    <span class="cart-text-desktop">
                        Mi Pedido (<?= $cantidad_total ?>) - $<?= number_format($monto_total, 2) ?>
                    </span>
                    <span class="cart-text-mobile"><?= $cantidad_total ?></span>
                </a>

                <!-- Avatar Circular de Usuario / Perfil -->
                <a class="user-avatar-btn"
                    href="<?= $u ? BASE_PATH . 'pages/perfil.php' : BASE_PATH . 'pages/login.php' ?>"
                    title="<?= $u ? 'Perfil de ' . e($u['nombre']) : 'Iniciar sesión' ?>">
                    <span class="material-symbols-outlined avatar-icon">person</span>
                </a>
            </div>
        </div>
    </header>

    <?php if ($notice): ?>
        <div class="container flash-wrapper">
            <div class="alert alert-<?= e($notice['type'] ?? 'info') ?> shadow-sm mb-0">
                <?= e($notice['message'] ?? '') ?>
            </div>
        </div>
    <?php endif; ?>