<?php
// Archivo: index.php
declare(strict_types=1);

// 1. Configuración de ruta base y arranque del sistema (incluye config.php, sesión, $pdo y functions.php)
define('BASE_PATH', '');
require_once __DIR__ . '/config/bootstrap.php';

// 2. Consulta de productos ordenados por el flujo lógico del menú
$menuGrouped = [];

try {
    $sql = "SELECT id, categoria, nombre, descripcion, precio, imagen 
            FROM productos 
            ORDER BY FIELD(categoria, 'Hamburguesas', 'Perros Calientes', 'Pizzas', 'Lasañas', 'Alitas y Acompañamientos', 'Bebidas'), id ASC";
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll();

    // 3. Agrupación de los productos por categoría
    foreach ($productos as $prod) {
        $categoriaKey = (string) ($prod['categoria'] ?? 'Otros');
        $menuGrouped[$categoriaKey][] = $prod;
    }
} catch (PDOException $e) {
    error_log('Error al consultar el catálogo de productos: ' . $e->getMessage());
}

// Diccionario visual de iconos para la interfaz
$iconos = [
    'Hamburguesas'             => '🍔',
    'Perros Calientes'         => '🌭',
    'Pizzas'                   => '🍕',
    'Lasañas'                  => '🍝',
    'Alitas y Acompañamientos' => '🍗',
    'Bebidas'                  => '🍹'
];

$pageTitle = 'Restaurante La 40 - Sabor Urbano y Tradición';
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- 1. Banner Principal (Hero Banner) -->
    <section class="hero-banner">
        <div class="banner-content">
            <h2>Sabor y tradición en cada bocado</h2>
            <p>Descubre nuestros platillos elaborados con ingredientes frescos y de la más alta calidad.</p>
            <a href="#menu" class="btn btn-primary">
                <span class="material-symbols-outlined">restaurant_menu</span>
                Ver Menú
            </a>
        </div>
    </section>

    <!-- 2. Sección del Menú Dinámico -->
    <section id="menu" class="container">
        <h2>Nuestro Menú</h2>

        <?php if (empty($menuGrouped)): ?>
            <div class="cart-empty">
                <p>En este momento estamos actualizando nuestros platillos. ¡Vuelve pronto!</p>
            </div>
        <?php else: ?>
            <!-- Barra de navegación rápida por categorías (Category Chips de DESIGN.md) -->
            <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Categorías del menú">
                <?php foreach (array_keys($menuGrouped) as $catNombre): 
                    $catIcono = $iconos[$catNombre] ?? '🍽️';
                    $catSlug  = 'cat-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($catNombre));
                ?>
                    <a href="#<?= e($catSlug) ?>" class="btn btn-secondary btn-sm rounded-pill px-3">
                        <?= $catIcono . ' ' . e($catNombre) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Renderizado de Categorías y Tarjetas de Producto -->
            <?php foreach ($menuGrouped as $categoria => $items):
                $icono = $iconos[$categoria] ?? '🍽️';
                $catSlug = 'cat-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($categoria));
            ?>
                <div id="<?= e($catSlug) ?>" class="menu-category">
                    <h3 class="category-title"><?= $icono . ' ' . e($categoria) ?></h3>

                    <div class="menu-grid">
                        <?php foreach ($items as $item):
                            // Construir y verificar la ruta física de la imagen en el disco
                            $nombreImagen = trim((string) ($item['imagen'] ?? ''));
                            $rutaImagenRelativa = 'assets/img/productos/' . $nombreImagen;
                            $rutaImagenDisco = __DIR__ . '/assets/img/productos/' . $nombreImagen;
                            $tieneImagen = ($nombreImagen !== '' && is_file($rutaImagenDisco));
                        ?>
                            <article class="menu-item">
                                <!-- Nivel Superior: Imagen y Narrativa del Producto -->
                                <div class="menu-item-top">
                                    <?php if ($tieneImagen): ?>
                                        <img src="<?= e($rutaImagenRelativa) ?>"
                                             alt="<?= e((string) $item['nombre']) ?>"
                                             class="producto-img"
                                             loading="lazy"
                                             decoding="async">
                                    <?php else: ?>
                                        <div class="producto-img-placeholder">
                                            <span><?= $icono . ' ' . e((string) $item['nombre']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <h3><?= e((string) $item['nombre']) ?></h3>
                                    <p><?= e((string) $item['descripcion']) ?></p>
                                </div>

                                <!-- Nivel Inferior: Precio y Acción de Compra Protegida con CSRF -->
                                <div class="menu-item-bottom">
                                    <div class="precio">$<?= number_format((float) $item['precio'], 2) ?></div>

                                    <form action="pages/pedidoCarrito.php" method="POST">
                                        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <input type="hidden" name="accion" value="agregar">
                                        <button type="submit" class="btn-agregar">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">add_shopping_cart</span>
                                            Añadir al carrito
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- 3. Sección Nosotros (Vinculada al enlace #nosotros del Navbar) -->
    <section id="nosotros" class="container">
        <h2>Sobre Nosotros</h2>
        <p class="mb-0">
            En <strong>Restaurante La 40</strong> combinamos la calidez de la tradición culinaria colombiana con ingredientes frescos seleccionados a diario. Cada hamburguesa artesanal, pizza de masa madre y platillo de nuestra cocina se prepara al instante para brindarte hospitalidad, sabor auténtico y el mejor precio alrededor de la mesa.
        </p>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>