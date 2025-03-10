<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

$mesletra = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$mesnum = array('01','02','03','04','05','06','07','08','09','10','11','12');
$diasemana = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
$anio = date("Y");
if (null !== (filter_input(INPUT_GET, 'anio'))) { $anio = filter_input(INPUT_GET, 'anio'); }
$mes = date("n");
if (null !== (filter_input(INPUT_GET, 'mes'))) { $mes = filter_input(INPUT_GET, 'mes'); }

if (null !== (filter_input(INPUT_GET, 'e'))) {
    $idregistro = filter_input(INPUT_GET, 'e');
    $clsBaseDatos->pagos_fraccionamientos_eliminar($idregistro);
}

if (null !== (filter_input(INPUT_GET, 'g'))) {
    $idcliente = filter_input(INPUT_GET, 'idcliente');
    $idestacion = filter_input(INPUT_GET, 'idestacion');
    $monto = filter_input(INPUT_GET, 'monto');
    $fecha = filter_input(INPUT_GET, 'fecha');
    $clsBaseDatos->pagos_fraccionamientos_nuevo($idcliente,$monto,$fecha,$_SESSION['usuario']['idusuario'],$idestacion);
}

$encontrado = false;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++) {
    if ($_SESSION['usuario']['plan_pago'][$i]["tipo_instalacion"] == 2) {
        $encontrado = true;
        break;
    }
}
if (!$encontrado) { ?>
    <h3 class="text-center">Uso exclusivo para fraccionamientos !</h3>
<?php exit; } ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card card-default card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Filtrar</h3>
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
                                        url: 'pagos.php?anio=' + this.value + '&mes=' + _('cbomes').value,
                                        success: function (data) {
                                        _('contenido').innerHTML = data;
                                        setTimeout(function() {formatoTabla(true,true,false,true,true,true,true,('csvHtml5,excelHtml5,pdfHtml5,copyHtml5').split(','));},1000);
                                        }
                                        });">
                                    <?php
                                    for ($i=-1;$i<3;$i++){ ?>
                                        <option value="<?= date('Y') - $i; ?>" <?php if ((date('Y') - $i) == $anio) { echo "selected"; } ?>><?= date('Y') - $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Mes</label>
                                <select  class="form-control select2" id="cbomes" onchange="ajaxpage('../common_files/cargando.html' , 'contenido');
                                        $.ajax({
                                        url: 'pagos.php?anio=' + _('cboanio').value + '&mes=' + this.value,
                                        success: function (data) {
                                        _('contenido').innerHTML = data;
                                        setTimeout(function() {formatoTabla(true,true,false,true,true,true,true,('csvHtml5,excelHtml5,pdfHtml5,copyHtml5').split(','));},1000);
                                        }
                                        });">
                                    <option value="0">Todo</option>
                                    <?php
                                    for ($i=0;$i<12;$i++){ ?>
                                        <option value="<?= $i+1; ?>" <?php if (($i +1) == $mes) { echo "selected"; } ?>><?= $mesletra[$i]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div><br>
            </div>
        </div>
    </div>
</div>

<?php
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
    if ($_SESSION['usuario']['plan_pago'][$i]["tipo_instalacion"] == 2){
        $sthestacion = $clsBaseDatos->estacion_nombre($_SESSION['usuario']['plan_pago'][$i]["idestacion"],"info");
        $row_estacion = $sthestacion->fetch(PDO::FETCH_NAMED);
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title"><?= $row_estacion['nombre_estacion']; ?> <label><?= $row_estacion['nombre_puerta']; ?></label></h3>
                    </div>
                    <div class="card-body">
                        <?php if($clsBaseDatos->usuarios_esAdmin($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$_SESSION['usuario']['idusuario']) > 0){ ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="txtmonto">Usuario:</label>
                                            <select  class="form-control select2" id="cbousuario">
                                                        <option value="0">Selecciona</option>
                                                        <?php
                                                        $sth_usuario = $clsBaseDatos->estacion_usuarios($_SESSION['usuario']['plan_pago'][$i]["idestacion"]);
                                                        while ($row_usuario = $sth_usuario->fetch(PDO::FETCH_NAMED)){ ?>
                                                            <option value="<?= $row_usuario['idregistro']; ?>" <?php if ($_SESSION['usuario']['plan_pago'][$i]["idclientepuerta"] == $row_usuario['idregistro']) { echo "selected"; } ?>><?= $row_usuario['nombre']; ?></option>
                                                        <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="txtmonto">Monto:</label>
                                            <input type="text" class="form-control" id="txtmonto<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>" placeholder="0.00" name="txtmonto<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>" autocomplete="false_<?= time(); ?>" >
                                        </div>
                                        <div class="col-md-3">
                                            <label for="dtfecha">Fecha:</label><br>
                                            <input type="date" id="txtdtfecha<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>" class="form-control" data-date-format="yyyy-mm-dd" value="<?= date("Y-m-d"); ?>"/>
                                        </div>
                                        <div class="col-md-3"><br>
                                            <button class="btn btn-primary float-right" onclick="ajaxpage('pagos.php?g=1&idestacion=<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>&idcliente='+_('cbousuario').value+'&monto='+_('txtmonto<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>').value+'&fecha=' + _('txtdtfecha<?= $_SESSION['usuario']['plan_pago'][$i]["idestacion"]; ?>').value,'contenido');"><i class="fa fa-save"></i> Guardar</button>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        <?php } ?>
                        <table id="lstpagos_<?= str_replace("=","",base64_encode($_SESSION['usuario']['plan_pago'][$i]["idestacion"])); ?>" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <?php if($clsBaseDatos->usuarios_esAdmin($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$_SESSION['usuario']['idusuario']) > 0){ ?>
                                    <th></th>
                                    <th>Nombre</th>
                                <?php } ?>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Confirmado por</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sth = $clsBaseDatos->pagos_fraccionamientos_reporte($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$_SESSION['usuario']['plan_pago'][$i]["idclientepuerta"],$anio, str_pad($mes,2,"0",STR_PAD_LEFT));
                            if ($clsBaseDatos->usuarios_esAdmin($_SESSION['usuario']['plan_pago'][$i]["idestacion"], $_SESSION['usuario']['idusuario']) > 0) {
                                $sth = $clsBaseDatos->pagos_fraccionamientos_reporte_admin($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$anio, str_pad($mes,2,"0",STR_PAD_LEFT));
                            }
                            while ($row = $sth->fetch(PDO::FETCH_NAMED)) { ?>
                                <tr>
                                    <?php if($clsBaseDatos->usuarios_esAdmin($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$_SESSION['usuario']['idusuario']) > 0){ ?>
                                        <td><button class="btn btn-danger" onclick="
                                        Swal.fire({ title: 'Seguro quieres eliminar este pago ?', showCancelButton: true, confirmButtonText: 'Si', cancelButtonColor: '#d33', cancelButtonText: 'No', }).then((result) => { if (result.value) {
                                                    ajaxpage('pagos.php?e=<?= $row['idregistro']; ?>','contenido');
                                                    }});" title="Eliminar"><i class="fa fa-trash"></i></button> </td>
                                        <td><?= $row['usuario']; ?></td>
                                    <?php } ?>
                                    <td><?= $row['fecha_pago']; ?></td>
                                    <td>$ <?= number_format($row['monto_pago'],2); ?></td>
                                    <td><?= $row['nombre']; ?></td>
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
<?php } } ?>