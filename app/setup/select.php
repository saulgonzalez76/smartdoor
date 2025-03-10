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
        curl_setopt($ch, CURLOPT_URL, "https://verify.twilio.com/v2/Services/VAa94e7cff3bff1a062a0418443993f9f3/Verifications");
        curl_setopt($ch, CURLOPT_USERPWD, "SK960f2cfb93a33cb74f2a255b986f7244:asj8DIfwnTbsIWKnItW4GPKle9s4Bim7");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("To"=>"+".$whatsapp,"Channel"=>"sms"));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);
        /*
         * NEXMO
        $payload = json_encode(array(
            "number"=>"52".$whatsapp,
            "brand"=>"SmartDoor",
            "lg"=>"es-mx",
            "api_key"=>"23c2abe7",
            "api_secret"=>"Vm9caNtcTngzmzP8"
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.nexmo.com/verify/json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);
        */
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
//        $id = filter_input(INPUT_POST, 'id');
        $whatsapp = filter_input(INPUT_POST, 'whatsapp');
        $codigo = filter_input(INPUT_POST, 'codigo');
        // TWILIO
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://verify.twilio.com/v2/Services/VAa94e7cff3bff1a062a0418443993f9f3/VerificationCheck");
        curl_setopt($ch, CURLOPT_USERPWD, "SK960f2cfb93a33cb74f2a255b986f7244:asj8DIfwnTbsIWKnItW4GPKle9s4Bim7");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("To"=>"+" . $whatsapp,"Code"=>$codigo));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);

        /*
         * NEXMO
        $payload = json_encode(array(
            "request_id"=>$id,
            "code"=>$codigo,
            "api_key"=>"23c2abe7",
            "api_secret"=>"Vm9caNtcTngzmzP8"
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.nexmo.com/verify/check/json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
        $respuesta = json_decode(curl_exec($ch));
        curl_close($ch);
        echo json_encode($respuesta);
         */
        break;
}
/*
 * -----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsMqzKW/x5TQxh7RhK1x2
9DtvdB5vbJUdW94PFF5/NE4ea/n0YI8upt/6czWLJ4L1s6b0a7hEyD0/PuUZUIQ6
K8YwEMTp7+VxStm4arpgsJb6Ho8hcK32sBfRhSEYTpwIjVQpWQuUe4l3+GZ9TSV/
r8T7Tk3tA3xXoVMNqVW1tIKcWK4jX8IDoBbOIB5PhffzvXJhn3L8ZoR496e5IQeC
dXjfxOvE8uk0vfzYYHfTcNs7kye8yLZRkVbCwEb7tBszv+iOCpl65uXxNTs0fMZE
WO+ta6N0FgFF6P59lO5eT89evClspNyFRQUFaOTYEmE06C/VrP8XuoiOPrcy1wGF
PwIDAQAB
-----END PUBLIC KEY-----

https://smartdoor.mx/vonage/incomming
https://smartdoor.mx/vonage/status


FRIENDLY NAME
SmartDoor

SID
SK960f2cfb93a33cb74f2a255b986f7244

KEY TYPE
Standard

SECRET
asj8DIfwnTbsIWKnItW4GPKle9s4Bim7


{
    "status": "pending",
    "payee": null,
    "date_updated": "2021-07-27T13:33:52Z",
    "send_code_attempts": [
        {
            "attempt_sid": "VLf82ec339401d741e8dffdffc4e43085f",
            "channel": "sms",
            "time": "2021-07-27T13:33:52.397Z"
        }
    ],
    "account_sid": "AC09f0f4f21c1cab4dba5f3167dd69c038",
    "to": "+528342717542",
    "amount": null,
    "valid": false,
    "lookup": {
        "carrier": {
            "mobile_country_code": "334",
            "type": "mobile",
            "error_code": null,
            "mobile_network_code": "020",
            "name": "RADIOMOVIL DIPSA/TELCEL/AMERICA MOVIL"
        }
    },
    "url": "https://verify.twilio.com/v2/Services/VA62580a0361b8eb4ebcf9b64314cc6995/Verifications/VE86d8510dd57b996b60276a8fee2cf60c",
    "sid": "VE86d8510dd57b996b60276a8fee2cf60c",
    "date_created": "2021-07-27T13:33:52Z",
    "service_sid": "VA62580a0361b8eb4ebcf9b64314cc6995",
    "channel": "sms"
}




{
    "status": "approved",
    "payee": null,
    "date_updated": "2021-07-27T13:39:03Z",
    "account_sid": "AC09f0f4f21c1cab4dba5f3167dd69c038",
    "to": "+528342717542",
    "amount": null,
    "valid": true,
    "sid": "VE86d8510dd57b996b60276a8fee2cf60c",
    "date_created": "2021-07-27T13:33:52Z",
    "service_sid": "VA62580a0361b8eb4ebcf9b64314cc6995",
    "channel": "sms"
}
 * */
