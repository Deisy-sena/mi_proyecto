<?php
$servidor = "localhost";
$usuario = "root"; 
$contrasena = "Mipecosita/2"; 
$base_datos = "mi_proyecto";

// Crear una conexión a la base de datos
$conn = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>
