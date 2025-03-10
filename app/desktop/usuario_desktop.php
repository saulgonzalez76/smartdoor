<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['usuario']['idusuario'])) { header('Location: ../login.php'); }
require_once "../common_files/clases/base_datos.php";
include '../common_files/clases/calendario.php';

//print_r($_SESSION['usuario']['plan_pago']);

$clsBaseDatos = new Base_Datos();
$sth_puertas = $clsBaseDatos->usuarios_listado_puertas_web();
while ($row_puerta = $sth_puertas->fetch()){
    $idestacion_b64 = str_replace("=","",base64_encode($row_puerta[0]));
    $index_plan = -1;
    for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
        if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $row_puerta[0]){ $index_plan = $i; break; }
    } ?>
        <div class="col-sm-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="cursor: pointer;" onmouseover="$(this).addClass('text-primary');" onmouseleave="$(this).removeClass('text-primary');" onclick="getGrafuso('<?= $idestacion_b64; ?>');"><?= $row_puerta[3]; ?> <label><?= $row_puerta[4]; ?></label></h3>
                <div class="card-tools" id="divsenal_<?= $row_puerta[2]; ?>"> </div>
            </div>
            <div class="card-body form-group text-center">
                <?php if (($_SESSION['usuario']['plan_pago'][$i]["plan_vigente"] == 0) && ($_SESSION['usuario']['plan_pago'][$index_plan]['idplan'] != 1)){
                    // que solo el administrador pueda generar pagos, al cambiar el plan tomar en cuenta que no pueda cambiar de instalacion como de fraccionamiento a negocio...
                    if ($clsBaseDatos->estacion_cliente_admin($row_puerta[0])) {
                        // si es el administrador de la estacion, dejamos que procese el pago y pueda hacer cambios de planes
                        $precio_plan = $_SESSION['usuario']['plan_pago'][$index_plan]['plan_costo_plan'];
                    ?>
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

                <?php } else {
                        // si no es administrador, informamos que el servicio esta bloqueado por falta de pago
                        ?>
                        <div class="row">
                            <h3 class="text-danger">Servicio bloqueado por falta de pago!</h3>
                        </div>
                        <div class="row">
                            <label>Por favor informa a algun administrador que realice el pago en este mismo sitio.</label>
                        </div>
                    <?php } }else { ?>
                    <label class="text-success text-lg" id="lblstatus_<?= $row_puerta[2]; ?>"></label>
                    <div class="row">
                        <?php
                        if ($row_puerta['hardware'] == "ESP01"){
                            // si es smartdoor de puerta o porton
                            $bloqueado_fracc = 0;
                            // si es un fraccionamiento ver si tiene habilitado el servicio de bloqueo automatico de pagos,
                            if ($row_puerta['fraccionamientos_pago_prorroga'] > 0){
                                // verificar si la fecha del pago corriente del usuario + los dias de prorroga es mayor al dia actual
                                $dias_prorroga = new DateInterval('P'.$row_puerta['fraccionamientos_pago_prorroga'].'D');
                                $fecha = new DateTime(date("Y-m-d",strtotime($row_puerta['fraccionamientos_pago_corriente'])));
                                $fecha->add($dias_prorroga);
                                $bloqueado_fracc = (new DateTime(date("Y-m-d")) > $fecha)?1:0;
                            }

                            if (($row_puerta[6] == 1) || ($bloqueado_fracc == 1)) { ?>
                                <img src="../common_files/img/blocked.png" style="width: 100%;">
                            <?php } else { ?>
                                <img src="../common_files/clases/img_qr.php?codigo=<?= $row_puerta[2]; ?>" onclick="abrepuerta('<?= base64_encode($row_puerta[2]); ?>','<?= base64_encode($row_puerta[4] . " de " . $row_puerta[3]); ?>');" style="width: 100%;">
                            <?php }
                        }
                        if ($row_puerta['hardware'] == "ESP02"){
                            // si es smartdoor de control break contactor
                            $calendar = new Calendar(date("Y-m-d"),$idestacion_b64);
                            // array de eventos
                            $calendar->eventos = $clsBaseDatos->eventos_casa($idestacion_b64);
                            echo $calendar;
                         }


                        ?>



                    </div>
                <?php } ?>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-4 text-left">
                        <label id="lblciudadtemperatura_<?= $row_puerta[2]; ?>"></label><br>
                        <i class="fas fa-temperature-high"></i>&nbsp;&nbsp;&nbsp;<label id="lbltemperatura_<?= $row_puerta[2]; ?>"></label> °C
                    </div>
                    <div class="col-4 text-center">
                        <label class="text-xs">Latencia ≈ <label class="text-danger">&nbsp;<?= $clsBaseDatos->latencia($row_puerta[0]); ?> seg</label></label>
                    </div>
                    <div class="col-4 text-right">
                        <img src="" id="icontemp_<?= $row_puerta[2]; ?>" class="bg-white float-right">
                    </div>
                </div>
            </div>
        </div>
        </div>
<?php } ?>
<br><br><br>
