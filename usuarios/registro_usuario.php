<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

$rol_actual = $_SESSION['usuario']['rol'];
if (!in_array($rol_actual, ['superadmin', 'admin', 'lider'])) {
    header("Location: ../panels/panel_superadmin.php?error=sin_permiso");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol_nuevo = $_POST['rol'] ?? 'usuario';

    if (empty($nombre) || empty($email) || empty($password)) {
        header("Location: ../panels/panel_superadmin.php?error=campos_vacios");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../panels/panel_superadmin.php?error=email_invalido");
        exit();
    }

    $roles_permitidos = [];
    if ($rol_actual === 'superadmin') {
        $roles_permitidos = ['superadmin', 'admin', 'lider', 'usuario'];
    } elseif ($rol_actual === 'admin') {
        $roles_permitidos = ['lider', 'usuario'];
    } elseif ($rol_actual === 'lider') {
        $roles_permitidos = ['usuario'];
    }

    if (!in_array($rol_nuevo, $roles_permitidos)) {
        header("Location: ../panels/panel_superadmin.php?error=rol_no_permitido");
        exit();
    }

    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: ../panels/panel_superadmin.php?error=email_existente");
        exit();
    }
    $stmt->close();

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $hash_password, $rol_nuevo);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../panels/panel_superadmin.php?success=usuario_creado");
        exit();
    } else {
        $stmt->close();
        header("Location: ../panels/panel_superadmin.php?error=error_crear_usuario");
        exit();
    }
} else {
    header("Location: ../panels/panel_superadmin.php");
    exit();
}
