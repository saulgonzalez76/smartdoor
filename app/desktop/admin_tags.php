<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

if (filter_input(INPUT_POST, 'idestacion') !== null) {
    $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
    $sth = $clsBaseDatos->estacion_usuarios($idestacion);
}
if ((filter_input(INPUT_POST, 'idusuario') !== null) && (filter_input(INPUT_POST, 'tag') !== null)) {
    $idusuario = filter_input(INPUT_POST, 'idusuario');
    $tag = str_replace("https://smartdoor.mx?t=","",filter_input(INPUT_POST, 'tag'));
    $tag = base64_decode($tag);
    $clsBaseDatos->usuarios_nfc_tag_guarda($idusuario,$tag);
}

?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-danger">
            <div class="card-body no-padding">
                <?php while ($row = $sth->fetch()){ ?>
                    <div class="row">
                            <div class="row elevation-2">
                                <div class="col-lg-12">
                                    <h3><?= $row[1]; ?></h3>
                                    <label>Mac Tag:</label>
                                    <input type="text" id="txttag_<?= $row[0]; ?>" value="" title="MAC TAG">
                                    <button class="btn btn-danger" id="btncalc_<?= $row[0]; ?>" onclick="admin_tags_encript(<?= $row[0]; ?>);">Calcular</button>
                                    <button class="btn btn-primary" id="btnguarda_<?= $row[0]; ?>" disabled onclick="admin_tags_guarda('<?= base64_encode($idestacion); ?>',<?= $row[0]; ?>)">Guardar</button>
                                </div>
                            </div>
                    </div><br>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
