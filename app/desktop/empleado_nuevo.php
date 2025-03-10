<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include("../common_files/clases/seguridad.php");


require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
if (null !== (filter_input(INPUT_GET,'id'))) {
    $id = filter_input(INPUT_GET,'id');
    $estacion = filter_input(INPUT_GET,'estacion');
}

if (null !== (filter_input(INPUT_POST,'guardar'))) {
    $id = filter_input(INPUT_POST,'id');
    $estacion = filter_input(INPUT_POST,'estacion');
    $nombre = filter_input(INPUT_POST,'nombre');
    $horario = filter_input(INPUT_POST,'horario');
    // generar un codigo y registro para el empleado
    $codigo = $clsBaseDatos->empleados_registro($nombre,$id,$horario);
}

$sth = $clsBaseDatos->estacion_puertas($id);

$index_plan = -1;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
    if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $id){ $index_plan = $i; break; }
}
$habilitado = (($_SESSION['usuario']['plan_pago'][$index_plan]['plan_empleados'] > $_SESSION['usuario']['plan_pago'][$index_plan]['uso_empleados'])||($_SESSION['usuario']['plan_pago'][$index_plan]['plan_empleados'] == -1))?1:0;
?>
<h1 class="text-bold"><?= $_SESSION['WEBPAGE_TITLE']; ?></h1>
<section class="content-header">
    <h1>
        Nuevo Empleado en <label><?= $estacion; ?></label>
    </h1>
</section>

<?php
if (null == (filter_input(INPUT_POST,'guardar'))) {
    if ($habilitado){

    $idpuerta = 0;
    while ($row = $sth->fetch()) {
        if ($idpuerta !== $row[0]) {

            $idpuerta = $row[0]; ?>
            <div class="col-md-6">
            <div class="card card-danger">
            <div class="card-header with-border">
                <h3 class="card-title">Para puerta: "<label><?= $row[1]; ?></label>"</h3>
            </div>
            <div class="card-body form-group">
        <?php } ?>
        <div class="row">
            <div class="col-md-8"><label>Nombre:</label><input type="text" id="txtNombre" name="txtNombre_<?= time(); ?>" autocomplete="off_<?= time(); ?>" class="form-control" placeholder="Nombre"></div>
        </div>
        <div class="row">
            <div class="col-md-8"><label>Horario:</label>
                <select  class="form-control select2" id="cboHorario">
                    <option value="0" selected>Selecciona</option>
                    <?php
                    echo $id;
                    $sth_horario = $clsBaseDatos->horarios_listado($id);
                    while ($row_horario = $sth_horario->fetch()) { ?>
                        <option value="<?= $row_horario[0]; ?>">Lunes: <?= $row_horario[1]; ?> - Martes: <?= $row_horario[2]; ?> - Miercoles: <?= $row_horario[3]; ?> - Jueves: <?= $row_horario[4]; ?> - Viernes: <?= $row_horario[5]; ?> - Sábado: <?= $row_horario[6]; ?> - Domingo: <?= $row_horario[7]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    <?php }
    if ($idpuerta > 0) { ?>
        </div>
        <div class="card-footer text-center">
            <div class="card-tools pull-right">
                <button type="button" id="btnagregausuario" class="btn btn-warning float-right" onclick="validaEmpleadoNuevo('<?= base64_encode($id); ?>','<?= base64_encode($estacion); ?>');">Agregar Empleado</button>
            </div>
        </div></div></div>
    <?php }
    } else { // limite de usuarios alcanzado, necesita comprar mas ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= $estacion; ?></label></h3>
            </div>
            <div class="card-body form-group text-center">
                <div class="row"><div class="col-12"><label class="text-danger">Límite de empleados alcanzado, Quieres comprar mas ?</label></div></div><br>

                <div class="row">
                    <div class="col-9">
                        Necesitas ayuda ? <a href="mailto:ayuda@smartdoor.mx">ayuda@smartdoor.mx</a>
                    </div>
                    <div class="col-1">
                    </div>
                    <div class="col-2">
                    </div>
                </div>
            </div>
        </div>

    <?php }
} else { ?>
<div class="col-md-6">
    <div class="card card-danger">
        <div class="card-header with-border">
            <h3 class="card-title">C&oacute;digo para: "<label><?= $nombre; ?></label>"</h3>
        </div>
        <div class="card-body form-group text-center">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10"><img id="imgqr" src="../common_files/clases/img_qr.php?codigo=<?= $codigo; ?>" width="400" height="400"><br><br></div>
                <div class="col-md-1"></div>
            </div>
        </div>
        <div class="card-footer text-center">
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-success float-right" onclick="descargarQr('<?= base64_encode($nombre); ?>');">Descargar QR <i class="fas fa-download"></i></button>
            </div>
        </div>
</div></div>
<?php } ?>

