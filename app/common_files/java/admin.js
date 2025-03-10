/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

var tmrCargaHora =  setInterval(lblhora, 1000);
var tmrSeguridad =  setInterval(seguridadjs, 5000);
var map;
const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
let infowin = null;

window.onload = function() {
    cargaMapa();
};

function cargaMapa() {
    var myLatlng = new google.maps.LatLng(22.953042, -102.028642);
    map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 6,
        mapTypeControl: false,
        center: myLatlng,
        mapTypeId: 'roadmap'
    });

    muestraSucursales();
}

function muestraSucursales(){
    var markers = [];
    for (let i = 0; i < jsonEstaciones.length; i++) {
        let latlng = jsonEstaciones[i].ubicacion;
        let icono_path = "";
        let status = (Number(jsonEstaciones[i].actualizado)>0)?2:Number(jsonEstaciones[i].status);
        switch (status){
            case 0:
            case 1:
                icono_path = {url: "../common_files/img/pin_activo.png", scaledSize: new google.maps.Size(40,40)};
                break;
            case 2:
                icono_path = {url: "../common_files/img/pin_inactivo.png", scaledSize: new google.maps.Size(40,40)};
                break;
        }
        if (latlng !== "") {
            latlng = latlng.split(",");
            let lat = latlng[0];
            let long = latlng[1];
            let location = new google.maps.LatLng(lat, long);
            let marker = new google.maps.Marker({
                position: location,
                icon: icono_path,
                animation: google.maps.Animation.DROP,
                title: jsonEstaciones[i].idestacion
            });
            markers.push(marker);
            let status_est = jsonEstaciones[i].status;
            if (jsonEstaciones[i].actualizado > 0){ status_est = 2; }
            switch (Number(status_est)){
                case 0:
                    status_est = "<label class=\"text-success\">En espera</label>";
                    break;
                case 1:
                    status_est = "<label class=\"text-danger\">En movimiento</label>";
                    break;
                case 2:
                    status_est = "<label class=\"text-danger\">ERROR: Sin conexion WIFI</label>";
                    break;
            }

            marker.addListener("click", () => {
                if (infowin) { infowin.setMap(null); }
                var strinfo = "<div class=\"card card-primary card-solid\">\n" +
                    "    <div class=\"card-header with-border\">\n" +
                    "        <h4 class=\"card-title\">"+jsonEstaciones[i].nombre_estacion +  ' ' + jsonEstaciones[i].nombre_puerta+"</h4>\n" +
                    "        <label class=\"card-title\">&nbsp;&nbsp;&nbsp;"+jsonEstaciones[i].idestacion+"</label>\n" +
                    "<div class=\"card-tools\">" +
                    "<button type=\"button\" class=\"btn btn-tool\" onclick=\"infowin.setMap(null);\"><i class=\"fas fa-times\"></i></button></div></div>" +
                    "<div class=\"card-body\">" +
                    "<div class=\"row\">Status:&nbsp;&nbsp;&nbsp;"+status_est+"</div>" +
                    "<div class=\"row\">WiFi:&nbsp;&nbsp;&nbsp;"+jsonEstaciones[i].wifi_signal+"</div>" +
                    "<div class=\"row\">Version:&nbsp;&nbsp;&nbsp;"+jsonEstaciones[i].version+"</div><br><br>" +
                    "<div class=\"row\">Aperturas:&nbsp;&nbsp;&nbsp;"+jsonEstaciones[i].aperturas+"</div><br><br>" +
                    "<div class=\"row\">Errores:&nbsp;&nbsp;&nbsp;"+jsonEstaciones[i].errores+"</div><br><br>" +
                    "<div class=\"row\"><button type=\"button\" class=\"btn btn-danger\" onclick=\"admin_tags('"+Base64.encode(jsonEstaciones[i].idestacion)+"');\">Nfc Tags</i></button></div><br><br>" +
                    "<div class=\"row\"><button type=\"button\" class=\"btn btn-danger\" onclick=\"reemplazo('"+Base64.encode(jsonEstaciones[i].idestacion)+"');\">Remplazo de dispositivo</i></button></div><br><br>" +
                    "</div></div>";
                infowin = new google.maps.InfoWindow({content: strinfo});
                infowin.open(map,marker);
            });

        } else {
            console.log("no encontrado: " + jsonEstaciones[i].idestacion);
        }
    }
    var markerCluster = new MarkerClusterer(map, markers, {imagePath: '../common_files/img/m'});
}

async function reemplazo(idestacion){
    Swal.fire({
        title: 'Quieres cambiar el dispositivo ?',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No',
    }).then(async (result) => {
        if (result.value) {
            const { value: mac } = await Swal.fire({
                title: 'Escriba la nueva mac',
                input: 'text'
            })
            if (mac){
                Swal.fire({
                    title: 'Guardando datos. Por favor espere, esto puede tardar varios minutos.',
                    didOpen: () => {
                        Swal.showLoading()
                        $.ajax({
                            url: 'admin.php',
                            data: {
                                idestacion: idestacion,
                                idnuevo: Base64.encode(mac)
                            },
                            type: 'post',
                            success: function (data) {
                                swal.close();
                                Swal.fire({
                                    position: 'top-end',
                                    type: 'success',
                                    title: 'Estación reemplazada',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        });
                    }, allowOutsideClick: () => !Swal.isLoading()
                });
            }
        }
    });
}

function admin_tags_encript(idusuario){
    _('txttag_'+idusuario).value = 'https://smartdoor.mx?t=' + Base64.encode(_('txttag_'+idusuario).value);
    _('btncalc_'+idusuario).disabled = true;
    _('btnguarda_'+idusuario).disabled = false;
}

function admin_tags_guarda(idestacion,idusuario){
    $.ajax({
        url: 'admin_tags.php',
        data: {
            idestacion: idestacion,
            idusuario: idusuario,
            tag: _('txttag_'+idusuario).value
        },
        type: 'post',
        success: function (data) {
            _('contenido').innerHTML = data;
        }
    });
}

function admin_tags(idestacion){
    $.ajax({
        url: 'admin_tags.php',
        data: {
            idestacion: idestacion
        },
        type: 'post',
        success: function (data) {
           _('contenido').innerHTML = data;
        }
    });
}

function seguridadjs() {
    var data;
    $.ajax({
        dataType: "json",
        url: '../common_files/clases/seguridad_main_js.php',
        data: data,
        success: function (data) {
            if (data.logout > 0) {
                location.href = "../lock.php";
            }
        }
    });
}

function lblhora (){
    var now = new Date();
    var nombreMes = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    var nombreDia = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];
    _("lblfecha").innerHTML = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
    _("txtHora").value = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
}

function _(el){
    return document.getElementById(el);
}

