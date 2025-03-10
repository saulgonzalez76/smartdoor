/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

var tmrFormatoTabla;
var tmrDatePicker;
var tmrfotosreportes;
var tmrCargaHora;
var tmrSeguridad;
var tmrPuertaStatus;
var url="";
var map;
var darection;
let arrInvitadoWhats = [];
let arrInvitadoMail = [];
let posLatLng = "";
let selCalendarioInicio = "";
let selCalendarioFin = "";

window.onload = function() {
    tmrPuertaStatus = setInterval(getPuertaStatus,1000);
    tmrCargaHora =  setInterval(lblhora, 1000);
    tmrSeguridad =  setInterval(seguridadjs, 5000);
    cargaWeather();
    paypal();
    getUbicacion();
};

function getUbicacion(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setLatLng);
    }
}

function setLatLng(position) {
    posLatLng = position.coords.latitude + "," + position.coords.longitude;
}

function cargaWeather(){
    $.ajax({
        dataType: "json",
        url: 'select.php?tipo=7',
        success: function (data) {
            for(let i=0;i<data.length;i++) {
                arrLatLng = data[i].ubicacion.split(",");
                if (arrLatLng.length > 1) {
                    let lat = arrLatLng[0].trim();
                    let lng = arrLatLng[1].trim();
                    $.ajax({
                        dataType: "json",
                        url: 'https://api.openweathermap.org/data/2.5/weather?lat=' + lat + '&lon=' + lng + '&appid=b090d3c9bcc07d471a8a5f4491d3acac&units=metric',
                        success: function (data_weather) {
                            _('lbltemperatura_' + data[i].idestacion).innerText = Math.round(data_weather.main.temp);
                            _('lblciudadtemperatura_' + data[i].idestacion).innerText = data_weather.name;
                            _('icontemp_' + data[i].idestacion).src = 'https://openweathermap.org/img/wn/' + data_weather.weather[0].icon + '.png';
                        }
                    });
                }
            }
        }
    });



    //23.775732, -99.123497
    //api.openweathermap.org/data/2.5/weather?lat=17.38&lon=78.48
    //ff16b8506437605a5f762ed9efce9053
//        url: 'http://api.openweathermap.org/geo/1.0/reverse?lat=23.775732&lon=-99.123497&limit=1&appid=b090d3c9bcc07d471a8a5f4491d3acac',

}

async function seguridadjs() {
    const cootok = await getCookie("k");
    $.ajax({
        dataType: "json",
        url: '../common_files/clases/seguridad_main_js.php',
        type: 'post',
        data: {
            k: cootok
        },
        success: function (data) {
            //console.log(data);
            if (data.logout > 0) {
                location.href = "../login.php";
            }
        }
    });
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function lblhora (){
    var now = new Date();
    var nombreMes = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    var nombreDia = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];
    _("lblfecha").innerHTML = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
    _("txtHora").value = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
}

function formatoTabla(paging,retrieve,lengthChange,searching,ordering,info,autoWidth,botones = true) {
    var tablas = document.getElementsByClassName("table");
    for (i = 0; i < tablas.length; i++) {
        if (tablas[i].getAttribute("id").match(/lst.*/)) {
            $("table#" + tablas[i].getAttribute("id")).DataTable({
                "stateSave": true,
                "paging": paging,
                "retrieve": retrieve,
                "lengthChange": lengthChange,
                "searching": searching,
                "ordering": ordering,
                "info": info,
                "autoWidth": autoWidth,
                "dom": 'Bfrtip',
                "buttons": (botones)?["csvHtml5","pdfHtml5","copyHtml5"]:[],
                "language": {
                    "buttons": {
                        "copy": "Copiar",
                        "csv": "Excel CSV",
                        "pdf": "PDF"
                    },
                    "search": "Buscar:",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "emptyTable": "No se encontraron registros",
                    "infoEmpty": "No se encontraron registros",
                    "zeroRecords": "No se encontraron registros",
                    "paginate": {
                        "previous": " < ",
                        "next": " > "
                    }
                }
            });
            clearInterval(tmrFormatoTabla);
        }
    }
    clearInterval(tmrFormatoTabla);
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function agregaInvitado(){
    let dato = "";
    if (_('txtemail').value !== "") {
        if (validateEmail(_('txtemail').value)){
            dato = _('txtemail').value
        } else {
            Swal.fire('Error!',"Error, correo electrónico incorrecto !",'error');
            return false;
        }
    }
    if (_('txtwhatsapp').value !== ""){
        if (_('txtwhatsapp').value.length === 10){
            dato = _('txtwhatsapp').value
        } else {
            Swal.fire('Error!',"Error, número de whatsapp incorrecto !",'error');
            return false;
        }
    }
    if (datoinvitadorem(Base64.encode(_('txtnombrecontacto').value),true)) {
        json_Contact_Api.push({
            "nombre": _('txtnombrecontacto').value,
            "correo": _('txtemail').value,
            "telefono": _('cbopais').value + '' + _('txtwhatsapp').value
        });
        let strDivDato = "<div id=\"tmp_" + Base64.encode(_('txtnombrecontacto').value) + "\" onclick=\"datoinvitadorem('" + Base64.encode(_('txtnombrecontacto').value) + "',false);\" style=\"padding: 8px;\"><div class=\"vue-treeselect__multi-value-item\"><span class=\"vue-treeselect__multi-value-label\">" + _('txtnombrecontacto').value + "&nbsp;&nbsp;W: " + _('cbopais').value + '' + _('txtwhatsapp').value + "&nbsp;&nbsp;@: " + _('txtemail').value + "</span><span class=\"vue-treeselect__icon vue-treeselect__value-remove\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 348.333 348.333\"><path d=\"M336.559 68.611L231.016 174.165l105.543 105.549c15.699 15.705 15.699 41.145 0 56.85-7.844 7.844-18.128 11.769-28.407 11.769-10.296 0-20.581-3.919-28.419-11.769L174.167 231.003 68.609 336.563c-7.843 7.844-18.128 11.769-28.416 11.769-10.285 0-20.563-3.919-28.413-11.769-15.699-15.698-15.699-41.139 0-56.85l105.54-105.549L11.774 68.611c-15.699-15.699-15.699-41.145 0-56.844 15.696-15.687 41.127-15.687 56.829 0l105.563 105.554L279.721 11.767c15.705-15.687 41.139-15.687 56.832 0 15.705 15.699 15.705 41.145.006 56.844z\"></path></svg></span></div></div>";
        $('#divinvitados').append(strDivDato);
        _('txtwhatsapp').value = "";
        _('txtemail').value = "";
        _('txtnombrecontacto').value = "";
    } else{
        Swal.fire('Error!',"Error, invitado ya esta en la lista !",'error');
    }
}

function datoinvitadorem(elemento,valida){
    for(let j=0;j<json_Contact_Api.length;j++){
        if (Base64.encode(json_Contact_Api[j].nombre) === elemento) {
            if (valida){
                return false;
            } else {
                json_Contact_Api.splice(j, 1);
                _('tmp_' + elemento).style = "display:none;";
                break;
            }
        }
    }
    return true;
}

function guardar_evento(idestacion){
    if (json_Contact_Api.length === 0){
        Swal.fire('Error!',"Error, Ningún invitado para el evento.",'error');
        return false;
    }
    if (_('txtnombre').value === ""){
        Swal.fire('Error!',"Error, falta el nomnbre del evento.",'error');
        _('txtnombre').focus();
        return false;
    }
    $.ajax({
        type: "POST",
        url: "select.php?tipo=8",
        dataType: "json",
        data: {
            idestacion: _('txtidestacioninvitados').value,
            nombre: _('txtnombre').value,
            fecha: _('txtdtfecha').value,
            hora: _('txtdthora').value,
            json_invitados: JSON.stringify(json_Contact_Api)
        }
    }).done(function(data) {
        _('divinvitados_1').style = "display: none;";
        _('divinvitados_2').style = "display: inline;";

        var tabla = _('lstlistainvitados');
        for (let i=0;i<data.length;i++) {
            let row = tabla.insertRow(tabla.getElementsByTagName("tr").length);
            cell = row.insertCell(0);
            cell.innerHTML = data[i].nombre;
            cell = row.insertCell(1);
            cell.innerHTML = data[i].correo;
            cell = row.insertCell(2);
            cell.innerHTML = data[i].telefono;
            cell = row.insertCell(3);
            cell.innerHTML = "<button class=\"btn btn-gray-light\" onclick=\"enviaWhatsInvitado('" + Base64.encode(data[i].codigo) + "','"+data[i].telefono+"');\" title=\"Enviar whatsapp\" id=\"btncontactos\"><i class=\"fab fa-whatsapp text-success fa-2x\"></i></button>";
        }
    });
}

function enviaWhatsInvitado(codigo,telefono){
    codigo = codigo.replaceAll("=","");
    let url = encodeURI("https://wa.me/"+telefono+"?text=Hey !, entra a esta página para ver el código de acceso. https://smartdoor.mx?v=" + codigo);
    window.open(url,"_blank");
}

function valorListaChk(tabla, limpia){
        var chkArray = [];
        var table = $('#' + tabla).DataTable();
        table.rows().nodes().to$().find('input[type="checkbox"]').each(function(){
            if(this.checked){
                if (limpia) { this.checked = false; } else { chkArray.push($(this).val()); }
            }
        });
        var selected = "";
        selected = chkArray.join(',') ;
        return selected;
    }

function _(el){
    return document.getElementById(el);
}

function uploadFile(tipo,idcliente, objfile){
    var cant = _(objfile).files.length;
    if (cant === 0) { Swal.fire('Error!',"Error, debe seleccionar al menos un archivo !",'error');  return; }
    for (i=0;i<cant;i++) {
        var file = _(objfile).files[i];
        var formdata = new FormData();
        formdata.append(objfile, file);
        formdata.append(tipo,1);
        formdata.append("idusuario",idcliente);
        var ajax = new XMLHttpRequest();
        ajax.open("POST", "subir_archivos.php");
        ajax.send(formdata);
    }
}

function setDatePicker(){
    if (_('datepicker')) {
        $('#datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
        $('#datepicker').datepicker()
            .on('changeDate', function (e) {
                getPrecios(e.format("yyyy-mm-dd"));
                _('lblfacturafecha').innerHTML = e.format("yyyy-mm-dd");
                _('txtfacturafecha').value = e.format("yyyy-mm-dd");
            });
        clearInterval(tmrDatePicker);
    }
}

function formatMoneda(n, c, d, t) {
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d === undefined ? "." : d,
        t = t === undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function carga_camara() {
    Webcam.set({
        width: 640,
        height: 480,
        image_format: 'png',
        jpeg_quality: 100
    });
    Webcam.attach('#camara');
}

function take_snapshot() {
    Webcam.snap( function(data_uri) {
        _('camara').innerHTML = '<img id="imgfoto_identificacion" src="'+data_uri+'"/>';
        //_('btnCamara_verificaion').style = "display: inline;";
        _('imgavatar').src = data_uri;
        guardar_imagen_camara();
    } );
}

function guardar_imagen_camara() {
    var myImg = _("imgfoto_identificacion").src;
    $.ajax({
        type: "POST",
        url: "subir_archivos.php",
        data: {
            foto_identidad: 1,
            imagen: myImg
        }
    }).done(function(data) {
        _('lblStatus').innerHTML = "Im&aacute;gen guardada.";
        _('divReiniciaCamara').style = "display: inline";
    });
}

function pagos_invitados(){
    let pagar = false;
    return pagar;
}

function invitar_est_selec(idestacion,id_usuario){
    _('txtidestacioninvitados').value= idestacion;
    _('divinvitados_1').style='display:inline;';
    _('divinvitados_0').style='display:none;';
    _('txtnombre').focus();
    $.ajax({
        type: "POST",
        url: "select.php?tipo=9",
        dataType: "json",
        data: {
            idestacion: idestacion,
            idusuario: id_usuario
        }
    }).done(function(data) {
        let tabla = _('tblhistorico');
        for (let i=0;i<data.length;i++) {
            let row = tabla.insertRow(tabla.getElementsByTagName("tr").length);
            var copiainvitados = function(datos) {
                return function() {
                    for (let i=0;i<datos.length;i++){
                        if (datoinvitadorem(Base64.encode(datos[i].nombre),true)) {
                            json_Contact_Api.push({
                                "nombre": datos[i].nombre,
                                "correo": datos[i].email,
                                "telefono": (datos[i].telefono.length === 10)?'52'+ datos[i].telefono: datos[i].telefono
                            });
                            let strDivDato = "<div id=\"tmp_" + Base64.encode(datos[i].nombre) + "\" onclick=\"datoinvitadorem('" + Base64.encode(datos[i].nombre) + "',false);\" style=\"padding: 8px;\"><div class=\"vue-treeselect__multi-value-item\"><span class=\"vue-treeselect__multi-value-label\">" + datos[i].nombre + "&nbsp;&nbsp;W: " + ((datos[i].telefono.length === 10)?'52'+ datos[i].telefono: datos[i].telefono) + "&nbsp;&nbsp;@: " + datos[i].email + "</span><span class=\"vue-treeselect__icon vue-treeselect__value-remove\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 348.333 348.333\"><path d=\"M336.559 68.611L231.016 174.165l105.543 105.549c15.699 15.705 15.699 41.145 0 56.85-7.844 7.844-18.128 11.769-28.407 11.769-10.296 0-20.581-3.919-28.419-11.769L174.167 231.003 68.609 336.563c-7.843 7.844-18.128 11.769-28.416 11.769-10.285 0-20.563-3.919-28.413-11.769-15.699-15.698-15.699-41.139 0-56.85l105.54-105.549L11.774 68.611c-15.699-15.699-15.699-41.145 0-56.844 15.696-15.687 41.127-15.687 56.829 0l105.563 105.554L279.721 11.767c15.705-15.687 41.139-15.687 56.832 0 15.705 15.699 15.705 41.145.006 56.844z\"></path></svg></span></div></div>";
                            $('#divinvitados').append(strDivDato);
                        }
                    }
                };
            };
            row.onclick = copiainvitados(data[i].invitados);
            cell = row.insertCell(0);
            cell.innerHTML = data[i].evento;
            cell = row.insertCell(1);
            cell.innerHTML = data[i].fecha + ' ' + data[i].hora;
            cell = row.insertCell(2);
            cell.innerHTML = strInvitados(data[i].invitados);
        }
        setTimeout(function() {formatoTabla(true,true,false,false,false,true,true);},500);
    });
}

function strInvitados(data){
    let strinvitados = "";
    for (let j=0;j<data.length;j++){
        (strinvitados !== "")? strinvitados+=", ":strinvitados+="";
        strinvitados += data[j].nombre;
    }
    return strinvitados;
}

function cambiaavatar(elemento,tipo,idcliente,objfile) {
    _(objfile).onchange = function (evt) {
        var tgt = evt.target || window.event.srcElement,
            files = tgt.files;
        // FileReader support
        if (FileReader && files && files.length) {
            var fr = new FileReader();
            fr.onload = function () {
                _(elemento).src = fr.result;
                uploadFile(tipo,idcliente,objfile);
            };
            fr.readAsDataURL(files[0]);
        }
    }
}

function getGrafuso(estacion){
    ajaxpage('../common_files/cargando.html','contenido');
    $.ajax({
        url: "inicio_estacion.php",
    }).done(function(data) {
        _('contenido').innerHTML = data;
        $.ajax({
            type: "POST",
            url: "select.php?tipo=5",
            dataType: "json",
            data: {
                id: estacion
            }
        }).done(function(data) {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: data.meses[1],
                    data: data.valores
                },{
                    name: data.meses[0],
                    data: data.valores2
                }],
                xaxis: {
                    categories: data.categorias
                },
                colors:['#E91E63','#dedddd'],
                dataLabels: {
                    enabled: true,
                    offsetX: -6,
                    style: {
                        fontSize: '12px',
                        colors: ['#fff','#020202']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    followCursor: true,
                    shared: true,
                    intersect: false
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                }
            }
            _('divgrafuso').innerHTML = "";
            var chart = new ApexCharts(_('divgrafuso'), options);
            chart.render();
        });





        $.ajax({
            type: "GET",
            url: "select.php?tipo=19",
            dataType: "json",
            data: {
                id: estacion
            }
        }).done(function(data) {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: data.categorias,
                    data: data.valores
                }],
                xaxis: {
                    categories: data.categorias
                },
                colors:['#E91E63','#dedddd'],
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    followCursor: true,
                    shared: true,
                    intersect: false
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
            }
            _('divgrafhora').innerHTML = "";
            var chart = new ApexCharts(_('divgrafhora'), options);
            chart.render();
        });
    });
}

function descargarQr(nombre){
    var canvas = document.createElement("canvas");
    canvas.width = _('imgqr').width;
    canvas.height = _('imgqr').height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(_('imgqr'), 0, 0);
    var img_b64 = canvas.toDataURL("image/png");
    var a = document.createElement("a"); //Create <a>
    a.href = img_b64; //Image Base64 Goes here
    a.download = Base64.decode(nombre) + ".png"; //File name Here
    a.click(); //Downloaded file
}

function valida_horarios(){
    let valido = true;
    if (_('dtlunesinicio').value === ""){ valido = false; }
    if (_('dtlunesfin').value === ""){ valido = false; }
    if (_('dtmartesinicio').value === ""){ valido = false; }
    if (_('dtmartesfin').value === ""){ valido = false; }
    if (_('dtmiercolesinicio').value === ""){ valido = false; }
    if (_('dtmiercolesfin').value === ""){ valido = false; }
    if (_('dtjuevesinicio').value === ""){ valido = false; }
    if (_('dtjuevesfin').value === ""){ valido = false; }
    if (_('dtviernesinicio').value === ""){ valido = false; }
    if (_('dtviernesfin').value === ""){ valido = false; }
    if (_('dtsabadoinicio').value === ""){ valido = false; }
    if (_('dtsabadofin').value === ""){ valido = false; }
    if (_('dtdomingoinicio').value === ""){ valido = false; }
    if (_('dtdomingofin').value === ""){ valido = false; }
    if (valido){
        _('btnGuardar').disabled = false;
    }
}

function guardar_horario(){
    $.ajax({
        url: 'select.php?tipo=10',
        type: 'post',
        data: {
            idestacion: Base64.encode(_('cboestacionhorario').value),
            lunes: _('dtlunesinicio').value + ',' + _('dtlunesfin').value,
            martes: _('dtmartesinicio').value + ',' + _('dtmartesfin').value,
            miercoles: _('dtmiercolesinicio').value + ',' + _('dtmiercolesfin').value,
            jueves: _('dtjuevesinicio').value + ',' + _('dtjuevesfin').value,
            viernes: _('dtviernesinicio').value + ',' + _('dtviernesfin').value,
            sabado: _('dtsabadoinicio').value + ',' + _('dtsabadofin').value,
            domingo: _('dtdomingoinicio').value + ',' + _('dtdomingofin').value
        },
        success: function (data) {
            menu_horarios();
        }
    });
}

function paypal(){
    $('.paypal-button-container').each(function() {
        this.innerHTML="";
        let idestacion = this.id.split('paypal-')[1];


        let precio_plan = _(idestacion + '_plan_precio').value;
        let desc_plan = _(idestacion + '_plan_desc').value;
        let nombre_plan = _(idestacion + '_plan_nombre').value;
        let sku_smartdoor = _(idestacion + '_sku_smartdoor').value;
        let cantidad = 0;
        if (Number(_(idestacion + '_visitas').value) === 1){
            //pagando visitas
            cantidad = _('cantvisitas').value;
        } else {
            // pagando plan de servicio
            cantidad = (Number(_(idestacion + '_plan_pago_x_usuario').value) === 1) ? Number(_(idestacion + '_usuarios').value) : 1;
        }

        paypal_sdk.Buttons({
            locale: 'es_MX',
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: precio_plan * cantidad,
                            currency_code: 'MXN',
                            breakdown: {
                                item_total: {
                                    currency_code: 'MXN',
                                    value: precio_plan * cantidad
                                }
                            }
                        },
                        items: [{
                            name: nombre_plan,
                            unit_amount: {
                                currency_code: 'MXN',
                                value: precio_plan
                            },
                            sku: sku_smartdoor,
                            quantity: cantidad,
                            description: desc_plan
                        }]
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    if (details.status === "COMPLETED") {
                        //console.log(details);
                        $.ajax({
                            type: "POST",
                            url: "select.php?tipo=13",
                            data: {
                                idestacion: idestacion,
                                idusuario: sku_smartdoor.split("_")[1],
                                monto: precio_plan * cantidad,
                                status: details.status,
                                sku: sku_smartdoor,
                                cantidad: cantidad,
                                paypal_json: JSON.stringify(details)
                            }
                        }).done(function(data) {
                            let jGoo = {
                                currency: "MXN",
                                transaction_id: details.id,
                                value: precio_plan * cantidad,
                                items: [
                                    {
                                        item_id: sku_smartdoor,
                                        item_name: nombre_plan,
                                        currency: "MXN",
                                        price: precio_plan,
                                        quantity: cantidad
                                    }
                                ]
                            };
                            //console.log(jGoo);
                            gtag("event", "purchase", jGoo);
                            if (Number(_(idestacion + '_visitas').value) === 1){
                                //pagando visitas
                                menu_invitar();
                            } else {
                                // pagando plan de servicio
                                menu_inicio();
                            }

                        });
                    }
                });
            }
        }).render(this);
    });
}

function selecPlanServicio(idplan,idestacion64,precio,descripcion,nombre){
    $('.lblselecplan').css('display','none');
    _('lblselec' + idplan).style='display:inline;';
    $('.planservicio').addClass('elevation-5');
    _(idestacion64 + '_plan_precio').value = Base64.decode(precio);
    _(idestacion64 + '_plan_desc').value = Base64.decode(descripcion);
    _(idestacion64 + '_plan_nombre').value = Base64.decode(nombre);
    let sku = idestacion64 + "_" + _(idestacion64 + '_sku_smartdoor').value.split("_")[1] + "_idplan_" + idplan + "_" + _(idestacion64 + '_sku_smartdoor').value.split("_")[4];
    _(idestacion64 + '_sku_smartdoor').value = sku;
    let pagoUsuario = (Number(_(idestacion64 + '_plan_pago_x_usuario').value) === 1);
    if (pagoUsuario){
        _('divTotalPlan').innerHTML = "<label class=\"text-danger\">$ " + formatMoneda(Base64.decode(precio) * Number(_(idestacion64 + '_usuarios').value),2) + "</label><br> <label class=\"text-sm text-gray\">( " + _(idestacion64 + '_usuarios').value + " Usuarios activos * $" + formatMoneda(Base64.decode(precio),2) + " )</label>";
    } else {
        _('divTotalPlan').innerHTML = "<label class=\"text-danger\">$ " + formatMoneda(Base64.decode(precio),2) + "</label>";
    }
    paypal();
}

async function getPlanesServicio(tipoInstalacion,idplanactual,idestacion64){
    let precio = 0;
    let descripcion = "";
    let nombre = "";
    await $.ajax({
        url: 'select.php?tipo=14',
        data: {
            tiposervicio: tipoInstalacion
        },
        type: 'post',
        dataType: "json",
        success: function (data) {
            _('divplanesservicio').innerHTML = "";
            for(var i=0;i<data.length;i++) {
                if (Number(data[i].idplan) === Number(idplanactual)){
                    precio = Base64.encode(data[i].monto);
                    descripcion = Base64.encode(data[i].descripcion);
                    nombre = Base64.encode(data[i].nombre);
                }
                var encabezado = "<div class=\"card card-default elevation-5 planservicio\" onclick=\"selecPlanServicio(" + data[i].idplan + ",'"+idestacion64+"','"+Base64.encode(data[i].monto)+"','"+Base64.encode(data[i].descripcion)+"','"+Base64.encode(data[i].nombre)+"');$(this).toggleClass('elevation-5');\">\n" +
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
            selecPlanServicio(idplanactual,idestacion64,precio,descripcion,nombre);
        }
    });
}