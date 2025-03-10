<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

    if(!isset($_SESSION)) { session_start(); }

    include("../common_files/clases/base_datos.php");
    $_SESSION['desconectar'] = 0;
    $clsBaseDatos = new Base_Datos();
    $result = $clsBaseDatos->logout();
    $_SESSION = array();
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header('Location: ../login.php');
?>

