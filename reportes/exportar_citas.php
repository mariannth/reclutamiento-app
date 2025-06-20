$empresa_id = $_GET['empresa_id'] ?? '';
if ($empresa_id !== '') {
    $condicion .= " AND c.empresa_id = " . intval($empresa_id);
}
