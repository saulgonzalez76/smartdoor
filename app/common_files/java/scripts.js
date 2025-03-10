
function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function _(el){
    return document.getElementById(el);
}

function getIonosEncrypt(data){
    Swal.fire({
        title: 'Loading ...',
        onBeforeOpen: () => {
            Swal.showLoading();
            $.ajax({
                url: "playground/ionosencrypt.php",
                type: 'POST',
                data: { codigo: data },
            }).done(function (data) {
                console.log(data);
                _('resultado').innerHTML = data;
                swal.close();
            });
        }, allowOutsideClick: () => !Swal.isLoading()
    });
}

function getPrestashop(catalogo,key,server,puerto){
    Swal.fire({
        title: 'Loading ...',
        onBeforeOpen: () => {
            Swal.showLoading();
            $.ajax({
                url: "playground/prestashop.php",
                type: 'POST',
                data: { server: server, catalogo: catalogo, key: key, serverport: puerto },
            }).done(function (data) {
                _('resultado2').innerHTML = data;
                swal.close();
            });
        }, allowOutsideClick: () => !Swal.isLoading()
    });
}

function validate() {
    var mensaje = "";
    if (!validateEmail(_('txtemail').value)) {
        _('txtemail').focus();
        mensaje = "Incorrect em@ail<br>";
    }
    if (_('txtname').value === "") {
        _('txtname').focus();
        mensaje += "I need your name !<br>";
    }
    if (_('txttitulo').value === "") {
        _('txttitulo').focus();
        mensaje += "What about the subject ?<br>";
    }
    if (_('txtbody').value === "") {
        _('txtbody').focus();
        mensaje += "What's the message ?'<br>";
    }
    return mensaje;
}

function contacto(){
    var mensaje = validate();
    if (mensaje !== "") {
        Swal.fire({
            title: 'Error!',
            html: mensaje,
            icon: 'error',
            confirmButtonText: 'Got it'
        })
    }


    $.ajax({
        url: '2.php',
        type: 'post',
        data: {
            email: _('txtemail').value,
            name: _('txtname').value,
            subject: _('txttitulo').value,
            body: _('txtbody').value
        },
        dataType: 'json',
        success: function(response){
            if (Number(response) === 0) {
                Swal.fire({
                    title: 'Error!',
                    html: "There seems to be a problem with my server, couldn't send the email... ",
                    icon: 'error',
                    confirmButtonText: 'Sorry'
                })
            } else {
                _('contenido').innerHTML = "<center><img src=\"../images/email.jpg\" width=\"40%\"><br><h3>Thanks for the email !</h3></center><br><br>";
            }
        }
    });


}