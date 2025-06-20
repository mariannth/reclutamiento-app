<?php
// === citas/registro_cita.php ===
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}
include '../includes/conexion.php';

// Obtener empresas para el select
$empresas = $conexion->query("SELECT id, cliente, sucursal FROM empresas ORDER BY cliente ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa_id = $_POST['empresa_id'];
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $edad = $_POST['edad'];
    $escolaridad = $_POST['escolaridad'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $telefono = $_POST['telefono'];
    $ruta = $_POST['ruta'];
    $coordinador = $_POST['coordinador'];
    $reclutador = $_POST['reclutador'];

    // Verificar duplicados
    $consulta = $conexion->prepare("SELECT c.fecha, c.estado, e.cliente FROM citas c JOIN empresas e ON c.empresa_id = e.id WHERE c.nombre = ? AND c.apellido_paterno = ? AND c.apellido_materno = ? AND c.telefono = ?");
    $consulta->bind_param("ssss", $nombre, $apellido_paterno, $apellido_materno, $telefono);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        $fechas = [];
        $empresasCitadas = [];
        $estados = [];

        while ($row = $resultado->fetch_assoc()) {
            $fechas[] = $row['fecha'];
            $empresasCitadas[] = $row['cliente'];
            $estados[] = $row['estado'];
        }

        $fechasTexto = implode(", ", $fechas);
        $empresasTexto = implode(", ", array_unique($empresasCitadas));
        $estadosTexto = implode(", ", array_unique($estados));

        echo "<script>alert('El/La candidat@ con nombre $nombre $apellido_paterno $apellido_materno y teléfono $telefono, se citó el día $fechasTexto para la/s empresa(s) $empresasTexto. Precaución, este es su estatus: $estadosTexto');</script>";
    } else {
        $insertar = $conexion->prepare("INSERT INTO citas (empresa_id, nombre, apellido_paterno, apellido_materno, edad, escolaridad, fecha, hora, telefono, ruta, coordinador, reclutador, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE')");
        $insertar->bind_param("isssisssssss", $empresa_id, $nombre, $apellido_paterno, $apellido_materno, $edad, $escolaridad, $fecha, $hora, $telefono, $ruta, $coordinador, $reclutador);
        $insertar->execute();

        echo "<script>alert('Citado correctamente, consulta tu cita en la tabla');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cita</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="formulario-cita">
        <h2>Registrar Nueva Cita</h2>
        <form method="POST">
            <label>🔰 EMPRESA:</label>
            <select name="empresa_id" required>
                <option value="">Seleccione una empresa</option>
                <?php while($e = $empresas->fetch_assoc()): ?>
                    <option value="<?= $e['id'] ?>"><?= $e['cliente'] . " - " . $e['sucursal'] ?></option>
                <?php endwhile; ?>
            </select><br>

            <label>🔰 NOMBRE:</label>
            <input type="text" name="nombre" required><br>
            <label>🔰 APELLIDO PATERNO:</label>
            <input type="text" name="apellido_paterno" required><br>
            <label>🔰 APELLIDO MATERNO:</label>
            <input type="text" name="apellido_materno" required><br>
            <label>🔰 EDAD:</label>
            <input type="number" name="edad" required><br>
            <label>🔰 ESCOLARIDAD:</label>
            <input type="text" name="escolaridad" required><br>
            <label>🔰 FECHA:</label>
            <input type="date" name="fecha" required><br>
            <label>🔰 HORA:</label>
            <input type="time" name="hora" required><br>
            <label>🔰 TELÉFONO:</label>
            <input type="text" name="telefono" required><br>
            <label>🔰 RUTA (si aplica):</label>
            <input type="text" name="ruta"><br>
            <label>🔰 COORDINADOR:</label>
            <input type="text" name="coordinador" required><br>
            <label>🔰 RECLUTADOR:</label>
            <input type="text" name="reclutador" required><br>
            <br>
            <button type="submit">Registrar Cita</button>
        </form>
    </div>
</body>
</html>
