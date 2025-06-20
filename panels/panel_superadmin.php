<?php
// === panels/panel_superadmin.php ===
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'superadmin') {
    header("Location: ../index.php");
    exit();
}
include '../includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Superadmin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container mt-4">
    <h2>Panel de Superadministrador</h2>

    <!-- ALERTAS -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <?php
            switch ($_GET['error']) {
                case 'sin_permiso':
                    echo "No tienes permiso para acceder a esta sección.";
                    break;
                case 'campos_vacios':
                    echo "Por favor, completa todos los campos requeridos.";
                    break;
                case 'email_invalido':
                    echo "El correo electrónico no es válido.";
                    break;
                case 'rol_no_permitido':
                    echo "No tienes permiso para asignar ese rol.";
                    break;
                case 'email_existente':
                    echo "El correo electrónico ya está registrado.";
                    break;
                case 'error_crear_usuario':
                    echo "Ocurrió un error al crear el usuario. Intenta de nuevo.";
                    break;
                default:
                    echo "Error desconocido.";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Éxito:</strong>
            <?php
            switch ($_GET['success']) {
                case 'usuario_creado':
                    echo "Usuario creado correctamente.";
                    break;
                default:
                    echo "Operación realizada con éxito.";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Navegación -->
    <nav class="mb-4">
        <button class="btn btn-primary me-2" onclick="mostrarSeccion('usuarios')">Gestión de Usuarios</button>
        <button class="btn btn-primary me-2" onclick="mostrarSeccion('empresas')">Gestión de Empresas</button>
        <button class="btn btn-primary" onclick="mostrarSeccion('reportes')">Reportes y Bitácoras</button>
        <a href="../logout.php" class="btn btn-danger float-end">Cerrar sesión</a>
    </nav>

    <!-- Secciones -->
    <section id="usuarios" style="display:none">
        <h3>Registrar / Actualizar / Eliminar Usuarios</h3>
        <form action="../usuarios/registro_usuario.php" method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" name="nombre" placeholder="Nombre" required class="form-control">
            </div>
            <div class="mb-3">
                <input type="email" name="email" placeholder="Correo" required class="form-control">
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Contraseña" required class="form-control">
            </div>
            <div class="mb-3">
                <select name="rol" required class="form-select">
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Usuario</option>
                    <option value="lider">Líder</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Registrar</button>
        </form>
        <?php
        $usuarios = $conexion->query("SELECT * FROM usuarios");
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr></thead><tbody>";
        while ($u = $usuarios->fetch_assoc()) {
            echo "<tr>
                    <td>{$u['nombre']}</td>
                    <td>{$u['email']}</td>
                    <td>{$u['rol']}</td>
                    <td>
                        <a href='../usuarios/editar_usuario.php?id={$u['id']}' class='btn btn-sm btn-primary me-1'>Editar</a>
                        <a href='../usuarios/eliminar_usuario.php?id={$u['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</a>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
        ?>
    </section>

    <section id="empresas" style="display:none">
        <h3>Registrar / Actualizar / Eliminar Empresas</h3>
        <form action="../empresas/nueva.php" method="POST" class="mb-4">
            <!-- Aquí agrega tus inputs con clases Bootstrap -->
            <input type="text" name="cliente" placeholder="Cliente" required class="form-control mb-2">
            <input type="text" name="sucursal" placeholder="Sucursal" required class="form-control mb-2">
            <textarea name="proceso_detallado" placeholder="Proceso detallado" class="form-control mb-2"></textarea>
            <!-- Resto de inputs con clases similares -->
            <label>¿Examen Médico?</label>
            <select name="examen_medico" class="form-select mb-2">
                <option value="SI">Sí</option>
                <option value="NO">No</option>
            </select>
            <!-- Puedes continuar agregando el resto igual -->
            <button type="submit" class="btn btn-success">Registrar Empresa</button>
        </form>
        <?php
        $empresas = $conexion->query("SELECT * FROM empresas");
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>Cliente</th><th>Sucursal</th><th>Vacante</th><th>Acciones</th></tr></thead><tbody>";
        while ($e = $empresas->fetch_assoc()) {
            echo "<tr>
                    <td>{$e['cliente']}</td>
                    <td>{$e['sucursal']}</td>
                    <td>{$e['vacante']}</td>
                    <td>
                        <a href='editar_empresa.php?id={$e['id']}' class='btn btn-sm btn-primary me-1'>Editar</a>
                        <a href='eliminar_empresa.php?id={$e['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</a>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
        ?>
    </section>

    <section id="reportes" style="display:none">
        <h3>Bitácoras e Informes</h3>
        <a href="generar_bitacora.php" class="btn btn-outline-primary mb-2">Generar Bitácora General</a><br>
        <a href="graficas_usuario.php" class="btn btn-outline-primary mb-2">Ver gráficas por usuario</a><br>
        <a href="graficas_empresa.php" class="btn btn-outline-primary">Ver gráficas por empresa</a>
    </section>
</div>

<script>
    function mostrarSeccion(id) {
        ['usuarios', 'empresas', 'reportes'].forEach(sec => {
            document.getElementById(sec).style.display = (sec === id) ? 'block' : 'none';
        });
    }
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
