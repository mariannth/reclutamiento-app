<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Validar que venga el id por GET
if (!isset($_GET['id'])) {
    header("Location: tabla_citas.php");
    exit();
}

$id = intval($_GET['id']);

// Función para determinar si la cita es editable
function esEditable($empresa, $fecha, $hora) {
    $horaActual = new DateTime();
    $fechaHoraCita = new DateTime("$fecha $hora");
    $diaSemana = (int)$fechaHoraCita->format('N'); // 1=Lunes ... 7=Domingo

    if ($empresa === 'CDS') {
        if ($diaSemana < 2 || $diaSemana > 5) return false;
        $limite = new DateTime("$fecha 17:40:00");
        return $horaActual < $limite;
    } else {
        if ($diaSemana < 1 || $diaSemana > 5) return false;
        $limite = new DateTime("$fecha 23:00:00");
        return $horaActual < $limite;
    }
}

// Obtener la cita para verificar existencia y permisos
$stmt = $conexion->prepare("SELECT c.*, e.cliente FROM citas c JOIN empresas e ON c.empresa_id = e.id WHERE c.id = ? AND c.usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Cita no encontrada o no pertenece al usuario
    header("Location: tabla_citas.php");
    exit();
}

$cita = $result->fetch_assoc();

// Validar si es editable según reglas
if (!esEditable($cita['cliente'], $cita['fecha'], $cita['hora'])) {
    echo "<script>alert('Esta cita ya no es editable según las políticas establecidas.'); window.location.href = 'tabla_citas.php';</script>";
    exit();
}

// Procesar actualización si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido_paterno = $_POST['apellido_paterno'] ?? '';
    $apellido_materno = $_POST['apellido_materno'] ?? '';
    $edad = intval($_POST['edad'] ?? 0);
    $escolaridad = $_POST['escolaridad'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $ruta = $_POST['ruta'] ?? '';
    $coordinador = $_POST['coordinador'] ?? '';
    $reclutador = $_POST['reclutador'] ?? '';

    // Validar que la fecha y hora estén dentro del rango editable para la empresa
    if (!esEditable($cita['cliente'], $fecha, $hora)) {
        echo "<script>alert('La nueva fecha/hora no cumple con las políticas de edición.');</script>";
    } else {
        // Actualizar cita en BD
        $update = $conexion->prepare("UPDATE citas SET nombre=?, apellido_paterno=?, apellido_materno=?, edad=?, escolaridad=?, fecha=?, hora=?, telefono=?, ruta=?, coordinador=?, reclutador=? WHERE id=? AND usuario_id=?");
        $update->bind_param(
            "sssissssssiii",
            $nombre,
            $apellido_paterno,
            $apellido_materno,
            $edad,
            $escolaridad,
            $fecha,
            $hora,
            $telefono,
            $ruta,
            $coordinador,
            $reclutador,
            $id,
            $usuario_id
        );

        if ($update->execute()) {
            echo "<script>alert('✅ Cita actualizada correctamente.'); window.location.href = 'tabla_citas.php';</script>";
            exit();
        } else {
            echo "<script>alert('❌ Error al actualizar la cita: " . $update->error . "');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Editar Cita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/css/styles.css" />
</head>

<body>
    <div class="container mt-4">
        <h3>Editar Cita</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input id="nombre" name="nombre" type="text" class="form-control" required value="<?= htmlspecialchars($cita['nombre']) ?>" />
            </div>

            <div class="mb-3">
                <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                <input id="apellido_paterno" name="apellido_paterno" type="text" class="form-control" required value="<?= htmlspecialchars($cita['apellido_paterno']) ?>" />
            </div>

            <div class="mb-3">
                <label for="apellido_materno" class="form-label">Apellido Materno</label>
                <input id="apellido_materno" name="apellido_materno" type="text" class="form-control" required value="<?= htmlspecialchars($cita['apellido_materno']) ?>" />
            </div>

            <div class="mb-3">
                <label for="edad" class="form-label">Edad</label>
                <input id="edad" name="edad" type="number" class="form-control" required min="1" value="<?= htmlspecialchars($cita['edad']) ?>" />
            </div>

            <div class="mb-3">
                <label for="escolaridad" class="form-label">Escolaridad</label>
                <input id="escolaridad" name="escolaridad" type="text" class="form-control" required value="<?= htmlspecialchars($cita['escolaridad']) ?>" />
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input id="fecha" name="fecha" type="date" class="form-control" required value="<?= $cita['fecha'] ?>" />
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label">Hora</label>
                <input id="hora" name="hora" type="time" class="form-control" required value="<?= $cita['hora'] ?>" />
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input id="telefono" name="telefono" type="text" class="form-control" required value="<?= htmlspecialchars($cita['telefono']) ?>" />
            </div>

            <div class="mb-3">
                <label for="ruta" class="form-label">Ruta (opcional)</label>
                <input id="ruta" name="ruta" type="text" class="form-control" value="<?= htmlspecialchars($cita['ruta']) ?>" />
            </div>

            <div class="mb-3">
                <label for="coordinador" class="form-label">Coordinador</label>
                <input id="coordinador" name="coordinador" type="text" class="form-control" required value="<?= htmlspecialchars($cita['coordinador']) ?>" />
            </div>

            <div class="mb-3">
                <label for="reclutador" class="form-label">Reclutador</label>
                <input id="reclutador" name="reclutador" type="text" class="form-control" required value="<?= htmlspecialchars($cita['reclutador']) ?>" />
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Cita</button>
            <a href="tabla_citas.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>
