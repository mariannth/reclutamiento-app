<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa_id = $_POST['empresa_id'];
    $usuario_id = $_SESSION['usuario']['id'];
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $edad = $_POST['edad'];
    $escolaridad = $_POST['escolaridad'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $telefono = $_POST['telefono'];
    $coordinador = $_POST['coordinador'];
    $reclutador = $_POST['reclutador'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("INSERT INTO citas (empresa_id, usuario_id, nombre, apellido_paterno, apellido_materno, edad, escolaridad, fecha, hora, telefono, coordinador, reclutador, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssissssss", $empresa_id, $usuario_id, $nombre, $apellido_paterno, $apellido_materno, $edad, $escolaridad, $fecha, $hora, $telefono, $coordinador, $reclutador, $estado);
    $stmt->execute();
    $stmt->close();
    header("Location: citas_admin.php?success=registrada");
    exit();
}

$empresas = $conexion->query("SELECT id, cliente FROM empresas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>📅 Registrar Nueva Cita</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Cita registrada</div>
    <?php endif; ?>

    <form method="POST" class="row g-2">
        <div class="col-md-3"><input type="text" name="nombre" required placeholder="Nombre" class="form-control"></div>
        <div class="col-md-3"><input type="text" name="apellido_paterno" required placeholder="Apellido Paterno" class="form-control"></div>
        <div class="col-md-3"><input type="text" name="apellido_materno" required placeholder="Apellido Materno" class="form-control"></div>
        <div class="col-md-1"><input type="number" name="edad" required placeholder="Edad" class="form-control"></div>
        <div class="col-md-2">
            <select name="escolaridad" class="form-select">
                <option value="primaria">Primaria</option>
                <option value="secundaria">Secundaria</option>
                <option value="preparatoria">Preparatoria</option>
                <option value="licenciatura">Licenciatura</option>
            </select>
        </div>
        <div class="col-md-3"><input type="date" name="fecha" required class="form-control"></div>
        <div class="col-md-2"><input type="time" name="hora" required class="form-control"></div>
        <div class="col-md-3"><input type="text" name="telefono" required placeholder="Teléfono" class="form-control"></div>
        <div class="col-md-2"><input type="text" name="coordinador" required placeholder="Coordinador" class="form-control"></div>
        <div class="col-md-2"><input type="text" name="reclutador" required placeholder="Reclutador" class="form-control"></div>
        <div class="col-md-2">
            <select name="estado" class="form-select">
                <option value="PENDIENTE">Pendiente</option>
                <option value="ACEPTADO">Aceptado</option>
                <option value="RECHAZADO">Rechazado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="empresa_id" class="form-select" required>
                <option value="">-- Empresa --</option>
                <?php while ($e = $empresas->fetch_assoc()): ?>
                    <option value="<?= $e['id'] ?>"><?= $e['cliente'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Registrar</button>
        </div>
    </form>
    <br>
    <a href="../panels/panel_admin.php" class="btn btn-secondary mt-3">⬅️ Volver al Panel Admin</a>
    <br><br>
</body>
</html>
