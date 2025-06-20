<?php
$conexion = new mysqli("localhost", "root", "", "reclutamiento");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
