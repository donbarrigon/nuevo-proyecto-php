<?php
require_once __DIR__ . "/../../../vendor/autoload.php";
use App\Orm\Conexion;

$conexion = Conexion::start();

echo var_dump($conexion);