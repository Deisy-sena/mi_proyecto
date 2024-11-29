<?php
include 'conexion.php';
session_start();


if (!isset($_SESSION['Nombre'])) {
    header('Location: login.php');
    exit();
}

$nombre = $_SESSION['Nombre'];

// Verificar si se ha enviado el id del producto a eliminar
if (isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];

    // Eliminar el producto del carrito
    $sql = "DELETE FROM carrito WHERE IdProducto = '$id_producto' AND Nombre = '$nombre'";

    if ($conn->query($sql) === TRUE) {
        // Si la eliminación fue exitosa, redirigir al carrito
        header('Location: carrito.php');
        exit();
    } else {
        // En caso de error, mostrar el mensaje
        echo "Error al eliminar el producto del carrito: " . $conn->error;
    }
} else {
    // Si no se recibe un id_producto, redirigir al carrito
    header('Location: carrito.php');
    exit();
}

$conn->close();
?>
