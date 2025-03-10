<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

//echo "https://smartdoor.mx?id=" . base64_dencode("ODQ6Q0M6QTg6QUY6Qzc6QTE=");

//falta agregar como opcional el costo por las visitas y los empleados a cada plan de pagos

    if(!isset($_SESSION)) { session_start(); }
    include ("../common_files/clases/session_config.php");
    include("../common_files/clases/base_datos.php");
    $clsBaseDatos = new Base_Datos();
    $errorMessage = "";

    $idestacion = $_SESSION['idestacion'];
    $idcliente = $_SESSION['idcliente'];

    // si el dispositivo esta asignado, mostrar solamente que esta asignado y se requiere una confirmacion del usuario para des asignar el dispositivo y despues poderlo asigar a un nuevo cliente
    $registrado_error = false;
    if ($clsBaseDatos->estacion_asignado($idestacion) > 0){ $registrado_error = true; }
    if ($clsBaseDatos->estacion_valida_nueva($idestacion) == 0){ $registrado_error = true; }

    ?>
<!doctype html>
<html lang="es">
<head>
    <?php include '../common_files/meta_tags.php'; ?>
    <link rel="icon" href="../common_files/img/logo_transparente.png">
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../common_files/css/ionicons.min.css">
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="../plugins/morris/morris.css">
    <link rel="stylesheet" href="../common_files/css/estilos.css?<?= time(); ?>">
    <script src="../common_files/java/jquery-latest.min.js"></script>
    <script src="../common_files/java/jquery.min.js"></script>
    <script src="../common_files/java/javascript.util.min.js" type="text/javascript"></script>
    <script src="../common_files/java/jsts.min.js" type="text/javascript"></script>
    <script src="../common_files/java/sweetalert.js" type="text/javascript"></script>
    <script src="../common_files/java/base64.js"></script>
    <?php if (getenv('APPLICATION_ENV') === "development") { ?>
        <script src="setup.js?<?= time(); ?>" type="text/javascript"></script>
    <?php } else { ?>
        <script src="setup.min.js?<?= time(); ?>" type="text/javascript"></script>
    <?php } ?>
    <style>
        .swal2-container {
            zoom: 1.5;
        }
        .swal2-icon {
            width: 5em !important;
            height: 5em !important;
            border-width: .25em !important;
        }
        .oculto{
            display: none;
            opacity: 0;
            transition: visibility 0s 2s, opacity 2s linear;
        }
        .visible{
            display: inline;
            opacity: 1;
            transition: opacity 2s linear;
        }
    </style>
</head>

<body>
<div id="container" style="background-color: #01052d; width: 100%; height: 100%; overflow: hidden;
                background: url('../common_files/img/logo.png') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                z-index: -1;opacity: 0.6;
                position: absolute;"></div>
<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
        <div id="divdatos" class="<?php if ($registrado_error) { echo "oculto"; } else { echo "visible"; } ?>">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Registro, por favor llena estos datos.</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <img src="../common_files/img/logo_small2.png" width="300">
                        </div>
                    </div><br>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label id="lblNombre" for="txtNombre">Nombre:</label><input type="text" id="txtNombre" name="txtNombre_<?= time(); ?>" autocomplete="false_<?= time(); ?>" value="" class="form-control" placeholder="Nombre del cliente" oninput="javascript:this.value = this.value.toUpperCase();valida1();">
                            </div>
                            <div class="col-sm-6">
                                <label for="txtApellido">Apellido:</label><input type="text" autocomplete="false_<?= time(); ?>" name="txtApellido_<?= time(); ?>" id="txtApellido" value="" class="form-control" placeholder="Apellido" oninput="javascript:this.value = this.value.toUpperCase();valida1();">
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="txtWhatsapp">Whatsapp:</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <select class="form-control select2" id="cbopais"><option value="52" selected>México</option><option value="1">Estados Unidos</option></select>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" autocomplete="false_<?= time(); ?>" name="txtWhatsapp_<?= time(); ?>" id="txtWhatsapp" value="" maxlength="10" class="form-control" placeholder="Whatsapp" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); valida1();">
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-sm-12">
                                <label for="txtEmail">Email:</label><input type="email" id="txtEmail" autocomplete="false_<?= time(); ?>" name="txtEmail_<?= time(); ?>" value="" class="form-control" placeholder="em@ail" oninput="valida1();">
                            </div>
                        </div><br>

                        <div class="row"><div class="col-sm-12 text-center"><label>-----</label></div></div><br>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="spinner"><img src="../common_files/img/loader.gif" width="100" height="100"></div>
                                <div class="fb-login-button" data-size="large" scope="public_profile,email" onlogin="checkLoginState();" data-button-type="continue_with" data-auto-logout-link="false" data-use-continue-as="true"></div>
                                <div id="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <label class="text-gray">Paso 1 de 8</label>
                    <button type="button" id="btnpaso1" disabled class="btn btn-primary float-right" onclick="siguiente_paso(2);">Siguiente</button>
                </div>
            </div>
        </div>

        <div id="divregistrado_error" class="<?php if ($registrado_error) { echo "visible"; } else { echo "oculto"; } ?>">
            <div class="card card-danger elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Error.</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="text-danger">SmartDoor</h3>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row"><div class="col-sm-12 text-center"><label>Esta estación ya se encuentra registrada, por favor envianos un correo si cres que sea un error.</label></div></div><br>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <label class="text-gray">Necesitas ayuda? <a href="mailto:ayuda@smartdoor.mx" class="text-danger">ayuda@smartdoor.mx</a></label>
                </div>
            </div>
        </div>

        <div id="divestacion"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Nombra tu estación.</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label>Nombre de estación:</label><input type="text" id="txtNombreEstacion" autocomplete="false_<?= time(); ?>" name="txtNombreEstacion_<?= time(); ?>" value="" class="form-control" placeholder="Casa" oninput="javascript:this.value = this.value.toUpperCase();valida3();">
                        <label>Nombre de puerta / portón:</label><input type="text" id="txtNombrePuerta" autocomplete="false_<?= time(); ?>" name="txtNombrePuerta_<?= time(); ?>" value="" class="form-control" placeholder="Pátio" oninput="javascript:this.value = this.value.toUpperCase();valida3();">
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger float-left" onclick="siguiente_paso(1);">Atras</button>
                    <label class="text-gray">Paso 2 de 8</label>
                    <button type="button" disabled class="btn btn-primary float-right" id="btnpaso2" onclick="siguiente_paso(3);">Siguiente</button>
                </div>
            </div>
            <br><br><br><br>
        </div>

        <div id="divtiposervicio"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Tipo de instalación.</h3>
                </div>
                <div class="card-body">
                    <h2>Selecciona donde esta instalado el módulo.</h2><br><br>

                    <div class="card card-default elevation-5 planservicio" onclick="selecTipoPlanServicio(4); $(this).toggleClass('elevation-5')">
                        <div class="card-header with-border">
                            <h3 class="card-title">Casa / Particular</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="../common_files/img/casa.png" class="elevation-5 tiposervicio" style="height: 150px;">
                        </div>
                        <div class="card-footer">
                            <label class="text-success float-left lblselecplan" id="lblplan4" style="display: none;">Seleccionado</label>
                        </div>
                    </div>

                    <div class="card card-default elevation-5 planservicio" onclick="selecTipoPlanServicio(3); $(this).toggleClass('elevation-5')">
                        <div class="card-header with-border">
                            <h3 class="card-title">Negocio / Oficina</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="../common_files/img/tienda.png" class="elevation-5 tiposervicio" style="height: 150px;">
                        </div>
                        <div class="card-footer">
                            <label class="text-success float-left lblselecplan" id="lblplan3" style="display: none;">Seleccionado</label>
                        </div>
                    </div>

                    <div class="card card-default elevation-5 planservicio" onclick="selecTipoPlanServicio(2); $(this).toggleClass('elevation-5')">
                        <div class="card-header with-border">
                            <h3 class="card-title">Fraccionamiento / Condominio</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="../common_files/img/fraccionamiento.jpg" class="elevation-5 tiposervicio"  style="height: 150px;">
                        </div>
                        <div class="card-footer">
                            <label class="text-success float-left lblselecplan" id="lblplan2" style="display: none;">Seleccionado</label>
                        </div>
                    </div>


                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger float-left" onclick="siguiente_paso(2);">Atras</button>
                    <label class="text-gray">Paso 3 de 8</label>
                    <button type="button" disabled class="btn btn-primary float-right" id="btnpaso3" onclick="siguiente_paso(4);">Siguiente</button>
                </div>
            </div>
            <br><br><br><br>
        </div>

        <div id="divtipopuerta"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Tipo de instalación.</h3>
                </div>
                <div class="card-body">
                    <h2>Tipo de acceso.</h2><br><br>
                    <input type="hidden" id="txtEsPuerta" value="">
                    <div class="card card-default elevation-5 tipopuerta" onclick="selecTipoPuerta(2); $(this).toggleClass('elevation-5');">
                        <div class="card-header with-border">
                            <h3 class="card-title">Puerta de seguridad o chapa eléctrica</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="../common_files/img/chapa_electrica.png" class="elevation-5 tiposervicio" style="height: 150px;">
                        </div>
                        <div class="card-footer">
                            <label class="text-success float-left lblselecpuerta" id="lblespuerta2" style="display: none;">Seleccionado</label>
                        </div>
                    </div>

                    <div class="card card-default elevation-5 tipopuerta" onclick="selecTipoPuerta(1); $(this).toggleClass('elevation-5');">
                        <div class="card-header with-border">
                            <h3 class="card-title">Portón eléctrico</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="../common_files/img/porton_electrico.png" class="elevation-5 tiposervicio" style="height: 150px;">
                        </div>
                        <div class="card-footer">
                            <label class="text-success float-left lblselecpuerta" id="lblespuerta1" style="display: none;">Seleccionado</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger float-left" onclick="siguiente_paso(3);">Atras</button>
                    <label class="text-gray">Paso 4 de 8</label>
                    <button type="button" disabled class="btn btn-primary float-right" id="btnpaso4" onclick="siguiente_paso(5);">Siguiente</button>
                </div>
            </div>
            <br><br><br><br>
        </div>

        <div id="divplanservicio"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Plan de servicio.</h3>
                </div>
                <div class="card-body">
                    <h2>Selecciona el plan de servicio.</h2><br><br>
                    <div id="divplanesservicio">
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger float-left" onclick="siguiente_paso(4);">Atras</button>
                    <label class="text-gray">Paso 5 de 8</label>
                    <button type="button" disabled class="btn btn-primary float-right" id="btnpaso5" onclick="siguiente_paso(6);">Siguiente</button>
                </div>
            </div>
            <br><br><br><br>
        </div>

        <div id="divubicacion"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Ubicación.</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label>Otorga permiso para tomar tu ubicación.</label>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="text-danger">Esperando GPS.</h3>
                            <div id="spinner_gps_est"><img src="../common_files/img/loader.gif" width="100" height="100"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger float-left" onclick="siguiente_paso(5);">Atras</button>
                    <label class="text-gray">Paso 6 de 8</label>
                </div>
            </div>
        </div>

        <div id="divconfirma"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">SMS confirmación.</h3>
                </div>
                <div class="card-body text-center">
                        <h5>Confirma el código enviado por SMS.</h5><br>
                        <div class="row">
                            <div class="col-1"></div>
                            <input type="number" autocomplete="false_<?= time(); ?>" id="txtWhatsapp1" name="txtWhatsapp1_<?= time(); ?>" style="width: 40px;" value="" maxlength="1" class="form-control" placeholder="0" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);valida2(1);">&nbsp;&nbsp;&nbsp;
                            <input type="number" autocomplete="false_<?= time(); ?>" id="txtWhatsapp2" name="txtWhatsapp2_<?= time(); ?>" style="width: 40px;" value="" maxlength="1" class="form-control" placeholder="0" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);valida2(2);">&nbsp;&nbsp;&nbsp;
                            <input type="number" autocomplete="false_<?= time(); ?>" id="txtWhatsapp3" name="txtWhatsapp3_<?= time(); ?>" style="width: 40px;" value="" maxlength="1" class="form-control" placeholder="0" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);valida2(3);">&nbsp;&nbsp;&nbsp;
                            <input type="number" autocomplete="false_<?= time(); ?>" id="txtWhatsapp4" name="txtWhatsapp4_<?= time(); ?>" style="width: 40px;" value="" maxlength="1" class="form-control" placeholder="0" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);valida2(4);">&nbsp;&nbsp;&nbsp;
                            <div class="col-1"></div>
                        </div>
                    <br><br>
                    <div class="row">
                        <div class="col-6 text-center">
                            Enviado a: <label class="text-danger" id="lblenviasms"></label>
                        </div>
                        <div class="col-6 text-center">
                            <h5 class="text-danger" onclick="siguiente_paso(1);">Cambiar número</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <label class="text-danger" id="lblwhatserror"></label>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <label class="text-gray">Paso 7 de 8</label>
                    <button type="button" id="btnreenviarmensaje" class="btn btn-success float-right" onclick="reenviarMensaje();">Reenviar sms&nbsp;&nbsp;&nbsp; <i class="fa fa-sync fa-spin"></i> </button>
                </div>
            </div>
        </div>



        <div id="divconexion"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Conecta tu estación.</h3>
                </div>
                <div class="card-body">
                    <div class="row"><h3>Sigue estos pasos para conectar tu estación:</h3></div>
                    <div class="row"><h6>1. En tu celular busca el WiFi <label class="text-danger">"SmartDoor"</label>.</h6></div>
                    <div class="row"><h6>2. Conectate usando la contraseña: <label class="text-danger">"smartdoor"</label>.</h6></div>
                    <div class="row"><h6>3. Una vez conectado te pedira que inicies sesion.</h6></div>
                    <div class="row"><h6>4. Da clic en "Configure WiFi".</h6></div>
                    <div class="row"><h6>5. Selecciona el WiFi donde estara conectada tu estación.</h6></div>
                    <div class="row"><h6>6. Introduce la contraseña del WiFi.</h6></div>
                    <div class="row"><h6>7. Da clic en "Save".</h6></div>
                    <label>Una vez hecho esto la estación se reinicia para conectarse a tu WiFi, en caso de no poder conectarse estará disponible el hotspot <label class="text-danger">"SmartDoor"</label>, regresa al paso 1 para volver a configurarlo.</label>
                </div>
                <div class="card-footer text-center">
                    <div class="col-12 text-center">
                        <h3 class="text-danger">Esperando conexión.</h3>
                        <div id="spinner_conn_est"><img src="../common_files/img/loader.gif" width="100" height="100"></div>
                    </div>
                </div>
            </div>
        </div>


        <div id="divterminar"  class="oculto">
            <div class="card card-primary elevation-5">
                <div class="card-header with-border">
                    <h3 class="card-title">Gracias !</h3>
                </div>
                <div class="card-body">
                    <h2>Listo !</h2>
                    <label>Gracias por utilizar <label class="text-danger">SmartDoor</label>, tu estación y cuenta de administrador estan listos, puedes ingresar en
                        <label class="text-danger">https://smartdoor.mx</label> útiliza tu contraseña temporal para entrar:</label>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="text-danger" id="lblpass"></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <label class="text-gray">Tu estación esta lista.</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1"></div>
</div>


<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/popper/popper.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
<script src="../plugins/fastclick/fastclick.js"></script>
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
</body>
</html>