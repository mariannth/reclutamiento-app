<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'lider') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $vacante = $_POST['vacante'];
    $horarios = $_POST['horarios'];
    $cantidad = (int)$_POST['cantidad_vacantes'];

    $stmt = $conexion->prepare("UPDATE empresas SET vacante = ?, horarios = ?, cantidad_vacantes = ? WHERE id = ?");
    $stmt->bind_param("ssii", $vacante, $horarios, $cantidad, $id);
    $stmt->execute();

    header("Location: empresas_lider.php?success=actualizada");
    exit();
}
