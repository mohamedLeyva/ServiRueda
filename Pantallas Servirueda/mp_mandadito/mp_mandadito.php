<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'mandadito') {
    header("Location: ../login/login.html");
    exit();
}

include '../conexion.php';
$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT u.usuario, u.correo, d.nombre, d.apellido_paterno, d.celular, d.marca_modelo_vehiculo, d.foto_perfil, d.foto_vehiculo, d.experiencia, d.colonias_concurridas 
        FROM usuarios u 
        INNER JOIN detalles_mandadito d ON u.id_usuario = d.id_usuario 
        WHERE u.id_usuario = '$id_usuario'";
$resultado = $conexion->query($sql);
$datos = $resultado->fetch_assoc();

$ruta_perfil = (!empty($datos['foto_perfil']) && $datos['foto_perfil'] !== 'sin_foto.png') 
             ? "../" . $datos['foto_perfil'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
             
$ruta_vehiculo = (!empty($datos['foto_vehiculo']) && $datos['foto_vehiculo'] !== 'sin_foto.png') 
             ? "../" . $datos['foto_vehiculo'] : "../avatar_generico.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Repartidor</title>
    <link rel="stylesheet" href="mp_mandadito.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="contenedor-perfil">
        
        <a href="../principalm/principalm.php" class="btn-volver"><i class="fas fa-chevron-left"></i> Volver</a>

        <div class="header-logo">
            <img src="../logo sr.png" alt="ServiRueda" class="logo">
        </div>

        <h1 class="titulo-perfil">Perfil de Repartidor</h1>

        <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
            <div class="mensaje-exito">
                <i class="fas fa-check-circle"></i> ¡Tus datos se actualizaron correctamente!
            </div>
        <?php endif; ?>

        <form class="formulario-edicion" action="procesar_perfil_m.php" method="POST" enctype="multipart/form-data">
            
            <div class="zona-fotos">
                <div class="seccion-foto">
                    <label class="label-foto">Mi Foto</label>
                    <img src="<?php echo $ruta_perfil; ?>" alt="Mi Foto" class="foto-actual">
                    <label for="nueva_foto_perfil" class="btn-subir-foto"><i class="fas fa-camera"></i> Cambiar</label>
                    <input type="file" id="nueva_foto_perfil" name="nueva_foto_perfil" accept="image/*" style="display: none;">
                </div>

                <div class="seccion-foto">
                    <label class="label-foto">Mi Vehículo</label>
                    <div class="caja-vehiculo">
                        <img src="<?php echo $ruta_vehiculo; ?>" alt="Vehículo" class="foto-vehiculo-actual">
                    </div>
                    <label for="nueva_foto_vehiculo" class="btn-subir-foto"><i class="fas fa-motorcycle"></i> Cambiar</label>
                    <input type="file" id="nueva_foto_vehiculo" name="nueva_foto_vehiculo" accept="image/*" style="display: none;">
                </div>
            </div>

            <div class="grupo-input">
                <label for="usuario">Nombre de Usuario (Login):</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($datos['usuario']); ?>" required>
            </div>

            <div class="grupo-input">
                <label for="celular">Número de Celular (WhatsApp/Llamadas):</label>
                <input type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($datos['celular']); ?>" required>
            </div>

            <div class="grupo-input">
                <label for="vehiculo">Marca y Modelo del Vehículo:</label>
                <input type="text" id="vehiculo" name="vehiculo" value="<?php echo htmlspecialchars($datos['marca_modelo_vehiculo']); ?>" required>
            </div>

            <div class="grupo-input">
                <label for="experiencia">Tiempo de Experiencia (Ej. 6 meses, 1 año):</label>
                <input type="text" id="experiencia" name="experiencia" value="<?php echo htmlspecialchars($datos['experiencia']); ?>">
            </div>

            <div class="grupo-input">
                <label for="colonias">Colonias Concurridas (Separadas por comas):</label>
                <input type="text" id="colonias" name="colonias" placeholder="Ej: Centro, Solidaridad, Pueblo Nuevo" value="<?php echo htmlspecialchars($datos['colonias_concurridas']); ?>">
            </div>

            <div class="grupo-input">
                <label for="contrasena">Nueva Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Déjalo en blanco para no cambiarla">
            </div>

            <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Guardar Cambios</button>
        </form>

    </div>
</body>
</html>