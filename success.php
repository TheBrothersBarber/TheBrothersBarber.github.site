<?php
require 'conexion.php';
require 'vendor\autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51Pah91AfMqazcVXecfqLCzVvw8zcsvtx4sAowkjzn9MMdm9xQUySYcuArv61SbNyTmS3fdhYbDorVmQ3IrrlhFYY00W8uruOF5');

function displayMessage($message, $success = true) {
    $color = $success ? '#4CAF50' : '#F44336'; // Verde para éxito, rojo para error
    echo "<div style='font-family: Arial, sans-serif; background-color: $color; color: white; padding: 20px; border-radius: 5px; text-align: center;'>";
    echo "<h2>" . ($success ? "¡Éxito!" : "¡Error!") . "</h2>";
    echo "<p>$message</p>";
    echo "<a href='Index.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: white; color: $color; border-radius: 5px; text-decoration: none; font-weight: bold;'>Regresar a la página de inicio</a>";
    echo "</div>";
}

if (isset($_GET['session_id'])) {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
    
    if ($session->payment_status === 'paid') {
        $id_servicio = $_GET['id_servicio'];
        $id_barbero = $_GET['id_barbero'];
        $fecha_reserva = $_GET['fecha_reserva'];
        $hora_reserva = $_GET['hora_reserva'];
        $nombre_cliente = $_GET['nombre_cliente'];
        $telefono_cliente = $_GET['telefono_cliente'];

        // Obtener el nombre del barbero
        $query_barbero = "SELECT nombre_barbero FROM barberos WHERE id_barbero = ?";
        $stmt_barbero = $conexion->prepare($query_barbero);
        $stmt_barbero->bind_param('i', $id_barbero);
        $stmt_barbero->execute();
        $result_barbero = $stmt_barbero->get_result();
        $barbero = $result_barbero->fetch_assoc();
        $nom_barbero = $barbero['nombre_barbero'];

        // Insertar la reserva en la base de datos
        $query = "INSERT INTO reservas (id_servicio, nom_barbero, fecha_reserva, hora_reserva, nombre_cliente, telefono_cliente, id_barbero) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('isssssi', $id_servicio, $nom_barbero, $fecha_reserva, $hora_reserva, $nombre_cliente, $telefono_cliente, $id_barbero);

        if ($stmt->execute()) {
            displayMessage("Reserva realizada con éxito.");
        } else {
            displayMessage("Error al realizar la reserva: " . $stmt->error, false);
        }
    } else {
        displayMessage("El pago no fue exitoso.", false);
    }
} else {
    displayMessage("No se recibió un ID de sesión válido.", false);
}
?>
