<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

$empresas = $conexion->query("SELECT * FROM empresas ORDER BY cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Empresas - Panel del Líder</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-control[readonly] {
            background-color: #e9ecef;
        }
        .editable-field {
            display: flex;
            align-items: center;
        }
        .editable-field i {
            margin-left: 10px;
            cursor: pointer;
            color: #0d6efd;
        }
    </style>
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2 class="mb-4">🏢 Empresas Registradas</h2>
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Sucursal</th>
                    <th>Vacante</th>
                    <th>Horario</th>
                    <th>Activa</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($empresa = $empresas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $empresa['cliente'] ?></td>
                        <td><?= $empresa['sucursal'] ?></td>
                        <td><?= $empresa['vacante'] ?></td>
                        <td><?= $empresa['horarios'] ?></td>
                        <td>
                            <?= $empresa['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?>
                        </td>
                        <td>
                            <?php if ($empresa['activo']): ?>
                                <button class="btn btn-sm btn-primary" onclick="mostrarFormulario(<?= $empresa['id'] ?>)">Editar información</button>
                            <?php else: ?>
                                <span class="text-muted">Inactiva</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr id="form-<?= $empresa['id'] ?>" style="display:none;">
                        <td colspan="6">
                            <form method="POST" action="editar_empresa_lider.php" class="bg-light p-3 rounded border">
                                <input type="hidden" name="id" value="<?= $empresa['id'] ?>">

                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label>Horario</label>
                                        <div class="editable-field">
                                            <input type="text" name="horarios" class="form-control" value="<?= $empresa['horarios'] ?>" readonly>
                                            <i class="bi bi-pencil-square" onclick="habilitarCampo(this)"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Vacante Activa</label>
                                        <div class="editable-field">
                                            <input type="text" name="vacante" class="form-control" value="<?= $empresa['vacante'] ?>" readonly>
                                            <i class="bi bi-pencil-square" onclick="habilitarCampo(this)"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Vacantes Disponibles</label>
                                        <div class="editable-field">
                                            <input type="number" name="cantidad_vacantes" class="form-control" value="<?= $empresa['cantidad_vacantes'] ?? 0 ?>" readonly>
                                            <i class="bi bi-pencil-square" onclick="habilitarCampo(this)"></i>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                <button type="button" class="btn btn-secondary" onclick="ocultarFormulario(<?= $empresa['id'] ?>)">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script>
        function mostrarFormulario(id) {
            document.getElementById('form-' + id).style.display = 'table-row';
        }

        function ocultarFormulario(id) {
            document.getElementById('form-' + id).style.display = 'none';
        }

        function habilitarCampo(icono) {
            const input = icono.parentElement.querySelector('input');
            input.removeAttribute('readonly');
            input.focus();
        }
    </script>
</body>
</html>
