<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header("Location: ../index.php");
    exit();
}

// Acción de eliminar empresa
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM empresas WHERE id = $idEliminar");
    header("Location: empresas_admin_gestion.php?success=eliminada");
    exit();
}

// Acción de actualizar activa desde tabla
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_activa'])) {
    $empresa_id = intval($_POST['empresa_id']);
    $activa = isset($_POST['activa']) ? 1 : 0;

    $stmt = $conexion->prepare("UPDATE empresas SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $activa, $empresa_id);
    $stmt->execute();
    $stmt->close();

    header("Location: empresas_admin_gestion.php?success=estado_actualizado");
    exit();
}

// Obtener empresas
$empresas = $conexion->query("SELECT id, cliente, sucursal, cantidad_vacantes, activo FROM empresas ORDER BY cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h2 class="mb-4">🏢 Gestión de Empresas</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            switch ($_GET['success']) {
                case 'eliminada': echo '✅ Empresa eliminada correctamente.'; break;
                case 'estado_actualizado': echo '🔄 Estado de visibilidad actualizado.'; break;
                case 'actualizada': echo '✅ Empresa actualizada.'; break;
            }
            ?>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <a href="empresas_admin.php" class="btn btn-success mb-3">➕ Registrar Nueva Empresa</a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Empresa</th>
                    <th>Sucursal</th>
                    <th>Vacantes</th>
                    <th>Activa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($empresa = $empresas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($empresa['cliente']) ?></td>
                        <td><?= htmlspecialchars($empresa['sucursal']) ?></td>
                        <td><?= $empresa['cantidad_vacantes'] ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                                <input type="checkbox" name="activa" onchange="this.form.submit()" <?= $empresa['activo'] ? 'checked' : '' ?>>
                                <input type="hidden" name="actualizar_activa" value="1">
                            </form>
                        </td>
                        <td>
                            <a href="empresas_admin.php?id=<?= $empresa['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                            <a href="?eliminar=<?= $empresa['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta empresa?')">🗑️ Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="../panels/panel_admin.php" class="btn btn-secondary mt-4">⬅️ Volver al Panel Admin</a>

</body>
</html>
