<?php
// 1. Configuración inicial y conexión
define('BASE_PATH', '');
require_once __DIR__ . '/config/bootstrap.php';
// Importante: se requiere la conexión a la BD para extraer los productos
require_once __DIR__ . '/config/config.php';

// 2. Consulta de productos ordenados por categoría lógica
$sql = "SELECT * FROM productos ORDER BY FIELD(categoria, 'Hamburguesas', 'Perros Calientes', 'Pizzas', 'Lasañas', 'Alitas y Acompañamientos', 'Bebidas'), id ASC";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll();

// 3. Agrupación de los productos por categoría
$menuGrouped = [];
foreach ($productos as $prod) {
    $menuGrouped[$prod['categoria']][] = $prod;
}

// Diccionario visual de iconos para la interfaz
$iconos = [
    'Hamburguesas' => '🍔',
    'Perros Calientes' => '🌭',
    'Pizzas' => '🍕',
    'Lasañas' => '🍝',
    'Alitas y Acompañamientos' => '🍗',
    'Bebidas' => '🍹'
];

require_once 'includes/header.php';
?>

<main>
    <!-- Banner Principal -->
    <section class="hero-banner">
        <div class="banner-content">
            <h2>Sabor y tradición en cada bocado</h2>
            <p>Descubre nuestros platillos elaborados con ingredientes frescos y de la más alta calidad.</p>
            <a href="#menu" class="btn-primary">Ver Menú</a>
        </div>
    </section>

    <!-- Sección del Menú Dinámico -->
    <section id="menu" class="container">
        <h2>Nuestro Menú</h2>

        <?php foreach ($menuGrouped as $categoria => $items):
            $icono = isset($iconos[$categoria]) ? $iconos[$categoria] : '🍽️';
        ?>
            <div class="menu-category">
                <h3 class="category-title"><?= $icono . ' ' . htmlspecialchars($categoria) ?></h3>

                <div class="menu-grid">
                    <?php foreach ($items as $item):
                        // Construir y verificar la ruta física de la imagen
                        $nombreImagen = trim($item['imagen']);
                        $rutaImagen = 'assets/img/productos/' . $nombreImagen;
                    ?>
                        <!-- Validación de la existencia de la imagen antes de mostrarla -->
                        <article class="menu-item">
                            <div class="menu-item-top">
                                <?php if (!empty($nombreImagen) && file_exists($rutaImagen)): ?>
                                    <img src="<?= htmlspecialchars($rutaImagen) ?>"
                                        alt="<?= htmlspecialchars($item['nombre']) ?>"
                                        class="producto-img"
                                        loading="lazy">
                                    <!-- Manejo de caso donde la imagen no existe o no se proporciona -->
                                <?php else: ?>
                                    <div class="producto-img-placeholder">
                                        <span>🍽️ <?= htmlspecialchars($item['nombre']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Información del producto -->
                                <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                                <p><?= htmlspecialchars($item['descripcion']) ?></p>
                            </div>

                            <!-- Sección inferior del producto con precio y botón de añadir al carrito -->
                            <div class="menu-item-bottom">
                                <div class="precio">$<?= number_format($item['precio'], 2) ?></div>

                                <!-- Formulario para añadir al carrito -->
                                <form action="pages/pedidoCarrito.php" method="POST">
                                    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="accion" value="agregar">
                                    <button type="submit" class="btn-agregar">Añadir al carrito</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    </section>
</main>

<?php
require_once 'includes/footer.php';
?>