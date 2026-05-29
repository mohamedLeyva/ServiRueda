<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login/login.html");
    exit();
}

include '../conexion.php';

$nombre_usuario = $_SESSION['usuario'];
$id_actual = $_SESSION['id_usuario'];

$sql_mi_foto = "SELECT foto_registro FROM usuarios WHERE id_usuario = '$id_actual'";
$res_mi_foto = $conexion->query($sql_mi_foto);
$fila_foto = $res_mi_foto->fetch_assoc();
$mi_foto = (!empty($fila_foto['foto_registro']) && $fila_foto['foto_registro'] !== 'sin_foto.png') 
           ? "../" . $fila_foto['foto_registro'] 
           : "https://cdn-icons-png.flaticon.com/512/149/149071.png";

$sql = "SELECT u.id_usuario, u.usuario, d.nombre, d.foto_perfil, d.marca_modelo_vehiculo, d.disponible, d.colonias_concurridas,
        (SELECT IFNULL(ROUND(AVG(estrellas), 1), 0) FROM calificaciones WHERE id_mandadito = u.id_usuario) as promedio_estrellas
        FROM usuarios u 
        INNER JOIN detalles_mandadito d ON u.id_usuario = d.id_usuario 
        WHERE u.rol = 'mandadito'";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal - ServiRueda</title>
    <link rel="stylesheet" href="principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .botones-filtro { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-top: 15px; }
        .btn-filtro { flex-grow: 1; max-width: 200px; }
    </style>
</head>
<body>
    <div class="dashboard-contenedor">
        
        <aside class="sidebar">
            <div class="logo-container">
                <img src="../logo sr.png" alt="ServiRueda" class="logo">
            </div>
            
            <nav class="menu-lateral">
                <a href="../principal/principal.php" class="activo"><i class="fas fa-home"></i> Inicio</a>
                <a href="../mp_cliente/mp_cliente.php"><i class="fas fa-user-circle"></i> Mi Perfil</a>
            </nav>

            <div class="logout-container">
                <a href="cerrar_sesion.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </aside>

        <main class="contenido">
            
            <header class="header-principal">
                <div class="foto-top-right">
                    <img src="<?php echo $mi_foto; ?>" alt="Mi Perfil">
                </div>
                
                <div class="header-centro">
                    <h1>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
                    
                    <div class="buscador">
                        <input type="text" id="buscadorInput" placeholder="Buscar mandadito o zona (ej. Centro)">
                        <i class="fas fa-search"></i>
                    </div>

                    <div class="botones-filtro">
                        <button class="btn-filtro" id="btnDisponibles"><i class="fas fa-check-circle"></i> Disponibles</button>
                        <button class="btn-filtro" id="btnMejores"><i class="fas fa-star"></i> Mejor Calificados</button>
                        <button class="btn-filtro" id="btnTodos"><i class="fas fa-list"></i> Ver Todos</button>
                    </div>
                </div>
            </header>

            <div class="grid-repartidores" id="contenedorTarjetas">
                <?php
                if ($resultado->num_rows > 0) {
                    while($mandadito = $resultado->fetch_assoc()) {
                        
                        $ruta_foto = !empty($mandadito['foto_perfil']) && $mandadito['foto_perfil'] !== 'sin_foto.png' 
                                     ? "../" . $mandadito['foto_perfil'] 
                                     : "https://cdn-icons-png.flaticon.com/512/149/149071.png"; 
                        
                        $promedio = $mandadito['promedio_estrellas'];
                        $calificacion = ($promedio > 0) ? $promedio : "Nuevo";
                        
                        $estado_bd = isset($mandadito['disponible']) ? $mandadito['disponible'] : 1;
                        if ($estado_bd == 1) {
                            $etiqueta_estado = '<span class="badge-disponible" style="background-color: #28a745;">Disponible</span>';
                        } else {
                            $etiqueta_estado = '<span class="badge-disponible" style="background-color: #dc3545;">Ocupado</span>';
                        }

                        $nombre_limpio = strtolower(htmlspecialchars($mandadito['nombre']));
                        $zonas_limpias = strtolower(htmlspecialchars($mandadito['colonias_concurridas'] ?? ''));

                        echo '
                        <div class="tarjeta-repartidor" data-nombre="' . $nombre_limpio . '" data-zonas="' . $zonas_limpias . '" data-estado="' . $estado_bd . '" data-promedio="' . $promedio . '">
                            <div class="avatar-container">
                                <img src="' . $ruta_foto . '" alt="Repartidor" class="foto-perfil">
                            </div>
                            <h3>' . htmlspecialchars($mandadito['nombre']) . '</h3>
                            
                            <div class="dato-tarjeta calificacion">
                                <i class="fas fa-star" style="color: #ffc107;"></i> ' . $calificacion . '
                            </div>
                            
                            <div class="dato-tarjeta">
                                <i class="fas fa-motorcycle"></i> 
                                ' . $etiqueta_estado . '
                            </div>
                            
                            <div class="dato-tarjeta ubicacion">
                                <i class="fas fa-map-marker-alt"></i> Constitución, B.C.S
                            </div>
                            
                            <a href="../datos_mandadito/datos_mandadito.php?id=' . $mandadito['id_usuario'] . '" class="btn-contactar" style="text-decoration:none; display:inline-block;">Contactar</a>
                        </div>
                        ';
                    }
                } else {
                    echo "<h2>Aún no hay repartidores registrados en la plataforma.</h2>";
                }
                ?>
            </div>

        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscador = document.getElementById("buscadorInput");
            const tarjetas = Array.from(document.querySelectorAll(".tarjeta-repartidor")); 
            
            const btnDisponibles = document.getElementById("btnDisponibles");
            const btnMejores = document.getElementById("btnMejores");
            const btnTodos = document.getElementById("btnTodos");

            // 1. Buscador
            buscador.addEventListener("keyup", function() {
                const textoBuscado = this.value.toLowerCase();
                tarjetas.forEach(tarjeta => {
                    const nombre = tarjeta.getAttribute("data-nombre");
                    const zonas = tarjeta.getAttribute("data-zonas");
                    if (nombre.includes(textoBuscado) || zonas.includes(textoBuscado)) {
                        tarjeta.style.display = "flex";
                    } else {
                        tarjeta.style.display = "none";
                    }
                });
            });

            btnDisponibles.addEventListener("click", function() {
                buscador.value = "";
                tarjetas.forEach(tarjeta => {
                    const estado = tarjeta.getAttribute("data-estado");
                    tarjeta.style.display = (estado === "1") ? "flex" : "none";
                });
            });

            btnMejores.addEventListener("click", function() {
                buscador.value = ""; 
                
                let tarjetasOrdenadas = [...tarjetas];
                
                tarjetasOrdenadas.sort(function(a, b) {
                    let promA = parseFloat(a.getAttribute("data-promedio")) || 0;
                    let promB = parseFloat(b.getAttribute("data-promedio")) || 0;
                    return promB - promA;
                });

                tarjetasOrdenadas.forEach((tarjeta, index) => {
                    tarjeta.style.order = index;
                    tarjeta.style.display = "flex";
                });
            });

            btnTodos.addEventListener("click", function() {
                buscador.value = "";
                tarjetas.forEach(tarjeta => {
                    tarjeta.style.order = 0;
                    tarjeta.style.display = "flex";
                });
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