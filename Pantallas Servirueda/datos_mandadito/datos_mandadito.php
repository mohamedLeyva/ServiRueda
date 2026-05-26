<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../conexion.php';

// Seguridad
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login/login.html");
    exit();
}


if (!isset($_GET['id'])) {
    header("Location: ../principal/principal.php");
    exit();
}
$id_mandadito_perfil = $_GET['id'];


$sql = "SELECT u.usuario, d.nombre, d.celular, d.marca_modelo_vehiculo, d.foto_perfil, d.foto_vehiculo, d.experiencia, d.colonias_concurridas, d.disponible,
        (SELECT IFNULL(ROUND(AVG(estrellas), 1), 0) FROM calificaciones WHERE id_mandadito = u.id_usuario) as promedio_estrellas
        FROM usuarios u 
        INNER JOIN detalles_mandadito d ON u.id_usuario = d.id_usuario 
        WHERE u.id_usuario = '$id_mandadito_perfil' AND u.rol = 'mandadito'";
        
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $datos = $resultado->fetch_assoc();
} else {
    echo "<h2>Mandadito no encontrado.</h2>";
    exit();
}

$ruta_perfil = (!empty($datos['foto_perfil']) && $datos['foto_perfil'] !== 'sin_foto.png') 
             ? "../" . $datos['foto_perfil'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
$ruta_vehiculo = (!empty($datos['foto_vehiculo']) && $datos['foto_vehiculo'] !== 'sin_foto.png') 
             ? "../" . $datos['foto_vehiculo'] : "../avatar_generico.png";

$promedio = $datos['promedio_estrellas'];
$calificacion = ($promedio > 0) ? $promedio : "Nuevo";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($datos['nombre']); ?> - ServiRueda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-image: url('../fondo sr.png'); 
            background-size: cover; 
            background-attachment: fixed; 
            margin: 0; 
            padding: 40px 20px; 
        }
        
        .perfil-contenedor { 
            background: transparent; 
            border: none; 
            box-shadow: none; 
            width: 100%; 
            max-width: 600px; 
            margin: 0 auto; 
            margin-top: 10px; 
            margin-bottom: 40px;
        }
        
        .info-card { flex: 1; padding: 20px; border-radius: 15px; border: 2px solid #000; }
        
        .btn-volver {
            position: fixed; 
            top: 30px; 
            left: 30px;
            font-size: 18px;
            color: #000;
            text-decoration: none;
            font-weight: 600;
            z-index: 100;
        }
        
        .btn-volver:hover { color: #3498db; }

        .estrellas-input { display: flex; flex-direction: row-reverse; justify-content: center; margin: 15px 0; gap: 10px;}
        .estrellas-input input { display: none; }
        .estrellas-input label { font-size: 40px; color: #ccc; cursor: pointer; transition: 0.2s; }
        .estrellas-input input:checked ~ label, .estrellas-input label:hover, .estrellas-input label:hover ~ label { color: #ffc107; }
    </style>
</head>
<body>
    
    <a href="../principal/principal.php" class="btn-volver"><i class="fas fa-chevron-left"></i> Volver</a>
    
    <div class="perfil-contenedor">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo $ruta_perfil; ?>" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #000; margin-bottom: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <h1 style="margin: 0; font-size: 32px; color: #111;"><?php echo htmlspecialchars($datos['nombre']); ?></h1>
            <p style="color: #444; font-size: 20px; font-weight: 600; margin-top: 5px; margin-bottom: 20px;"><i class="fas fa-star" style="color: #ffc107;"></i> <?php echo $calificacion; ?></p>
            
            <?php if ($datos['disponible'] == 1): ?>
                <span style="background: #28a745; color: white; padding: 8px 20px; border-radius: 20px; font-size: 15px; font-weight: bold; border: 2px solid #1e7e34;">Disponible para viajes</span>
            <?php else: ?>
                <span style="background: #dc3545; color: white; padding: 8px 20px; border-radius: 20px; font-size: 15px; font-weight: bold; border: 2px solid #a71d2a;">Ocupado en este momento</span>
            <?php endif; ?>
        </div>

        <div style="display: flex; align-items: center; gap: 20px; background: #f4f2f0; padding: 20px; border-radius: 15px; border: 2px solid #000; margin-bottom: 20px;">
            <div style="width: 80px; height: 80px; background: white; border-radius: 10px; border: 1px solid #ccc; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                <img src="<?php echo $ruta_vehiculo; ?>" style="max-width: 90%; max-height: 90%; object-fit: contain;">
            </div>
            <div>
                <h3 style="margin: 0; font-size: 18px;">Vehículo Registrado</h3>
                <p style="margin: 5px 0 0 0; color: #444; font-size: 16px; font-weight: 600;"><?php echo htmlspecialchars($datos['marca_modelo_vehiculo']); ?></p>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div class="info-card" style="background: #e3f2fd;">
                <h3 style="margin-top: 0; font-size: 17px;"><i class="fas fa-map-marker-alt" style="color: #3498db;"></i> Zonas:</h3>
                <?php 
                if (!empty($datos['colonias_concurridas'])) {
                    $lista_colonias = explode(',', $datos['colonias_concurridas']);
                    foreach ($lista_colonias as $colonia) {
                        echo '<p style="margin: 5px 0; font-weight: 600; color: #333;">- ' . htmlspecialchars(trim($colonia)) . '</p>';
                    }
                } else {
                    echo '<p>No especificadas</p>';
                }
                ?>
            </div>

            <div class="info-card" style="background: #e8f5e9; text-align: center; display: flex; flex-direction: column; justify-content: center;">
                <h3 style="margin-top: 0; font-size: 17px;"><i class="fas fa-briefcase" style="color: #28a745;"></i> Experiencia:</h3>
                <p style="font-size: 22px; font-weight: bold; margin: 0; color: #2e7d32;">
                    <?php echo !empty($datos['experiencia']) ? htmlspecialchars($datos['experiencia']) : 'N/A'; ?>
                </p>
            </div>
        </div>

        <a href="https://wa.me/52<?php echo preg_replace('/[^0-9]/', '', $datos['celular']); ?>?text=¡Hola!%20Te%20encontré%20en%20ServiRueda%20y%20necesito%20un%20mandado." target="_blank" style="display: block; width: 100%; text-align: center; background: #25D366; color: white; padding: 18px; border-radius: 20px; font-size: 18px; font-weight: bold; text-decoration: none; border: 2px solid #000; margin-bottom: 40px; transition: transform 0.1s;">
            <i class="fab fa-whatsapp" style="font-size: 22px; vertical-align: text-bottom; margin-right: 8px;"></i> Contactar directamente
        </a>

        <div style="background: #fff; padding: 30px; border-radius: 20px; border: 2px solid #000; box-shadow: 4px 4px 0 rgba(0,0,0,0.1);">
            <h3 style="text-align: center; margin-bottom: 5px; font-size: 20px;">¿Qué tal fue tu experiencia?</h3>
            <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 15px;">Califica a <?php echo htmlspecialchars($datos['nombre']); ?></p>
            
            <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
                <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 15px; font-weight: bold; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> ¡Gracias por tu calificación!
                </div>
            <?php endif; ?>

            <form action="guardar_calificacion.php" method="POST">
                <input type="hidden" name="id_mandadito" value="<?php echo $id_mandadito_perfil; ?>">
                
                <div class="estrellas-input">
                    <input type="radio" id="star5" name="estrellas" value="5" required><label for="star5" class="fas fa-star"></label>
                    <input type="radio" id="star4" name="estrellas" value="4"><label for="star4" class="fas fa-star"></label>
                    <input type="radio" id="star3" name="estrellas" value="3"><label for="star3" class="fas fa-star"></label>
                    <input type="radio" id="star2" name="estrellas" value="2"><label for="star2" class="fas fa-star"></label>
                    <input type="radio" id="star1" name="estrellas" value="1"><label for="star1" class="fas fa-star"></label>
                </div>

                <button type="submit" style="width: 100%; padding: 15px; background: #3498db; color: white; border: none; border-radius: 20px; font-size: 16px; font-weight: bold; cursor: pointer; border: 2px solid #000; box-shadow: 2px 2px 0 rgba(0,0,0,1); transition: 0.1s;">
                     Enviar Calificación
                </button>
            </form>
        </div>
        
    </div>

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