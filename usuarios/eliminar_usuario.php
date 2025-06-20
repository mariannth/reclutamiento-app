<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../index.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: ../panels/panel_superadmin.php?error=id_invalido");
    exit();
}

if ($id == $_SESSION['usuario']['id']) {
    header("Location: ../panels/panel_superadmin.php?error=no_eliminar_propio_usuario");
    exit();
}

$stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../panels/panel_superadmin.php?success=usuario_eliminado");
} else {
    header("Location: ../panels/panel_superadmin.php?error=error_eliminar_usuario");
}
$stmt->close();
exit();
