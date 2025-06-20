<?php
include '../includes/conexion.php';

$id = $_GET['id'];
$empresa = $conexion->query("SELECT * FROM empresas WHERE id = $id")->fetch_assoc();

if ($empresa) {
    echo "
    <h4>🏭{$empresa['cliente']}🏭<br><small>{$empresa['ubicacion']}</small></h4>
    <p>👷🏻 {$empresa['puesto']}<br>
    Sexo: {$empresa['sexo']}<br>
    Escolaridad: {$empresa['escolaridad']}<br>
    Edad: {$empresa['edad']}<br><br>

    💸 <b>SALARIO</b><br>
    💲 Día: {$empresa['salario_dia']}<br>
    💲 Semana: {$empresa['salario_semana']}<br><br>

    📋 Actividades: {$empresa['actividades']}<br><br>

    🕒 <b>HORARIOS</b><br>
    {$empresa['horarios']}<br><br>

    🎁 <b>OFRECEMOS:</b><br>
    {$empresa['prestaciones']}<br><br>

    📍 Dirección: {$empresa['direccion']}<br>
    <a href='{$empresa['mapa']}' target='_blank'>📍 Ver en Google Maps</a>
    </p>";
} else {
    echo "❌ Empresa no encontrada";
}
?>
