<?php
include '../includes/conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

$stmt = $conexion->prepare("SELECT * FROM empresas WHERE id = ? AND activo = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Empresa no encontrada o inactiva']);
}
