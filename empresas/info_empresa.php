<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

// Obtener empresas activas
$resultado = $conexion->query("SELECT id, cliente, sucursal FROM empresas WHERE activo = 1 ORDER BY cliente");
$empresas = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información de Empresas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .tabla-empresa th {
            background-color: #000;
            color: #fff;
        }
        .tabla-empresa td, .tabla-empresa th {
            padding: 5px 10px;
            border: 1px solid #dee2e6;
        }
        .seccion-titulo {
            background-color: #f1f1f1;
            font-weight: bold;
            padding: 5px;
        }
    </style>
</head>
<body class="container py-4">
    <h4 class="mb-3">🏢 Información de Empresa</h4>

    <div class="mb-3">
        <label for="empresaSeleccionada" class="form-label">Selecciona una empresa:</label>
        <select class="form-select" id="empresaSeleccionada" onchange="mostrarInfoEmpresa(this.value)">
            <option value="">-- Selecciona --</option>
            <?php foreach ($empresas as $empresa): ?>
                <option value="<?= $empresa['id'] ?>">
                    <?= $empresa['cliente'] ?> - <?= $empresa['sucursal'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="infoEmpresa" style="display: none;">
        <table class="table tabla-empresa table-bordered" id="tablaContenido">
            <!-- Aquí se carga la información -->
        </table>
    </div>

    <script>
        function mostrarInfoEmpresa(id) {
            if (!id) {
                document.getElementById('infoEmpresa').style.display = 'none';
                return;
            }

            fetch("obtener_empresa.php?id=" + id)
                .then(res => res.json())
                .then(data => {
                    const tabla = document.getElementById('tablaContenido');
                    tabla.innerHTML = `
                        <tr><th>CLIENTE:</th><td>${data.cliente}</td></tr>
                        <tr><th>SUCURSAL:</th><td>${data.sucursal}</td></tr>
                        <tr><th colspan="2" class="seccion-titulo">Proceso Detallado</th></tr>
                        <tr><th>Vacante:</th><td>${data.vacante}</td></tr>
                        <tr><th>Examen Médico:</th><td>${data.examen_medico}</td></tr>
                        <tr><th>Responsables Entrevista:</th><td>${data.responsables_entrevista}</td></tr>
                        <tr><th>Rutas Transporte:</th><td>${data.rutas_transporte}</td></tr>
                        <tr><th>Dirección Entrevista:</th><td>${data.direccion_entrevista}</td></tr>
                        <tr><th>Link Google Maps:</th><td><a href="${data.link_maps}" target="_blank">${data.link_maps}</a></td></tr>
                        <tr><th colspan="2" class="seccion-titulo">Requisitos</th></tr>
                        <tr><th>Sexo:</th><td>${data.sexo}</td></tr>
                        <tr><th>Escolaridad:</th><td>${data.escolaridad}</td></tr>
                        <tr><th>Horarios:</th><td>${data.horarios}</td></tr>
                        <tr><th>Edad:</th><td>${data.edad}</td></tr>
                        <tr><th>Salario:</th><td>${data.salario}</td></tr>
                        <tr><th>Prestaciones:</th><td>${data.prestaciones}</td></tr>
                        <tr><th>Experiencia:</th><td>${data.experiencia}</td></tr>
                        <tr><th>Aspecto Físico:</th><td>${data.aspecto_fisico}</td></tr>
                        <tr><th>Requisitos Adicionales:</th><td>${data.requisitos_adicionales}</td></tr>
                        <tr><th>Categorías:</th><td>${data.categorias}</td></tr>
                        <tr><th>Tiempo en la planta:</th><td>${data.tiempo_planta}</td></tr>
                        <tr><th>Banco de Pago:</th><td>${data.banco_pago}</td></tr>
                        <tr><th>Semana de Fondo:</th><td>${data.semana_fondo}</td></tr>
                        <tr><th>Actividades:</th><td>${data.actividades}</td></tr>
                        <tr><th colspan="2" class="seccion-titulo">Prestaciones Extra</th></tr>
                        <tr><th>Prestaciones Extra:</th><td>${data.prestaciones_extra}</td></tr>
                        <tr><th colspan="2" class="seccion-titulo">Documentos Requeridos</th></tr>
                        ${generarDocs(data)}
                    `;
                    document.getElementById('infoEmpresa').style.display = 'block';
                });
        }

        function generarDocs(data) {
            const docs = {
                doc_solicitud: "Solicitud",
                doc_ine: "INE",
                doc_acta: "Acta de nacimiento",
                doc_curp: "CURP",
                doc_nss: "NSS",
                doc_rfc: "RFC",
                doc_domicilio: "Comprobante domicilio",
                doc_estudios: "Comprobante estudios",
                doc_retencion: "Carta retención"
            };

            let html = "";
            for (const [campo, label] of Object.entries(docs)) {
                html += `<tr><th>${label}:</th><td>${data[campo] == 1 ? "✅ Requerido" : "❌ No requerido"}</td></tr>`;
            }
            return html;
        }
    </script>
    
</body>
</html>
