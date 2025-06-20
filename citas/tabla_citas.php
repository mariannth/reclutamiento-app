<?php
// === panels/tabla_citas.php ===
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];
$hoy = date("Y-m-d");

// Obtener citas del usuario
$query = $conexion->prepare("SELECT c.*, e.cliente, e.sucursal FROM citas c 
    JOIN empresas e ON c.empresa_id = e.id 
    WHERE c.usuario_id = ? ORDER BY c.fecha DESC, c.hora DESC");
$query->bind_param("i", $usuario_id);
$query->execute();
$resultado = $query->get_result();
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
        <h3>Mis Citas Registradas</h3>

        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Empresa</th>
                    <th>Sucursal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cita = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($cita['nombre']) ?></td>
                        <td><?= $cita['fecha'] ?></td>
                        <td><?= $cita['hora'] ?></td>
                        <td><?= htmlspecialchars($cita['cliente']) ?></td>
                        <td><?= htmlspecialchars($cita['sucursal']) ?></td>
                        <td>
                            <!-- Solo editable si no ha pasado de las 11 PM -->
                            <?php
                            $horaActual = new DateTime();
                            $fechaCita = new DateTime($cita['fecha'] . ' ' . $cita['hora']);
                            $fechaCita->setTime(23, 0, 0); // Límite editable 11:00 PM

                            if ($horaActual < $fechaCita): ?>
                                <a href="../citas/editar_cita.php?id=<?= $cita['id'] ?>">✏️ Editar</a> |
                            <?php else: ?>
                                <span style="color:gray;">No editable</span> |
                            <?php endif; ?>

                            <!-- Botón para mostrar formato -->
                            <button onclick="parent.mostrarModalCita(<?= $cita['id'] ?>)">📄 Ver Formato</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>