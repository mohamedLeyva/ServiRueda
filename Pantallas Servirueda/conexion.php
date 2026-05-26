<?php
$servidor = "localhost";
$usuario_db = "root"; // Por defecto en servidores locales como XAMPP suele ser 'root'
$password_db = "PilloFon33."; // Por defecto suele estar vacía. Si le pusiste una en Workbench, ponla aquí
$base_datos = "srprueba";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

// Comprobar la conexión
if ($conexion->connect_error) {
    die("La conexión a la base de datos falló: " . $conexion->connect_error);
}
?>