<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

include("common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();

$version = time();
if (null !== (filter_input(INPUT_GET, 'v'))) { $token = base64_decode(filter_input(INPUT_GET, 'v')); }


?>
<!doctype html>
<html lang="es">
    <head>
        <?php    include 'common_files/meta_tags.php'; ?>
        <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="dist/css/adminlte.min.css">
        <link href="common_files/css/index.css" rel="stylesheet">
        <link href="plugins/flipclock/css/flipclock.css" rel="stylesheet">
        <script src="common_files/java/jquery-latest.min.js?<?= $version; ?>"></script>
        <script src="common_files/java/jquery.min.js?<?= $version; ?>"></script>
        <script src="plugins/flipclock/js/flipclock.min.js?<?= $version; ?>"></script>
        <script src="common_files/java/base64.js"></script>
        <script src="common_files/java/sweetalert.js?<?= time(); ?>" type="text/javascript"></script>
        <script src="common_files/java/visitas.min.js"></script>
        <style>
            html{
                top: 20px;
                left: 10px;
                right: 10px;
            }
            .swal2-container {
                zoom: 1.5;
            }
            .swal2-icon {
                width: 5em !important;
                height: 5em !important;
                border-width: .25em !important;
            }
        </style>
    </head>
    <body>
    <div class="row text-center">

    <?php
        $datos = json_decode($clsBaseDatos->busca_codigo($token),true);
        if ($datos['iniciado'] == 1){
            $interval = strtotime($datos["vigencia"]) - strtotime(date("Y-m-d H:i:s"));
        } else {
            $interval =  strtotime($datos["inicio"]) - strtotime(date("Y-m-d H:i:s")) ;
        }
        ?>

        <div class="row text-left">
            <div class="col-12">
                <h1><label><?= $datos["nombre"]; ?></label></h1>
                <h3 class="text-danger"><?= $datos["evento"]; ?></h3>
            </div>
        </div><br>
        <?php
        if (($interval < 0) || ($datos['iniciado'] == 0)){
            if (($datos['iniciado'] == "0") && ($datos['terminado'] == "0")){
                $interval =  strtotime($datos["inicio"]) - strtotime(date("Y-m-d H:i:s")) ; ?>
                    <h3>Token valido en:</h3><br><br>
                    <div class="container"><div class="clock" style="zoom: 0.6; -moz-transform: scale(0.6);"></div><br><br></div>
                    <script type="text/javascript">
                        var clock = $('.clock').FlipClock(<?= $interval; ?>, {
                            clockFace: 'DailyCounter',
                            language: 'es',
                            countdown: true,
                            callbacks: {
                                stop: function() {
                                    setTimeout(function(){ location.reload(); },1000);
                                }
                            }
                        });
                    </script>
                    <br><br>
                    <div class="container"><video style="width: 90%;" controls autoplay>
                            <source src="common_files/img/invitados.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                    </video></div>


            <?php } else { ?>
                    <h2 class="text-danger">Token caducado !</h2>
            <label>Gracias por utilizar SmartDoor, este token ya a caducado, el evento se a terminado.</label>
            <br><br>
            <div class="container"><video style="width: 90%;" controls autoplay>
                <source src="common_files/img/invitados.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video></div>
                <?php }
        } else { ?>
            <label>Utiliza este código para entrar ó da clic en la imágen.</label><br>
            <br>
            <img src="common_files/clases/img_qr.php?codigo=<?= $token; ?>" onclick="abrepuerta('<?= base64_encode($token); ?>','<?= base64_encode($datos["estacion"]); ?>','<?= base64_encode($datos["ubicacion"]); ?>');" style="width: 100%;"><br><br>
            <div class="container"><div class="clock" style="zoom: 0.5; -moz-transform: scale(0.5); left: 20px; z-index: 1"></div><br><br><br></div>
            <script type="text/javascript">
                var clock = $('.clock').FlipClock(<?= $interval; ?>, {
                    clockFace: 'DailyCounter',
                    language: 'es',
                    countdown: true,
                    callbacks: {
                        stop: function() {
                            setTimeout(function(){ location.reload(); },1000);
                        }
                    }
                });
            </script>
            <div class="container"><video style="width: 90%;" controls autoplay>
                <source src="common_files/img/invitados.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video><br><br><br><br><br><br><br><br></div>
        <?php } ?>
    </div>
    <footer class="main-footer fixed-bottom text-sm">
        <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>
        <div class="float-right">V<b><?= date("ymd",filectime(__FILE__)); ?></b></div>
    </footer>

    <script src="common_files/java/popper.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="common_files/java/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
