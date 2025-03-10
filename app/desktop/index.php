<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }

include("../common_files/clases/seguridad.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$clsBaseDatos->login_activity();
$version = time();

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php include '../common_files/meta_tags.php'; ?>
        <link rel="icon" href="../common_files/img/logo_transparente.png">
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="../common_files/css/ionicons.min.css">
        <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">
        <link rel="stylesheet" href="../plugins/fullcalendar/fullcalendar.min.css">
        <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css">
        <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
        <link rel="stylesheet" href="../plugins/iCheck/all.css">
        <link rel="stylesheet" href="../plugins/select2/select2.min.css">
        <link rel="stylesheet" href="../common_files/css/vue-treeselect.min.css">
        <link rel="stylesheet" href="../common_files/css/estilos.css?<?= time(); ?>">
        <link rel="stylesheet" href="../common_files/css/calendario.css?<?= time(); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
        <link rel="manifest" href="../site.webmanifest">

        <script src="../common_files/java/sentry.tracing.replay.7.64.min.js" crossorigin="anonymous"></script>
        <script>
            Sentry.init({
                dsn: "<?= $_SESSION['SENTRY_DSN']; ?>",
                tracesSampleRate: 1.0,
                profilesSampleRate: 1.0,
                enableProfiling: true,
                environment: "<?= $_SERVER['HTTP_HOST']; ?>",
                tracePropagationTargets: ["<?= $_SERVER['HTTP_HOST']; ?>"],
                integrations: [
                    // Add browser profiling integration to the list of integrations
                    new Sentry.BrowserTracing()
                ]
            });
            Sentry.setUser({ id: <?= $_SESSION['usuario']['idusuario']; ?>, name: "<?= $_SESSION['usuario']['nombre']; ?>", username: "<?= $_SESSION['usuario']['nickname']; ?>", email: "<?= $_SESSION['usuario']['email']; ?>" });
        </script>

        <script src="../common_files/java/codigo.js"></script>
        <script src="../common_files/java/jquery-latest.min.js"></script>
        <script src="../common_files/java/jquery.min.js"></script>
        <script src="../common_files/java/numeroALetras.js"></script>
        <script src="../common_files/java/wicket.js" type="text/javascript"></script>
        <script src="../common_files/java/wicket-gmap3.js" type="text/javascript"></script>
        <script src="../common_files/java/javascript.util.min.js" type="text/javascript"></script>
        <script src="../common_files/java/jsts.min.js" type="text/javascript"></script>
        <script src="../common_files/java/sweetalert.js?<?= time(); ?>" type="text/javascript"></script>
        <script src="../common_files/java/tv.js" type="text/javascript"></script>
        <script src="../common_files/java/Chart.js"></script>
        <script src="../common_files/java/jszip.js"></script>
        <script src="../common_files/java/base64.js"></script>
        <script src="../common_files/java/apexcharts.js"></script>
        <script src="../plugins/moment/moment.min.js"></script>
        <script src="../common_files/java/xlsx.full.min.js"></script>
        <?php if (getenv('APPLICATION_ENV') === "development") { ?>
            <script src="../common_files/java/main_desktop.js?<?= $version; ?>"></script>
            <script src="../common_files/java/menu_desktop.js?<?= $version; ?>" type="text/javascript"></script>
            <script src="../common_files/java/usuarios.js?<?= $version; ?>" type="text/javascript"></script>
            <script src="../common_files/java/contact_picker_api.js?<?= $version; ?>" type="text/javascript"></script>
        <?php } else { ?>
            <script src="../common_files/java/main_desktop.min.js?<?= $version; ?>"></script>
            <script src="../common_files/java/menu_desktop.min.js?<?= $version; ?>" type="text/javascript"></script>
            <script src="../common_files/java/usuarios.min.js?<?= $version; ?>" type="text/javascript"></script>
            <script src="../common_files/java/contact_picker_api.min.js?<?= $version; ?>" type="text/javascript"></script>
        <?php } ?>
        <script src="https://www.paypal.com/sdk/js?client-id=<?= $_SESSION['PAYPAL_API_CLIENT_ID']; ?>&currency=MXN&locale=es_MX"  data-namespace="paypal_sdk"></script>
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
    </head>

    <body class="hold-transition layout-fixed sidebar-mini text-sm">
    <div class="wrapper">
        <div id="divseguridad"></div>
        <input type="hidden" id="token" name="token" value="">
        <nav class="main-header navbar navbar-expand navbar-gray-dark navbar-light fixed-top">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="javascript:menu_inicio();" class="nav-link text-white">Inicio</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="dropdown user user-menu text-white">
                    <label class="text-success"><b>smart</b>DOOR</label>&nbsp;&nbsp;<label><?= $_SESSION['usuario']['nombre']; ?></label>&nbsp;&nbsp;
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php $imgsrc = "../common_files/img/0.png"; if (file_exists("../common_files/img/usuarios/".$_SESSION['usuario']['idusuario'].".png")) { $imgsrc = "../common_files/img/usuarios/".$_SESSION['usuario']['idusuario'].".png"; } ?>
                        <img src="<?= $imgsrc . "?" . filemtime($imgsrc); ?>" id="imgavatarusr" class="user-image" alt="Imagen de usuario">
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <form id="frmfotoavatarusuario" action="#" method="POST" enctype="multipart/form-data">
                                <input type="file" id="archavatarusr" name="archavatarusr" style="display: none">
                            </form>
                            <img style="cursor: pointer" src="<?= $imgsrc . "?" . filemtime($imgsrc); ?>" id="imgavatarusr2" onclick="javascript:cambiaavatar('imgavatarusr2','fotousuario',<?= $_SESSION['usuario']['idusuario']; ?>,'archavatarusr');archavatarusr.click();" class="img-circle" alt="Imagen de usuario">
                            <p> <?= $_SESSION['usuario']['nombre']; ?> <small>Ultimo Acceso: <?= $_SESSION['usuario']['fecha_acceso']; ?></small> </p>
                        </li>
                        <li class="user-footer">
                            <div class="float-left">
                                <a href="javascript:ajaxpage('registro_config.php' ,'contenido');" class="btn btn-default btn-flat"><i class="fas fa-cogs"></i></a>
                                <a href="../lock.php" class="btn btn-default btn-flat"><i class="fas fa-lock"></i></a>
                            </div>
                            <div class="float-right">
                                <a href="salir.php" class="btn btn-default btn-flat"><i class="fas fa-sign-out-alt"></i></a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-light-dark elevation-5">
            <a href="http://localhost" class="brand-link text-center bg-red">
                <img src="../common_files/img/logo_transparente.png?1" alt="Spartan" class="brand-image img-circle elevation-3" style="opacity: .7">
                <span class="brand-text font-weight-light"><b>smart</b>DOOR</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                        <?php include "menu.php"; ?>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="content-wrapper">
            <section class="content"><br><br><br>
                <div class="row" id="divmensaje" style="display: none;"></div>
                <div class="row" id="divactualiza" style="display: none;">
                    <div class="card card-danger bg-danger elevation-4">
                        <div class="card-body">
                            <div class='col-sm-12 text-center text-bold'><br>Algunos archivos se actualizaron en el servidor, es necesario refrestar la pagina para garantizar el buen funcionamiento del sistema.<br><br><button type="button" class="btn bg-white input-lg" onclick="location.reload();">A C T U A L I Z A R</button></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div id="contenido">
                        <?php include "usuario_desktop.php"; ?>
                    </div>
                    <br><br><br><br>
                </div>
            </section>
        </div>
        <?php include ("../common_files/footer.php"); ?>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/popper/popper.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../plugins/datatables/jquery.dataTables.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <script src="../common_files/java/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../dist/js/adminlte.js"></script>
    <script src="../common_files/java/dataTables.buttons.min.js"></script>
    <script src="../common_files/java/buttons.html5.min.js"></script>
    <script src="../plugins/select2/select2.full.min.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="../plugins/iCheck/icheck.min.js"></script>
    <script src="../plugins/fastclick/fastclick.js"></script>
    <script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    </body>
</html>
