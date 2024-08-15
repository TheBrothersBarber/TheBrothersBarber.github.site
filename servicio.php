<?php
session_start();
$conexion = include('conexion.php');

if (!$conexion) {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}

// Consulta para obtener los datos de los servicios
$query = "SELECT id_servicio, nombre_servicio, descripcion_servicio, precio FROM servicios";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Arreglo para almacenar los datos de los servicios
$servicios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $servicios[$row['id_servicio']] = $row; // Usamos el ID como índice
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbería</title>
    <link rel="stylesheet" href="servicio.css">
    <link rel="stylesheet" href="index.css">
    <style>
        
        
        .agendar-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .agendar-btn:hover {
            background-color: #9b9b9b;
        }
    </style>
</head>
<body>
    <header class="header">
    <div class="menu container">
            <img src="IMG\Logo.png" class="menu-icono" alt="Los brothers">
        </div>          
        <nav class="navbar">
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="Servicios.html">Nuestros barberos</a></li>
                <li><a href="Sucursal.html">Sucursal</a></li>
                <li><a href="login.php">Iniciar sesion</a></li>
            </ul>
        </nav>
    </header>

<div class="container">
    <section class="section-cortes">
        <h2>Nuestros cortes</h2>
        <div class="grid-container">
            <div class="card">
                <img src="cortes/clasico.jpg" alt="Corte Clásico">
                <div class="card-content">
                    <h3 class="card-title"><?php echo $servicios[1]['nombre_servicio']; ?></h3>
                    <p><?php echo $servicios[1]['descripcion_servicio']; ?></p>
                    <p><?php echo '$' . $servicios[1]['precio']; ?></p>
                    <a href="reservar.php?id_servicio=<?php echo $servicios[1]['id_servicio']; ?>" class="agendar-btn">Agendar cita</a>
                </div>
            </div>
            
            <div class="card">
                <img src="cortes/degradado.jpeg" alt="Corte Desvanecidos">
                <div class="card-content">
                    <h3 class="card-title"><?php echo $servicios[2]['nombre_servicio']; ?></h3>
                    <p><?php echo $servicios[2]['descripcion_servicio']; ?></p>
                    <p><?php echo '$' . $servicios[2]['precio']; ?></p>
                    <a href="reservar.php?id_servicio=<?php echo $servicios[2]['id_servicio']; ?>" class="agendar-btn">Agendar cita</a>
                </div>
            </div>
            
            <div class="card">
                <img src="cortes/moderno.jpeg" alt="Corte Moderno">
                <div class="card-content">
                    <h3 class="card-title"><?php echo $servicios[3]['nombre_servicio']; ?></h3>
                    <p><?php echo $servicios[3]['descripcion_servicio']; ?></p>
                    <p><?php echo '$' . $servicios[3]['precio']; ?></p>
                    <a href="reservar.php?id_servicio=<?php echo $servicios[3]['id_servicio']; ?>" class="agendar-btn">Agendar cita</a>
                </div>
            </div>
            
            <div class="card">
                <img src="cortes/mohicano.jpg" alt="Corte Mohicano">
                <div class="card-content">
                    <h3 class="card-title"><?php echo $servicios[4]['nombre_servicio']; ?></h3>
                    <p><?php echo $servicios[4]['descripcion_servicio']; ?></p>
                    <p><?php echo '$' . $servicios[4]['precio']; ?></p>
                    <a href="reservar.php?id_servicio=<?php echo $servicios[4]['id_servicio']; ?>" class="agendar-btn">Agendar cita</a>
                </div>
            </div>
            <div class="card">
                <img src="IMG\Barba.jpeg" alt="Corte Mohicano">
                <div class="card-content">
                    <h3 class="card-title"><?php echo $servicios[6]['nombre_servicio']; ?></h3>
                    <p><?php echo $servicios[6]['descripcion_servicio']; ?></p>
                    <p><?php echo '$' . $servicios[4]['precio']; ?></p>
                    <a href="reservar.php?id_servicio=<?php echo $servicios[6]['id_servicio']; ?>" class="agendar-btn">Agendar cita</a>
                </div>
            </div>
        </div>
    </section>
   
</body>
</html>
