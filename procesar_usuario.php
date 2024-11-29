<?php
include 'conexion.php'; 


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $accion = $_POST['accion'];
    $id = $_POST['IdUsuario'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Verificación si el correo ya existe
    if ($accion == 'Agregar' || $accion == 'Modificar') {
        // Verificar si el correo electrónico ya está registrado
        $sql_email_check = $conn->prepare("SELECT * FROM usuarios WHERE Email = ?");
        $sql_email_check->bind_param("s", $email);
        $sql_email_check->execute();
        $result_email_check = $sql_email_check->get_result();

        if ($result_email_check->num_rows > 0) {
            echo "<p>Ya existe un usuario con el correo electrónico: $email</p>";
            echo "<p><a href='index.html' class='btn' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al inicio</a></p>";
            exit;
        }

        // Verificar si el ID de usuario ya está registrado
        $sql_id_check = $conn->prepare("SELECT * FROM usuarios WHERE IdUsuario = ?");
        $sql_id_check->bind_param("s", $id);
        $sql_id_check->execute();
        $result_id_check = $sql_id_check->get_result();

        if ($result_id_check->num_rows > 0 && $accion == 'Agregar') {
            echo "<p>Ya existe un usuario con el ID: $id</p>";
            echo "<p><a href='index.html' class='btn' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al inicio</a></p>";
            exit;
        }
    }

    if ($accion && $id) {
        if (($accion == 'Agregar' || $accion == 'Modificar') && (!$nombre || !$email || !$contrasena)) {
            echo "Todos los campos son obligatorios.";
            exit;
        } elseif ($accion == 'Eliminar' && !$id) {
            echo "El ID del usuario es obligatorio para eliminar.";
            exit;
        }

        if ($accion == 'Agregar') {
            $sql = $conn->prepare("INSERT INTO usuarios (IdUsuario, Nombre, Email, Contrasena) VALUES (?, ?, ?, ?)");
            $sql->bind_param("ssss", $id, $nombre, $email, $contrasena); 

            if ($sql->execute()) {
                echo "Usuario agregado con éxito.";
                header("Location:login.html");
            } else {
                echo "Error al agregar el usuario: " . $sql->error;
            }
        } elseif ($accion == 'modificar') {
            $sql = $conn->prepare("UPDATE usuarios SET Nombre=?, Correo=?, Contrasena=? WHERE IdUsuario=?");
            $sql->bind_param("ssss", $nombre, $email, $contrasena, $id); 

            echo "Consulta SQL para modificar: $sql<br>";

            if ($sql->execute()) {
                echo "Usuario modificado con éxito.";
            } else {
                echo "Error al modificar el usuario: " . $sql->error;
            }
        } elseif ($accion == 'eliminar') {
            $sql = $conn->prepare("DELETE FROM usuarios WHERE IdUsuario=?");
            $sql->bind_param("s", $id); 

            echo "Consulta SQL para eliminar: $sql<br>";

            if ($sql->execute()) {
                echo "Usuario eliminado con éxito.";
            } else {
                echo "Error al eliminar el usuario: " . $sql->error;
            }
        }
    } else {
        echo "Acción o ID no válido.";
    }
}

$conn->close();
?>
