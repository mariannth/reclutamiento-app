<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'cliente',
        'sucursal',
        'proceso_detallado',
        'examen_medico',
        'responsables_entrevista',
        'rutas_transporte',
        'direccion_entrevista',
        'link_maps',
        'vacante',
        'sexo',
        'escolaridad',
        'edad',
        'horarios',
        'salario',
        'prestaciones',
        'experiencia',
        'aspecto_fisico',
        'requisitos_adicionales',
        'categorias',
        'tiempo_planta',
        'banco_pago',
        'semana_fondo',
        'actividades',
        'prestaciones_extra',
        'doc_solicitud',
        'doc_ine',
        'doc_acta',
        'doc_curp',
        'doc_nss',
        'doc_rfc',
        'doc_domicilio',
        'doc_estudios',
        'doc_retencion'
    ];

    $valores = [];
    foreach ($campos as $campo) {
        // Convertir checkboxs a 1 o 0
        if (strpos($campo, 'doc_') === 0) {
            $valores[$campo] = isset($_POST[$campo]) ? 1 : 0;
        } else {
            $valores[$campo] = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
        }
    }

    $sql = "
        INSERT INTO empresas (
            cliente, sucursal, proceso_detallado, examen_medico, responsables_entrevista,
            rutas_transporte, direccion_entrevista, link_maps, vacante, sexo, escolaridad,
            edad, horarios, salario, prestaciones, experiencia, aspecto_fisico,
            requisitos_adicionales, categorias, tiempo_planta, banco_pago, semana_fondo,
            actividades, prestaciones_extra,
            doc_solicitud, doc_ine, doc_acta, doc_curp, doc_nss,
            doc_rfc, doc_domicilio, doc_estudios, doc_retencion
        )VALUES (
    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,?,?
)    ";

    $stmt = $conexion->prepare($sql);

    // Verificar si hubo error al preparar
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    // Vincular parámetros
    $tipos = str_repeat('s', 24) . str_repeat('i', 9); // 24 strings + 9 enteros
    $stmt->bind_param($tipos, ...array_values($valores));

    // Ejecutar
    if ($stmt->execute()) {
        header("Location: ../panels/panel_superadmin.php");
        exit();
    } else {
        echo "Error al registrar empresa: " . $stmt->error;
    }
}
