<?php
// Archivo: includes/header.php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '');
}

$u = function_exists('me') ? me() : null;
$notice = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Cálculo dinámico de unidades y monto total del carrito en sesión
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

// Identificar página actual para marcar el estado activo en el navbar
$currentScript = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$isHome = ($currentScript === 'index.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= function_exists('e') ? e($pageTitle ?? 'Restaurante La 40 - Sabor Urbano y Tradición') : 'Restaurante La 40' ?></title>

    <!-- Favicon del sitio usando el logo local -->
    <link rel="icon" type="image/jpeg" href="<?= BASE_PATH ?>assets/img/la40_logo_1x.jpg">

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
            <!-- 1. Marca y Logo Responsivo Multi-Resolución (1x, 2x, 3x) -->
            <a href="<?= BASE_PATH ?>index.php" class="brand-group" aria-label="Ir al inicio de Restaurante La 40">
                <img alt="Logo Restaurante La 40"
                    class="brand-logo"
                    src="<?= BASE_PATH ?>assets/img/la40_logo_1x.jpg"
                    srcset="<?= BASE_PATH ?>assets/img/la40_logo_1x.jpg 1x,
                             <?= BASE_PATH ?>assets/img/la40_logo_2x.jpg 2x,
                             <?= BASE_PATH ?>assets/img/la40_logo_3x.jpg 3x"
                    width="48"
                    height="48"
                    decoding="async">
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