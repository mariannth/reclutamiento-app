<?php
include 'conexion.php';

$email = 'superadmin@admin.com';
$existe = $conexion->query("SELECT * FROM usuarios WHERE email = '$email'");

if ($existe->num_rows === 0) {
    $nombre = 'Super Admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $rol = 'superadmin';

    $conexion->query("INSERT INTO usuarios (nombre, email, password, rol) VALUES (
        '$nombre', '$email', '$password', '$rol'
    )");

    echo "Superadmin creado correctamente.<br>";
} else {
    echo "El superadmin ya existe.<br>";
}
