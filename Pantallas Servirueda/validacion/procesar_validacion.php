<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../conexion.php';

$nombre = $_POST['nombre'];
$apellido_p = $_POST['apellido_p'];
$apellido_m = $_POST['apellido_m'];
$celular = $_POST['celular'];
$fecha_nac = $_POST['fecha_nac'];
$marca_modelo = $_POST['marca_modelo'];

$carpeta_destino = "../fotos_usuarios/";

function procesarFoto($nombre_input, $carpeta_destino) {
    if (isset($_FILES[$nombre_input]) && $_FILES[$nombre_input]['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = time() . "_" . $nombre_input . "_" . basename($_FILES[$nombre_input]["name"]);
        $ruta_final = $carpeta_destino . $nombre_archivo;
        
        // Movemos el archivo
        if (move_uploaded_file($_FILES[$nombre_input]["tmp_name"], $ruta_final)) {
            return "fotos_usuarios/" . $nombre_archivo; 
        }
    }
    return "sin_foto.png";
}

$foto_perfil = procesarFoto("foto_perfil", $carpeta_destino);
$foto_ine = procesarFoto("foto_ine", $carpeta_destino);
$foto_vehiculo = procesarFoto("foto_vehiculo", $carpeta_destino);

$resultado = $conexion->query("SELECT MAX(id_usuario) AS ultimo_id FROM usuarios");
$fila = $resultado->fetch_assoc();
$id_usuario = $fila['ultimo_id'];

$sql = "INSERT INTO detalles_mandadito (id_usuario, nombre, apellido_paterno, apellido_materno, celular, fecha_nacimiento, foto_perfil, foto_ine, marca_modelo_vehiculo, foto_vehiculo, estado_validacion) 
        VALUES ('$id_usuario', '$nombre', '$apellido_p', '$apellido_m', '$celular', '$fecha_nac', '$foto_perfil', '$foto_ine', '$marca_modelo', '$foto_vehiculo', 'pendiente')";

if ($conexion->query($sql) === TRUE) {
    header("Location: ../inicio/inicio.html");
    exit();
} else {
    echo "<h2>Error al guardar los detalles: " . $conexion->error . "</h2>";
}

$conexion->close();
?>