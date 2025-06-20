<?php
// === citas/eliminar_cita.php ===
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Validar que viene el ID por GET
if (!isset($_GET['id'])) {
    echo "<script>alert('ID de cita no proporcionado.'); window.location.href = '../citas/tabla_citas.php';</script>";
    exit();
}

$cita_id = intval($_GET['id']);

// Verificar que la cita pertenece al usuario
$stmt = $conexion->prepare("SELECT id FROM citas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $cita_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Cita no encontrada o no tienes permisos.'); window.location.href = '../citas/tabla_citas.php';</script>";
    exit();
}

// Eliminar la cita
$delete = $conexion->prepare("DELETE FROM citas WHERE id = ?");
$delete->bind_param("i", $cita_id);

if ($delete->execute()) {
    echo "<script>alert('✅ Cita eliminada correctamente.'); window.location.href = '../citas/tabla_citas.php';</script>";
} else {
    echo "<script>alert('❌ Error al eliminar la cita: " . $delete->error . "'); window.location.href = '../citas/tabla_citas.php';</script>";
}
?>
