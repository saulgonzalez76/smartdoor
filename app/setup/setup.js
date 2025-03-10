/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

var map;
var appIdFace = '1194460337672607';
var codigo_est = "";
var idface = '';
var pingEstacion;
var tmrGPS;
var ubicacion = "";
var whatscodigo = "";
var whatsapp_tel = "";
var tiempo_espera_sms = 0;
var tiempo_resta_sms = 0;
var tmrEspera;
var tipoInstalacion = 0;
var idplanservicio = 0;

function faceLogin(){
    FB.api('/me?fields=id,first_name, last_name,email', function (response) {
        _('txtNombre').value = response.first_name;
        _('txtApellido').value = response.last_name;
        _('txtEmail').value = response.email;
        idface = response.id;
        _('txtWhatsapp').focus();
        /*FB.api(
                    '/' + idface + '/picture',
                    'GET',
                    {"redirect": "false", "type": "large"},
                    function (response) {
                        console.log("foto");
                        console.log(response);
                    });
        * */


    });
}

function controlcookies(){localStorage.controlcookie=localStorage.controlcookie||0,localStorage.controlcookie++,cookie1.style.display="none"}
function statusChangeCallback(e){"connected"===e.status?faceLogin():_("status").innerHTML="Por favor da permisos a esta app en facebook."}
function checkLoginState(){FB.getLoginStatus(function(e){statusChangeCallback(e)})}
function openWindowWithPost(e,t){var o=document.createElement("form");o.method="POST",o.action=e,o.style.display="none";for(var n in t){var a=document.createElement("input");a.type="hidden",a.name=n,a.value=t[n],o.appendChild(a)}document.body.appendChild(o),o.submit()}window.fbAsyncInit=function(){FB.init({appId:appIdFace,cookie:!0,xfbml:!0,version:"v10.0"});var e=function(){var e=document.getElementById("spinner");e.removeAttribute("style"),e.removeChild(e.childNodes[0]),e.style="display: none"};FB.Event.subscribe("xfbml.render",e)},function(e,t,o){var n,a=e.getElementsByTagName(t)[0];e.getElementById(o)||(n=e.createElement(t),n.id=o,n.src="https://connect.facebook.net/en_US/sdk.js",a.parentNode.insertBefore(n,a))}(document,"script","facebook-jssdk");

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function _(el){
    return document.getElementById(el);
}

function uploadFile(tipo,archivo, idcliente, progress, objfile, idregistro, uuid){
    var cant = _(objfile).files.length;
    if (cant === 0) { Swal.fire('Error!',"Error, debe seleccionar al menos un archivo !",'error');  return; }
    for (i=0;i<cant;i++) {
        var file = _(objfile).files[i];
        var formdata = new FormData();
        formdata.append(objfile, file);
        formdata.append(tipo,archivo);
        formdata.append("idcliente",idcliente);
        if (tipo === 'anticipo') { formdata.append("idregistro",idregistro); formdata.append("uuid",uuid); }
        var ajax = new XMLHttpRequest();
        ajax.open("POST", "../desktop/subir_archivos.php");
        ajax.send(formdata);
    }
}

async function valida2(id) {
    if (isNaN(_('txtWhatsapp'+id).value)){
        _('txtWhatsapp'+id).value = "";
        _('txtWhatsapp'+id).focus();
    } else {
        var numero = "";
        if (_('txtWhatsapp1').value !== ''){ numero += _('txtWhatsapp1').value; }
        if (_('txtWhatsapp2').value !== ''){ numero += _('txtWhatsapp2').value; }
        if (_('txtWhatsapp3').value !== ''){ numero += _('txtWhatsapp3').value; }
        if (_('txtWhatsapp4').value !== ''){ numero += _('txtWhatsapp4').value; }
        if (numero.length === 4){
            const data = await validaWhatsCodigo(numero);
            if (data){
                if (codigo_est === "") {
                    codigo_est = await crea_estacion();
                }
                siguiente_paso(8);
            } else {
                _('lblwhatserror').innerText = "Código invalido !";
                _('txtWhatsapp1').value = "";
                _('txtWhatsapp2').value = "";
                _('txtWhatsapp3').value = "";
                _('txtWhatsapp4').value = "";
                _('txtWhatsapp1').focus();
            }
        } else {
            if (id < 5) {
                _('txtWhatsapp' + (id + 1)).focus();
            } else {
                _('txtWhatsapp1').focus();
            }
        }
    }
}

async function crea_estacion(){
    const result = await $.ajax({
        dataType: "json",
        type: "post",
        url: 'select.php?tipo=1',
        data: {
            email: _('txtEmail').value,
            nombre: _('txtNombre').value + ' ' + _('txtApellido').value,
            fbid: idface,
            whatsapp: _('cbopais').value + '' + _('txtWhatsapp').value,
            nombre_est: _('txtNombreEstacion').value,
            nombre_puerta: _('txtNombrePuerta').value,
            planservicio: idplanservicio,
            es_puerta: _('txtEsPuerta').value,
            latlng: ubicacion
        },
        success: function (data) {
            if (data.password === "") {
                _('lblpass').innerText = "Agregado a tu usuario";
            } else {
                _('lblpass').innerText = data.password;
            }
            retorno = data.codigo;
        }
    });
    return retorno;
}

function valida1(){
    let activo = true;
    _('btnpaso1').disabled = true;
    if (_('txtNombre').value === ''){ activo = false; }
    if (_('txtWhatsapp').value === ''){ activo = false; }
    if (_('txtEmail').value === ''){ activo = false; }
    if (_('txtWhatsapp').value.length < 10){ activo = false; }
    if (!validateEmail(_('txtEmail').value)) { activo = false; }
    if (activo) {
        _('btnpaso1').disabled = false;
    }
}

function valida3(){
    let activo = true;
    _('btnpaso1').disabled = true;
    if (_('txtNombreEstacion').value === ''){ activo = false; }
    if (_('txtNombrePuerta').value === ''){ activo = false; }
    if (activo) {
        _('btnpaso2').disabled = false;
    }
}

async function getWhatsCodigo(){
    const result = await $.ajax({
        dataType: "json",
        type: "post",
        url: 'select.php?tipo=3',
        data: {
            whatsapp: _('cbopais').value + '' + _('txtWhatsapp').value
        },
        success: async function (data) {
            codigojson = true;
        }
    });
    return codigojson;
}

async function validaWhatsCodigo(codwhats){
    const result = await $.ajax({
        dataType: "json",
        type: "post",
        url: 'select.php?tipo=7',
        data: {
            codigo: codwhats,
            whatsapp: _('cbopais').value + '' + _('txtWhatsapp').value
        },
        success: async function (data) {
            codigojson = data.valid;
        }
    });
    return codigojson;
}

function func_espera() {
    tiempo_resta_sms--;
    _('btnreenviarmensaje').innerText = tiempo_resta_sms + "s";
    if (tiempo_resta_sms === 1 ) {
        clearInterval(tmrEspera);
        _('btnreenviarmensaje').disabled = false;
        _('btnreenviarmensaje').innerHTML = "Reenviar sms&nbsp;&nbsp;&nbsp; <i class=\"fa fa-sync fa-spin\"></i>";
    }
}

async function reenviarMensaje(){
    if (whatsapp_tel !== "") {
        whatscodigo = await getWhatsCodigo();
        tiempo_espera_sms += 30;
        tiempo_resta_sms = tiempo_espera_sms;
        _('btnreenviarmensaje').innerText = tiempo_resta_sms + "s";
        _('btnreenviarmensaje').disabled = true;
        tmrEspera = setInterval(func_espera, 1000);
        _('txtWhatsapp1').value = "";
        _('txtWhatsapp2').value = "";
        _('txtWhatsapp3').value = "";
        _('txtWhatsapp4').value = "";
        _('txtWhatsapp1').focus();
    } else {
        _('divubicacion').style = "display:none;";
        siguiente_paso(1);
        Swal.fire('Error!',"Error, falta el número de celular / whatsapp !",'error');
    }
}

async function siguiente_paso(paso){
    switch (paso){
        case 1:
            _('divdatos').style = "display:inline;";
            _('divconfirma').style = "display:none;";
            _('divestacion').style = "display:none;";
            break;
        case 2:
            _('lblenviasms').innerText = _('txtWhatsapp').value;
            whatsapp_tel = _('cbopais').value + '' + _('txtWhatsapp').value;
            _('divdatos').style = "display:none;";
            _('divestacion').style = "display:inline;";
            _('divtiposervicio').style = "display:none;";
            break;
        case 3:
            _('divtiposervicio').style = "display:inline;";
            _('divtipopuerta').style = "display:none;";
            _('divestacion').style = "display:none;";
            _('btnpaso3').disabled = true;
            break;
        case 4:
            _('divtiposervicio').style = "display:none;";
            _('divtipopuerta').style = "display:inline;";
            _('divplanservicio').style = "display:none;";
            _('btnpaso4').disabled = true;
            break;
        case 5:
            await getPlanesServicio();
            _('divtipopuerta').style = "display:none;";
            _('divplanservicio').style = "display:inline;";
            _('divubicacion').style = "display:none;";
            _('btnpaso5').disabled = true;
            clearInterval(tmrGPS);
            break;
        case 6:
            tmrGPS = setInterval(getUbicacion,1000);
            _('divplanservicio').style = "display:none;";
            _('divubicacion').style = "display:inline;";
            _('divconfirma').style = "display:none;";
            break;
        case 7:
            if (whatsapp_tel !== "") {
                whatscodigo = await getWhatsCodigo();
                _('divubicacion').style = "display:none;";
                _('divconfirma').style = "display:inline;";
                _('divconexion').style = "display:none;";
                _('txtWhatsapp1').focus();
            } else {
                _('divubicacion').style = "display:none;";
                siguiente_paso(1);
                Swal.fire('Error!',"Error, falta el número de celular / whatsapp !",'error');
            }
            break;
        case 8:
            _('divubicacion').style = "display:none;";
            _('divdatos').style = "display:none;";
            _('divconfirma').style = "display:none;";
            _('divestacion').style = "display:none;";
            _('divconexion').style = "display:inline;";
            pingEstacion = setInterval(getPuertaStatus,1000);
            break;
        case 9:
            clearInterval(pingEstacion);
            _('divconexion').style = "display:none;";
            _('divterminar').style = "display:inline;";
            break;
    }
}

function getPuertaStatus(){
    $.ajax({
        url: 'select.php?tipo=2&codigo='+codigo_est,
        success: function (data) {
            data = JSON.parse(data);
            data = data.estatus;
            if (Number(data) === 0){
                clearInterval(pingEstacion);
                siguiente_paso(9);
            }
        }
    });
}

function selecPlanServicio(idplan){
    $('.lblselecplan').css('display','none');
    _('lblselec' + idplan).style='display:inline;';
    idplanservicio = idplan;
    _('btnpaso5').disabled = false;
    $('.planservicio').addClass('elevation-5');
}

function selecTipoPuerta(tipo){
    $('.lblselecpuerta').css('display','none');
    _('lblespuerta'+tipo).style='display:inline;';
    _('txtEsPuerta').value=tipo-1;
    _('btnpaso4').disabled = false;
    $('.tipopuerta').addClass('elevation-5');
}

function selecTipoPlanServicio(idplan){
    $('.lblselecplan').css('display','none');
    _('lblplan' + idplan).style='display:inline;';
    tipoInstalacion = idplan;
    _('btnpaso3').disabled = false;
    $('.planservicio').addClass('elevation-5');
}

async function getPlanesServicio(){
    await $.ajax({
        url: 'select.php?tipo=6',
        data: {
            tiposervicio: tipoInstalacion
        },
        type: 'post',
        dataType: "json",
        success: function (data) {
            _('divplanesservicio').innerHTML = "";
            for(var i=0;i<data.length;i++) {
                var encabezado = "<div class=\"card card-default elevation-5 planservicio\" onclick=\"selecPlanServicio(" + data[i].idplan + ");$(this).toggleClass('elevation-5');\">\n" +
                    "                        <div class=\"card-header with-border\">\n" +
                    "                            <h3 class=\"card-title\">" + data[i].nombre + ' - ' + data[i].descripcion + "</h3>\n" +
                    "                        </div>\n" +
                    "                        <div class=\"card-body\">";

                var lim_usuarios = data[i].usuarios;
                if (Number(data[i].usuarios) === -1){ lim_usuarios = "Ilimitado"; }

                var lim_administradores = data[i].administradores;
                if (Number(data[i].administradores) === -1){ lim_administradores = "Ilimitado"; }

                var cuerpo = "<i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Usuario(s): <label>" + lim_usuarios + "</label><br><i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Administrador(es): <label>" + lim_administradores + "</label><br>";
                if (Number(data[i].empleados) === -1){
                    cuerpo += "<i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Empleado(s): <label>Ilimitado</label><br>";
                } else{
                    if (Number(data[i].empleados) > 0) {
                        cuerpo += "<i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Empleado(s): <label>" + data[i].empleados + "</label><br>";
                    } else {
                        cuerpo += "<i class=\"fa fa-times text-danger\"></i> &nbsp;&nbsp; Agregar empleados.<br>";
                    }
                }

                if (Number(data[i].visitas) === -1){
                    cuerpo += "<i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Visitas: <label>Ilimitado</label><br>";
                } else{
                    if (Number(data[i].visitas) > 0) {
                        cuerpo += "<i class=\"fa fa-check text-success\"></i> &nbsp;&nbsp; Visitas: <label>" + data[i].visitas + "</label><br>";
                    } else {
                        cuerpo += "<i class=\"fa fa-times text-danger\"></i> &nbsp;&nbsp; Visitas.<br>";
                    }
                }

                var pago_x_usuario = "";
                if (Number(data[i].pago_x_usuario) === 1){ pago_x_usuario = "por usuario"; }
                var periodo = "Anuales";
                if (Number(data[i].periodo) !== 12){ periodo = "Cada " + data[i].periodo + " mese(s)"; }
                var pie = "</div><div class=\"card-footer text-right\"><label class=\"text-success float-left lblselecplan\" id=\"lblselec" + data[i].idplan + "\" style=\"display: none;\">Seleccionado</label><label class=\"text-gray\">$" + data[i].monto + " MXN " + pago_x_usuario + " " + periodo + " </label></div></div><br><br>";
                $('#divplanesservicio').append(encabezado + cuerpo + pie);

            }
        }
    });
}

function getUbicacion(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setLatLng);
    }
}

function setLatLng(position) {
    ubicacion = position.coords.latitude + ',' + position.coords.longitude;
    $.ajax({
        type: 'post',
        data: {
            email: _('txtEmail').value,
        },
        url: 'select.php?tipo=5',
        success: async function (data) {
            if (Number(data) === 0){
                siguiente_paso(7);
            } else {
                codigo_est = await crea_estacion();
                siguiente_paso(8);
            }
        }
    });
    clearInterval(tmrGPS);
}
