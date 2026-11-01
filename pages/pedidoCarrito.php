<?php
declare(strict_types=1);

// 1. Definir ruta base para que header.php y footer.php resuelvan enlaces y CSS
define('BASE_PATH', '../');

// 2. Cargar bootstrap (inicia sesión segura, conecta a BD con $pdo y carga functions.php)
require_once __DIR__ . '/../config/bootstrap.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = []; 
}

// 3. PROCESAMIENTO DE ACCIONES DEL CARRITO (Protegido con CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    check_csrf();

    $accion = (string) ($_POST['accion'] ?? '');
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

    // A. AGREGAR PRODUCTO (Consultando la BD para evitar manipulación de precios)
    if ($accion === 'agregar' && $id) {
        $stmt = $pdo->prepare('SELECT id, nombre, precio FROM productos WHERE id = ?');
        $stmt->execute([$id]);
        $producto = $stmt->fetch(); 

        if ($producto) { 
            $cantidadActual = (int) ($_SESSION['carrito'][$id]['cantidad'] ?? 0);
            $_SESSION['carrito'][$id] = [ 
                'nombre'   => (string) $producto['nombre'], 
                'precio'   => (float) $producto['precio'], 
                'cantidad' => min(99, $cantidadActual + 1)
            ];
            flash('Producto añadido al pedido.');
        } else {
            flash('El producto seleccionado no existe.', 'danger');
        }
        go('pedidoCarrito.php');
    }

    // B. ACTUALIZAR CANTIDAD INDIVIDUAL
    if ($accion === 'actualizar' && $id) { 
        $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);

        if ($cantidad !== false && $cantidad > 0) { 
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] = min(99, $cantidad);
                flash('Cantidad actualizada.');
            }
        } else {
            unset($_SESSION['carrito'][$id]);
            flash('Producto eliminado del pedido.', 'warning');
        }
        go('pedidoCarrito.php');
    }

    // C. ELIMINAR UN PRODUCTO
    if ($accion === 'eliminar' && $id) { 
        unset($_SESSION['carrito'][$id]); 
        flash('Producto eliminado del carrito.', 'warning');
        go('pedidoCarrito.php');
    }

    // D. VACIAR TODO EL CARRITO
    if ($accion === 'vaciar') {
        $_SESSION['carrito'] = [];
        flash('El carrito ha sido vaciado.', 'info');
        go('pedidoCarrito.php');
    }

    // E. CONFIRMAR PEDIDO
    if ($accion === 'confirmar' && !empty($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
        flash('¡Pedido realizado con éxito! En 45 minutos llegará a su domicilio.', 'success');
        go('pedidoCarrito.php');
    }
}

// 4. INICIO DE LA VISTA
$pageTitle = 'Tu Pedido - Restaurante La 40';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="container">
    <h2>🛒 Tu Pedido</h2>
    
    <?php if (empty($_SESSION['carrito'])): ?>
        <div class="cart-empty">
            <p>El carrito está vacío. <a href="<?= BASE_PATH ?>index.php#menu">Ver menú</a></p>
        </div>
    <!-- Si hay productos en el carrito, mostrar la tabla con los detalles -->    
    <?php else: ?>
        <div class="table-responsive">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Platillo</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <!-- Cuerpo de la tabla con los productos del carrito -->
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $id => $item): 
                        $subtotal = $item['precio'] * $item['cantidad']; 
                        $total += $subtotal; 
                    ?>
                        <tr>
                            <td><?= e((string) $item['nombre']) ?></td>
                            <td>$<?= number_format((float) $item['precio'], 2) ?></td>
                            <td>
                                <form action="pedidoCarrito.php" method="POST" class="form-inline">
                                    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                                    <input type="hidden" name="accion" value="actualizar">
                                    <input type="number" name="cantidad" value="<?= (int) $item['cantidad'] ?>" min="1" max="99" class="input-cantidad" required>
                                    <button type="submit" class="btn btn-secondary">OK</button>
                                </form>
                            </td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <form action="pedidoCarrito.php" method="POST" class="form-inline">
                                    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" class="total">Total a Pagar:</td>
                        <td colspan="2" class="total">$<?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Botones de acción para seguir pidiendo, vaciar el carrito o confirmar el pedido -->
        <div class="cart-actions">
            <a href="<?= BASE_PATH ?>index.php#menu" class="btn btn-secondary">⬅ Seguir pidiendo</a>

            <form action="pedidoCarrito.php" method="POST" class="form-inline">
                <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                <input type="hidden" name="accion" value="vaciar">
                <button type="submit" class="btn btn-danger">Vaciar Carrito</button>
            </form>

            <form action="pedidoCarrito.php" method="POST" class="form-inline">
                <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                <input type="hidden" name="accion" value="confirmar">
                <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>