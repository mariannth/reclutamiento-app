<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Líder</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">📊 Panel del Líder</h2>
    <nav class="mb-3">
        <button class="btn btn-outline-primary me-2" onclick="mostrarSeccion('bitacoras')">Ver Bitácoras</button>
        <button class="btn btn-outline-success me-2" onclick="mostrarSeccion('seguimiento')">Seguimiento a Usuarios</button>
        <button class="btn btn-outline-warning me-2" onclick="mostrarSeccion('reportes')">Reportes de Citas</button>
        <button class="btn btn-outline-info me-2" onclick="mostrarSeccion('empresas')">Actualizar Empresas</button>
        <a href="../logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </nav>

    <section id="bitacoras" style="display: none;">
        <iframe src="../reportes/bitacoras_lider.php" width="100%" height="600" frameborder="0"></iframe>
    </section>

    <section id="seguimiento" style="display: none;">
        <iframe src="../seguimiento/seguimiento_citas.php" width="100%" height="600" frameborder="0"></iframe>
    </section>

    <section id="reportes" style="display: none;">
        <iframe src="../reportes/formulario_reporte_citas.php" width="100%" height="600" frameborder="0"></iframe>
    </section>

    <section id="empresas" style="display: none;">
        <iframe src="../empresas/editar_empresas_lider.php" width="100%" height="600" frameborder="0"></iframe>
    </section>
</div>

<script>
function mostrarSeccion(id) {
    document.querySelectorAll('section').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';
}
</script>
</body>
</html>
