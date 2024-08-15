<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_barbero'])) {
    header('Location: login.php');
    exit();
}

$tablas = ['reservas', 'Servicios', 'barberos'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tabla_seleccionada']) && isset($_POST['accion'])) {
        $tabla_seleccionada = $_POST['tabla_seleccionada'];
        $accion = $_POST['accion'];
        $_SESSION['tabla_seleccionada'] = $tabla_seleccionada;
        $_SESSION['accion'] = $accion;

        if ($accion === 'editar') {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?accion=editar');
            exit();
        }
    } elseif (isset($_SESSION['tabla_seleccionada']) && $_SESSION['accion'] === 'editar' && isset($_POST['id_registro'])) {
        $id_registro = $_POST['id_registro'];
        $_SESSION['id_registro'] = $id_registro;
    } elseif (isset($_SESSION['tabla_seleccionada'])) {
        $tabla_seleccionada = $_SESSION['tabla_seleccionada'];
        $accion = $_SESSION['accion'];

        switch ($tabla_seleccionada) {
            case 'reservas':
                $id_servicio = $_POST['id_servicio'];
                $nom_barbero = $_POST['nom_barbero'];
                $fecha_reserva = $_POST['fecha_reserva'];
                $hora_reserva = $_POST['hora_reserva'];
                $nombre_cliente = $_POST['nombre_cliente'];
                $telefono_cliente = $_POST['telefono_cliente'];
                $id_barbero = $_SESSION['id_barbero'];

                if ($accion === 'agregar') {
                    $query = "INSERT INTO reservas (id_servicio, nom_barbero, fecha_reserva, hora_reserva, nombre_cliente, telefono_cliente, id_barbero) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('isssssi', $id_servicio, $nom_barbero, $fecha_reserva, $hora_reserva, $nombre_cliente, $telefono_cliente, $id_barbero);
                } else {
                    $query = "UPDATE reservas SET id_servicio=?, nom_barbero=?, fecha_reserva=?, hora_reserva=?, nombre_cliente=?, telefono_cliente=?, id_barbero=? WHERE id_reserva=?";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('isssssii', $id_servicio, $nom_barbero, $fecha_reserva, $hora_reserva, $nombre_cliente, $telefono_cliente, $id_barbero, $_SESSION['id_registro']);
                }
                break;

            case 'Servicios':
                $nombre_servicio = $_POST['nombre_servicio'];
                $precio = $_POST['precio'];

                if ($accion === 'agregar') {
                    $query = "INSERT INTO Servicios (nombre_servicio, precio) VALUES (?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('sd', $nombre_servicio, $precio);
                } else {
                    $query = "UPDATE Servicios SET nombre_servicio=?, precio=? WHERE id_servicio=?";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('sdi', $nombre_servicio, $precio, $_SESSION['id_registro']);
                }
                break;

            case 'barberos':
                $nombre_barbero = $_POST['nombre_barbero'];
                $password = $_POST['password'];

                if ($accion === 'agregar') {
                    $query = "INSERT INTO barberos (nombre_barbero, password) VALUES (?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('ss', $nombre_barbero, $password);
                } else {
                    $query = "UPDATE barberos SET nombre_barbero=?, password=? WHERE id_barbero=?";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param('ssi', $nombre_barbero, $password, $_SESSION['id_registro']);
                }
                break;
        }

        // Ejecutar la consulta y redirigir
        if ($stmt->execute()) {
            header('Location: admin_panel.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

if ($_SESSION['accion'] === 'editar' && isset($_SESSION['tabla_seleccionada'])) {
    $tabla_seleccionada = $_SESSION['tabla_seleccionada'];
    $query = "SELECT * FROM $tabla_seleccionada";
    $result = $conexion->query($query);
    $registros = $result->fetch_all(MYSQLI_ASSOC);
}

$query = "SELECT * FROM Servicios";
$result = $conexion->query($query);
$servicios = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregarr</title>
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
        <h2>Agregar </h2>
        <a href="admin_panel.php">Regresar al panel de administración</a>
        <?php if (!isset($_SESSION['tabla_seleccionada']) || !isset($_SESSION['accion'])): ?>
            <form method="post" action="">
                <label for="tabla_seleccionada">Selecciona la tabla:</label>
                <select name="tabla_seleccionada" required>
                    <?php foreach ($tablas as $tabla): ?>
                        <option value="<?php echo htmlspecialchars($tabla); ?>"><?php echo htmlspecialchars($tabla); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="accion">¿Qué quieres hacer?</label>
                <select name="accion" required>
                    <option value="agregar">Agregar</option>
                    <option value="editar">Editar</option>
                </select>
                <button type="submit">Seleccionar</button>
            </form>
        <?php elseif ($_SESSION['accion'] === 'editar' && !isset($_SESSION['id_registro'])): ?>
            <h3>Selecciona el registro que deseas editar de la tabla: <?php echo htmlspecialchars($_SESSION['tabla_seleccionada']); ?></h3>
            <form method="post" action="">
                <label for="id_registro">Seleccionar registro:</label>
                <select name="id_registro" required>
                    <?php foreach ($registros as $registro): ?>
                        <option value="<?php echo htmlspecialchars($registro['id_' . $_SESSION['tabla_seleccionada']]); ?>">
                            <?php echo htmlspecialchars($registro['nombre_' . $_SESSION['tabla_seleccionada']]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Seleccionar</button>
            </form>
        <?php else: ?>
            <h3><?php echo ucfirst($_SESSION['accion']); ?> en la tabla: <?php echo htmlspecialchars($_SESSION['tabla_seleccionada']); ?></h3>
            <form method="post" action="">
                <?php switch ($_SESSION['tabla_seleccionada']) {
                    case 'reservas': ?>
                        <label for="id_servicio">ID Servicio:</label>
                        <select name="id_servicio" required>
                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?php echo htmlspecialchars($servicio['id_servicio']); ?>">
                                    <?php echo htmlspecialchars($servicio['nombre_servicio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="nom_barbero">Nombre del Barbero:</label>
                        <input type="text" name="nom_barbero" required>
                        <label for="fecha_reserva">Fecha de la Reserva:</label>
                        <input type="date" name="fecha_reserva" required>
                        <label for="hora_reserva">Hora de la Reserva:</label>
                        <input type="time" name="hora_reserva" required>
                        <label for="nombre_cliente">Nombre del Cliente:</label>
                        <input type="text" name="nombre_cliente" required>
                        <label for="telefono_cliente">Teléfono del Cliente:</label>
                        <input type="text" name="telefono_cliente" required>
                        <?php break;

                    case 'Servicios': ?>
                        <label for="nombre_servicio">Nombre del Servicio:</label>
                        <input type="text" name="nombre_servicio" required>
                        <label for="precio">Precio:</label>
                        <input type="number" name="precio" required>
                        <?php break;

                    case 'barberos': ?>
                        <label for="nombre_barbero">Nombre del Barbero:</label>
                        <input type="text" name="nombre_barbero" required>
                        <label for="password">Contraseña:</label>
                        <input type="password" name="password" required>
                        <?php break;
                } ?>
                <button type="submit"><?php echo ucfirst($_SESSION['accion']); ?></button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
