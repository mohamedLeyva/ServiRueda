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

if (isset($_GET['eliminar'])) {
    $id_borrar = $_GET['eliminar'];
    $conexion->query("DELETE FROM detalles_mandadito WHERE id_usuario = '$id_borrar'");
    $conexion->query("DELETE FROM calificaciones WHERE id_cliente = '$id_borrar' OR id_mandadito = '$id_borrar'");
    $conexion->query("DELETE FROM usuarios WHERE id_usuario = '$id_borrar'");
    header("Location: panel_admin.php?exito=eliminado");
    exit();
}

$sql = "SELECT id_usuario, usuario, correo, rol FROM usuarios ORDER BY id_usuario DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - ServiRueda</title>
    <link rel="stylesheet" href="../principal/principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Ajustes específicos para la tabla del administrador */
        .admin-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 2px solid #2b4c5e; margin-top: 20px;}
        .header-admin { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .tabla-usuarios { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tabla-usuarios th, .tabla-usuarios td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .tabla-usuarios th { background-color: #2b4c5e; color: white; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; color: white; }
        .badge-admin { background: #e74c3c; }
        .badge-mandadito { background: #f39c12; }
        .badge-cliente { background: #3498db; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 5px; color: white; font-weight: bold; font-size: 14px; display: inline-block; }
        .btn-editar { background: #2ecc71; }
        .btn-eliminar { background: #e74c3c; }
        .btn-crear { background: #34495e; padding: 10px 20px; margin-bottom: 15px; border-radius: 8px; }
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

            <div class="admin-container">

                <?php if (isset($_GET['exito'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                        <i class="fas fa-check-circle"></i> Operación realizada con éxito.
                    </div>
                <?php endif; ?>

                <a href="crear_usuario.php" class="btn btn-crear"><i class="fas fa-plus"></i> Registrar Nuevo Usuario</a>

                <table class="tabla-usuarios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $fila['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($fila['rol']); ?>">
                                    <?php echo strtoupper($fila['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="editar_usuario.php?id=<?php echo $fila['id_usuario']; ?>" class="btn btn-editar"><i class="fas fa-edit"></i></a>
                                <?php if ($fila['id_usuario'] != $_SESSION['id_usuario']): ?>
                                    <a href="panel_admin.php?eliminar=<?php echo $fila['id_usuario']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Estás seguro de borrar a este usuario para siempre?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </main>
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