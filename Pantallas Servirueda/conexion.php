<?php
$servidor = "localhost";
$usuario_db = "root"; 
$password_db = "Pillofon.3"; 
$base_datos = "srprueba";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

// Comprobar la conexión
if ($conexion->connect_error) {
    die("La conexión a la base de datos falló: " . $conexion->connect_error);
}
?>