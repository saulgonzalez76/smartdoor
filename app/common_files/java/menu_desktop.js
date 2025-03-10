/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

function setInActivo(){
    for (let i=1;i<10;i++) {
        $('#menu' + i).removeClass('active');
    }
    clearInterval(tmrPuertaStatus);
}

function menu_inicio(){
    setInActivo();
    $('#menu1').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'usuario_desktop.php',
        success: function (data) {
            _('contenido').innerHTML = data;
            tmrPuertaStatus = setInterval(getPuertaStatus,1000);
            cargaWeather();
            paypal();
            selCalendarioInicio = "";
            selCalendarioFin = "";
        }
    });
}

function menu_pagos(){
    setInActivo();
    $('#menu6').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'pagos.php',
        success: function (data) {
            _('contenido').innerHTML = data;
            setTimeout(function() {formatoTabla(true,true,false,false,true,true,true);},500);
        }
    });
}

function menu_relacion_usuario(){
    setInActivo();
    $('#menu7').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'relacion_usuarios.php',
        success: function (data) {
            _('contenido').innerHTML = data;
            setTimeout(function() {formatoTabla(true,true,false,false,false,true,true,false);},500);
        }
    });
}

function menu_admin(){
    setInActivo();
    $('#menu8').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'inicio.php',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function menu_distribuidor(){
    setInActivo();
    $('#menu9').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'distribuidor.php',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function menu_reporte_entradas(){
    setInActivo();
    $('#menu4').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'reporte_entradas.php',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function menu_reporte_transacciones(){
    setInActivo();
    $('#menu5').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'reporte_transaccion.php',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function menu_horarios(){
    setInActivo();
    $('#menu3').addClass('active');
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: 'horarios.php',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function menu_invitar(){
    setInActivo();
    $('#menu2').addClass('active');
    $.ajax({
        url: 'invitar.php',
        success: function (data) {
            json_Contact_Api = [];
            _('contenido').innerHTML = data;
            paypal();
        }
    });

}
