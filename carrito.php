<?php
// Incluir la conexión a la base de datos
include 'conexion.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre'])) {
    header('Location: login.php');
    exit();
}

// Obtener el nombre del usuario desde la sesión
$nombre = $_SESSION['Nombre'];

// Obtener los productos en el carrito del usuario
$sql = "SELECT carrito.IdProducto, catalogo.Nombre AS Producto, carrito.Cantidad, catalogo.Precio 
        FROM carrito 
        JOIN catalogo ON carrito.IdProducto = catalogo.ID
        WHERE carrito.Nombre = '$nombre'";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('imagenes/fondo.avif');
            background-size: cover;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #fff;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f1f1f1;
        }
        td input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
        }
        .actions form {
            display: inline;
        }
        .total {
            text-align: right;
            font-size: 1.5em;
            margin-top: 20px;
            color: #fff;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }

        .empty-message {
            text-align: center;
            font-size: 1.2em;
            color: #333;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <?php if ($result->num_rows > 0): ?>
        <h1>Carrito de Compras</h1>
        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>

            <?php 
            $total = 0;
            while ($row = $result->fetch_assoc()) {
                $producto_id = $row['IdProducto'];
                $producto_nombre = $row['Producto'];
                $cantidad = $row['Cantidad'];
                $precio = $row['Precio'];
                $total_producto = $cantidad * $precio;
                $total += $total_producto;
            ?>
            <tr>
                <td><?php echo $producto_nombre; ?></td>
                <td>
                    <form method="POST">
                    <span><?php echo $cantidad; ?></span>
                        <input type="hidden" name="id_producto" value="<?php echo $producto_id; ?>">
                    </form>
                </td>
                <td>$<?php echo number_format($precio, 2); ?></td>
                <td>$<?php echo number_format($total_producto, 2); ?></td>
                <td class="actions">
                    <form method="POST" action="eliminar_carrito.php">
                        <input type="hidden" name="id_producto" value="<?php echo $producto_id; ?>">
                        <input type="submit" value="Eliminar" class="btn" style="background-color: #f44336;">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <div class="total">
            <strong>Total a Pagar: $<?php echo number_format($total, 2); ?></strong>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="catalogo.php" class="btn">Volver al Catálogo</a>
            <a href="finalizar_compra.php" class="btn" style="background-color: #000;">Finalizar Compra</a>
        </div>

    <?php else: ?>
        <div class="empty-message">
            <h2>Tu carrito está vacío.</h2>
            <a href="catalogo.php" class="btn">Volver al Catálogo</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
