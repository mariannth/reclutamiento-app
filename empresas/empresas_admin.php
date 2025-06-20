<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'superadmin'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'nombre', 'contacto', 'cliente', 'sucursal', 'proceso_detallado', 'examen_medico',
        'responsables_entrevista', 'rutas_transporte', 'direccion_entrevista', 'link_maps',
        'vacante', 'sexo', 'escolaridad', 'edad', 'horarios', 'salario', 'prestaciones',
        'experiencia', 'aspecto_fisico', 'requisitos_adicionales', 'categorias', 'tiempo_planta',
        'banco_pago', 'semana_fondo', 'actividades', 'prestaciones_extra'
    ];

    $valores = [];
    foreach ($campos as $campo) {
        $valores[$campo] = $_POST[$campo] ?? null;
    }

    $cantidad_vacantes = intval($_POST['cantidad_vacantes'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;

    $docs = [
        'doc_solicitud', 'doc_ine', 'doc_acta', 'doc_curp', 'doc_nss', 'doc_rfc',
        'doc_domicilio', 'doc_estudios', 'doc_retencion'
    ];

    foreach ($docs as $doc) {
        $valores[$doc] = isset($_POST[$doc]) ? 1 : 0;
    }

    $sql = "
        INSERT INTO empresas (
            nombre, contacto, cliente, sucursal, proceso_detallado, examen_medico,
            responsables_entrevista, rutas_transporte, direccion_entrevista, link_maps,
            vacante, sexo, escolaridad, edad, horarios, salario, prestaciones,
            experiencia, aspecto_fisico, requisitos_adicionales, categorias, tiempo_planta,
            banco_pago, semana_fondo, actividades, prestaciones_extra,
            doc_solicitud, doc_ine, doc_acta, doc_curp, doc_nss, doc_rfc, doc_domicilio,
            doc_estudios, doc_retencion, cantidad_vacantes, activo
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?
        )
    ";

    $stmt = $conexion->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'ssssssssssssssssssssssssssiiiiiiiiii',
            $valores['nombre'], $valores['contacto'], $valores['cliente'], $valores['sucursal'], $valores['proceso_detallado'], $valores['examen_medico'],
            $valores['responsables_entrevista'], $valores['rutas_transporte'], $valores['direccion_entrevista'], $valores['link_maps'],
            $valores['vacante'], $valores['sexo'], $valores['escolaridad'], $valores['edad'], $valores['horarios'], $valores['salario'], $valores['prestaciones'],
            $valores['experiencia'], $valores['aspecto_fisico'], $valores['requisitos_adicionales'], $valores['categorias'], $valores['tiempo_planta'],
            $valores['banco_pago'], $valores['semana_fondo'], $valores['actividades'], $valores['prestaciones_extra'],
            $valores['doc_solicitud'], $valores['doc_ine'], $valores['doc_acta'], $valores['doc_curp'], $valores['doc_nss'], $valores['doc_rfc'], $valores['doc_domicilio'],
            $valores['doc_estudios'], $valores['doc_retencion'], $cantidad_vacantes, $activo
        );

        if ($stmt->execute()) {
            header("Location: ../panels/panel_admin.php?success=empresa_registrada");
            exit();
        } else {
            $error = "❌ Error al registrar la empresa: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "❌ Error en la preparación del query: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2 class="mb-4">📋 Registro de Empresa y Vacantes</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" class="row g-3">
        <?php
        function input($label, $name, $type = 'text') {
            echo "<div class='col-md-6'>
                    <label class='form-label'>$label</label>
                    <input type='$type' name='$name' class='form-control' required>
                  </div>";
        }

        input("Nombre", "nombre");
        input("Contacto", "contacto");
        input("Cliente", "cliente");
        input("Sucursal", "sucursal");
        input("Proceso Detallado", "proceso_detallado");
        input("Examen Médico (SI/NO)", "examen_medico");
        input("Responsables Entrevista", "responsables_entrevista");
        input("Rutas Transporte", "rutas_transporte");
        input("Dirección Entrevista", "direccion_entrevista");
        input("Link Google Maps", "link_maps");
        input("Vacante", "vacante");
        input("Sexo (H/M/Indistinto)", "sexo");
        input("Escolaridad", "escolaridad");
        input("Edad", "edad");
        input("Horarios", "horarios");
        input("Salario", "salario");
        input("Prestaciones", "prestaciones");
        input("Experiencia", "experiencia");
        input("Aspecto Físico", "aspecto_fisico");
        input("Requisitos Adicionales", "requisitos_adicionales");
        input("Categorías", "categorias");
        input("Tiempo en Planta", "tiempo_planta");
        input("Banco de Pago", "banco_pago");
        input("Semana de Fondo", "semana_fondo");
        input("Actividades", "actividades");
        input("Prestaciones Extra", "prestaciones_extra");

        echo "<div class='col-md-4'>
                <label class='form-label'>Cantidad de Vacantes</label>
                <input type='number' name='cantidad_vacantes' class='form-control' min='1' required>
              </div>";

        echo "<div class='col-md-2 form-check mt-4'>
                <input type='checkbox' class='form-check-input' name='activo' id='activo' checked>
                <label class='form-check-label' for='activo'>Activa</label>
              </div>";
        ?>

        <div class="col-12">
            <label class="form-label">📎 Documentos Requeridos</label><br>
            <?php
            $docs = [
                'doc_solicitud' => 'Solicitud',
                'doc_ine' => 'INE',
                'doc_acta' => 'Acta Nacimiento',
                'doc_curp' => 'CURP',
                'doc_nss' => 'NSS',
                'doc_rfc' => 'RFC',
                'doc_domicilio' => 'Comprobante Domicilio',
                'doc_estudios' => 'Certificado Estudios',
                'doc_retencion' => 'Carta Retención'
            ];
            foreach ($docs as $name => $label) {
                echo "<div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='$name' id='$name'>
                        <label class='form-check-label' for='$name'>$label</label>
                      </div>";
            }
            ?>
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-success">Registrar Empresa</button>
            <a href="../panels/panel_admin.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</body>
</html>
