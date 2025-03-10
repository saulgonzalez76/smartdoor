<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
?>
<!doctype html>
<html lang="es">
<head>
    <?php    include '../common_files/meta_tags.php'; ?>
    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../common_files/css/sign-in.css" rel="stylesheet">
    <script language="JavaScript" type="text/javascript">
        function cargafoto() {
            document.getElementById('archivo').onchange = function (evt) {
                var tgt = evt.target || window.event.srcElement,
                    files = tgt.files;
                // FileReader support
                if (FileReader && files && files.length) {
                    var fr = new FileReader();
                    fr.onload = function () {
                        document.getElementById("imgusuario").src = fr.result;
                    }
                    fr.readAsDataURL(files[0]);
                }
            }
        }

    </script>
</head>
<body class="text-center">


<form id="datosusuario" class="form-signin" action="subir_archivos.php?fotousuario=1" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- /.box -->
            <!-- general form elements disabled -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Actualiza tu imagen.</h3>
                </div>
                <div class="box-body">
                        <input type="hidden" name="idcliente" value="<?= $idcliente; ?>" id="idcliente" />
                        <div class="form-group has-error">
                            <div class="row">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6">
                                    <input type="file" id="archivo" name="archivo" style="display: none">
                                    <div id="foto"><img src="../common_files/img/no_image.png" onclick="javascript:cargafoto();archivo.click();" height="240" style="cursor: pointer" width="360" id="imgusuario" onError="this.onerror=null;this.src='../common_files/img/no_image.png';"></div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-info pull-left" onclick="javascript:window.location.href = 'main.php';">Despues</button>
                                    <button type="button" class="btn btn-info pull-right" onclick="submit()">Guardar</button>
                                </div>
                            </div>

                        </div>

                </div>
            </div>
        </div>

    </div>

</form>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="../common_files/java/jquery.min.js"></script>
<script src="../common_files/java/popper.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../common_files/java/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
