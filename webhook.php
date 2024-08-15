<?php
require 'vendor\autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51Pah91AfMqazcVXecfqLCzVvw8zcsvtx4sAowkjzn9MMdm9xQUySYcuArv61SbNyTmS3fdhYbDorVmQ3IrrlhFYY00W8uruOF5');

$endpoint_secret = 'whsec_94ea0960d8ce19100c0b36154f117858304ed04ecef98e3bcc8d543bf838f8ac'; // Reemplaza con tu secreto del endpoint

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );

    // Maneja el evento
    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        // Guarda los datos de la reserva en la base de datos
        $id_servicio = $session->metadata->id_servicio;
        $id_barbero = $session->metadata->id_barbero;
        $fecha_reserva = $session->metadata->fecha_reserva;
        $hora_reserva = $session->metadata->hora_reserva;
        $nombre_cliente = $session->metadata->nombre_cliente;
        $telefono_cliente = $session->metadata->telefono_cliente;

        // Conectar a la base de datos y guardar los datos de la reserva
        $conexion = include('conexion.php');
        $query = "INSERT INTO reservas (id_servicio, id_barbero, fecha_reserva, hora_reserva, nombre_cliente, telefono_cliente) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("iissss", $id_servicio, $id_barbero, $fecha_reserva, $hora_reserva, $nombre_cliente, $telefono_cliente);
        $stmt->execute();
    }

    http_response_code(200);
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}
?>
