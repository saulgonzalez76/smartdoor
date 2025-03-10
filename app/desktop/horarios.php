<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth_estaciones = $clsBaseDatos->estacion_admin();
$estacion = "";
if (!is_null(filter_input(INPUT_GET, 'estacion'))) { $estacion = base64_decode(filter_input(INPUT_GET, 'estacion')); }
if ($sth_estaciones->rowCount() > 0){
?>
<h1 class="text-bold">Horarios</h1>
<label>Crea un horario de apertura para controlar el acceso.</label>
<br><br>
<div class="row">
    <div class="col-sm-4 float-left">
        <label>Estación:</label>
        <select class="form-control select2" id="cboestacionhorario" onchange="ajaxpage('horarios.php?estacion=' + Base64.encode(this.value),'contenido');">
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
        <div class="row">
            <div class="col-md-3">

                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Lunes</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtlunesinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtlunesfin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Martes</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtmartesinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtmartesfin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Miercoles</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtmiercolesinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtmiercolesfin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Jueves</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtjuevesinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtjuevesfin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Viernes</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtviernesinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtviernesfin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Sábado</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtsabadoinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtsabadofin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Domingo</h3>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Inicio:</label>
                        <input type="time" class="form-control" id="dtdomingoinicio" onchange="valida_horarios();"/>
                    </div>
                    <div class="col-md-6">
                        <label>Fin:</label>
                        <input type="time" class="form-control" id="dtdomingofin" onchange="valida_horarios();"/>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button id="btnGuardar" disabled class="btn float-right btn-primary" onclick="guardar_horario();">Guardar&nbsp;&nbsp;&nbsp;<i class="fas fa-save"></i> </button>
            </div>
        </div>
    </div>
</div>
<br><br>
<div class="row">
<div class="col-sm-12">
    <div class="card card-default card-solid">
        <div class="card-header with-border">
            <h3 class="card-title">Listado de horarios</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <table id="lstentradas" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miercoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sabado</th>
                    <th>Domingo</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sth = $clsBaseDatos->horarios_listado($estacion);
                while ($row = $sth->fetch()) {
                    ?>
                    <tr>
                        <?php for($i=1;$i<8;$i++){ ?>
                            <td><?= date("H:i",strtotime(explode(",",$row[$i])[0])) . " a " . date("H:i",strtotime(explode(",",$row[$i])[1])); ?></td>
                        <?php }?>
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
