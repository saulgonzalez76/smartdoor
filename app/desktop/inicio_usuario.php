<?php

if(!isset($_SESSION)) { session_start(); }

include("../common_files/clases/seguridad.php");
include("../common_files/clases/qrcode/qrlib.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth_puertas = $clsBaseDatos->usuarios_listado_puertas_web();

while ($row_puerta = $sth_puertas->fetch()){ ?>
    <div class="col-md-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $row_puerta[4]; ?> <label><?= $row_puerta[5]; ?></label></h3>
                <div class="pull-right"><label class="text-success" id="lblstatus_<?= $row_puerta[2] ?>"></label></div>
            </div>
            <div class="box-body form-group text-center">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div onclick="abrepuerta('<?= base64_encode($row_puerta[3]); ?>','<?= base64_encode($row_puerta[5] . " de " . $row_puerta[4]); ?>',<?= $row_puerta[2]; ?>);" id="container" style="height: 400px; overflow: hidden;
                                background: url(../common_files/clases/img_qr.php?codigo=<?= $row_puerta[3]; ?>) center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                                z-index: 999;opacity: 1;
                                position: relative;"></div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>