<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'], $_POST['estado_seguimiento'])) {
    $cita_id = intval($_POST['cita_id']);
    $estado_seguimiento = $_POST['estado_seguimiento'];
    $usuario_id = $_SESSION['usuario']['id'];

    $stmt = $conexion->prepare("UPDATE citas SET estado_seguimiento = ?, seguimiento_actualizado_por = ?, fecha_actualizacion_seguimiento = NOW() WHERE id = ?");
    $stmt->bind_param("sii", $estado_seguimiento, $usuario_id, $cita_id);
    $stmt->execute();
}

$citas = $conexion->query("
    SELECT c.*, u.nombre AS actualizado_por
    FROM citas c
    LEFT JOIN usuarios u ON c.seguimiento_actualizado_por = u.id
    ORDER BY c.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento a Citas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">🔎 Seguimiento a Citas</h2>

        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Nombre</th>
                    <th>Empresa</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Seguimiento</th>
                    <th>Actualizado por</th>
                    <th>Fecha de Actualización</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cita = $citas->fetch_assoc()): ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
                            <td><?= $cita['nombre'] . " " . $cita['apellido_paterno'] ?></td>
                            <td><?= $cita['empresa_id'] ?></td>
                            <td><?= $cita['fecha'] ?></td>
                            <td><?= $cita['hora'] ?></td>
                            <td><?= $cita['estado'] ?></td>
                            <td>
                                <select name="estado_seguimiento" class="form-select">
                                    <option <?= $cita['estado_seguimiento'] == '' ? 'selected' : '' ?> disabled>Selecciona</option>
                                    <option value="aceptado" <?= $cita['estado_seguimiento'] == 'aceptado' ? 'selected' : '' ?>>Aceptado</option>
                                    <option value="rechazado" <?= $cita['estado_seguimiento'] == 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                                    <option value="no acudió" <?= $cita['estado_seguimiento'] == 'no acudió' ? 'selected' : '' ?>>No acudió</option>
                                    <option value="contratado" <?= $cita['estado_seguimiento'] == 'contratado' ? 'selected' : '' ?>>Contratado</option>
                                    <option value="reingreso" <?= $cita['estado_seguimiento'] == 'reingreso' ? 'selected' : '' ?>>Reingreso</option>
                                    <option value="primer día" <?= $cita['estado_seguimiento'] == 'primer día' ? 'selected' : '' ?>>Primer día</option>
                                </select>
                            </td>
                            <td><?= $cita['actualizado_por'] ?: '-' ?></td>
                            <td><?= $cita['fecha_actualizacion_seguimiento'] ?: '-' ?></td>
                            <td><button class="btn btn-sm btn-success" type="submit">Actualizar</button></td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- <a href="panel_lider.php" class="btn btn-secondary">⬅️ Volver al Panel</a> -->
    </div>
</body>
</html>
