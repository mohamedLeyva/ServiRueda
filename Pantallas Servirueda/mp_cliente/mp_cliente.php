<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login/login.html");
    exit();
}

include '../conexion.php';
$id_usuario = $_SESSION['id_usuario'];


$sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
$resultado = $conexion->query($sql);
$datos_cliente = $resultado->fetch_assoc();

$ruta_foto = (!empty($datos_cliente['foto_registro']) && $datos_cliente['foto_registro'] !== 'sin_foto.png') 
             ? "../" . $datos_cliente['foto_registro'] 
             : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - ServiRueda</title>
    <link rel="stylesheet" href="mp_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="contenedor-perfil">
        
        <a href="../principal/principal.php" class="btn-volver"><i class="fas fa-chevron-left"></i> Volver</a>

        <div class="header-logo">
            <img src="../logo sr.png" alt="ServiRueda" class="logo">
        </div>

        <h1 class="titulo-perfil">Mi Perfil</h1>

        <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
            <div class="mensaje-exito">
                <i class="fas fa-check-circle"></i> ¡Tus datos se actualizaron correctamente!
            </div>
        <?php endif; ?>

        <form class="formulario-edicion" action="procesar_perfil.php" method="POST" enctype="multipart/form-data">
            
            <div class="seccion-foto">
                <img src="<?php echo $ruta_foto; ?>" alt="Mi Foto" class="foto-actual">
                <label for="nueva_foto" class="btn-subir-foto"><i class="fas fa-camera"></i> Cambiar Foto</label>
                <input type="file" id="nueva_foto" name="nueva_foto" accept="image/*" style="display: none;">
            </div>

            <div class="grupo-input">
                <label for="usuario">Nombre de Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($datos_cliente['usuario']); ?>" required>
            </div>

            <div class="grupo-input">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($datos_cliente['correo']); ?>" required>
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