<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no válido");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vacante = $_POST['vacante'];
    $horarios = $_POST['horarios'];
    $activa = isset($_POST['activa']) ? 1 : 0;

    $stmt = $conexion->prepare("UPDATE empresas SET vacante = ?, horarios = ?, activo = ? WHERE id = ?");
    $stmt->bind_param("ssii", $vacante, $horarios, $activa, $id);
    $stmt->execute();
    header("Location: ../panels/panel_lider.php?success=empresa_actualizada");
    exit();
}

$empresa = $conexion->query("SELECT * FROM empresas WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Editar Empresa - <?= $empresa['cliente'] ?> / <?= $empresa['sucursal'] ?></h2>
    <form method="POST">
        <div class="mb-3">
            <label>Vacante:</label>
            <input type="text" name="vacante" class="form-control" value="<?= $empresa['vacante'] ?>">
        </div>
        <div class="mb-3">
            <label>Horarios:</label>
            <input type="text" name="horarios" class="form-control" value="<?= $empresa['horarios'] ?>">
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="activa" class="form-check-input" id="activoCheck" <?= $empresa['activo'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="activoCheck">Empresa Activa</label>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Empresa</button>
        <a href="../panels/panel_lider.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
