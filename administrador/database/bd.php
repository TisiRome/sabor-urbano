<?php
$env=parse_ini_file('variables.env');

$host=$env['DB_HOST'];
$bd=$env['DB_NAME'];
$usuario=$env['DB_USER'];
$contrasenia=$env['DB_PASS'];
define("KEY", $env['ENCRYPTION_KEY']);
define("COD", "AES-128-ECB");

try {
    $conexion=new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $usuario, $contrasenia, [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES=>false, 
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT=>false
    ]);
} catch (PDOException $ex) {
    error_log("Error de conexión BD: " . $ex->getMessage());
    die("Error de conexión. Intente más tarde.");
}
