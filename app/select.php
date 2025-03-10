<?php
if(!isset($_SESSION)) { session_start(); }
require_once 'common_files/clases/session_config.php';
include "common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$tipo = filter_input(INPUT_GET,"tipo");
switch ($tipo){
    case 1:
        echo json_encode([
            "registros"=>$clsBaseDatos->registro_totales_todo(),
            "particulares"=>$clsBaseDatos->registro_totales_particulares(),
            "negocios"=>$clsBaseDatos->registro_totales_negocio(),
            "fracc"=>$clsBaseDatos->registro_totales_fraccionamiento()
        ]);
        break;
    case 2:
        $email = filter_input(INPUT_POST,"email");
        $titulo = filter_input(INPUT_POST,"titulo");
        $nombre = filter_input(INPUT_POST,"nombre");
        $mensaje = filter_input(INPUT_POST,"mensaje");
        include('desktop/email.php');
        echo enviaCorreo($mensaje,$email,$nombre,$titulo,true,"saul");
        break;
    case 3:
        $email = filter_input(INPUT_POST,"email");
        // buscar el usuario por email, despues enviar el correo para cambio de contraseña
        $idusuario = $clsBaseDatos->usuarios_email($email);
        if ($idusuario > 0){
            $nombre = $clsBaseDatos->usuarios_nombre($idusuario);
            include('desktop/email.php');
            enviaCorreo(correoNuevoPass($email),$email,$nombre,"SmartDoor - Recuperacion de password",false,"no-reply");
        }
        break;
    case 4:
        // crea un identificador unico para el dispositivo, debo cambiarlo cada 7 dias
        echo $clsBaseDatos->keygen(32);
        break;
    case 5:
        // login
        $email = filter_input(INPUT_POST,"email");
        $pass = filter_input(INPUT_POST,"pass");
        $key = filter_input(INPUT_POST,"key");
        $phoneinfo = filter_input(INPUT_POST,"phoneinfo");
        $datos = $clsBaseDatos->login($email,$pass,$key,$phoneinfo);
        $_SESSION['LAST_ACTIVITY'] = time();
        echo json_encode(["p"=>explode(";",$datos)[0],"t"=>explode(";",$datos)[1]]);
        break;
    case 6:
        // login con token jwt
        $token = filter_input(INPUT_POST,"t");
        $key = filter_input(INPUT_POST,"k");
        $phoneinfo = filter_input(INPUT_POST,"phoneinfo");
        $datos = $clsBaseDatos->loginJWT($token,$key,$phoneinfo);
        $_SESSION['LAST_ACTIVITY'] = time();
        echo json_encode(["p"=>explode(";",$datos)[0],"t"=>explode(";",$datos)[1]]);
        break;
    case 15:
        // espera hasta que se habre la puerta o pasan 3 min para cancelar
        $idregistro = filter_input(INPUT_GET, 'idregistro');
        $hora = time() + (30); // 30 seg
        $abierto = false;
        while ((!$abierto) && ($hora > time())) {
            $abierto = $clsBaseDatos->puerta_abierta($idregistro);
        }
        if (!$abierto){
            $clsBaseDatos->puerta_cancelar_apertura($idregistro);
        }
        echo ($abierto)?1:0;
        break;
    case 16:
        $codigo = filter_input(INPUT_GET, 'codigo');
        echo $clsBaseDatos->getPuertaStatus($codigo);
        break;
    case 17:
        $codigo = filter_input(INPUT_GET, 'codigo');
        $pos = filter_input(INPUT_GET, 'pos');
        $nombre = $clsBaseDatos->validaRegistro($codigo);
        if ($nombre == "") {
            // ABRE PUERTA AL DARLE CLIC A LA IMAGEN
            echo $clsBaseDatos->registro($codigo,$pos);
        } else { echo $nombre; }
        break;
}

