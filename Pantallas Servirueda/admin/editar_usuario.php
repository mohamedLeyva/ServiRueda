<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../conexion.php';

// Seguridad: Solo administradores
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login/login.html");
    exit();
}

// Si se envió el formulario para actualizar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_usuario'];
    $nuevo_usuario = $conexion->real_escape_string($_POST['usuario']);
    $nuevo_correo = $conexion->real_escape_string($_POST['correo']);
    $nuevo_rol = $conexion->real_escape_string($_POST['rol']);

    $sql_update = "UPDATE usuarios SET usuario = '$nuevo_usuario', correo = '$nuevo_correo', rol = '$nuevo_rol' WHERE id_usuario = '$id'";
    
    if ($conexion->query($sql_update)) {
        header("Location: panel_admin.php?exito=actualizado");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}

// Obtener datos actuales del usuario a editar
if (isset($_GET['id'])) {
    $id_editar = $_GET['id'];
    $resultado = $conexion->query("SELECT * FROM usuarios WHERE id_usuario = '$id_editar'");
    $datos = $resultado->fetch_assoc();
} else {
    header("Location: panel_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - ServiRueda</title>
    <link rel="stylesheet" href="../principal/principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; border: 2px solid #2b4c5e; margin: 30px auto;}
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
        .btn-guardar { width: 100%; padding: 12px; background: #2ecc71; color: white; border: none; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; font-size: 15px;}
        .btn-guardar:hover { background: #27ae60; }
        .btn-volver { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #555; font-weight: bold; }
        .btn-volver:hover { text-decoration: underline; }
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
                <h2 style="margin-top: 0; color: #2b4c5e; border-bottom: 2px solid #eee; padding-bottom:10px;"><i class="fas fa-user-edit"></i> Editar Usuario</h2>
                
                <?php if(isset($error)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="editar_usuario.php" method="POST">
                    <input type="hidden" name="id_usuario" value="<?php echo $datos['id_usuario']; ?>">
                    
                    <div class="form-group">
                        <label>Usuario:</label>
                        <input type="text" name="usuario" value="<?php echo htmlspecialchars($datos['usuario']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="correo" value="<?php echo htmlspecialchars($datos['correo']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Rol:</label>
                        <select name="rol" required>
                            <option value="cliente" <?php if($datos['rol'] == 'cliente') echo 'selected'; ?>>Cliente</option>
                            <option value="mandadito" <?php if($datos['rol'] == 'mandadito') echo 'selected'; ?>>Mandadito</option>
                            <option value="admin" <?php if($datos['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <a href="panel_admin.php" class="btn-volver">Cancelar y volver</a>
                </form>
            </div>

        </main>
    </div>

</body>
</html>