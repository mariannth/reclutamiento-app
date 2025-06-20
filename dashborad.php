<?php
include 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Bienvenido, <?= $_SESSION['usuario']['nombre'] ?> (<?= $_SESSION['usuario']['rol'] ?>)</span>
    <a href="logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
</nav>

<div class="container mt-4">
    <h2>Panel de Control</h2>
    <p>Selecciona una acción:</p>
    <ul>
        <li><a href="#">Registrar nueva cita</a></li>
        <li><a href="#">Ver bitácora de entrevistas</a></li>
        <li><a href="#">Agregar empresa / candidato</a></li>
        <li><a href="#">Generar reportes</a></li>
        <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
            <li><a href="#">Gestión de usuarios</a></li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
