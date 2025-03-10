<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
require_once '../common_files/clases/session_config.php';

function estacion_update($version,$tiposmart) {
    $scanned_directory = array_diff(scandir(__DIR__), array('..', '.','login.php'));
    rsort($scanned_directory);
    $resultado = "";
    foreach ($scanned_directory as $key => $value){
        $tiposmartServer = substr(substr($value,4),0,1);
        $version_arch = substr(substr($value,5),0,-4);
        if($tiposmart == $tiposmartServer){
            if ($version < $version_arch){
                if ($version < 23) {
                    return "http://firmware.smartdoor.mx/ESP0" . $tiposmart . $version_arch . ".bin";
                } else {
                    return "https://firmware.smartdoor.mx/ESP0" . $tiposmart . $version_arch . ".bin";
                }
            }
        }
    }
    return $resultado;
}

function estacion_version($idestacion,$version,$conexion) {
    $sql = "select * from tblEstacion where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $idestacion]);
    if ($sth->rowCount() == 0) {
        $sql = "select * from tblPingEstaciones where idestacion = :idestacion";
        $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $idestacion]);
        if ($sth->rowCount() == 0) {
            $sql = "insert into tblPingEstaciones values (:idestacion,'" . date("Y-m-d H:i:s") . "',:version)";
            $sth = $conexion->prepare($sql);
            $sth->execute(["idestacion" => $idestacion,"version"=>$version]);
        } else{
            $sql = "update tblPingEstaciones set version = $version, ping = now() where idestacion = :idestacion";
            $sth = $conexion->prepare($sql);
            $sth->execute(["idestacion" => $idestacion]);
        }
    } else {
        $sql = "update tblEstacion set version = $version where idestacion = :idestacion";
        $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $idestacion]);
    }
    $sth = null;
    return 1;
}

$json = json_decode(file_get_contents('php://input'),true);
$conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
$tiposmart = 1;
if (isset($json['tiposmart'])) {
    $tiposmart = $json['tiposmart'];
}
estacion_version($json['id'],$json['version'],$conexion);
echo estacion_update($json['version'],$tiposmart);
$conexion = null;
?>