<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$idusuario = 0;
$idpuerta = "";
$empleado = 0;
if (null !== (filter_input(INPUT_GET,'idcliente'))) {
    $idcliente = filter_input(INPUT_GET,'idcliente');
    $idpuerta = filter_input(INPUT_GET,'idpuerta');
    $sth = $clsBaseDatos->usuarios_puerta_datos($idcliente,$idpuerta);
}
if (null !== (filter_input(INPUT_GET,'idempleado'))) {
    $idcliente = filter_input(INPUT_GET,'idempleado');
    $idpuerta = filter_input(INPUT_GET,'idpuerta');
    $sth = $clsBaseDatos->empleado_puerta_datos($idcliente,$idpuerta);
    $empleado = 1;
}

$es_admin = $clsBaseDatos->usuarios_esAdmin($idpuerta,$idcliente);
$cantidad_admin = $clsBaseDatos->usuarios_cantAdmin($idpuerta);

$index_plan = -1;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
    if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $idpuerta){ $index_plan = $i; break; }
}
//print_r($_SESSION['usuario']['plan_pago']);
$cant_maxAdmin = $_SESSION['usuario']['plan_pago'][$index_plan]['plan_administradores'];
$tipo_install = $_SESSION['usuario']['plan_pago'][$index_plan]['tipo_instalacion'];


?>

<?php if ($row = $sth->fetch(PDO::FETCH_NAMED)){ ?>
<input type="hidden" id="txtbloqueado" value="<?= ($row['bloqueado'] == 0)?1:0; ?>">
<input type="hidden" id="txtadmin" value="<?= ($es_admin == 0)?1:0; ?>">

<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title">C&oacute;digo de: "<label><?= $row['nombre']; ?></label>" para puerta: "<label><?= $row['nombre_estacion']; ?> - <?= $row['nombre_puerta']; ?></label>"</h3>
    </div>
    <div class="card-body form-group text-center">
        <?php $imgsrc = "../common_files/clases/img_qr.php?codigo=" . $row['codigo']; if ($row['bloqueado'] == 1){ $imgsrc = "../common_files/img/blocked.png"; } ?>
        <img id="imgqr" src="<?= $imgsrc; ?>" style="width: 100%;">
    </div>
    <div class="card-footer text-sm">
        <div class="row">
            <?php if ($tipo_install != 2) { ?>
                <div class="col-sm-12">
                    <?php if ($idcliente != $_SESSION['usuario']['idusuario']){ ?>
                        <button id="btneliminar" type="button" class="btn btn-danger" onclick="eliminarUsuario('<?= base64_encode($row['codigo']); ?>','<?= base64_encode($idpuerta); ?>',<?= $idcliente; ?>);">Eliminar usuario <i class="fas fa-trash"></i></button>
                    <?php } else { ?>
                        <button id="btneliminar" type="button" disabled class="btn btn-danger">Imposible borrarse uno mismo <i class="fas fa-trash"></i></button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div><br>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!$empleado) { if ($es_admin == 0) { ?>
                    <button id="btnadmin" type="button" class="btn btn-primary" <?php if ($cantidad_admin == $cant_maxAdmin){ ?> title="Limite de administradores alcanzado" disabled <?php } ?> onclick="setAdminUsuario('<?= $idpuerta; ?>',<?= $idcliente; ?>,_('txtadmin').value);">Hacer administrador <?php if ($cantidad_admin == 3){ ?> (Limite de administradores alcanzado !) <?php } ?><i class="fas fa-user-cog"></i></button>
                <?php } else { ?>
                    <button id="btnadmin" type="button" class="btn btn-danger" <?php if ($cantidad_admin == 1){ ?> title="Administrador único, imposible deshabilitar" disabled <?php } ?> onclick="setAdminUsuario('<?= $idpuerta; ?>',<?= $idcliente; ?>,_('txtadmin').value);">Quitar administrador <?php if ($cantidad_admin == 1){ ?> (Administrador único, imposible deshabilitar) <?php } ?><i class="fas fa-user-cog"></i></button>
                <?php } }?>
            </div>
        </div><br>
        <div class="row">
            <div class="col-sm-12">
                <?php if ($row['bloqueado'] == 1) { ?>
                    <button id="btnbloquear" type="button" class="btn btn-primary" onclick="bloquearUsuario('<?= $row['codigo']; ?>',_('txtbloqueado').value);">Habilitar usuario <i class="fas fa-unlock-alt"></i></button>
                <?php } else { ?>
                    <button id="btnbloquear" type="button" class="btn btn-danger" onclick="bloquearUsuario('<?= $row['codigo']; ?>',_('txtbloqueado').value);">Bloquear usuario <i class="fas fa-user-lock"></i></button>
                <?php } ?>
            </div>
        </div><br>
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-success" onclick="descargarQr('<?= base64_encode($row['nombre']); ?>');">Descargar QR <i class="fas fa-download"></i></button>
            </div>
        </div>
    </div>
</div>
<?php }

?>
