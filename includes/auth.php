<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

function soloAdmin() {
    if ($_SESSION['usuario']['rol'] !== 'admin') {
        header("Location: dashboard.php");
        exit;
    }
}
?>
