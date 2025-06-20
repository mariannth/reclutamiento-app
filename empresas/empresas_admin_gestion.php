<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header("Location: ../index.php");
    exit();
}

// Actualización desde formulario si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['empresa_id'];
    $vacantes = $_POST['cantidad_vacantes'];
    $activa = isset($_POST['activa']) ? 1 : 0;

    $stmt = $conexion->prepare("UPDATE empresas SET cantidad_vacantes = ?, activo = ? WHERE id = ?");
    $stmt->bind_param("iii", $vacantes, $activa, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: empresas_admin_gestion.php?success=actualizada");
    exit();
}

// Obtener todas las empresas
$empresas = $conexion->query("SELECT id, cliente, sucursal, cantidad_vacantes, activo FROM empresas ORDER BY cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Rápida de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-inline-edit input[readonly] {
            background-color: #f8f9fa;
            border: none;
        }
        .form-inline-edit .edit-btn {
            cursor: pointer;
        }
    </style>
</head>
<body class="container py-4">
    <h2 class="mb-4">🏢 Gestión Rápida de Empresas</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Empresa actualizada correctamente.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Empresa</th>
                    <th>Sucursal</th>
                    <th>Cantidad Vacantes</th>
                    <th>Activa</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($empresa = $empresas->fetch_assoc()): ?>
                    <tr>
                        <form method="POST" class="form-inline-edit">
                            <td><?= htmlspecialchars($empresa['cliente']) ?></td>
                            <td><?= htmlspecialchars($empresa['sucursal']) ?></td>
                            <td>
                                <input type="number" name="cantidad_vacantes" value="<?= $empresa['cantidad_vacantes'] ?>" readonly class="form-control form-control-sm">
                            </td>
                            <td>
                                <input type="checkbox" name="activa" <?= $empresa['activo'] ? 'checked' : '' ?> disabled class="form-check-input">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-btn">🖉</button>
                                <button type="submit" class="btn btn-sm btn-success d-none guardar-btn">Guardar</button>
                                <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="../panels/panel_admin.php" class="btn btn-secondary mt-3">⬅️ Volver al Panel Admin</a>

    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('form');
                row.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                });
                row.querySelector('.guardar-btn').classList.remove('d-none');
                button.classList.add('d-none');
            });
        });
    </script>
</body>
</html>
