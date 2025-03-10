<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('email.php');
include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
if (null !== (filter_input(INPUT_GET,'id'))) {
    $id = filter_input(INPUT_GET,'id');
    $estacion = filter_input(INPUT_GET,'estacion');
}

if (null !== (filter_input(INPUT_POST,'guardar'))) {
    $id = filter_input(INPUT_POST,'id');
    if (null !== (filter_input(INPUT_POST,'idusuario'))) { $idusuario = filter_input(INPUT_POST,'idusuario'); $nuevo = true; } else { $idusuario = 0; $nuevo = false; }
    $whatsapp = filter_input(INPUT_POST,'whatsapp');
    $estacion = filter_input(INPUT_POST,'estacion');
    $nombre = filter_input(INPUT_POST,'nombre');
    $email = filter_input(INPUT_POST,'email');
    // generar un password para enviar por correo
    $codigo = $clsBaseDatos->cliente_nuevo($email,$nombre,$whatsapp,$id,$idusuario);
    if ($codigo !== "") {
        // falta enviar el password por correo ...
        $password = explode(";", $codigo)[1];
        $codigoemail = str_replace("=", "", base64_encode($email));
        // envio correo electronico para confirmar email
        $confirmado = ($clsBaseDatos->usuarios_valida_confirmado($email) > 0) ? true : false;
        if (!$confirmado) {
            //enviaCorreo(correoNuevoUusario($codigoemail, $email, $password), $email, $nombre, "SmartDoor - Confirma tu correo.", false, "no-reply");
        }
        $index_plan = -1;
        for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
            if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $id){ $index_plan = $i; break; }
        }
        $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios'] += 1;
    } else {
        echo "";
        exit;
    }
}

$sth = $clsBaseDatos->estacion_puertas($id);
if (!$row = $sth->fetch()) { ?>
    <h1 class="text-danger">Error: tenemos un error en el sistema, disculpa las molestias.</h1>
<?php }
// falta limitar el numero de usuarios del plan
//print_r($_SESSION['usuario']['plan_pago']);
$index_plan = -1;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
    if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $id){ $index_plan = $i; break; }
}
$usuarios_disponibles = 0;
$habilitado = (($_SESSION['usuario']['plan_pago'][$index_plan]['plan_usuarios'] > $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios'])||($_SESSION['usuario']['plan_pago'][$index_plan]['plan_usuarios'] == -1))?1:0;
$usuarios_disponibles = $_SESSION['usuario']['plan_pago'][$index_plan]['plan_usuarios'] - $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios'];
?>
<h1 class="text-bold"><?= $_SESSION['WEBPAGE_TITLE']; ?></h1>
<section class="content-header">
    <h1>
        Nuevo Usuario en <label><?= $estacion; ?></label>
    </h1>
</section>

<?php
if (null == (filter_input(INPUT_POST,'guardar'))) {
    if ($habilitado){ ?>
            <div class="row">
        <div class="col-md-6">
        <div class="card card-danger">
        <div class="card-header with-border">
            <h3 class="card-title">Para puerta: "<label><?= $row[1]; ?></label>"</h3>
            <label class="float-right"><?= $usuarios_disponibles; ?> Usuario(s) disponibles.</label>
        </div>
        <div class="card-body form-group">

        <div class="row">
            <div class="col-md-8"><label>Nombre:</label><input type="text" id="txtNombre" name="txtNombre_<?= time(); ?>" autocomplete="off_<?= time(); ?>" class="form-control" placeholder="Nombre"></div>
        </div>
        <div class="row">
            <div class="col-md-8"><label>Correo:</label>
                    <input type="email" id="txtCorreo" name="txtCorreo_<?= time(); ?>" autocomplete="off_<?= time(); ?>" class="form-control" placeholder="eMail">
            </div>
        </div>
        <div class="row">
            <div class="col-md-8"><label>Whatsapp / Celular:</label>
                <div class="row">
                    <div class="col-6">
                        <select class="form-control select2" id="cbopais"><option value="52" selected>México</option><option value="1">Estados Unidos</option></select>
                    </div>
                    <div class="col-6">
                        <input type="number" id="txtWhatsapp" name="txtWhatsapp_<?= time(); ?>" min="1111111111" max="9999999999"  maxlength="10" class="form-control" autocomplete="off_<?= time(); ?>" placeholder="Whatsapp / Cel" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <div class="card-tools pull-right">
            <button type="button" id="btnagregausuario" class="btn btn-primary float-right" onclick="validaUsuarioNuevo('<?= base64_encode($id); ?>','<?= base64_encode($estacion); ?>');">Agregar Usuario</button>
        </div>
    </div></div></div>

        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header with-border">
                    <h3 class="card-title">Para puerta: "<label><?= $row[1]; ?></label>"</h3>
                    <label class="float-right"><?= $usuarios_disponibles; ?> Usuario(s) disponibles.</label>
                </div>
                <div class="card-body form-group">
                    <div class="row">
                        <label>Para poder leer el archivo es necesario que tenga 3 columnas, no modifique el encabezado.</label><br><br>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="pull-left text-gray">Ejemplo de archivo:</label>
                            <a href="../common_files/img/listado.xlsx" class="float-right">Descargar machote</a>
                        </div>
                    </div>
                    <div class="row">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th>A</th>
                                <th>B</th>
                                <th>C</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>NOMBRE</td>
                                    <td>CORREO</td>
                                    <td>TEL</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>nombre completo del usuario</td>
                                    <td>correo_del_usuario@dominio.com</td>
                                    <td>528341234567</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-8">
                            <label>Buscar archivo:</label>
                            <input type="file" id="archnuevousuario" onchange="procesar_lista_nuevo_usuario(<?= $usuarios_disponibles; ?>);">
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="lstListaImportar" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Whatsapp</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <label class="float-left text-danger text-bold" id="lblerrorImporta"></label>
                    <button type="button" id="btnagregausuariolista" disabled class="btn btn-primary float-right" onclick="creaUsuariosLista('<?= base64_encode($id); ?>','<?= base64_encode($estacion); ?>');">Procesar lista</button>
                </div>
            </div>
        </div>
            </div>
<?php } else { // limite de usuarios alcanzado, necesita comprar mas ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= $estacion; ?></label></h3>
            </div>
            <div class="card-body form-group text-center">
                <div class="row"><div class="col-12"><label class="text-danger">Límite de usuarios alcanzado, Quieres comprar mas ?</label></div></div><br>

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
                    <div class="col-md-10"><img src="../common_files/clases/img_qr.php?codigo=<?= explode(";",$codigo)[2]; ?>" width="400" height="400"><br><br></div>
                    <div class="col-md-1"></div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        Usuario: <label><?= $email; ?></label><br>
                        Pass: <label><?= explode(";",$codigo)[1]; ?></label>
                        <?php if (($nuevo) && (!$confirmado)){ ?>
                            <br>
                            <label>Importante: </label><h3 class="text-danger">Es necesario que el usuario confirme su correo electrótico para activar su cuenta.</h3>
                        <?php } ?>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="card-footer text-center">
                <div class="card-tools pull-right">
                    <div id="descarga"></div>
                    <a href="descarga.php?tipo=1&archivo=<?= $codigo; ?>"><button type="button" class="btn btn-primary" >Descargar</button></a>

                </div>
            </div>
        </div>
    </div>
<?php } ?>

