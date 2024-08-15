<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_barbero'])) {
    header('Location: login.php');
    exit();
}

$tablas = ['Servicios', 'reservas', 'barberos']; // Añade aquí todas las tablas que quieras incluir

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tabla_seleccionada'])) {
        $_SESSION['tabla_seleccionada'] = $_POST['tabla_seleccionada'];
        unset($_SESSION['id_modificar']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['id_modificar'])) {
        $_SESSION['id_modificar'] = $_POST['id_modificar'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['guardar_cambios'])) {
        $tabla = $_SESSION['tabla_seleccionada'];
        $id = $_SESSION['id_modificar'];
        $campo_id = 'id_' . strtolower(substr($tabla, 0, -1)); // Suponiendo que los campos ID siguen este patrón

        $set_clause = [];
        $types = '';
        $params = [];
        foreach ($_POST as $key => $value) {
            if ($key != 'guardar_cambios') {
                $set_clause[] = "$key = ?";
                $types .= 's'; // Asumimos que todos los campos son strings por simplicidad
                $params[] = $value;
            }
        }
        $types .= 'i'; // Para el ID
        $params[] = $id;

        $query = "UPDATE $tabla SET " . implode(', ', $set_clause) . " WHERE $campo_id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        unset($_SESSION['tabla_seleccionada']);
        unset($_SESSION['id_modificar']);
        header('Location: admin_panel.php');
        exit();
    }
}

$fila = null;
if (isset($_SESSION['tabla_seleccionada']) && isset($_SESSION['id_modificar'])) {
    $tabla = $_SESSION['tabla_seleccionada'];
    $id = $_SESSION['id_modificar'];
    $campo_id = 'id_' . strtolower(substr($tabla, 0, -1));

    $query = "SELECT * FROM $tabla WHERE $campo_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fila = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar</title>
    <link rel="stylesheet" href="sesion.css">
</head>
<body>
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