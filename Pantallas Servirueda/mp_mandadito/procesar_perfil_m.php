<?php
session_start();
include '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nuevo_usuario = $_POST['usuario'];
$nuevo_celular = $_POST['celular'];
$nuevo_vehiculo = $_POST['vehiculo'];
$nueva_experiencia = $_POST['experiencia'];
$nuevas_colonias = $_POST['colonias'];
$nueva_contrasena = $_POST['contrasena'];

$sql_usuarios = "UPDATE usuarios SET usuario = '$nuevo_usuario'";
if (!empty($nueva_contrasena)) {
    $sql_usuarios .= ", contrasena = '$nueva_contrasena'";
}
$sql_usuarios .= " WHERE id_usuario = '$id_usuario'";
$conexion->query($sql_usuarios);

$sql_detalles = "UPDATE detalles_mandadito 
                 SET celular = '$nuevo_celular', 
                     marca_modelo_vehiculo = '$nuevo_vehiculo',
                     experiencia = '$nueva_experiencia',
                     colonias_concurridas = '$nuevas_colonias'";

if (isset($_FILES['nueva_foto_perfil']) && $_FILES['nueva_foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $nombre_perfil = time() . "_perfil_" . basename($_FILES['nueva_foto_perfil']['name']);
    $ruta_perfil_final = "../fotos_usuarios/" . $nombre_perfil;
    
    if (move_uploaded_file($_FILES['nueva_foto_perfil']['tmp_name'], $ruta_perfil_final)) {
        $ruta_bd_perfil = "fotos_usuarios/" . $nombre_perfil;
        $sql_detalles .= ", foto_perfil = '$ruta_bd_perfil'";
    }
}

if (isset($_FILES['nueva_foto_vehiculo']) && $_FILES['nueva_foto_vehiculo']['error'] === UPLOAD_ERR_OK) {
    $nombre_vehiculo = time() . "_moto_" . basename($_FILES['nueva_foto_vehiculo']['name']);
    $ruta_vehiculo_final = "../fotos_usuarios/" . $nombre_vehiculo; // Guardamos en la misma carpeta por simplicidad
    
    if (move_uploaded_file($_FILES['nueva_foto_vehiculo']['tmp_name'], $ruta_vehiculo_final)) {
        $ruta_bd_vehiculo = "fotos_usuarios/" . $nombre_vehiculo;
        $sql_detalles .= ", foto_vehiculo = '$ruta_bd_vehiculo'";
    }
}

$sql_detalles .= " WHERE id_usuario = '$id_usuario'";

if ($conexion->query($sql_detalles) === TRUE) {
    $_SESSION['usuario'] = $nuevo_usuario;
    header("Location: mp_mandadito.php?exito=1");
    exit();
} else {
    echo "Error al actualizar los datos: " . $conexion->error;
}

$conexion->close();
?>