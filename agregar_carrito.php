<?php
// Incluir la conexión a la base de datos
include 'conexion.php';
session_start();

// Verificar si los datos necesarios han sido enviados por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['cantidad'])) {
    // Recibir los datos del producto
    $producto_id = $_POST['id'];
    $cantidad = $_POST['cantidad'];
    $nombre = $_SESSION["Nombre"];

    // Verificar si el producto existe en el catálogo
    $sql = "SELECT * FROM catalogo WHERE ID = '$producto_id'"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();

        // Verificar si la cantidad solicitada no supera la cantidad disponible en el catálogo
        $nueva_cantidad = $producto['Cantidad'] - $cantidad;

        if ($nueva_cantidad < 0) {
            exit("La cantidad solicitada supera la cantidad disponible.");
        }


        // Verificar si el producto ya está en el carrito del usuario
        $carrito_sql = "SELECT * FROM carrito WHERE IdProducto = '$producto_id' AND Nombre = '$nombre'";
        $carrito_result = $conn->query($carrito_sql);

        if ($carrito_result->num_rows > 0) {
            // Si el producto ya está en el carrito, actualizar la cantidad
            $carrito = $carrito_result->fetch_assoc();
            $nueva_cantidad_carrito = $carrito['Cantidad'] + $cantidad;

            $update_carrito_sql = "UPDATE carrito SET Cantidad = '$nueva_cantidad_carrito' WHERE IdProducto = '$producto_id' AND Nombre = '$nombre'";

            if ($conn->query($update_carrito_sql) === TRUE) {
                echo "Cantidad del producto en el carrito actualizada.";
            } else {
                echo "Error al actualizar el producto en el carrito: " . $conn->error;
            }
        } else {
            // Si el producto no está en el carrito, insertarlo
            $insert_sql = "INSERT INTO carrito (IdProducto, Nombre, Cantidad) VALUES ('$producto_id', '$nombre', '$cantidad')";
            if ($conn->query($insert_sql) === TRUE) {
                echo "Producto agregado al carrito.";
            } else {
                echo "Error al agregar el producto al carrito: " . $conn->error;
            }
        }

    } else {
        echo "Producto no encontrado en el catálogo.";
    }
} else {
    echo "Faltan datos del producto.";
}

// Redirigir a la página del catálogo después de la operación
header('Location: catalogo.php');
exit();

$conn->close();
?>