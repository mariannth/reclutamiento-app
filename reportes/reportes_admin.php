<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header("Location: ../index.php");
    exit();
}

$empresa = $_GET['empresa'] ?? '';
$fecha = $_GET['fecha'] ?? '';
$usuario = $_GET['usuario'] ?? '';

// Obtener datos para los selects
$empresas = $conexion->query("SELECT id, cliente FROM empresas");
$usuarios = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'usuario'");

// Condiciones de filtrado
$condiciones = [];
if ($empresa) $condiciones[] = "c.empresa_id = " . intval($empresa);
if ($fecha) $condiciones[] = "c.fecha = '$fecha'";
if ($usuario) $condiciones[] = "c.usuario_id = " . intval($usuario);

$where = count($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

$citas = $conexion->query("SELECT c.*, e.cliente, u.nombre AS nombre_usuario 
    FROM citas c 
    JOIN empresas e ON c.empresa_id = e.id 
    JOIN usuarios u ON c.usuario_id = u.id 
    $where ORDER BY c.fecha");

// Gráficas
$graf_dia = $conexion->query("SELECT fecha, COUNT(*) as total FROM citas GROUP BY fecha ORDER BY fecha DESC LIMIT 7");
$graf_mes = $conexion->query("SELECT MONTH(fecha) as mes, COUNT(*) as total FROM citas GROUP BY mes");
$graf_empresa = $conexion->query("SELECT e.cliente, COUNT(*) as total FROM citas c JOIN empresas e ON c.empresa_id = e.id GROUP BY e.cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📊 Reportes Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">📑 Reportes de Citas</h2>

        <!-- Filtros -->
        <form class="row g-3 mb-4" method="GET">
            <div class="col-md-3">
                <label>Empresa:</label>
                <select name="empresa" class="form-select">
                    <option value="">Todas</option>
                    <?php while ($e = $empresas->fetch_assoc()): ?>
                        <option value="<?= $e['id'] ?>" <?= $empresa == $e['id'] ? 'selected' : '' ?>>
                            <?= $e['cliente'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Usuario:</label>
                <select name="usuario" class="form-select">
                    <option value="">Todos</option>
                    <?php while ($u = $usuarios->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>" <?= $usuario == $u['id'] ? 'selected' : '' ?>>
                            <?= $u['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">🔍 Filtrar</button>
            </div>
        </form>

        <!-- Tabla de resultados -->
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-secondary">
                <tr>
                    <th>Usuario</th><th>Nombre</th><th>Fecha</th><th>Hora</th><th>Empresa</th><th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $citas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $c['nombre_usuario'] ?></td>
                        <td><?= $c['nombre'] . " " . $c['apellido_paterno'] ?></td>
                        <td><?= $c['fecha'] ?></td>
                        <td><?= $c['hora'] ?></td>
                        <td><?= $c['cliente'] ?></td>
                        <td><?= $c['estado'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Gráficas -->
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <h5 class="text-center">📅 Citas por Día (últimos 7)</h5>
                <canvas id="diaChart"></canvas>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="text-center">🗓️ Citas por Mes</h5>
                <canvas id="mesChart"></canvas>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="text-center">🏢 Citas por Empresa</h5>
                <canvas id="empresaChart"></canvas>
            </div>
        </div>

        <a href="../panels/panel_admin.php" class="btn btn-secondary mt-3">⬅️ Volver al Panel Admin</a>
    </div>

    <script>
        // Citas por día
        new Chart(document.getElementById('diaChart'), {
            type: 'line',
            data: {
                labels: [<?php $dias = []; $valores = []; while ($row = $graf_dia->fetch_assoc()) { $dias[] = $row['fecha']; $valores[] = $row['total']; } echo "'" . implode("','", $dias) . "'"; ?>],
                datasets: [{
                    label: 'Citas por día',
                    data: [<?= implode(",", $valores) ?>],
                    backgroundColor: 'rgba(13,110,253,0.2)',
                    borderColor: '#0d6efd',
                    fill: true
                }]
            }
        });

        // Citas por mes
        new Chart(document.getElementById('mesChart'), {
            type: 'bar',
            data: {
                labels: [<?php $meses = []; $totales = []; while ($row = $graf_mes->fetch_assoc()) { $meses[] = 'Mes ' . $row['mes']; $totales[] = $row['total']; } echo "'" . implode("','", $meses) . "'"; ?>],
                datasets: [{
                    label: 'Citas',
                    data: [<?= implode(",", $totales) ?>],
                    backgroundColor: '#ffc107'
                }]
            }
        });

        // Citas por empresa
        new Chart(document.getElementById('empresaChart'), {
            type: 'pie',
            data: {
                labels: [<?php $nombres = []; $cantidades = []; while ($row = $graf_empresa->fetch_assoc()) { $nombres[] = $row['cliente']; $cantidades[] = $row['total']; } echo "'" . implode("','", $nombres) . "'"; ?>],
                datasets: [{
                    data: [<?= implode(",", $cantidades) ?>],
                    backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14']
                }]
            }
        });
    </script>
</body>
</html>
