<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");


require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth_estaciones = $clsBaseDatos->estacion_admin();
$estacion = "";
if (!is_null(filter_input(INPUT_GET, 'estacion'))) { $estacion = base64_decode(filter_input(INPUT_GET, 'estacion')); }

$mesletra = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$mesnum = array('01','02','03','04','05','06','07','08','09','10','11','12');
$diasemana = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
$anio = date("Y");
if (null !== (filter_input(INPUT_GET, 'anio'))) { $anio = filter_input(INPUT_GET, 'anio'); }
$mes = date("n");
if (null !== (filter_input(INPUT_GET, 'mes'))) { $mes = filter_input(INPUT_GET, 'mes'); }



if ($sth_estaciones->rowCount() > 0){
?>
<h1 class="text-bold">Reporte de entradas</h1>
<br><br>
<div class="row">
    <div class="col-sm-4 float-left">
        <label>Estación:</label>
        <select class="form-control select2" id="cboestacionhorario" onchange="ajaxpage('reporte_entradas.php?estacion=' + Base64.encode(this.value),'contenido'); setTimeout(function() {formatoTabla(true,true,false,false,true,true,true);},500);">
            <option <?php if($estacion == ""){ echo "selected"; } ?> value="">Selecciona una estación</option>
            <?php
            while ($row = $sth_estaciones->fetch()){ ?>
                <option <?php if($estacion == $row[0]){ echo "selected"; } ?> value="<?= $row[0]; ?>"><?= $row[1]; ?> - <?= $row[2]; ?></option>
            <?php }
            ?>
        </select>
    </div>
</div><br>
    <?php if($estacion != ""){ ?>
<div class="row">
<div class="col-sm-12">
    <div class="card card-default card-solid">
        <div class="card-header with-border">
            <h3 class="card-title">Registros de entrada</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Año</label>
                            <select  class="form-control select2" id="cboanio" onchange="
                                    ajaxpage('../common_files/cargando.html' , 'contenido');
                                    $.ajax({
                                    url: 'reporte_entradas.php?estacion=<?= base64_encode($estacion); ?>&anio=' + this.value + '&mes=' + _('cbomes').value,
                                    success: function (data) {
                                    _('contenido').innerHTML = data;
                                    setTimeout(function() {formatoTabla(true,true,false,true,true,true,true,('csvHtml5,excelHtml5,pdfHtml5,copyHtml5').split(','));},1000);
                                    }
                                    });">
                                <?php
                                for ($i=0;$i<3;$i++){ ?>
                                    <option value="<?= date('Y') - $i; ?>" <?php if ((date('Y') - $i) == $anio) { echo "selected"; } ?>><?= date('Y') - $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Mes</label>
                            <select  class="form-control select2" id="cbomes" onchange="ajaxpage('../common_files/cargando.html' , 'contenido');
                                    $.ajax({
                                    url: 'reporte_entradas.php?estacion=<?= base64_encode($estacion); ?>&anio=' + _('cboanio').value + '&mes=' + this.value,
                                    success: function (data) {
                                    _('contenido').innerHTML = data;
                                    setTimeout(function() {formatoTabla(true,true,false,true,true,true,true,('csvHtml5,excelHtml5,pdfHtml5,copyHtml5').split(','));},1000);
                                    }
                                    });">
                                <?php
                                for ($i=0;$i<12;$i++){ ?>
                                    <option value="<?= $i+1; ?>" <?php if (($i +1) == $mes) { echo "selected"; } ?>><?= $mesletra[$i]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div><br>
            <table id="lstentradas" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Fecha hora</th>
                    <th>Usuario / Empleado</th>
                    <th>Ubicacion</th>
                    <th>Foto</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sth = $clsBaseDatos->reporte_entradas($estacion,$anio, str_pad($mes,2,"0",STR_PAD_LEFT));
                while ($row = $sth->fetch(PDO::FETCH_NAMED)) {
                    $nombre = $row['nombre'];
                    if ($row['empleado'] !== "") { $nombre = $row['empleado']; }

                    $dir = "";
                    if (is_dir("../esp8266/img/registro/" . $row['idregistro'])){
                        $dir = "../esp8266/img/registro/" . $row['idregistro'];
                    }

                    ?>
                    <tr>
                        <td><?= $row['hora']; ?></td>
                        <td><?= $nombre; ?></td>
                        <td><?php if(($row['ubicacion'] != "") && ($row['ubicacion'] != "nfc")){ ?><img src="../common_files/img/google_maps.png" height="50" style="cursor: pointer;" onclick="verUbicacionEntrada('<?= $row['ubicacion']; ?>');" alt=""><?php } ?></td>
                        <?php
                        if($dir != ""){ ?>
                            <td><button type="button" class="btn btn-primary" onclick="ajaxpage('reporte_entradas_fotos.php?id=<?= $row['idregistro']; ?>','contenido');">Ver</button></td>
                        <?php } else { echo "<td></td>"; }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?php } } else { ?>
    <h1 class="text-danger">No administras ningúna estación !</h1>
<?php } ?>
