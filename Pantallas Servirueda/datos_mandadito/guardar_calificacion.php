<?php
session_start();
include '../conexion.php';


if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_SESSION['id_usuario'];
    $id_mandadito = $_POST['id_mandadito'];
    $estrellas = (int)$_POST['estrellas'];
    
    // Eliminamos la variable $comentario y la quitamos de la consulta SQL
    $sql = "INSERT INTO calificaciones (id_cliente, id_mandadito, estrellas) 
            VALUES ('$id_cliente', '$id_mandadito', '$estrellas')
            ON DUPLICATE KEY UPDATE estrellas = '$estrellas', fecha = CURRENT_TIMESTAMP";

    if ($conexion->query($sql)) {
        header("Location: datos_mandadito.php?id=$id_mandadito&exito=1");
    } else {
        echo "Error al guardar calificación: " . $conexion->error;
    }
}
?>