<?php
include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    echo "Cita no encontrada.";
    exit();
}

$id = intval($_GET['id']);
$result = $conexion->query("SELECT c.*, e.cliente, e.sucursal, e.direccion_entrevista, e.link_maps 
                            FROM citas c 
                            JOIN empresas e ON c.empresa_id = e.id 
                            WHERE c.id = $id");

if ($result->num_rows == 0) {
    echo "Cita no encontrada.";
    exit();
}

$cita = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formato de Cita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Formato de Cita</h5>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <?= $cita['nombre'] ?></p>
                <p><strong>Correo:</strong> <?= $cita['correo'] ?></p>
                <p><strong>Teléfono:</strong> <?= $cita['telefono'] ?></p>
                <p><strong>Fecha:</strong> <?= $cita['fecha'] ?> a las <?= $cita['hora'] ?></p>
                <p><strong>Empresa:</strong> <?= $cita['cliente'] ?> - <?= $cita['sucursal'] ?></p>
                <p><strong>Dirección:</strong> <?= $cita['direccion_entrevista'] ?></p>
                <p><strong>Mapa:</strong> <a href="<?= $cita['link_maps'] ?>" target="_blank">Ver en Google Maps</a></p>
                <?php if (!empty($cita['comentarios'])): ?>
                    <p><strong>Comentarios:</strong> <?= nl2br(htmlspecialchars($cita['comentarios'])) ?></p>
                <?php endif; ?>
                <hr>
                <button class="btn btn-outline-primary" onclick="copiarContenido()">📋 Copiar Información</button>
            </div>
        </div>
    </div>

    <script>
        function copiarContenido() {
            const texto = `Cita confirmada:
Nombre: <?= $cita['nombre'] ?> 
Correo: <?= $cita['correo'] ?> 
Teléfono: <?= $cita['telefono'] ?> 
Fecha: <?= $cita['fecha'] ?> a las <?= $cita['hora'] ?>
Empresa: <?= $cita['cliente'] ?> - <?= $cita['sucursal'] ?>
Dirección: <?= $cita['direccion_entrevista'] ?>
Mapa: <?= $cita['link_maps'] ?><?= !empty($cita['comentarios']) ? "\\nComentarios: " . str_replace("\n", "\\n", addslashes($cita['comentarios'])) : '' ?>`;

            navigator.clipboard.writeText(texto).then(() => {
                alert("Formato copiado al portapapeles.");
            });
        }
    </script>
</body>

</html>