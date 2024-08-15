<?php
require 'conexion.php';
session_start(); // Asegúrate de iniciar la sesión para acceder a variables de sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre_cliente = $_POST['nombre_cliente'];
    $telefono_cliente = $_POST['telefono_cliente'];
    $id_servicio = $_POST['id_servicio'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_reserva = $_POST['hora_reserva'];
    $id_barbero = $_POST['id_barbero'];

    // Validar si los datos requeridos están presentes
    if (!empty($nombre_cliente) && !empty($telefono_cliente) && !empty($id_servicio) && !empty($fecha_reserva) && !empty($hora_reserva) && !empty($id_barbero)) {

        // Consultar disponibilidad del barbero en la fecha y hora especificadas
        $query = "SELECT * FROM reservas WHERE id_barbero = ? AND fecha_reserva = ? AND hora_reserva = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('iss', $id_barbero, $fecha_reserva, $hora_reserva);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Insertar la nueva reserva en la base de datos
            $query = "INSERT INTO reservas (id_servicio, nom_barbero, fecha_reserva, hora_reserva, nombre_cliente, telefono_cliente, id_barbero) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('isssssi', $id_servicio, $nom_barbero, $fecha_reserva, $hora_reserva, $nombre_cliente, $telefono_cliente, $id_barbero);

            // Obtener el nombre del barbero para almacenarlo en la reserva
            $query_barbero = "SELECT nombre_barbero FROM barberos WHERE id_barbero = ?";
            $stmt_barbero = $conexion->prepare($query_barbero);
            $stmt_barbero->bind_param('i', $id_barbero);
            $stmt_barbero->execute();
            $result_barbero = $stmt_barbero->get_result();
            $barbero = $result_barbero->fetch_assoc();
            $nom_barbero = $barbero['nombre_barbero'];

            if ($stmt->execute()) {
                echo "Reserva realizada con éxito.";
                // Redireccionar o realizar otras acciones después de la reserva exitosa
            } else {
                echo "Error al realizar la reserva. Por favor, intente nuevamente.";
            }
        } else {
            echo "El barbero ya tiene una reserva para el horario seleccionado. Por favor, elija otro horario.";
        }
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>
