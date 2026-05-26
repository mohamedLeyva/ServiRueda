<?php
session_start();


if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'mandadito') {
    header("Location: ../login/login.html");
    exit();
}


$nombre_usuario = $_SESSION['usuario'];
$id_actual = $_SESSION['id_usuario'];


$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$dominio = $_SERVER['HTTP_HOST'];
$ruta_base = dirname(dirname($_SERVER['PHP_SELF'])); 
$url_perfil = $protocolo . "://" . $dominio . $ruta_base . "/datos_mandadito/datos_mandadito.php?id=" . $id_actual;

$mensaje_wa = "¡Hola! Soy " . $nombre_usuario . ", tu mandadito de confianza en ServiRueda 🛵. Mira mi perfil, experiencia y vehículo aquí: " . $url_perfil;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Perfil - ServiRueda</title>
    <link rel="stylesheet" href="compartir.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-contenedor">
        
        <aside class="sidebar">
            <div class="logo-container">
                <img src="../logo sr.png" alt="ServiRueda" class="logo">
            </div>
            
            <nav class="menu-lateral">
                <a href="../principalm/principalm.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="#" class="activo"><i class="fas fa-share-alt"></i> Compartir Perfil</a>
                <a href="../mp_mandadito/mp_mandadito.php"><i class="fas fa-user-circle"></i> Mi Perfil</a>
            </nav>

            <div class="logout-container">
                <a href="../principal/cerrar_sesion.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </aside>

        <main class="contenido">
            
            <div class="header-centro" style="padding-top: 20px;">
                <h1>Consigue más viajes.</h1>
                <p style="color: #444; font-size: 18px;">Comparte tu perfil para que los clientes vean tu experiencia y vehículo.</p>
            </div>

            <div class="tarjeta-compartir">
                
                <div class="qr-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo urlencode($url_perfil); ?>" alt="Código QR">
                    <p>Escanea para ver mi perfil</p>
                </div>

                <div class="link-container">
                    <input type="text" id="linkPerfil" value="<?php echo $url_perfil; ?>" readonly>
                    <button onclick="copiarLink()" class="btn-copiar"><i class="fas fa-copy"></i> Copiar</button>
                </div>

                <a href="https://wa.me/?text=<?php echo urlencode($mensaje_wa); ?>" target="_blank" class="btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                </a>

                <div id="mensaje-copiado" class="mensaje-oculto">¡Enlace copiado al portapapeles!</div>
            </div>

        </main>
    </div>

    <script>
        function copiarLink() {
            var copyText = document.getElementById("linkPerfil");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Para celulares
            navigator.clipboard.writeText(copyText.value);
            
        
            var msj = document.getElementById("mensaje-copiado");
            msj.style.display = "block";
            setTimeout(function(){ msj.style.display = "none"; }, 3000);
        }
    </script>
</body>
</html>