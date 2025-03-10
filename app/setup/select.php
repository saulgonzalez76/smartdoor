<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include('../desktop/email.php');
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$tipo=0;
if (null !== (filter_input(INPUT_GET,'tipo'))) { $tipo = filter_input(INPUT_GET,'tipo'); }
switch ($tipo) {
    case 1:
        // registro de usuario, regresa el id del usuario nuevo o el id del usuario ya existente
        $email = filter_input(INPUT_POST, 'email');
        $nombre = filter_input(INPUT_POST, 'nombre');
        $fbid = filter_input(INPUT_POST, 'fbid');
        $whatsapp = filter_input(INPUT_POST, 'whatsapp');
        $nombre_puerta = filter_input(INPUT_POST, 'nombre_puerta');
        $nombre_estacion = filter_input(INPUT_POST, 'nombre_est');
        $latlng = filter_input(INPUT_POST, 'latlng');
        $planservicio = filter_input(INPUT_POST, 'planservicio');
        $es_puerta = filter_input(INPUT_POST, 'es_puerta');
        $idestacion = $_SESSION['idestacion'];

        $idusuario = $clsBaseDatos->usuarios_email($email);
        $password = "";
        $confirmado = 0;
        if ($idusuario == 0) {
            // si el usuario no existe entonces creo un login nuevo
            $password = $clsBaseDatos->keygen(5);
            $idusuario = $clsBaseDatos->usuarios_nuevo($nombre, $password, $email, $fbid, $whatsapp);
            enviaCorreo(correoNuevoUusario(str_replace("=","",base64_encode($email)),$email,$password),$email,$nombre,"SmartDoor - Confirma tu correo.",false,"no-reply");
        } else {
            $confirmado = 1;
        }
        // agrego la estacion a su id
        $codigo = $clsBaseDatos->estacion_nuevo($idestacion,$idusuario,$nombre_estacion,$nombre_puerta,$latlng,$nombre,$fbid,$email,$whatsapp,$confirmado,$planservicio,$es_puerta);
        echo json_encode(["codigo" => $codigo,"password"=>$password]);
        break;
    case 2:
        $codigo = filter_input(INPUT_GET, 'codigo');
        echo $clsBaseDatos->getPuertaStatus($codigo);
        break;
    case 3:
        $whatsapp = filter_input(INPUT_POST, 'whatsapp');
        // TWILIO
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://verify.twilio.com/v2/Services/".$_SESSION['TWILIO_SERVICE']."/Verifications");
        curl_setopt($ch, CURLOPT_USERPWD, $_SESSION['TWILIO_CREDS']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("To"=>"+".$whatsapp,"Channel"=>"sms"));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);
        break;
    case 4:
        $clave = str_pad(rand(0, pow(10, 5)-1), 5, '0', STR_PAD_LEFT);
        echo json_encode(["codigo"=>$clave]);
        break;

    case 5:
        $email = filter_input(INPUT_POST, 'email');
        echo $clsBaseDatos->usuarios_email($email);
        break;
    case 6:
        // regresa los planes de servicio de un tipo de instalacion
        $tiposervicio = filter_input(INPUT_POST, 'tiposervicio');
        echo $clsBaseDatos->planes_servicio($tiposervicio);
        break;
    case 7:
        $whatsapp = filter_input(INPUT_POST, 'whatsapp');
        $codigo = filter_input(INPUT_POST, 'codigo');
        // TWILIO
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://verify.twilio.com/v2/Services/".$_SESSION['TWILIO_SERVICE']."/VerificationCheck");
        curl_setopt($ch, CURLOPT_USERPWD, $_SESSION['TWILIO_CREDS']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("To"=>"+" . $whatsapp,"Code"=>$codigo));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);

        break;
}
