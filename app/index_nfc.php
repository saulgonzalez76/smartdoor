<?php

/*
 * GET ids ocupadas
 * id = idestacion para setup
 * t = token de usuario para abrir con nfc tag
 *  i = imei
 * v = visitas
 * c = confirmar correo
 *
 *
 * licencias o cuentas de pagos
 * tblEstacionTipoCuenta
 *  1 = fraccionamientos (tienen que pagar todos los usuarios o se bloquea a todos, pagos mensuales), usuarios ilimitados 50 por usuario
 *  2 = negocios (paga por un usuario de negocio, pagos mensuales), hasta 50 usuarios 200 pesos mensuales
 *  3 = casa o particulares (paga anualidad por usuario, solo un usuario por estacion), hasta 10 usuarios
 *  4 = demo un usuario, no puede agregar usuarios, no puede invitar, vigencia indefinida
 *
 *
 * bloqueos
 *  1 = bloqueado por administrador
 *  2 = bloqueado por falta de pago
 *
 *
 * por hacer !!
 * hacer el pago en linea por paypal y guardar sus datos
 * agregar a configuracion de admin una opcion para borrar la configuracion wifi del dispositivo
 *
 * planes de servicio
 * -1 = ilimitado
 * > -1 = limite de uso
 *
 *NFC tags
 * agregar la mac del tag a tblNfcTag con el idregistro de tblClientePuerta
 *
 * IMPORTANTE, AL CREAR NUEVOS AGREGAR A LA TABLA DE PING PARA QUE PUEDAN SER INSTALADOS
 * IMPORTANTE, AL ENTREGAR EQUIPO AL DISTRIBUIDOR ASIGNARLE EL EQUIPO EN LA TABLA
 * */

if(!isset($_SESSION)) { session_start(); }
include('common_files/clases/session_config.php');
require_once "common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();



$t = "";
$nombre = "";
$bloqueado = "";

// si se escaneo el tag de la estacion entra aqui
// token = serial number del tag
$token = "";
$usuario = "";
if (null !== (filter_input(INPUT_GET,'t'))) {
    $t = base64_decode(filter_input(INPUT_GET,'t'));
    $sth = $clsBaseDatos->estacion_nombre($t,"nfc");
    if ($row=$sth->fetch(PDO::FETCH_NAMED)){
        $nombre = $row['nombre'];
        $bloqueado = $row['bloqueado'];
        $usuario = $row['usuario'];
        $token = $row['codigo'];
    } else {
        $nombre = "";
        $bloqueado = 0;
    }

}

$version = time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include "common_files/meta_tags.php" ?>
    <script src="common_files/java/jquery-latest.min.js"></script>
    <script src="common_files/java/jquery.min.js?<?= $version; ?>"></script>
    <script src="common_files/java/sweetalert.js?<?= time(); ?>"></script>
    <script src="common_files/java/base64.js"></script>
    <script src="common_files/java/index_nfc.min.js"></script>
    <style>
        .swal2-container {
            zoom: 1.5;
        }
        .swal2-icon {
            width: 5em !important;
            height: 5em !important;
            border-width: .25em !important;
        }
    </style>
    <script>
        let token = '<?= $token; ?>';
        let nombre = '<?= $nombre; ?>';
        let usuario = '<?= $usuario; ?>';
        let bloqueado = Number(<?= $bloqueado; ?>);
    </script>
</head>
<body>
<div id="container" style="background-color: #01052d; width: 100%; height: 100%; overflow: hidden;
                        background: url('common_files/img/logo.png') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                        z-index: -1;opacity: 1;
                        position: absolute;"></div>

</body>
</html>