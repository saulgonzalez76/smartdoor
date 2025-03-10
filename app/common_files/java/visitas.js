let posision = "";
getUbicacion();
function getUbicacion(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setLatLng);
    }
}

function setLatLng(position) {
    posision = position.coords.latitude + "," + position.coords.longitude;
}

async function abrepuerta(codigo, nombre, ubicacion){
    codigo = Base64.decode(codigo);
    nombre = Base64.decode(nombre);
    ubicacion = Base64.decode(ubicacion);
    if (posision === ""){
        Swal.fire('Error','Es necesario que nos compartas tu ubicación para poder abrir !','error');
    } else {
        if (getDistanceFromLatLonInKm(posision.split(",")[0],posision.split(",")[1],ubicacion.split(",")[0],ubicacion.split(",")[1]) < 100) {
            Swal.fire({
                title: 'Quieres abrir ' + nombre + ' ?',
                showCancelButton: true,
                confirmButtonText: 'Abrir puerta !',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No, dejala cerrada.',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: 'select.php?tipo=16&codigo=' + codigo+'&pos='+posision,
                        success: function (data) {
                            data = JSON.parse(data);
                            data = data.estatus;
                            switch (Number(data)) {
                                case 0:
                                    $.ajax({
                                        url: 'select.php?tipo=17&codigo=' + codigo,
                                        success: function (data) {
                                            let idRegistro = Number(data);
                                            if (isNaN(idRegistro)) {
                                                swal.fire(nombre, data + " a hecho una apertura, debe esperar un momento para ejecutar de nuevo.", 'error');
                                            } else {
                                                Swal.fire({
                                                    title: 'Esperando a ' + nombre,
                                                    html: 'Espere mientras recibimos confirmación de la puerta, se cancela en: <b>30</b>',
                                                    timer: 30000,
                                                    timerProgressBar: true,
                                                    didOpen: () => {
                                                        Swal.showLoading()
                                                        const b = Swal.getHtmlContainer().querySelector('b')
                                                        timerInterval = setInterval(() => {
                                                            b.textContent = Math.round(Swal.getTimerLeft()/1000)
                                                        }, 100)
                                                        $.ajax({
                                                            url: 'select.php?tipo=15&idregistro=' + idRegistro,
                                                            success: function (data) {
                                                                if (Number(data) === 1) {
                                                                    swal.close();
                                                                    swal.fire(nombre, "Hecho, se abrio la puerta !", 'success');
                                                                } else {
                                                                    swal.close();
                                                                    swal.fire(nombre, "No se a podido abrir la puerta, operación cancelada !", 'error');
                                                                }
                                                                clearInterval(timerInterval);
                                                            }
                                                        });
                                                    },allowOutsideClick: () => !Swal.isLoading()
                                                });
                                            }
                                        }
                                    });
                                    break;
                                case 1:
                                    swal.fire(nombre, "La puerta se encuentra en movimiento, intenta en un minuto.", 'error');
                                    break;
                                case 2:
                                    swal.fire(nombre, "La puerta se encuentra sin conexion WIFI.", 'error');
                                    break;
                            }
                        }
                    });
                }
            });
        } else {
            Swal.fire('Error','Como invitado, solo puedes abrir dentro de un radio de 100 mts.','error');
        }
    }
}

function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2-lat1);  // deg2rad below
    var dLon = deg2rad(lon2-lon1);
    var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2)
    ;
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c * 1000; // Distancia en metros
}

function deg2rad(deg) {
    return deg * (Math.PI/180)
}