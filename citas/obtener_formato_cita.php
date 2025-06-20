<?php
include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
    exit();
}

$id = intval($_GET['id']);
$res = $conexion->query("SELECT c.*, e.cliente, e.sucursal, e.direccion_entrevista, e.link_maps 
                         FROM citas c 
                         JOIN empresas e ON c.empresa_id = e.id 
                         WHERE c.id = $id");

if ($res->num_rows === 0) {
    echo "Cita no encontrada.";
    exit();
}

$cita = $res->fetch_assoc();
?>

<p><strong>Nombre:</strong> <?= $cita['nombre'] ?></p>
<p><strong>Fecha:</strong> <?= $cita['fecha'] ?> a las <?= $cita['hora'] ?></p>
<p><strong>Empresa:</strong> <?= $cita['cliente'] ?> - <?= $cita['sucursal'] ?></p>
<p><strong>Dirección:</strong> <?= $cita['direccion_entrevista'] ?></p>
<p><strong>Mapa:</strong> <a href="<?= $cita['link_maps'] ?>" target="_blank"><?= $cita['link_maps'] ?></a></p>