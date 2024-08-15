<?php
session_start();
require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_barbero = $_POST['nombre_barbero'];
    $password_ingresada = $_POST['password'];

    $query = "SELECT id_barbero, nombre_barbero, rol, password FROM barberos WHERE nombre_barbero = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $nombre_barbero);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $barbero = $result->fetch_assoc();
        $password = $barbero['password'];
        $rol = $barbero['rol']; // Cambiado de $rol['admin'] a $barbero['rol']

        if ($password_ingresada === $password || $rol === 'admin' || $rol === 'barbero') {
            $_SESSION['loggedin'] = true;
            $_SESSION['id_barbero'] = $barbero['id_barbero'];
            $_SESSION['nombre_barbero'] = $barbero['nombre_barbero'];
            
            header("Location: admin_panel.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="sesion.css"> <!-- Enlaza el archivo CSS para mantener el estilo consistente -->
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-image: url('IMG/10.jpg'); /* Cambia 'ruta/de/tu/imagen.jpg' por la ruta de tu imagen */
            background-size: cover;
            background-repeat: no-repeat;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #2b2b22;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    
    
    <div class="container">
    <div class="menu container">
            <img src="IMG\Logo.png" class="menu-icono" alt="Los brothers">
            <a href="index.html">Inicio</a>
        </div>
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
        <form method="post" action="">
            <label for="nombre_barbero">Nombre de Usuario:</label>
            <input type="text" id="nombre_barbero" name="nombre_barbero" required>
            <br><br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <br><br>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
