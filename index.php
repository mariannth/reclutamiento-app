<?php
session_start();
include 'includes/conexion.php';

$mostrarLogin = false;
$mensajeToast = '';
$tipoToast = 'danger'; // danger o success

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario;
            $mostrarLogin = true;
            $mensajeToast = "👋 Bienvenido, " . htmlspecialchars($usuario['nombre']) . ". Redirigiendo...";
            $tipoToast = 'success';

            // Redirección con delay en JS
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'panels/panel_{$usuario['rol']}.php';
                }, 2500);
            </script>";
        } else {
            $mostrarLogin = true;
            $mensajeToast = "⚠️ Contraseña incorrecta.";
        }
    } else {
        $mostrarLogin = true;
        $mensajeToast = "❌ Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Plataforma de Reclutamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style_index.css">
</head>
<body>

<!-- Toasts -->
<?php if ($mensajeToast): ?>
<div class="toast-container p-3">
    <div class="toast align-items-center text-white bg-<?= $tipoToast ?> border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= $mensajeToast ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container mt-5">
    <!-- Pantalla de bienvenida -->
    <div class="bienvenida" id="seccionBienvenida" style="<?= $mostrarLogin ? 'display:none;' : '' ?>">
        <h1 class="mb-4">¡Bienvenido a la Plataforma de Reclutamiento!</h1>
        <p>Gestiona tus entrevistas, empresas y candidatos de forma eficiente.</p>
        <button class="btn btn-recluta mt-4" onclick="mostrarLogin()">Iniciar Sesión</button>
    </div>

    <!-- Formulario de login -->
    <div id="formLogin" class="login-form <?= $mostrarLogin ? 'mostrar' : '' ?>">
        <h3 class="text-center mb-4">Iniciar Sesión</h3>
        <form method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-recluta w-100">Ingresar</button>
        </form>
        <div class="text-center mt-3">
            <p>¿No tienes cuenta? <a href="registro_usuarios.php">Regístrate aquí</a></p>
        </div>
    </div>
</div>

<script>
    function mostrarLogin() {
        document.getElementById('seccionBienvenida').style.display = 'none';
        document.getElementById('formLogin').classList.add('mostrar');
    }

    // Si hubo error o éxito, mostrar formulario automáticamente
    <?php if ($mostrarLogin): ?>
        document.addEventListener("DOMContentLoaded", () => {
            mostrarLogin();
        });
    <?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
