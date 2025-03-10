<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['usuario']['idusuario'])) { header('Location: ../login.php'); }

include("../common_files/clases/qrcode/qrlib.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$version = time();

?>
<!DOCTYPE html>
<html>
    <head>
        <?php include '../common_files/meta_tags.php'; ?>
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="../common_files/css/ionicons.min.css">
        <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">
        <link rel="stylesheet" href="../plugins/fullcalendar/fullcalendar.min.css">
        <link rel="stylesheet" href="../plugins/datatables/jquery.dataTables.min.css">
        <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
        <link rel="stylesheet" href="../plugins/iCheck/all.css">
        <link rel="stylesheet" href="../plugins/select2/select2.min.css">
        <link rel="stylesheet" href="../plugins/morris/morris.css">
        <link rel="stylesheet" href="../plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
        <link rel="stylesheet" href="../common_files/css/estilos.css">

        <script src="../common_files/java/codigo.js?<?= $version; ?>"></script>
        <script src="../common_files/java/jquery-latest.min.js"></script>
        <script src="../common_files/java/jquery.min.js"></script>
        <script src="../common_files/java/usuarios.js?<?= $version; ?>"></script>
        <script src="../common_files/java/javascript.util.min.js" type="text/javascript"></script>
        <script src="../common_files/java/jsts.min.js" type="text/javascript"></script>
        <script src="../common_files/java/sweetalert.js" type="text/javascript"></script>
        <script src="../common_files/java/jszip.js"></script>
        <script src="../common_files/java/base64.js"></script>
        <style>
            .swal2-container {
                zoom: 1.5;
            }
            .swal2-icon {
                width: 5em !important;
                height: 5em !important;
                border-width: .25em !important;
            }
            body {
                margin-top: 20px;
                width: 100%;
                background-color: #eceaea;
            }

            .bloqueado{
                position: fixed;
                top: 0;
                bottom: 50px;
                left: 0;
                right: 0;
                width: 100%;
                height: 85%;
                z-index: 9999;
            }

            .bloqueado_content{
                position: relative;
                width: 100%;
                height: 100%;
                top: 20%;
                left: 20%;
            }

            #lblbloqueado{
                transform: rotate(-25deg);
                font: italic small-caps bold 50px Georgia, serif;
            }

            #footer{
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 35px;
                border-top: 2px solid #0d6aad;
                vertical-align: center;
                padding-top: 8px;
                background-color: #f5f1f1;
                z-index: 999;
            }

        </style>
        <script type="javascript">
            var tmrDatePicker;
            var tmrPuertaStatus = setInterval(getPuertaStatus, 1000);
        </script>
    </head>
    <body class="hold-transition layout-fixed sidebar-mini">
    <div id="divseguridad"></div>
    <input type="hidden" id="token" name="token" value="">
    <div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php $imgsrc = "../common_files/img/0.png"; if (file_exists("../".$_SESSION['usuario']['img'])) { $imgsrc = "../".$_SESSION['usuario']['img']; } ?>
                    <img src="<?= $imgsrc; ?>?<?= $version; ?>" id="imgavatarusr" class="user-image">
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <form id="frmfotoavatarusuario" action="#" method="POST" enctype="multipart/form-data">
                            <input type="file" id="archavatarusr" name="archavatarusr" style="display: none">
                        </form>
                        <img style="cursor: pointer" src="<?= $imgsrc; ?>?<?= $version; ?>" id="imgavatarusr2" onclick="javascript:cambiaavatar('imgavatarusr2','fotousuario',<?= $_SESSION['usuario']['idusuario']; ?>,'archavatarusr');archavatarusr.click();" class="img-circle" alt="Imagen de usuario">
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

    <aside class="main-sidebar sidebar-dark-primary elevation-5">
        <a href="#" class="brand-link text-center">
            <img src="../common_files/img/logo.jpg" alt="Spartan" class="brand-image img-circle elevation-3" style="opacity: .7">
            <span class="brand-text font-weight-light">smartDoor</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <?php include "menu_usuario.php"; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content" id="contenido">


            <?php $sth_puertas = $clsBaseDatos->usuarios_listado_puertas_web();
            while ($row_puerta = $sth_puertas->fetch()){ ?>
                <div class="col-md-12">
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                            <div class="pull-right"><label class="text-success text-lg" id="lblstatus_<?= $row_puerta[2]; ?>"></label></div>
                        </div>
                        <div class="box-body form-group text-center">
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10">
                                    <div onclick="abrepuerta('<?= base64_encode($row_puerta[2]); ?>','<?= base64_encode($row_puerta[4] . " de " . $row_puerta[3]); ?>');" id="container" style="height: 400px; overflow: hidden;
                                            background: url('../common_files/clases/img_qr.php?codigo=<?= $row_puerta[2]; ?>') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                                            z-index: 999;opacity: 1;
                                            position: relative;"></div>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                        </div>
                        </div>
                </div>
            <?php } ?>
        </section>
    </div>




    <div id="footer">
        <div class="container">
            <div class="float-right">
                <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>
            </div>
        </div>
    </div>
    </div>
    <script src="../plugins/jquery/jquery.min.js?<?= $version; ?>"></script>
    <script src="../plugins/popper/popper.min.js?<?= $version; ?>"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js?<?= $version; ?>"></script>
    <script src="../plugins/datatables/jquery.dataTables.js?<?= $version; ?>"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js?<?= $version; ?>"></script>
    <script src="../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
    <script src="../common_files/java/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js?<?= $version; ?>"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js?<?= $version; ?>"></script>
    <script src="../dist/js/adminlte.js?<?= $version; ?>"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="../plugins/select2/select2.full.min.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="../plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="../plugins/iCheck/icheck.min.js"></script>
    <script src="../plugins/fastclick/fastclick.js"></script>
    <script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="../plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
    </body>
</html>
