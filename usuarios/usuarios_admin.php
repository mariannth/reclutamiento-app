<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header("Location: ../index.php");
    exit();
}

// Evitar eliminar al Superadmin
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    // Verificar si el usuario a eliminar no es superadmin
    $verificar = $conexion->query("SELECT rol FROM usuarios WHERE id = $id")->fetch_assoc();
    if ($verificar && $verificar['rol'] !== 'superadmin') {
        $conexion->query("DELETE FROM usuarios WHERE id = $id");
        header("Location: usuarios_admin.php?success=eliminado");
        exit();
    } else {
        header("Location: usuarios_admin.php?error=no_autorizado");
        exit();
    }
}

// Agregar nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    if ($rol !== 'superadmin') { // prevenir crear superadmin desde este panel
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $password, $rol);
        $stmt->execute();
        $stmt->close();
        header("Location: usuarios_admin.php?success=creado");
        exit();
    }
}

$usuarios = $conexion->query("SELECT * FROM usuarios WHERE rol != 'superadmin'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>👤 Gestión de Usuarios</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Operación exitosa</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">❌ Acción no autorizada</div>
    <?php endif; ?>

    <form method="POST" class="row g-2 mb-4">
        <div class="col-md-3"><input type="text" name="nombre" placeholder="Nombre" required class="form-control"></div>
        <div class="col-md-3"><input type="email" name="email" placeholder="Correo" required class="form-control"></div>
        <div class="col-md-2"><input type="password" name="password" placeholder="Contraseña" required class="form-control"></div>
        <div class="col-md-2">
            <select name="rol" class="form-select" required>
                <option value="usuario">Usuario</option>
                <option value="lider">Líder</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-success w-100">Agregar</button></div>
    </form>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Acción</th></tr>
        </thead>
        <tbody>
            <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= ucfirst($u['rol']) ?></td>
                    <td>
                        <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
     <a href="../panels/panel_admin.php" class="btn btn-secondary mt-3">⬅️ Volver al Panel Admin</a>
</body>
</html>
