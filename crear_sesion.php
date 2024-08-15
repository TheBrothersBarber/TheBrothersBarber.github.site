<?php
require 'vendor\autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51Pah91AfMqazcVXecfqLCzVvw8zcsvtx4sAowkjzn9MMdm9xQUySYcuArv61SbNyTmS3fdhYbDorVmQ3IrrlhFYY00W8uruOF5');

$precio_servicio = $_POST['precio_servicio'];
$nombre_servicio = $_POST['nombre_servicio'] ?? 'Servicio de Barbería';
$id_servicio = $_POST['id_servicio'];
$id_barbero = $_POST['id_barbero'];
$fecha_reserva = $_POST['fecha_reserva'];
$hora_reserva = $_POST['hora_reserva'];
$nombre_cliente = $_POST['nombre_cliente'];
$telefono_cliente = $_POST['telefono_cliente'];

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'mxn',
                'product_data' => [
                    'name' => $nombre_servicio,
                ],
                'unit_amount' => $precio_servicio * 100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:8080/BARBERIA/public/success.php?session_id={CHECKOUT_SESSION_ID}&id_servicio=' . urlencode($id_servicio) . '&id_barbero=' . urlencode($id_barbero) . '&fecha_reserva=' . urlencode($fecha_reserva) . '&hora_reserva=' . urlencode($hora_reserva) . '&nombre_cliente=' . urlencode($nombre_cliente) . '&telefono_cliente=' . urlencode($telefono_cliente),
        'cancel_url' => 'https://tu_dominio.com/public/cancel.php',
        'metadata' => [
            'id_servicio' => $id_servicio,
            'id_barbero' => $id_barbero,
            'fecha_reserva' => $fecha_reserva,
            'hora_reserva' => $hora_reserva,
            'nombre_cliente' => $nombre_cliente,
            'telefono_cliente' => $telefono_cliente,
        ],
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
