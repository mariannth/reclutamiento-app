// citas/lista.php
$query = $pdo->query("SELECT c.*, e.nombre AS empresa, ca.nombre AS candidato
FROM citas c
JOIN empresas e ON c.empresa_id = e.id
JOIN candidatos ca ON c.candidato_id = ca.id
ORDER BY c.fecha DESC");

while ($cita = $query->fetch()) {
echo "<tr>
    <td>{$cita['fecha']}</td>
    <td>{$cita['empresa']}</td>
    <td>{$cita['candidato']}</td>
    <td>{$cita['comentarios']}</td>
</tr>";
}