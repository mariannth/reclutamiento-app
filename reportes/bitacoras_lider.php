<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

// Obtener estadísticas
$estadisticas = [
    'estado' => [],
    'empresa' => [],
    'fechas' => [],
    'seguimiento' => [
        'aceptado' => 0,
        'rechazado' => 0,
        'no_acudio' => 0,
        'contratado' => 0,
        'reingreso' => 0,
        'primer_dia' => 0
    ]
];

// Estados
$res = $conexion->query("SELECT estado, COUNT(*) AS total FROM citas GROUP BY estado");
while ($row = $res->fetch_assoc()) {
    $estadisticas['estado'][$row['estado']] = (int)$row['total'];
}

// Empresas
$res = $conexion->query("SELECT e.cliente, COUNT(*) AS total FROM citas c JOIN empresas e ON c.empresa_id = e.id GROUP BY e.cliente");
while ($row = $res->fetch_assoc()) {
    $estadisticas['empresa'][$row['cliente']] = (int)$row['total'];
}

// Fechas
$res = $conexion->query("SELECT fecha, COUNT(*) AS total FROM citas GROUP BY fecha ORDER BY fecha DESC LIMIT 7");
while ($row = $res->fetch_assoc()) {
    $estadisticas['fechas'][$row['fecha']] = (int)$row['total'];
}

// Seguimiento
$res = $conexion->query("SELECT estado_seguimiento FROM citas WHERE estado_seguimiento IS NOT NULL");
while ($row = $res->fetch_assoc()) {
    $valor = strtolower($row['estado_seguimiento']);
    switch ($valor) {
        case 'aceptado':
            $estadisticas['seguimiento']['aceptado']++;
            break;
        case 'rechazado':
            $estadisticas['seguimiento']['rechazado']++;
            break;
        case 'no acudió':
            $estadisticas['seguimiento']['no_acudio']++;
            break;
        case 'contratado':
            $estadisticas['seguimiento']['contratado']++;
            break;
        case 'reingreso':
            $estadisticas['seguimiento']['reingreso']++;
            break;
        case 'primer día':
            $estadisticas['seguimiento']['primer_dia']++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bitácoras del Líder</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">📊 Análisis de Bitácoras</h2>

        <div class="row">
            <!-- Gráfico por estado -->
            <div class="col-md-6 mb-4">
                <h5 class="text-center">Citas por Estado</h5>
                <canvas id="estadoChart"></canvas>
            </div>

            <!-- Gráfico por empresa -->
            <div class="col-md-6 mb-4">
                <h5 class="text-center">Citas por Empresa</h5>
                <canvas id="empresaChart"></canvas>
            </div>

            <!-- Gráfico por fecha -->
            <div class="col-md-6 mb-4">
                <h5 class="text-center">Citas por Fecha (últimos 7 días)</h5>
                <canvas id="fechaChart"></canvas>
            </div>

            <!-- Seguimiento -->
            <div class="col-md-6 mb-4">
                <h5 class="text-center">Seguimiento de Citas</h5>
                <canvas id="seguimientoChart"></canvas>
            </div>
            <!-- Nueva Gráfica: Seguimiento por Responsable -->
            <div class="col-md-6 mb-4">
                <h5 class="text-center">Seguimiento por Responsable</h5>
                <canvas id="seguimientoResponsableChart"></canvas>
            </div>
        </div>
        <!-- 
        <a href="panel_lider.php" class="btn btn-secondary mt-4">⬅️ Volver al Panel</a> -->
    </div>

    <script>
        // Gráfica por estado
        new Chart(document.getElementById('estadoChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($estadisticas['estado'])) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($estadisticas['estado'])) ?>,
                    backgroundColor: ['#0d6efd', '#dc3545', '#ffc107', '#198754', '#6f42c1', '#fd7e14']
                }]
            }
        });

        // Gráfica por empresa
        new Chart(document.getElementById('empresaChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($estadisticas['empresa'])) ?>,
                datasets: [{
                    label: 'Citas',
                    data: <?= json_encode(array_values($estadisticas['empresa'])) ?>,
                    backgroundColor: '#0dcaf0'
                }]
            }
        });

        // Gráfica por fecha
        new Chart(document.getElementById('fechaChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($estadisticas['fechas'])) ?>,
                datasets: [{
                    label: 'Citas',
                    data: <?= json_encode(array_values($estadisticas['fechas'])) ?>,
                    fill: true,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: '#0d6efd'
                }]
            }
        });

        // Gráfica de seguimiento
        new Chart(document.getElementById('seguimientoChart'), {
            type: 'doughnut',
            data: {
                labels: ['Aceptado', 'Rechazado', 'No acudió', 'Contratado', 'Reingreso', 'Primer día'],
                datasets: [{
                    data: [
                        <?= $estadisticas['seguimiento']['aceptado'] ?>,
                        <?= $estadisticas['seguimiento']['rechazado'] ?>,
                        <?= $estadisticas['seguimiento']['no_acudio'] ?>,
                        <?= $estadisticas['seguimiento']['contratado'] ?>,
                        <?= $estadisticas['seguimiento']['reingreso'] ?>,
                        <?= $estadisticas['seguimiento']['primer_dia'] ?>
                    ],
                    backgroundColor: ['#198754', '#dc3545', '#6c757d', '#0d6efd', '#f39c12', '#6610f2']
                }]
            }
        });
        new Chart(document.getElementById('seguimientoResponsableChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($responsables) ?>,
                datasets: [{
                    label: 'Seguimientos Realizados',
                    data: <?= json_encode($totales) ?>,
                    backgroundColor: '#6f42c1'
                }]
            }
        });
    </script>
    <!-- Nueva gráfica: Seguimiento por Responsable -->
    <?php
    $res = $conexion->query("
    SELECT u.nombre, COUNT(*) AS total 
    FROM citas c
    JOIN usuarios u ON c.seguimiento_actualizado_por = u.id
    WHERE c.estado_seguimiento IS NOT NULL
    GROUP BY u.nombre
");
    $responsables = [];
    $totales = [];
    while ($r = $res->fetch_assoc()) {
        $responsables[] = $r['nombre'];
        $totales[] = $r['total'];
    }
    ?>


</body>

</html>