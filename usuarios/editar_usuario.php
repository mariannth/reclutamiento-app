<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['superadmin', 'admin'])) {
    header('Location: ../index.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: ../panels/panel_superadmin.php?error=id_invalido");
    exit();
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: ../panels/panel_superadmin.php?error=usuario_no_encontrado");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $nueva_password = $_POST['password'];

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../usuarios/editar_usuario.php?id=$id&error=email_invalido");
        exit();
    }

    // Validar rol permitido por el usuario actual
    $rol_actual = $_SESSION['usuario']['rol'];
    $roles_permitidos = [];

    if ($rol_actual === 'superadmin') {
        $roles_permitidos = ['superadmin', 'admin', 'lider', 'usuario'];
    } elseif ($rol_actual === 'admin') {
        $roles_permitidos = ['lider', 'usuario'];
    }

    if (!in_array($rol, $roles_permitidos)) {
        header("Location: ../usuarios/editar_usuario.php?id=$id&error=rol_no_permitido");
        exit();
    }

    // Si la contraseña se proporciona, la actualiza
    if (!empty($nueva_password)) {
        $hash_password = password_hash($nueva_password, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $email, $rol, $hash_password, $id);
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../panels/panel_superadmin.php?success=usuario_actualizado");
    } else {
        $stmt->close();
        header("Location: ../usuarios/editar_usuario.php?id=$id&error=error_actualizar");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Editar Usuario</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <?php
                $rol_actual = $_SESSION['usuario']['rol'];
                $roles_disponibles = [];

                if ($rol_actual === 'superadmin') {
                    $roles_disponibles = ['superadmin', 'admin', 'lider', 'usuario'];
                } elseif ($rol_actual === 'admin') {
                    $roles_disponibles = ['lider', 'usuario'];
                }

                foreach ($roles_disponibles as $rol_opcion) {
                    $selected = ($usuario['rol'] == $rol_opcion) ? 'selected' : '';
                    echo "<option value='$rol_opcion' $selected>" . ucfirst($rol_opcion) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="../panels/panel_superadmin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
