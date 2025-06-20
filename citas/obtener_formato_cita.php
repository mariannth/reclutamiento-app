<?php
// obtener_formato_cita.php
include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
    exit();
}

$cita_id = intval($_GET['id']);

$stmt = $conexion->prepare("SELECT c.*, e.cliente, e.sucursal, e.direccion_entrevista, e.link_maps, e.responsables_entrevista FROM citas c JOIN empresas e ON c.empresa_id = e.id WHERE c.id = ?");
$stmt->bind_param("i", $cita_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Cita no encontrada";
    exit();
}

$cita = $resultado->fetch_assoc();
$nombre_completo = $cita['nombre'] . ' ' . $cita['apellido_paterno'] . ' ' . $cita['apellido_materno'];

// Función para mostrar fecha y hora en español completa
function fechaHoraEnEspanol($fechaHora) {
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'Spanish');
    $dt = new DateTime($fechaHora);
    // Ejemplo: miércoles, 7 de mayo de 2025 a las 13:00
    return strftime('%A, %e de %B de %Y a las %H:%M', $dt->getTimestamp());
}

$fechaHoraCompleta = fechaHoraEnEspanol($cita['fecha'] . ' ' . $cita['hora']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; margin: 2rem; }
        ul { margin-top: 0; }
        h3, h4, p { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <h3>RECUERDA PRESENTAR TUS DOCUMENTOS</h3>
    <p><strong>👉🏻 COPIA LEGIBLE:</strong></p>
    <ul>
        <li>Acta de Nacimiento</li>
        <li>CURP</li>
        <li>NSS</li>
        <li>RFC</li>
        <li>INE</li>
        <li>Comprobante de estudios</li>
        <li>Comprobante de domicilio</li>
        <li>Estado de cuenta bancario (No NU, Stori, Mercado Libre, etc.)</li>
    </ul>
    <p><strong>LLEVA PAPELES COMPLETOS PORQUE ES CONTRATACIÓN INMEDIATA</strong></p>

    <p><strong>Tu entrevista sería el día:</strong> <?= $fechaHoraCompleta ?></p>
    <p><strong>Nombre del candidato:</strong> <?= htmlspecialchars($nombre_completo) ?></p>
    <p><strong>Te presentarías con la Supervisora /</strong> <?= htmlspecialchars($cita['responsables_entrevista'] ?? '___________________') ?></p>

    <p><strong>DIRECCIÓN:</strong><br>
    <?= htmlspecialchars($cita['direccion_entrevista'] ?? '___________________') ?><br>
    <a href="<?= $cita['link_maps'] ?>" target="_blank">Ver en Google Maps</a></p>

    <p><strong>NOTA:</strong> En cuanto te encuentres fuera de las instalaciones, avísanos para darte acceso y retroalimentación.</p>
    <p><strong>En caso de no poder asistir, avisa con tiempo para poder reagendar. ¡FAVOR DE CONFIRMAR ASISTENCIA!</strong></p>
</body>
</html>
