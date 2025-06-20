<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://kit.fontawesome.com/a2d9d6b6ad.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4">👩‍💼 Panel del Administrador</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <a href="../empresas/empresas_admin.php" class="btn btn-outline-warning w-100"><i class="fas fa-building"></i> Registrar Vacantes y empresas</a>
            </div>
            <div class="col-md-6">
                <a href="../empresas/empresas_admin_gestion.php" class="btn btn-outline-primary w-100"><i class="fas fa-building"></i> Gestionar Empresas</a>
            </div>
            <div class="col-md-6">
                <a href="../usuarios/usuarios_admin.php" class="btn btn-outline-dark w-100"><i class="fas fa-users"></i> Gestionar Usuarios</a>
            </div>
            <div class="col-md-6">
                <a href="../citas/agregar_cita_admin.php" class="btn btn-outline-success w-100"><i class="fas fa-calendar-plus"></i> Agregar Cita</a>
            </div>
            <div class="col-md-6">
                <a href="../reportes/reportes_admin.php" class="btn btn-outline-info w-100"><i class="fas fa-file-export"></i> Reportes</a>
            </div>
            <div class="col-md-6">
                <a href="../logout.php" class="btn btn-danger w-100">Cerrar sesión</a>
            </div>
        </div>

        
    </div>
</body>
</html>
