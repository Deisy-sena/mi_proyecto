<?php
 
 include 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    if (empty($email) || empty($contrasena)) {
        echo "Por favor, ingrese su correo electrónico y contraseña.";
        exit;
    }

    $sql = $conn->prepare("SELECT * FROM usuarios WHERE Email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $resultado = $sql->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        if ($contrasena == $usuario['Contrasena']) { 
            session_start();
            $_SESSION['IdUsuario'] = $usuario['IdUsuario'];
            $_SESSION['Nombre'] = $usuario['Nombre'];
            $_SESSION['Email'] = $usuario['Email'];

            if ($usuario["Nombre"] == 'admin'){
                header("Location: admin.php");
                exit; 
            } else {

            echo "Inicio de sesión exitoso. Redirigiendo a la página principal...";
            
            
            header("Location: catalogo.php"); 
            exit;
            }
        } else {
            echo "<p>Contraseña incorrecta.</p>"; 
            echo "<p><a href='login.html' class='btn' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al inicio de sesión</a></p>";
            exit;
        }
    } else {
        echo "El correo electrónico no está registrado.";
        echo "<p><a href='login.html' class='btn' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al inicio</a></p>";
        exit;
    }

    $conn->close();
}
?>
