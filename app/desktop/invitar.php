<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

include("../common_files/clases/seguridad.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth = $clsBaseDatos->usuarios_listado_puertas();
?>
<h1 class="text-bold">Invitar</h1>

<div class="row">
    <div class="col-12">
        <p>Los token son validos por 4 horas a partir de la fecha y hora de inicio.</p>
    </div>
</div>


<?php
$sth_puertas = $clsBaseDatos->usuarios_listado_puertas_web();

//print_r($_SESSION['usuario']['plan_pago']);
?>
<h3>Selecciona el acceso para tus invitados:</h3>
<div class="row" id="divinvitados_0">
<?php while ($row_puerta = $sth_puertas->fetch()){
    $index_plan = -1;
    $idestacion_b64 = str_replace("=","",base64_encode($row_puerta[0]));
    for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
        if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $row_puerta[0]){ $index_plan = $i; break; }
    }
    $visitas = $_SESSION['usuario']['plan_pago'][$i]['plan_visitas'] - $_SESSION['usuario']['plan_pago'][$i]['uso_visitas'];
    $visitas_costo = $_SESSION['usuario']['plan_pago'][$i]['plan_costo_visitas'];

    // falta meter el pago aqui !!!!
    if (($_SESSION['usuario']['plan_pago'][$i]["plan_vigente"] == 0) && ($_SESSION['usuario']['plan_pago'][$index_plan]['idplan'] != 1)){
        if ($clsBaseDatos->estacion_cliente_admin($row_puerta[0])) {
        // si es el administrador de la estacion, dejamos que procese el pago y pueda hacer cambios de planes
            $precio_plan = $_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_plan']; ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                </div>
                <div class="card-body form-group text-center">
                    <div class="row"><div class="col-12"><label class="text-danger">Lo sentimos, tu cuenta esta vencida, por favor realiza el pago para seguir disfrutando de nuestros servicios.</label></div></div><br>
                    <div class="row">
                        <div class="col-12">
                            <div class="row"><div class="col-12 text-left">Paquete actual:</div></div>
                            <div class="row"><div class="col-12 text-left"><h4><?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_nombre']; ?></h4></div></div>
                            <div class="row"><div class="col-12 text-left"><label><?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_descripcion']; ?></label></div></div>
                            <div class="row">&nbsp;</div>
                            <div class="row"><div class="col-12"><button class="btn btn-danger btn-sm float-right" onclick="_('divPaquetes').style='display:inline;';getPlanesServicio(<?= $_SESSION['usuario']['plan_pago'][$index_plan]['tipo_instalacion']; ?>,<?= $_SESSION['usuario']['plan_pago'][$index_plan]['idplan']; ?>,'<?= $idestacion_b64; ?>');">Cambiar Plan</button></div></div>
                            <div class="row" style="display: none;" id="divPaquetes">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Selecciona un plan de servicio</h3>
                                        </div>
                                        <div class="card-body form-group text-center">
                                            <div id="divplanesservicio">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-right" id="divTotalPlan">
                                    <?php
                                    if ($_SESSION['usuario']['plan_pago'][$index_plan]['plan_pago_x_usuario'] == 1)  { ?>
                                        <label class="text-danger">$ <?= number_format($precio_plan * $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios'],2); ?></label><br> <label class="text-sm text-gray">( <?= $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios']; ?> Usuarios activos * $<?= number_format($_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_plan'],2); ?> )</label>
                                    <?php } else { ?>
                                        <label class="text-danger">$ <?= number_format($precio_plan,2); ?></label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div><br><br>
                    <input type="hidden" id="<?= $idestacion_b64; ?>_visitas" value="0">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_usuarios" value="<?= $_SESSION['usuario']['plan_pago'][$index_plan]['uso_usuarios']; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_pago_x_usuario" value="<?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_pago_x_usuario']; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_precio" value="<?= $precio_plan; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_desc" value="<?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_descripcion']; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_nombre" value="<?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_nombre']; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_sku_smartdoor" value="<?= $idestacion_b64; ?>_<?= $row_puerta[1]; ?>_idplan_<?= $_SESSION['usuario']['plan_pago'][$index_plan]['idplan']; ?>_<?= date("Y-m"); ?>">
                    <div class="paypal-button-container" id="paypal-<?= $idestacion_b64; ?>"></div><br><br>
                    <div class="row">
                        <div class="col-9">
                            Necesitas ayuda ?<a href="mailto:ayuda@smartdoor.mx">ayuda@smartdoor.mx</a>
                        </div>
                        <div class="col-1">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-success" href="javascript:window.open('https://wa.me/528342717542');"> <i class="fab fa-whatsapp"></i> </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else {
        // si no es administrador, informamos que el servicio esta bloqueado por falta de pago
        ?>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                </div>
                <div class="card-body form-group text-center">
                <div class="row">
                    <h3 class="text-danger">Servicio bloqueado por falta de pago!</h3>
                </div>
                <div class="row">
                    <label>Por favor informa a algun administrador que realice el pago en este mismo sitio.</label>
                </div>
                </div>
            </div>
        </div>
    <?php } } else {
        if (($visitas > 0) || ($_SESSION['usuario']['plan_pago'][$i]['plan_visitas'] == -1)) { ?>
            <div class="col-12">
                <div class="card" style="cursor: pointer;" <?php if ($row_puerta[6] == 0) { ?> onclick="invitar_est_selec('<?= base64_encode($row_puerta[0]); ?>',<?= $row_puerta[1]; ?>);" <?php } ?>>
                    <div class="card-header">
                        <h3 class="card-title"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                        <label class="float-right text-danger">Visitas disponibles: <?php if ($_SESSION['usuario']['plan_pago'][$i]['plan_visitas'] > -1) { echo $visitas; } else { ?> <i class="fas fa-infinity"></i> <?php } ?></label>
                    </div>
                    <div class="card-body form-group text-center">
                        <div class="row">
                            <?php if ($row_puerta[6] == 1) { ?>
                                <img src="../common_files/img/blocked.png" style="width: 100%;">
                            <?php } else { ?>
                                <img src="../common_files/clases/img_qr.php?codigo=<?= $row_puerta[2]; ?>" style="width: 100%;">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
<?php } else { ?>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                </div>
                <div class="card-body form-group text-center">
                    <div class="row"><div class="col-12"><label class="text-danger">Lo sentimos, tu cuenta no tiene saldo para visitas, por favor realiza el pago para seguir disfrutando de nuestros servicios.</label></div></div><br>
                   <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 text-left">Adquirir Visitas:</div>
                                <div class="col-6 text-right"><input placeholder="Cantidad Min 50" type="number" id="cantvisitas" class="form-control" oninput="_('lblTotal').innerHTML = '$ ' + formatMoneda(Number(this.value) * Number(<?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_visitas']; ?>),2); if (this.value > 49){ _('paypal-<?= $idestacion_b64; ?>').style = 'display:inline;'; paypal(); } else { _('paypal-<?= $idestacion_b64; ?>').style = 'display:none;'; }"></div>
                            </div>
                            <div class="row">&nbsp;</div>
                            <div class="row"><div class="col-12 text-right">Costo: <label>$ <?= number_format($_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_visitas'],2); ?></label></div></div>
                            <div class="row"><div class="col-12 text-right">Total: <label id="lblTotal" class="text-danger">$ 0.00</label></div></div>
                        </div>
                    </div><br><br>
                    <input type="hidden" id="<?= $idestacion_b64; ?>_visitas" value="1">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_precio" value="<?= $_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_visitas']; ?>">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_desc" value="Vsitas extra para smartdoor.mx">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_plan_nombre" value="Visitas Smartdoor">
                    <input type="hidden" id="<?= $idestacion_b64; ?>_sku_smartdoor" value="<?= $idestacion_b64; ?>_<?= $row_puerta[1]; ?>_visitas">
                    <div class="paypal-button-container" id="paypal-<?= $idestacion_b64; ?>" style="display: none;"></div><br><br>
                    <div class="row">
                        <div class="col-9">
                            Necesitas ayuda ?<a href="mailto:ayuda@smartdoor.mx">ayuda@smartdoor.mx</a>
                        </div>
                        <div class="col-1">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-success" onclick="javascript:window.open('https://wa.me/528342717542');"> <i class="fab fa-whatsapp"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   <?php } } } ?>
</div>

<input type="hidden" id="txtidestacioninvitados" value="">
<div class="wrapper" id="divinvitados_1" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Datos del evento</h3>
        </div>
        <div class="card-body form-group">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtnombre">Nombre del evento:</label>
                            <input type="text" class="form-control" id="txtnombre" placeholder="Nombre del evento" name="txtnombre_<?= time(); ?>" autocomplete="false_<?= time(); ?>" >
                        </div>
                    </div><br>


                    <div class="row">
                        <div class="col-md-12">
                            <label for="dtfecha">Fecha:</label><br>
                            <input type="date" id="txtdtfecha" class="form-control" data-date-format="yyyy-mm-dd" value="<?= date("Y-m-d"); ?>"/>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="dthora">Hora de inicio:</label> <label class="text-danger">A partir de esta hora los invitados tendrán 10 horas de acceso.</label><br>
                            <input type="time" id="txtdthora" class="form-control" onfocus="console.log(time().);" value="<?= date("H:i"); ?>"/>
                        </div>
                    </div><br>


                    <button class="btn btn-primary" onclick="contactos_verifica_soporte();" id="btncontactos">Agregar invitados&nbsp;&nbsp;&nbsp;<i class="fa fa-user-friends"></i></button>
                    <div class="row" id="divContactoManual" style="display: none;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-danger">Este navegador no soporta el acceso a "Contactos" de tu dispositivo.</label><br>
                                    <label for="txtnombrecontacto">Nombre del invitado:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="txtnombrecontacto" placeholder="Nombre invitado" name="txtnombrecontacto<?= time(); ?>" autocomplete="false_<?= time(); ?>">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><i class="fa fa-user-friends text-success"></i></div>
                                        </div>
                                    </div><br>
                                    <label for="txtemail">Correo:</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="txtemail" placeholder="Correo electrónico" name="txtemail_<?= time(); ?>" autocomplete="false_<?= time(); ?>">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><i class="fas fa-at text-success"></i></div>
                                        </div>
                                    </div><br>
                                    <label for="txtemail">Whatsapp:</label>
                                    <div class="input-group">
                                        <select class="form-control select2" id="cbopais"><option value="52" selected>México</option><option value="1">Estados Unidos</option></select>&nbsp;&nbsp;
                                        <input type="number" maxlength="10" class="form-control" id="txtwhatsapp" placeholder="Whatsapp" oninput="if (this.value.length > this.maxLength) {this.value = this.value.slice(0, this.maxLength);}" name="txtwhats_<?= time(); ?>" autocomplete="false_<?= time(); ?>" >
                                        <div class="input-group-append">
                                            <div class="input-group-text"><i class="fab fa-whatsapp text-success"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary  float-right" onclick="agregaInvitado();">Agregar invitado&nbsp;&nbsp;&nbsp;<i class="fa fa-plus"></i></button>
                                </div>
                            </div><br><br>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="divinvitados" style="position: relative; display: flex; flex-wrap: wrap;"></div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary  float-right" id="btnmasinvitados" style="display: none;" onclick="getContacts();">Agregar más&nbsp;&nbsp;&nbsp;<i class="fa fa-plus"></i></button>
                        </div>
                    </div><br>


                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary  float-right" onclick="guardar_evento();" id="btncontactos">Guardar evento&nbsp;&nbsp;&nbsp;<i class="fa fa-save"></i></button>
                </div>
            </div><br>
            <div class="row">
            <div class="col-sm-12">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Copiar de historial</h3>
                    </div>
                    <div class="card-body">
                        <table id="lsthistorico" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Invitados</th>
                            </tr>
                            </thead>
                            <tbody id="tblhistorico">


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="divinvitados_2" style="display: none">
    <div class="col-sm-12">
        <div class="card card-default card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Enviar QR</h3>
            </div>
            <div class="card-body">
                <table id="lstlistainvitados" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Clic para enviar</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
