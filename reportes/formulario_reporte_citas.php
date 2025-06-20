<?php
session_start();
require '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

// Obtener lista de empresas
$empresas = $conexion->query("SELECT id, cliente FROM empresas ORDER BY cliente ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte de Citas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { padding: 2rem; background: #f8f9fa; }
        .card { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h3 class="mb-4">📊 Generar Reporte de Citas</h3>
        <form method="GET" action="exportar_citas.php" target="_blank">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="tipo" class="form-label">Tipo de Reporte:</label>
                    <select name="tipo" id="tipo" class="form-select" required>
                        <option value="dia">Por Día</option>
                        <option value="mes">Por Mes</option>
                        <option value="anio">Por Año</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="valor" class="form-label">Fecha:</label>
                    <input type="date" name="valor" id="valor" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="formato" class="form-label">Formato de Exportación:</label>
                    <select name="formato" id="formato" class="form-select" required>
                        <option value="excel">📁 Excel</option>
                        <option value="pdf">🖨️ PDF</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="empresa_id" class="form-label">Filtrar por Empresa (opcional):</label>
                    <select name="empresa_id" id="empresa_id" class="form-select">
                        <option value="">-- Todas las empresas --</option>
                        <?php while ($empresa = $empresas->fetch_assoc()): ?>
                            <option value="<?= $empresa['id'] ?>"><?= $empresa['cliente'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Generar Reporte</button>
            
        </form>
    </div>
</div>
</body>
</html>
