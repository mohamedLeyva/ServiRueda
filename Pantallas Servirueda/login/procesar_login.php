<?php
session_start();
include '../conexion.php';

$input_usuario = "";
if (isset($_POST['usuario'])) {
    $input_usuario = $_POST['usuario'];
} elseif (isset($_POST['correo'])) {
    $input_usuario = $_POST['correo'];
} elseif (isset($_POST['email'])) {
    $input_usuario = $_POST['email'];
}

$usuario = $conexion->real_escape_string($input_usuario); 
$contrasena = $_POST['contrasena']; 

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' OR correo = '$usuario'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $usuario_bd = $resultado->fetch_assoc();
    
    if ($contrasena == $usuario_bd['contrasena']) {
        
        $_SESSION['id_usuario'] = $usuario_bd['id_usuario'];
        $_SESSION['usuario'] = $usuario_bd['usuario'];
        $_SESSION['rol'] = $usuario_bd['rol'];

        if ($usuario_bd['rol'] == 'mandadito') {
            header("Location: ../principalm/principalm.php");
        } else if ($usuario_bd['rol'] == 'cliente') {
            header("Location: ../principal/principal.php");
        } else if ($usuario_bd['rol'] == 'admin') {
            header("Location: ../admin/panel_admin.php");
        }
        exit();
        
    } else {
        echo "<script>alert('Contraseña incorrecta'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('El usuario no existe'); window.location.href='login.html';</script>";
}
?>