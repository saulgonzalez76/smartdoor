window.onload = function() {
    if ((token !== "") && (nombre !== "")){
        if (bloqueado === 1){
            swal.fire({
                title: nombre,
                text: "Usuario bloqueado por administrador.",
                icon: 'error'
            }).then((result) => {
                window.location.replace("index.php");
            });
        } else {
            abrepuerta(Base64.encode(token), Base64.encode(nombre));
        }
    } else {
        window.location.replace("index.php");
    }
};

function abrepuerta(codigo, nombre){
    codigo = Base64.decode(codigo);
    nombre = Base64.decode(nombre);
    $.ajax({
        url: 'desktop/select.php?tipo=2&codigo='+codigo,
        success: function (data) {
            data = JSON.parse(data);
            data = data.estatus;
            switch (Number(data)){
                case 0:
                    $.ajax({
                        url: 'desktop/select.php?tipo=1&codigo='+codigo+'&pos=nfc',
                        success: function (data) {
                            let idRegistro = Number(data);
                            if (isNaN(idRegistro)) {
                                swal.fire({
                                    title: nombre,
                                    text: data + " a hecho una apertura, debe esperar un momento para ejecutar de nuevo.",
                                    icon: 'error'
                                }).then((result) => {
                                    window.location.replace("index.php");
                                });
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
                                                    swal.fire({ title: nombre, text: "Hecho, se abrio la puerta !", icon: 'success' }).then((result) => { window.location.replace("index.php"); });
                                                } else {
                                                    swal.close();
                                                    swal.fire({
                                                        title: nombre,
                                                        text: "No se a podido abrir la puerta, operación cancelada !",
                                                        icon: 'error'
                                                    }).then((result) => {
                                                        window.location.replace("index.php");
                                                    });
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
                    swal.fire({
                        title: nombre,
                        text: "La puerta se encuentra en movimiento, intenta en un minuto.",
                        icon: 'error'
                    }).then((result) => {
                        window.location.replace("index.php");
                    });
                    break;
                case 2:
                    swal.fire({
                        title: nombre,
                        text: "La puerta se encuentra sin conexion WIFI.",
                        icon: 'error'
                    }).then((result) => {
                        window.location.replace("index.php");
                    });
                    break;
            }
        }
    });
}
