<?php
session_start();
include '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nuevo_usuario = $_POST['usuario'];
$nuevo_correo = $_POST['correo'];
$nueva_contrasena = $_POST['contrasena'];

$sql = "UPDATE usuarios SET usuario = '$nuevo_usuario', correo = '$nuevo_correo'";

if (!empty($nueva_contrasena)) {
    $sql .= ", contrasena = '$nueva_contrasena'";
}

if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
    $carpeta_destino = "../fotos_usuarios/";
    $nombre_archivo = time() . "_perfil_" . basename($_FILES['nueva_foto']['name']);
    $ruta_final = $carpeta_destino . $nombre_archivo;
    
    if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $ruta_final)) {
        $ruta_bd = "fotos_usuarios/" . $nombre_archivo;
        $sql .= ", foto_registro = '$ruta_bd'";
    }
}

$sql .= " WHERE id_usuario = '$id_usuario'";

if ($conexion->query($sql) === TRUE) {
    $_SESSION['usuario'] = $nuevo_usuario;
    header("Location: mp_cliente.php?exito=1");
    exit();
} else {
    echo "Error al actualizar los datos: " . $conexion->error;
}

$conexion->close();
?>