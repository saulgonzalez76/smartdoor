

window.onload = function() {
    setInterval(getRegistros,2000);
    $('#totreg').FlipClock(total_registros, {clockFace: 'Counter'});
    $('#totpart').FlipClock(total_particulares, {clockFace: 'Counter'});
    $('#totneg').FlipClock(total_negocios, {clockFace: 'Counter'});
    $('#totfracc').FlipClock(total_fracc, {clockFace: 'Counter'});
};

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function _(el){
    return document.getElementById(el);
}

function getRegistros(){
    $.ajax({
        url: 'select.php?tipo=1',
        dataType: 'json',
        success: function(data){
            if (Number(data.registros) !== total_registros) {
                total_registros = Number(data.registros);
                $('#totreg').FlipClock(total_registros, {clockFace: 'Counter'});
            }

            if (Number(data.particulares) !== total_particulares) {
                total_particulares = Number(data.particulares);
                $('#totpart').FlipClock(total_particulares, {clockFace: 'Counter'});
            }

            if (Number(data.negocios) !== total_negocios) {
                total_negocios = Number(data.negocios);
                $('#totneg').FlipClock(total_negocios, {clockFace: 'Counter'});
            }

            if (Number(data.fracc) !== total_fracc) {
                total_fracc = Number(data.fracc);
                $('#totfracc').FlipClock(total_fracc, {clockFace: 'Counter'});
            }
        }
    });
}

function contacto(){
    var mensaje = "";
    if (!validateEmail(_('txtemail').value)) { mensaje = "Correo electronico incorrecto.<br>"; }
    if (_('txtname').value === "") { mensaje += "Falta el nombre.<br>"; }
    if (_('txttitulo').value === "") { mensaje += "Falta el titulo del mensaje.<br>"; }
    if (_('txtbody').value === "") { mensaje += "Falta el mensaje.<br>"; }
    if (mensaje !== "") {
        Swal.fire({
            title: 'Error!',
            html: mensaje,
            icon: 'error',
            confirmButtonText: 'Ok'
        })
    } else {
        $.ajax({
            url: 'select.php?tipo=2',
            type: 'post',
            data: {
                email: _('txtemail').value,
                nombre: _('txtname').value,
                titulo: _('txttitulo').value,
                mensaje: _('txtbody').value
            },
            success: function (response) {
                console.log("email:" + response);
                if (response !== "1") {
                    Swal.fire({
                        title: 'Error!',
                        html: "Al parecer tenemos un problema en nuestro servidor, no se envio el mensaje... ",
                        icon: 'error',
                        confirmButtonText: 'Disculpa'
                    });
                } else {
                    Swal.fire({
                        title: 'Enviado !',
                        html: "Gracias por contactarnos !",
                        icon: 'succes'
                    });
                    _('txtemail').value = "";
                    _('txtname').value = "";
                    _('txttitulo').value = "";
                    _('txtbody').value = "";
                }
            }
        });
    }
}
