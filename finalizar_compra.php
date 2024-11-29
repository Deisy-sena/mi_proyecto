<?php
include 'conexion.php';
session_start();

if (!isset($_SESSION['Nombre'])) {
    header('Location: login.php');
    exit();
}

$nombre = $_SESSION['Nombre'];

// Verificar si hay productos en el carrito
$sql = "SELECT carrito.IdProducto, carrito.Cantidad, catalogo.Precio, catalogo.Nombre 
        FROM carrito 
        JOIN catalogo ON carrito.IdProducto = catalogo.ID
        WHERE carrito.Nombre = '$nombre'";

$result = $conn->query($sql);

// Si el carrito está vacío
if ($result->num_rows == 0) {
    echo "<div class='mensaje'>Tu carrito está vacío. No puedes realizar la compra.</div>";
    exit();
}

$total = 0;
$productos_comprados = [];

// Calcular el total de la compra y preparar los productos
while ($row = $result->fetch_assoc()) {
    $producto_id = $row['IdProducto'];
    $cantidad = $row['Cantidad'];
    $precio = $row['Precio'];
    $total_producto = $cantidad * $precio;
    $total += $total_producto;

    // Guardar los productos comprados para luego actualizar el inventario
    $productos_comprados[] = [
        'producto_id' => $producto_id,
        'cantidad' => $cantidad,
        'precio' => $precio
    ];
}

// Actualizar el inventario (restar las cantidades de productos comprados)
foreach ($productos_comprados as $producto) {
    $producto_id = $producto['producto_id'];
    $cantidad = $producto['cantidad'];

    // Actualizar la cantidad del producto en el catálogo
    $update_producto_sql = "UPDATE catalogo SET Cantidad = Cantidad - $cantidad WHERE ID = '$producto_id'";
    $conn->query($update_producto_sql);
}

// Eliminar los productos del carrito
$delete_carrito_sql = "DELETE FROM carrito WHERE Nombre = '$nombre'";
if ($conn->query($delete_carrito_sql) === TRUE) {
    echo "<div class='mensaje'>
            <h2>¡Gracias por tu compra, $nombre!</h2>
            <p>El total de tu compra es: <strong>$" . number_format($total, 2) . "</strong></p>
            <p><a href='catalogo.php' class='btn'>Volver al Catálogo</a></p>
          </div>";
} else {
    echo "<div class='mensaje'>Hubo un problema al eliminar los productos del carrito. Intenta nuevamente más tarde.</div>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Finalizada</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('imagenes/fondo.avif');
            background-size: cover;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .mensaje {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        .mensaje h2 {
            color: #4CAF50;
            font-size: 24px;
        }
        .mensaje p {
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        strong {
            color: #d32f2f;
        }
    </style>
</head>
<body>

</body>
</html>
