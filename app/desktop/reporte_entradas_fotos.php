<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

include("../common_files/clases/seguridad.php");
if(!isset($_SESSION)) { session_start(); }
if (!is_null(filter_input(INPUT_GET, 'id'))) { $id = filter_input(INPUT_GET, 'id'); }

$dir = "../esp8266/img/registro/" . $id;
$archivos = array_diff(scandir($dir), array('..', '.'));
sort($archivos);
for ($i=0;$i<sizeof($archivos);$i++) {
    $nombre_cam = $archivos[$i];
    $fotos_dir = array_diff(scandir($dir . "/" . $archivos[$i]), array('..', '.'));
    sort($fotos_dir);
    ?>
    <div class="row">
    <div class="col-sm-12">
        <div class="card card-default card-solid">
            <div class="card-header with-border">
                <h3 class="card-title"><?= $nombre_cam; ?></h3>
            </div>
            <div class="card-body">
                <?php
                for ($j=0;$j<sizeof($fotos_dir);$j++) {
                    echo "<div style=\"padding: 20px;\"><img class=\"elevation-3\" src='" . $dir . "/" . $nombre_cam . "/" . $fotos_dir[$j] . "' height='200' onclick=\"window.open('" . $dir . "/" . $nombre_cam . "/" . $fotos_dir[$j] . "','_blank');\"></div>";
                }
                ?>
            </div>
        </div>
    </div>
    </div>
<?php    }
