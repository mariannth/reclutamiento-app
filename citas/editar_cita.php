<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];
if (!isset($_GET['id'])) {
    echo "ID de cita no proporcionado.";
    exit();
}

$cita_id = intval($_GET['id']);

$stmt = $conexion->prepare("SELECT * FROM citas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $cita_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Cita no encontrada o no tienes permiso.";
    exit();
}

$cita = $result->fetch_assoc();
$fechaLimite = new DateTime($cita['fecha'] . ' 23:00:00');
$ahora = new DateTime();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ahora < $fechaLimite) {
    $nombre = $_POST['nombre'];
    $empresa = $_POST['empresa'];
    $sucursal = $_POST['sucursal'];
    $vacante = $_POST['vacante'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $comentarios = $_POST['comentarios'];

    $update = $conexion->prepare("UPDATE citas SET nombre = ?, empresa = ?, sucursal = ?, vacante = ?, fecha = ?, hora = ?, comentarios = ? WHERE id = ?");
    $update->bind_param("sssssssi", $nombre, $empresa, $sucursal, $vacante, $fecha, $hora, $comentarios, $cita_id);
    
    if ($update->execute()) {
        header("Location: panel_usuario.php");
        exit();
    } else {
        echo "Error al actualizar la cita.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
<div class="container">
    <h2>Editar Cita</h2>

    <?php if ($ahora < $fechaLimite): ?>
        <form method="POST">
            <label>Nombre del candidato:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($cita['nombre']) ?>" required><br>

            <label>Empresa:</label>
            <input type="text" name="empresa" value="<?= htmlspecialchars($cita['empresa']) ?>" required><br>

            <label>Sucursal:</label>
            <input type="text" name="sucursal" value="<?= htmlspecialchars($cita['sucursal']) ?>" required><br>

            <label>Vacante:</label>
            <input type="text" name="vacante" value="<?= htmlspecialchars($cita['vacante']) ?>" required><br>

            <label>Fecha:</label>
            <input type="date" name="fecha" value="<?= $cita['fecha'] ?>" required><br>

            <label>Hora:</label>
            <input type="time" name="hora" value="<?= $cita['hora'] ?>" required><br>

            <label>Comentarios:</label>
            <textarea name="comentarios"><?= htmlspecialchars($cita['comentarios']) ?></textarea><br>

            <button type="submit">Guardar Cambios</button>
            <a href="panel_usuario.php">Cancelar</a>
        </form>
    <?php else: ?>
        <p>⛔ Esta cita ya no se puede editar (pasó el límite de las 11 PM).</p>
        <a href="panel_usuario.php">Volver</a>
    <?php endif; ?>
</div>
</body>
</html>
