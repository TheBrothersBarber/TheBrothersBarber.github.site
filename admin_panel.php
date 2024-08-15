<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

//  incluir el contenido del panel de administración
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="sesion.css"> <!-- Asegúrate de que la ruta sea correcta -->
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_barbero']); ?>!</h1>
    </header>
    <div class="container">
        <header class="header">
            <h1>Panel de Administración</h1>
            <!--  elementos del encabezado  -->
        </header>
        <div class="buttons-container">
            <a href="eliminar_servicio.php">Eliminar</a>
            <a href="modificar_servicio.php">modificar</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
    <!--  contenido y funcionalidades para el administrador -->
</body>
</html>
