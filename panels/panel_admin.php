<?php
// === panels/panel_admin.php ===
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Panel del Administrador</h1>
    <ul>
        <li><a href="#">Agregar/Eliminar Empresas</a></li>
        <li><a href="#">Agregar/Eliminar Usuarios</a></li>
        <li><a href="#">Agregar Citas</a></li>
        <li><a href="#">Generar Reportes</a></li>
    </ul>
</div>
</body>
</html>
