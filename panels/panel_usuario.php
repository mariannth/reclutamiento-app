<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1100px;
        }

        nav {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        iframe {
            border: none;
            min-height: 700px;
        }

        .modal-custom {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content-custom {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-4">👤 Panel de Usuario</h2>

        <nav>
            <button class="btn btn-primary" onclick="mostrarSeccion('registro')">📅 Registrar Cita</button>
            <button class="btn btn-secondary" onclick="mostrarSeccion('empresas')">🏢 Información Empresas</button>
            <button class="btn btn-info text-white" onclick="mostrarSeccion('citas')">📋 Citas Registradas</button>
            <a href="../logout.php" class="btn btn-outline-danger ms-auto">🔒 Cerrar sesión</a>
        </nav>

        <section id="registro" style="display:none">
            <iframe src="../citas/registro_cita.php" width="100%"></iframe>
        </section>

        <section id="empresas" style="display:none">
            <iframe src="../empresas/info_empresa.php" width="100%"></iframe>
        </section>

        <section id="citas" style="display:none">
            <iframe src="../citas/tabla_citas.php" width="100%"></iframe>
        </section>
    </div>

    <!-- Modal Flotante -->
    <div id="modalFormatoCita" class="modal-custom">
        <div class="modal-content-custom">
            <span onclick="cerrarModal()" class="close-btn">❌</span>
            <div id="contenidoModalCita">
                <!-- Aquí se carga el contenido -->
            </div>
            <button onclick="copiarContenido()" class="btn btn-success mt-3 w-100">📋 Copiar Información</button>
        </div>
    </div>

    <script>
        function mostrarSeccion(id) {
            const secciones = ['registro', 'empresas', 'citas'];
            secciones.forEach(seccion => {
                document.getElementById(seccion).style.display = 'none';
            });
            document.getElementById(id).style.display = 'block';
        }

        function mostrarModalCita(id) {
            fetch('../citas/obtener_formato_cita.php?id=' + id)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('contenidoModalCita').innerHTML = html;
                    document.getElementById('modalFormatoCita').classList.add('d-flex');
                });
        }

        function cerrarModal() {
            document.getElementById('modalFormatoCita').classList.remove('d-flex');
        }

        function copiarContenido() {
            const texto = document.getElementById('contenidoModalCita').innerText;
            navigator.clipboard.writeText(texto).then(() => {
                alert("✅ Formato copiado al portapapeles.");
            });
        }
    </script>
</body>

</html>
