<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena']; 
    $rol = $conexion->real_escape_string($_POST['rol']);

    $check = $conexion->query("SELECT id_usuario FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
    
    if ($check->num_rows > 0) {
        $error = "Error: El nombre de usuario o el correo ya están registrados.";
    } else {
        $sql_insert = "INSERT INTO usuarios (usuario, correo, contrasena, rol) VALUES ('$usuario', '$correo', '$contrasena', '$rol')";
        
        if ($conexion->query($sql_insert)) {
            $nuevo_id = $conexion->insert_id; 
            
            if ($rol === 'mandadito') {
                $sql_detalles = "INSERT INTO detalles_mandadito (id_usuario, nombre, foto_perfil, foto_vehiculo, marca_modelo_vehiculo, experiencia, colonias_concurridas, celular, disponible) 
                                VALUES ('$nuevo_id', '$usuario', 'sin_foto.png', 'sin_foto.png', 'No especificado', 'Nuevo', 'No especificadas', '0000000000', 1)";
                $conexion->query($sql_detalles);
            }
            header("Location: panel_admin.php?exito=creado");
            exit();
        } else {
            $error = "Error de base de datos: " . $conexion->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="../principal/principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; border: 2px solid #2b4c5e; margin: 30px auto;}
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
        .btn-guardar { width: 100%; padding: 12px; background: #34495e; color: white; border: none; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; font-size: 15px;}
        .btn-guardar:hover { background: #2c3e50; }
        .btn-volver { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>

    <div class="dashboard-contenedor">
        
        <aside class="sidebar">
            <div class="logo-container">
                <img src="../logo sr.png" alt="ServiRueda" class="logo">
            </div>
            <nav class="menu-lateral">
                <a href="panel_admin.php" class="activo"><i class="fas fa-users-cog"></i> Gestión Usuarios</a>
            </nav>
            <div class="logout-container">
                <a href="../principal/cerrar_sesion.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </aside>

        <main class="contenido">
            
            <header class="header-principal">
                <div class="header-centro" style="text-align: left; width: 100%;">
                    <h1>Modo Administrador</h1>
                </div>
            </header>

            <div class="form-container">
                <h2 style="margin-top: 0; color: #2b4c5e; border-bottom: 2px solid #eee; padding-bottom:10px;"><i class="fas fa-user-plus"></i> Crear Usuario</h2>
                
                <?php if(isset($error)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="crear_usuario.php" method="POST">
                    <div class="form-group">
                        <label>Nombre de Usuario:</label>
                        <input type="text" name="usuario" placeholder="Ej. juanperez123" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="correo" placeholder="Ej. juan@correo.com" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" name="contrasena" placeholder="Asigna una contraseña" required>
                    </div>
                    <div class="form-group">
                        <label>Rol:</label>
                        <select name="rol" required>
                            <option value="" disabled selected>-- Seleccionar --</option>
                            <option value="cliente">Cliente (Busca servicios)</option>
                            <option value="mandadito">Mandadito (Ofrece servicios)</option>
                            <option value="admin">Administrador (Control total)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-guardar"><i class="fas fa-plus-circle"></i> Registrar Usuario</button>
                    <a href="panel_admin.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Cancelar y volver a la tabla</a>
                </form>
            </div>

        </main>
    </div>

</body>
</html>