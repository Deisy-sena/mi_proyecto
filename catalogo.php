<?php
session_start();

include 'conexion.php';

// Definir los productos iniciales con su cantidad en inventario
$productos_iniciales = [
    ["nombre" => "Aretes de Pedreria", "descripcion" => "Accesorio elaborado artesanalmente con detalles unicos.", "precio" => 10000, "cantidad" => 50],
    ["nombre" => "Collar Tejido con Pedreria", "descripcion" => "Hermoso collar elaborado a mano con delicada pedreria.", "precio" => 15000, "cantidad" => 50],
    ["nombre" => "Tobillera de Macrame", "descripcion" => "Tejido manual con diseno elegante y duradero.", "precio" => 5000, "cantidad" => 50],
    ["nombre" => "Set de Tres Piezas", "descripcion" => "Elaborado con pedreria, ideal para ocasiones especiales.", "precio" => 12000, "cantidad" => 50],
    ["nombre" => "Collar Tejido", "descripcion" => "Hermoso collar elaborado a mano con delicado detalle de pedreria.", "precio" => 22000, "cantidad" => 50],
];

// Función para verificar si el producto existe en la base de datos, si no, agregarlo
foreach ($productos_iniciales as $producto) {
    $nombre = $producto['nombre'];
    $descripcion = $producto['descripcion'];
    $precio = $producto['precio'];
    $cantidad = $producto['cantidad'];

    $sql = "SELECT * FROM catalogo WHERE Nombre = '$nombre'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $insert_sql = "INSERT INTO catalogo (Nombre, Descripcion, Precio, Cantidad) VALUES ('$nombre', '$descripcion', '$precio', '$cantidad')";
        if ($conn->query($insert_sql) === TRUE) {
        } else {
        }
    }
}

$sql = "SELECT * FROM catalogo";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Creaciones M.I.A</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    
    <header>
        <h1>Bienvenido al catálogo de Creaciones M.I.A</h1>
    </header>

    <!-- Menú de navegación -->
    <nav>
        <ul>
            <li><a href="carrito.php">Ir al Carrito</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenedor del catálogo -->
    <div class="catalogo_container">
        <div class="producto">
            <?php
            if ($result->num_rows > 0) {
                while ($producto = $result->fetch_assoc()) {
                    ?>
                    <div class="item">
                        <img src="imagenes/<?php echo strtolower(str_replace(' ', '_', $producto['Nombre'])); ?>.webp" alt="">
                        <h3><?php echo $producto['Nombre']; ?></h3>
                        <p><?php echo $producto['Descripcion']; ?></p>
                        <label for="producto-precio-<?php echo $producto['ID']; ?>">Precio:</label>
                        <input type="number" id="producto-precio-<?php echo $producto['ID']; ?>" step="0.01" value="<?php echo $producto['Precio']; ?>" readonly>
                        <label for="producto-cantidad-<?php echo $producto['ID']; ?>">Cantidad disponible:</label>
                        <input type="number" id="producto-cantidad-<?php echo $producto['ID']; ?>" value="<?php echo $producto['Cantidad']; ?>" readonly>
                        <form action="agregar_carrito.php" method="POST">
                            <label for="cantidad">Cantidad a agregar:</label>
                            <input type="number" name="cantidad" min="1" max="<?php echo $producto['Cantidad']; ?>" required>
                            <input type="hidden" name="id" value="<?php echo $producto['ID']; ?>">
                            <button type="submit" class="add-to-cart">Agregar al carrito</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "No se encontraron productos.";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php

$conn->close();
?>
