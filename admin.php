<?php
// Incluir la conexión a la base de datos
include 'conexion.php';
session_start();


// Buscar por ID si se ha enviado un formulario
$search_id = isset($_POST['search_id']) ? $_POST['search_id'] : '';

// Consulta para obtener los usuarios
$sql = "SELECT * FROM usuarios WHERE IdUsuario LIKE '%$search_id%'";
$result = $conn->query($sql);

// Verificar si se ha eliminado un usuario
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $conn->query("DELETE FROM usuarios WHERE IdUsuario = $delete_id");
    header('Location: admin.php'); // Recargar la página para mostrar los cambios
    exit();
}

// Actualizar los datos de un usuario
if (isset($_POST['update_user'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Actualizamos los datos del usuario
    $conn->query("UPDATE usuarios SET Nombre = '$nombre', Email = '$email', Contrasena = '$contrasena' WHERE IdUsuario = $id_usuario");
    header('Location: admin.php'); // Recargar para mostrar cambios
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestión de Usuarios</title>
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
        td a {
            text-decoration: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #4CAF50;
        }
        td a:hover {
            background-color: #45a049;
        }
        .search-form {
            text-align: center;
            margin: 20px 0;
        }
        .search-form input[type="text"] {
            padding: 5px;
            width: 200px;
        }
        .search-form input[type="submit"] {
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
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

        /* Estilos para el modal */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .logout-container {
        text-align: center; /* Centra el contenido dentro del contenedor */
        margin-top: 20px; /* Margen superior para separarlo de la tabla o contenido anterior */
    }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestión de Usuarios</h1>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <form method="POST">
            <input type="text" name="search_id" placeholder="Buscar por ID" value="<?php echo $search_id; ?>">
            <input type="submit" value="Buscar">
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['IdUsuario']; ?></td>
                    <td><?php echo $row['Nombre']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td>
                        <!-- Botones para modificar y eliminar -->
                        <a href="javascript:void(0);" onclick="openModal(<?php echo $row['IdUsuario']; ?>, '<?php echo $row['Nombre']; ?>', '<?php echo $row['Email']; ?>')">Modificar</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['IdUsuario']; ?>">
                            <input type="submit" value="Eliminar" class="btn" style="background-color: #f44336;">
                        </form>
                        
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="empty-message">
            <h2>No se encontraron usuarios.</h2>
        </div>
    <?php endif; ?>
</div>

<div class="logout-container">
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit" class="btn">Cerrar Sesión</button>
    </form>
</div>

<!-- Modal para editar usuario -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Modificar Usuario</h2>
        <form method="POST">
            <input type="hidden" name="id_usuario" id="id_usuario">
            <label for="nombre">Nombre:</label><br>
            <input type="text" name="nombre" id="nombre" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>
            <label for="contrasena">Contraseña:</label><br>
            <input type="password" name="contrasena" id="contrasena" required><br><br>
            <input type="submit" name="update_user" value="Guardar Cambios" class="btn">
        </form>
    </div>
</div>

<script>
    // Función para abrir el modal
    function openModal(id, nombre, email) {
        document.getElementById("editModal").style.display = "block";
        document.getElementById("id_usuario").value = id;
        document.getElementById("nombre").value = nombre;
        document.getElementById("email").value = email;
    }

    // Función para cerrar el modal
    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }

    // Cerrar el modal si se hace clic fuera de la ventana modal
    window.onclick = function(event) {
        if (event.target == document.getElementById("editModal")) {
            closeModal();
        }
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
