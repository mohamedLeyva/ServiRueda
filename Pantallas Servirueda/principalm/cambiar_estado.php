<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'mandadito') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT disponible FROM detalles_mandadito WHERE id_usuario = '$id_usuario'";
$resultado = $conexion->query($sql);

if (!$resultado) {
    echo json_encode(['success' => false, 'error' => 'Falta la columna en la BD: ' . $conexion->error]);
    exit();
}

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();

    $nuevo_estado = ($fila['disponible'] == 1) ? 0 : 1;

    $update = "UPDATE detalles_mandadito SET disponible = '$nuevo_estado' WHERE id_usuario = '$id_usuario'";
    if ($conexion->query($update)) {
        echo json_encode(['success' => true, 'nuevo_estado' => $nuevo_estado]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar: ' . $conexion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Este mandadito no tiene registro en la tabla detalles_mandadito.']);
}
?>