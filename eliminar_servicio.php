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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['id_eliminar'])) {
        $_SESSION['id_eliminar'] = $_POST['id_eliminar'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['confirmar_eliminacion'])) {
        $tabla = $_SESSION['tabla_seleccionada'];
        $id = $_SESSION['id_eliminar'];

        // Detectar el campo ID basado en la tabla seleccionada
        if ($tabla === 'Servicios') {
            $campo_id = 'id_servicio';
        } elseif ($tabla === 'reservas') {
            $campo_id = 'Id_reserva';
        } elseif ($tabla === 'barberos') {
            $campo_id = 'id_barbero';
        } else {
            echo "Tabla no reconocida.";
            exit();
        }

        $query = "DELETE FROM $tabla WHERE $campo_id = ?";
        $stmt = $conexion->prepare($query);

        if ($stmt === false) {
            echo "Error en la preparación de la consulta.";
            exit();
        }

        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            unset($_SESSION['tabla_seleccionada']);
            unset($_SESSION['id_eliminar']);
            header('Location: admin_panel.php');
            exit();
        } else {
            echo "Error al eliminar el registro: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Registro</title>
    <link rel="stylesheet" href="sesion.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h2, h3 {
            color: #333;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        input[type="password"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #202426;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
            text-align: center;
            display: block;
            margin-top: 15px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Eliminar Registro</h2>
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
        <?php elseif (!isset($_SESSION['id_eliminar'])): ?>
            <form method="post" action="">
                <label for="id_eliminar">Ingresa el ID del registro a eliminar:</label>
                <input type="number" name="id_eliminar" required>
                <button type="submit">Seleccionar</button>
            </form>
        <?php else: ?>
            <p>¿Estás seguro de que quieres eliminar este registro de la tabla <?php echo htmlspecialchars($_SESSION['tabla_seleccionada']); ?> con ID <?php echo htmlspecialchars($_SESSION['id_eliminar']); ?>?</p>
            <form method="post" action="">
                <button type="submit" name="confirmar_eliminacion">Eliminar</button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
