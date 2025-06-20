<?php
// === panels/panel_usuario.php ===
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
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Panel de Usuario</h2>
        <nav>
            <button onclick="mostrarSeccion('registro')">Registrar Cita</button>
            <button onclick="mostrarSeccion('empresas')">Información Empresas</button>
            <button onclick="mostrarSeccion('citas')">Citas Registradas</button>
            <a href="../logout.php">Cerrar sesión</a>
        </nav>

        <section id="registro" style="display:none">
            <iframe src="../citas/registro_cita.php" width="100%" height="800px" frameborder="0"></iframe>
        </section>

        <section id="empresas" style="display:none">
            <iframe src="../empresas/info_empresa.php" width="100%" height="800px" frameborder="0"></iframe>
        </section>

        <section id="citas" style="display:none">
            <iframe src="../citas/tabla_citas.php" width="100%" height="800px" frameborder="0"></iframe>
        </section>
    </div>
    <script>
        function mostrarSeccion(id) {
            document.getElementById('registro').style.display = 'none';
            document.getElementById('empresas').style.display = 'none';
            document.getElementById('citas').style.display = 'none';
            document.getElementById(id).style.display = 'block';
        }

        function mostrarModalCita(id) {
            fetch('../citas/obtener_formato_cita.php?id=' + id)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('contenidoModalCita').innerHTML = html;
                    document.getElementById('modalFormatoCita').style.display = 'flex';
                });
        }

        function cerrarModal() {
            document.getElementById('modalFormatoCita').style.display = 'none';
        }

        function copiarContenido() {
            const texto = document.getElementById('contenidoModalCita').innerText;
            navigator.clipboard.writeText(texto).then(() => {
                alert("Formato copiado al portapapeles.");
            });
        }
    </script>
    <!-- Modal Flotante -->
    <div id="modalFormatoCita" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999;">
        <div class="modal-content" style="background:#fff; padding:20px; border-radius:10px; width:90%; max-width:600px; position:relative;">
            <span onclick="cerrarModal()" style="position:absolute; top:10px; right:15px; cursor:pointer;">❌</span>
            <div id="contenidoModalCita">
                <!-- Aquí se carga el contenido -->
            </div>
            <button onclick="copiarContenido()" style="margin-top:10px;">📋 Copiar Información</button>
        </div>
    </div>

</body>

</html>
