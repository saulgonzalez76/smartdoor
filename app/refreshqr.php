<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$_SESSION['DB_HOST'] = $_ENV['DB_HOST'];
$_SESSION['DB_DATABASE'] = $_ENV['DB_DATABASE'];
$_SESSION['DB_USERNAME'] = $_ENV['DB_USERNAME'];
$_SESSION['DB_PASSWORD'] = $_ENV['DB_PASSWORD'];

if (php_sapi_name() !== 'cli') { echo "Ups. no puedes correr esto aqui !"; exit; }
require_once "common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$clsBaseDatos->refreshqr();
exit;