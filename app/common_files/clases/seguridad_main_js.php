<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
require_once "../clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

//ini_set("date.timezone","America/Mexico_City");
$jsondata = array();
$jsondata['logout'] = "0";
$jsondata['session'] = $_SESSION['config']['timeout'];
$jsondata['timeout'] = time() - $_SESSION['LAST_ACTIVITY'];
$jsondata['actividad'] = $_SESSION['LAST_ACTIVITY'];

//if (null !== (filter_input(INPUT_POST,'k'))) if (filter_input(INPUT_POST,'k') !== $clsBaseDatos->getKey()) $jsondata['logout'] = "1";

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $_SESSION['config']['timeout'])) $jsondata['logout'] = "1";

echo json_encode($jsondata);
exit;
?>