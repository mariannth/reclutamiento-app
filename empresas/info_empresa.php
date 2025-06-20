<?php
include '../includes/conexion.php';

$resultado = $conexion->query("SELECT * FROM empresas");
echo "<h3>Información de Empresas</h3>";

while ($empresa = $resultado->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
    echo "<h4>{$empresa['cliente']} - {$empresa['sucursal']}</h4>";
    echo "<p><strong>Vacante:</strong> {$empresa['vacante']}</p>";
    echo "<p><strong>Dirección:</strong> {$empresa['direccion_entrevista']}</p>";
    echo "<p><strong>Responsables:</strong> {$empresa['responsables_entrevista']}</p>";
    echo "<p><strong>Requisitos:</strong> {$empresa['requisitos_adicionales']}</p>";
    echo "<a href='{$empresa['link_maps']}' target='_blank'>Ver en Google Maps</a>";
    echo "</div>";
}
?>
