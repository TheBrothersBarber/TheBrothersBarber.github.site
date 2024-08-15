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
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_barbero']); ?>!</h1>
    <p>Este es tu panel de administración.</p>
    <a href="logout.php">Cerrar sesión</a>

    
    <h2>Consultar</h2>
    <a href="admin_panel.php">Regresar al panel de administración</a>
    <?php if (!isset($_SESSION['tabla_seleccionada'])): ?>
        <form method="post" action="">
            <label for="tabla_seleccionada">Selecciona la tabla:</label>
            <select name="tabla_seleccionada" required>
                <?php foreach ($tablas as $tabla): ?>
                    <option value="<?php echo htmlspecialchars($tabla); ?>"><?php echo htmlspecialchars($tabla); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Seleccionar</button>
        </form>
    <?php elseif (!isset($_SESSION['id_modificar'])): ?>
        <form method="post" action="">
            <label for="id_modificar">Ingresa el ID del registro a modificar:</label>
            <input type="number" name="id_modificar" required>
            <button type="submit">Seleccionar</button>
        </form>
    <?php elseif ($fila): ?>
        <form method="post" action="">
            <?php foreach ($fila as $campo => $valor): ?>
                <?php if ($campo !== 'id_' . strtolower(substr($_SESSION['tabla_seleccionada'], 0, -1))): ?>
                    <label for="<?php echo htmlspecialchars($campo); ?>"><?php echo htmlspecialchars($campo); ?>:</label>
                    <input type="text" name="<?php echo htmlspecialchars($campo); ?>" value="<?php echo htmlspecialchars($valor); ?>" required>
                    <br>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" name="guardar_cambios">Guardar Cambios</button>
        </form>
    <?php else: ?>
        <p>No se encontró el registro.</p>
    <?php endif; ?>
</body>
</html>