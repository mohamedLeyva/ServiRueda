<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../conexion.php';


$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena']; 
$rol = $_POST['rol'];

$carpeta_destino = "../fotos_usuarios/";

$nombre_archivo = time() . "_" . basename($_FILES["foto"]["name"]);
$ruta_final = $carpeta_destino . $nombre_archivo;

if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_final)) {
    $foto_ruta = "fotos_usuarios/" . $nombre_archivo;
} else {
    $foto_ruta = "sin_foto.png";
}

$sql = "INSERT INTO usuarios (usuario, correo, contrasena, rol, foto_registro) 
        VALUES ('$usuario', '$correo', '$contrasena', '$rol', '$foto_ruta')";

if ($conexion->query($sql) === TRUE) {
    if ($rol === 'cliente') {
        header("Location: ../inicio/inicio.html");
    } else if ($rol === 'mandadito') {
        header("Location: ../validacion/validacion.html");
    }
    exit();
} else {
    echo "<h2>Error crítico en MySQL: " . $conexion->error . "</h2>";
}

$conexion->close();
?>