<?php
// === registro_usuario.php ===
session_start();
include 'includes/conexion.php';

// Permitir registro libre solo si no hay usuarios en la tabla
$hayUsuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'] > 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = ($hayUsuarios && isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin']))
        ? $_POST['rol']
        : 'usuario';

    // Verificar si ya existe el correo
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Usuario duplicado
        $stmt->close();
        header("Location: registro_usuario.php?error=duplicado");
        exit();
    }
    $stmt->close();

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $password, $rol);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php?success=usuario_creado");
        exit();
    } else {
        $stmt->close();
        header("Location: registro_usuario.php?error=insertar");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Registrar Usuario</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                switch ($_GET['error']) {
                    case 'duplicado':
                        echo "El correo ya está registrado.";
                        break;
                    case 'insertar':
                        echo "Ocurrió un error al registrar el usuario.";
                        break;
                    default:
                        echo "Error desconocido.";
                }
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo" required />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Correo" required />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required />
            </div>

            <?php if ($hayUsuarios && isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])): ?>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Administrador</option>
                        <option value="usuario">Usuario</option>
                        <option value="lider">Líder</option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="rol" value="usuario" />
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>

        <div class="mt-3 text-center">
            <a href="index.php" class="btn btn-secondary">Volver al inicio</a>
        </div>
    </div>
</body>

</html>
