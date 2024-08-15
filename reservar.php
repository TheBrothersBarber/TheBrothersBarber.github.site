<?php
session_start();
$conexion = include('conexion.php');

// Obtener el id del servicio seleccionado
$id_servicio = $_GET['id_servicio'] ?? null;

if ($id_servicio) {
    // Obtener la información del servicio
    $query = "SELECT * FROM servicios WHERE id_servicio = $id_servicio";
    $result = mysqli_query($conexion, $query);
    $servicio = mysqli_fetch_assoc($result);
} else {
    die("No se ha seleccionado ningún servicio.");
}

// Obtener los barberos
$query_barberos = "SELECT id_barbero, nombre_barbero, horario_inicio, horario_fin, foto FROM barberos";
$result_barberos = mysqli_query($conexion, $query_barberos);
$barberos = [];
while ($row = mysqli_fetch_assoc($result_barberos)) {
    $barberos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita</title>
    <link rel="stylesheet" href="reservar.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0a0a0a;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .header {
            background-color: #333;
            padding: 10px 0;
        }

        .navbar {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            display: inline-block;
            margin-right: 20px;
        }

        .navbar ul li:last-child {
            margin-right: 0;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #555;
            border-radius: 5px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background: #9b9b9b;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .card-content {
            padding: 10px;
        }

        .card-content h3 {
            margin: 10px 0;
            font-size: 16px;
        }

        .card-content p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .navigation-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .navigation-buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <header class="header">
            <nav class="navbar">
                <ul>
                    <li><a href="Index.html">Inicio</a></li>
                    <li><a href="Servicios.html">Nuestros barberos</a></li>
                    <li><a href="Sucursal.html">Sucursal</a></li>
                    <li><a href="login.php">Iniciar sesión</a></li>
                </ul>
            </nav>
        </header>
        <h2>Reservar cita para <?php echo $servicio['nombre_servicio']; ?></h2>
        <form id="reserva-form">
            <input type="hidden" name="id_servicio" value="<?php echo $servicio['id_servicio']; ?>">
            <input type="hidden" name="precio_servicio" value="<?php echo $servicio['precio']; ?>">

            <!-- Paso 1: Selección de Barbero -->
            <div class="step active" id="step1">
                <h3>Selecciona un barbero</h3>
                <div class="card-container">
                    <?php foreach ($barberos as $barbero): ?>
                        <div class="card" onclick="selectBarbero(<?php echo $barbero['id_barbero']; ?>, '<?php echo $barbero['nombre_barbero']; ?>', '<?php echo $barbero['foto']; ?>')">
                            <img src="<?php echo $barbero['foto']; ?>" alt="<?php echo $barbero['nombre_barbero']; ?>">
                            <div class="card-content">
                                <h3><?php echo $barbero['nombre_barbero']; ?></h3>
                                <p>De: <?php echo $barbero['horario_inicio']; ?></p>
                                <p>A: <?php echo $barbero['horario_fin']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="selected-barbero" style="display: none;">
                    <input type="hidden" name="id_barbero" id="selected-barbero-id">
                </div>
            </div>

            <!-- Paso 2: Fecha y Hora -->
            <div class="step" id="step2">
                <h3>Selecciona fecha y hora</h3>
                <label for="fecha_reserva">Fecha:</label>
                <input type="date" name="fecha_reserva" required>

                <label for="hora_reserva">Hora:</label>
                <input type="time" name="hora_reserva" required>
            </div>

            <!-- Paso 3: Información del Cliente -->
            <div class="step" id="step3">
                <h3>Información del cliente</h3>
                <label for="nombre_cliente">Nombre:</label>
                <input type="text" name="nombre_cliente" required>
                
                <label for="telefono_cliente">Teléfono:</label>
                <input type="text" name="telefono_cliente" required>
            </div>

            <div class="navigation-buttons">
                <button type="button" id="prevBtn" onclick="nextPrev(-1)">Anterior</button>
                <button type="button" id="nextBtn" onclick="nextPrev(1)">Siguiente</button>
                <button type="button" id="paymentBtn" style="display: none;" onclick="createCheckoutSession()">Proceder con el pago</button>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 0;
        showStep(currentStep);

        function showStep(n) {
            let steps = document.querySelectorAll(".step");
            steps[n].style.display = "block";

            // Hide the current step
            for (let i = 0; i < steps.length; i++) {
                if (i !== n) {
                    steps[i].style.display = "none";
                }
            }

            // Adjust navigation buttons
            if (n === 0) {
                document.getElementById("prevBtn").style.display = "none";
            } else {
                document.getElementById("prevBtn").style.display = "inline";
            }

            if (n === (steps.length - 1)) {
                document.getElementById("nextBtn").style.display = "none";
                document.getElementById("paymentBtn").style.display = "inline";
            } else {
                document.getElementById("nextBtn").style.display = "inline";
                document.getElementById("paymentBtn").style.display = "none";
            }
        }

        function nextPrev(n) {
            let steps = document.querySelectorAll(".step");
            
            // Hide the current step
            steps[currentStep].style.display = "none";
            
            // Increment or decrement the current step index
            currentStep += n;
            
            // Show the next or previous step
            showStep(currentStep);
        }

        function selectBarbero(id, nombre, foto) {
            // Set selected barbero ID and show it in the hidden field
            document.getElementById("selected-barbero-id").value = id;

            // Hide barbero selection and show the next step
            document.getElementById("step1").style.display = "none";
            document.getElementById("step2").style.display = "block";
        }

        const stripe = Stripe('pk_test_51Pah91AfMqazcVXebmSqsczYgcbvccLPMbuwJjYefmTkS1m0KWHCGLmxhReKPcwymumZC2BXs43AD4hHboWc6YX700qQFYFSQP'); // Reemplaza 'tu_llave_publica' con tu Publishable Key de Stripe

        function createCheckoutSession() {
    const form = document.getElementById('reserva-form');
    const formData = new FormData(form);

    fetch('crear_sesion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(session => {
        return stripe.redirectToCheckout({ sessionId: session.id });
    })
    .then(result => {
        if (result.error) {
            alert(result.error.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
    </script>
</body>
</html>
