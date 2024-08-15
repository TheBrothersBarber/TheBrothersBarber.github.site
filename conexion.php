<?php
$conexion = mysqli_connect("127.0.0.1:3307", "root", "", "thebrothers");

if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

return $conexion;
?>