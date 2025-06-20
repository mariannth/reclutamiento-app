<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];
$usuario_nombre = $_SESSION['usuario']['nombre'] ?? 'Usuario';

// === ACTUALIZAR SEGUIMIENTO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];
    $estado_seguimiento = $_POST['estado_seguimiento'] ?? 'citado';
    $comentarios_usuario = $_POST['comentarios_usuario'] ?? '';

    $stmt = $conexion->prepare("UPDATE citas SET estado_seguimiento = ?, seguimiento_usuario = ?, seguimiento_actualizado_por = ?, fecha_actualizacion_seguimiento = NOW() WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ssiii", $estado_seguimiento, $comentarios_usuario, $usuario_id, $cita_id, $usuario_id);
    $stmt->execute();
}

$query = $conexion->prepare("SELECT c.*, e.cliente, e.sucursal, e.direccion_entrevista, e.link_maps, e.responsables_entrevista FROM citas c JOIN empresas e ON c.empresa_id = e.id WHERE c.usuario_id = ? ORDER BY c.fecha DESC, c.hora DESC");
$query->bind_param("i", $usuario_id);
$query->execute();
$resultado = $query->get_result();

function esEditable($empresa, $fecha, $hora) {
    $horaActual = new DateTime();
    $fechaHoraCita = new DateTime("$fecha $hora");
    $diaSemana = (int)(new DateTime($fecha))->format('N');
    $empresaClean = strtoupper(trim($empresa));

    if ($empresaClean === 'CDS') {
        if ($diaSemana < 2 || $diaSemana > 5) return false;
        $limite = new DateTime("$fecha 17:40:00");
    } else {
        if ($diaSemana < 1 || $diaSemana > 5) return false;
        $limite = new DateTime("$fecha 23:00:00");
    }

    return $horaActual <= $limite;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas Registradas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Mis Citas Registradas</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Empresa</th>
            <th>Sucursal</th>
            <th>Estado</th>
            <th>Fecha Actualización</th>
            <th>Seguimiento Usuario</th>
            <th>Seguimiento Líder</th>
            <th>Actualizado por Líder</th>
            <th>Fecha Actualización Líder</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($cita = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido_paterno'] . ' ' . $cita['apellido_materno']) ?></td>
                <td><?= $cita['fecha'] ?></td>
                <td><?= $cita['hora'] ?></td>
                <td><?= htmlspecialchars($cita['cliente']) ?></td>
                <td><?= htmlspecialchars($cita['sucursal']) ?></td>
                <td><?= htmlspecialchars($cita['estado_seguimiento']) ?></td>
                <td><?= $cita['fecha_actualizacion_seguimiento'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
                        <select name="estado_seguimiento" class="form-select form-select-sm mb-1" required>
                            <?php
                            $opciones = ['citado', 'aceptado', 'rechazado', 'no acudió', 'contratado', 'reingreso', 'primer día'];
                            foreach ($opciones as $opcion):
                                $selected = ($cita['estado_seguimiento'] === $opcion) ? 'selected' : '';
                                echo "<option value=\"$opcion\" $selected>$opcion</option>";
                            endforeach;
                            ?>
                        </select>
                        <textarea name="comentarios_usuario" class="form-control form-control-sm" placeholder="Comentarios..."><?= htmlspecialchars($cita['seguimiento_usuario']) ?></textarea>
                        <button type="submit" class="btn btn-sm btn-success mt-1">💾 Guardar</button>
                    </form>
                </td>
                <td><?= nl2br(htmlspecialchars($cita['seguimiento_lider'] ?? '')) ?></td>
                <td><?= htmlspecialchars($cita['actualizado_por_lider'] ?? '') ?></td>
                <td><?= $cita['fecha_actualizacion_lider'] ?? '' ?></td>
                <td>
                    <?php if (esEditable($cita['cliente'], $cita['fecha'], $cita['hora'])): ?>
                        <a href="../citas/editar_cita.php?id=<?= $cita['id'] ?>" class="btn btn-sm btn-primary">✏️ Editar</a>
                    <?php else: ?>
                        <span class="text-muted">No editable</span>
                    <?php endif; ?>
                    <a href="../citas/obtener_formato_cita.php?id=<?= $cita['id'] ?>" target="_blank" class="btn btn-sm btn-info mt-1">📄 Formato</a>
                    <a href="../citas/eliminar_cita.php?id=<?= $cita['id'] ?>" onclick="return confirm('¿Eliminar esta cita?');" class="btn btn-sm btn-danger mt-1">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
