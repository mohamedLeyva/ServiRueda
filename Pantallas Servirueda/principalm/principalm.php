<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'mandadito') {
    header("Location: ../login/login.html");
    exit();
}

include '../conexion.php';

$nombre_usuario = $_SESSION['usuario'];
$id_actual = $_SESSION['id_usuario'];

$sql_mi_foto = "SELECT foto_perfil FROM detalles_mandadito WHERE id_usuario = '$id_actual'";
$res_mi_foto = $conexion->query($sql_mi_foto);

$mi_foto = "https://cdn-icons-png.flaticon.com/512/149/149071.png"; // Foto por defecto
if ($res_mi_foto && $res_mi_foto->num_rows > 0) {
    $fila_foto = $res_mi_foto->fetch_assoc();
    if (!empty($fila_foto['foto_perfil']) && $fila_foto['foto_perfil'] !== 'sin_foto.png') {
        $mi_foto = "../" . $fila_foto['foto_perfil'];
    }
}

$sql_estado = "SELECT disponible FROM detalles_mandadito WHERE id_usuario = '$id_actual'";
$res_estado = $conexion->query($sql_estado);
$es_disponible = 1;
if ($res_estado && $res_estado->num_rows > 0) {
    $fila_estado = $res_estado->fetch_assoc();
    if(isset($fila_estado['disponible'])) {
        $es_disponible = $fila_estado['disponible'];
    }
}

$clase_estado = ($es_disponible == 1) ? 'activo' : 'inactivo';
$texto_estado = ($es_disponible == 1) ? 'Disponible para viajes' : 'Ocupado / En un viaje';
$icono_estado = ($es_disponible == 1) ? 'fa-check-circle' : 'fa-times-circle';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - ServiRueda</title>
    <link rel="stylesheet" href="principalm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-contenedor">
        
        <aside class="sidebar">
            <div class="logo-container">
                <img src="../logo sr.png" alt="ServiRueda" class="logo">
            </div>
            
            <nav class="menu-lateral">
                <a href="#" class="activo"><i class="fas fa-home"></i> Inicio</a>
                <a href="../compartir/compartir.php"><i class="fas fa-share-alt"></i> Compartir Perfil</a>
                <a href="../mp_mandadito/mp_mandadito.php"><i class="fas fa-user-circle"></i> Mi Perfil</a>
            </nav>

            <div class="logout-container">
                <a href="../principal/cerrar_sesion.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </aside>

        <main class="contenido">
            
            <header class="header-principal">
                <div class="foto-top-right">
                    <img src="<?php echo $mi_foto; ?>" alt="Mi Perfil">
                </div>
                
                <div class="header-centro">
                    <h1>¡A rodar, "<?php echo htmlspecialchars($nombre_usuario); ?>"!</h1>
                    
                    <div class="estado-repartidor">
                        <span>Mi Estado Actual:</span>
                        <div class="toggle-estado <?php echo $clase_estado; ?>" id="btnEstado">
                            <i class="fas <?php echo $icono_estado; ?>" id="iconoEstado"></i> 
                            <span id="textoEstado"><?php echo $texto_estado; ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <div style="text-align: center; margin-top: 80px;">
                <i class="fas fa-mobile-alt" style="font-size: 80px; color: #3498db; margin-bottom: 20px;"></i>
                <h2 style="font-size: 28px; color: #111; margin-bottom: 10px;">Atento a tu celular</h2>
                <p style="font-size: 18px; color: #444; font-weight: 600;">Los clientes te contactarán directamente a tu número registrado.<br></p>
            </div>

        </main>
    </div>

    <script>
        document.getElementById('btnEstado').addEventListener('click', function() {
            fetch('cambiar_estado.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById('btnEstado');
                    const icono = document.getElementById('iconoEstado');
                    const texto = document.getElementById('textoEstado');
                    
                    if (data.nuevo_estado == 1) {
                        btn.classList.remove('inactivo');
                        btn.classList.add('activo');
                        icono.className = 'fas fa-check-circle';
                        texto.innerText = 'Disponible para viajes';
                    } else {
                        btn.classList.remove('activo');
                        btn.classList.add('inactivo');
                        icono.className = 'fas fa-times-circle';
                        texto.innerText = 'Ocupado / En un viaje';
                    }
                } else {
                    alert("Error: " + (data.error ? data.error : "Problema desconocido."));
                }
            }).catch(error => {
                console.error("Error en la petición:", error);
            });
        });
    </script>

<script>
    window.addEventListener('pageshow', function(event) {
        // Si la página se restauró desde el historial del navegador (BFCache)
        if (event.persisted) {
            window.location.reload(); // Obliga al navegador a recargar y verificar PHP
        }
    });
</script>

</body>
</html>