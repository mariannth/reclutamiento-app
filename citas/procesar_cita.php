<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

$empresas = $conexion->query("SELECT id, cliente, sucursal, vacante FROM empresas ORDER BY cliente ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h3>Registrar Nueva Cita</h3>
        <form action="procesar_cita.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">

            <label for="nombre">Nombre del candidato:</label><br>
            <input type="text" name="nombre" id="nombre" required><br><br>

            <label for="telefono">Teléfono:</label><br>
            <input type="tel" name="telefono" id="telefono" required pattern="[0-9]{10}" placeholder="10 dígitos"><br><br>

            <label for="correo">Correo electrónico:</label><br>
            <input type="email" name="correo" id="correo" required><br><br>

            <label for="empresa_id">Empresa / Sucursal / Vacante:</label><br>
            <select name="empresa_id" id="empresa_id" required>
                <option value="">-- Seleccionar --</option>
                <?php while ($e = $empresas->fetch_assoc()): ?>
                    <option value="<?= $e['id'] ?>">
                        <?= htmlspecialchars($e['cliente'] . " - " . $e['sucursal'] . " - " . $e['vacante']) ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label for="fecha">Fecha:</label><br>
            <input type="date" name="fecha" id="fecha" required min="<?= date('Y-m-d') ?>"><br><br>

            <label for="hora">Hora:</label><br>
            <input type="time" name="hora" id="hora" required><br><br>

            <button type="submit">Guardar Cita</button>
        </form>
    </div>
</body>
</html>
