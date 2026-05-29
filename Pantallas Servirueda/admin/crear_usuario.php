<?php
session_start();

// BLOQUEO DE CACHÉ
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
            
            // Si el rol es mandadito, insertamos los datos en TODAS las columnas de la tabla
            if ($rol === 'mandadito') {
                $nombre = $conexion->real_escape_string($_POST['nombre']);
                $apellido_p = $conexion->real_escape_string($_POST['apellido_p']);
                $apellido_m = $conexion->real_escape_string($_POST['apellido_m']);
                
                $celular = $conexion->real_escape_string($_POST['celular']);
                $fecha_nac = $conexion->real_escape_string($_POST['fecha_nac']);
                $vehiculo = $conexion->real_escape_string($_POST['vehiculo']);

                // Insertamos mapeando exactamente las columnas que vimos en tu BD
                $sql_detalles = "INSERT INTO detalles_mandadito 
                                (id_usuario, nombre, apellido_paterno, apellido_materno, celular, fecha_nacimiento, foto_perfil, foto_ine, marca_modelo_vehiculo, foto_vehiculo, estado_validacion, disponible, experiencia, colonias_concurridas) 
                                VALUES 
                                ('$nuevo_id', '$nombre', '$apellido_p', '$apellido_m', '$celular', '$fecha_nac', 'sin_foto.png', 'sin_foto.png', '$vehiculo', 'sin_foto.png', 'aprobado', 1, 'Nuevo', 'No especificadas')";
                
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
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; font-size: 14px;}
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
        .btn-guardar { width: 100%; padding: 12px; background: #34495e; color: white; border: none; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; font-size: 15px;}
        .btn-guardar:hover { background: #2c3e50; }
        .btn-volver { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #e74c3c; font-weight: bold; }
        
        /* Caja especial para los datos de Validación del mandadito */
        #campos-mandadito {
            background: #f4f7f6;
            padding: 15px;
            border-radius: 10px;
            border: 2px dashed #2b4c5e;
            margin-top: 15px;
            margin-bottom: 15px;
            display: none; 
        }
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
                        <label>Nombre de Usuario (Login):</label>
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
                        <label>Rol del Sistema:</label>
                        <select name="rol" id="rolSelect" onchange="toggleMandadito()" required>
                            <option value="" disabled selected>-- Seleccionar --</option>
                            <option value="cliente">Cliente (Busca servicios)</option>
                            <option value="mandadito">Mandadito (Ofrece servicios)</option>
                            <option value="admin">Administrador (Control total)</option>
                        </select>
                    </div>

                    <div id="campos-mandadito">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #2b4c5e;">Datos de Validación</h4>
                        
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" id="req_nombre" placeholder="Ej. Juan">
                        </div>

                        <div class="form-group">
                            <label>Apellido Paterno:</label>
                            <input type="text" name="apellido_p" id="req_ap" placeholder="Ej. Pérez">
                        </div>

                        <div class="form-group">
                            <label>Apellido Materno:</label>
                            <input type="text" name="apellido_m" id="req_am" placeholder="Ej. López">
                        </div>
                        
                        <div class="form-group">
                            <label>Número de Celular:</label>
                            <input type="text" name="celular" id="req_cel" maxlength="10">
                        </div>

                        <div class="form-group">
                            <label>Fecha de Nacimiento:</label>
                            <input type="date" name="fecha_nac" id="req_fecha">
                        </div>
                        
                        <div class="form-group">
                            <label>Marca y modelo del vehículo:</label>
                            <input type="text" name="vehiculo" id="req_vehiculo" placeholder="Ej. Moto Italika FT150">
                        </div>
                    </div>

                    <button type="submit" class="btn-guardar"><i class="fas fa-plus-circle"></i> Registrar Usuario</button>
                    <a href="panel_admin.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Cancelar y volver a la tabla</a>
                </form>
            </div>

        </main>
    </div>

    <script>
        function toggleMandadito() {
            var selector = document.getElementById("rolSelect").value;
            var caja = document.getElementById("campos-mandadito");
            
            var inputsReq = [
                document.getElementById("req_nombre"),
                document.getElementById("req_ap"),
                document.getElementById("req_am"),
                document.getElementById("req_cel"),
                document.getElementById("req_fecha"),
                document.getElementById("req_vehiculo")
            ];

            if (selector === "mandadito") {
                caja.style.display = "block"; 
                inputsReq.forEach(function(input) { input.required = true; });
            } else {
                caja.style.display = "none"; 
                inputsReq.forEach(function(input) { input.required = false; });
            }
        }

        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>

</body>
</html>