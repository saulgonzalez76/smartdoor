/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

function validaUsuarioNuevo(idestacion,nombreestacion) {
    res = "";
    if (_('txtNombre').value === "") { res = "Falta el nombre completo del usuario.<br>"; }
    if (_('txtCorreo').value === "") { res += "Falta el correo electronico del usuario.<br>"; }
    if ((_('txtCorreo').value !== "") && (!validateEmail(_('txtCorreo').value))) { res += "El correo electronico no es valido.<br>"; }
    if (_('txtWhatsapp').value.length < 10){ res += "Falta el número de whatsapp o celular del usuario.<br>"; }
    if (res !== "") {
        Swal.fire('Error!',"Se encontraron algunos errores:<br><br>" + res + "<br>Favor de corregirlos antes de guardar.",'error');
        return false;
    } else {
        _('btnagregausuario').disabled = true;
        $.ajax({
            type: 'post',
            url: 'select.php?tipo=6',
            data: {
                email: _('txtCorreo').value
            },
            success: function (data) {
                let idusuario = 0;
                if (Number(data)>0){ idusuario = data; }
                $.ajax({
                    type: 'post',
                    url: 'usuario_nuevo.php',
                    data: {
                        email: _('txtCorreo').value,
                        guardar: 1,
                        id: Base64.decode(idestacion),
                        idusuario: idusuario,
                        estacion: Base64.decode(nombreestacion),
                        nombre: _('txtNombre').value,
                        whatsapp: _('cbopais').value + _('txtWhatsapp').value
                    },
                    success: function (data) {
                        console.log("data:"+data);
                        if (data == ""){
                            swal.fire('Error','Ups, a ocurrido un error y no se guardo el usuario. Si el problema persiste por favor reportelo a ayuda@smartdoor.mx','error');
                        } else {
                            _('contenido').innerHTML = data;
                        }
                    }
                });
            }
        });
    }
}

function validaEmpleadoNuevo(idestacion,nombreestacion) {
    res = "";
    if (_('txtNombre').value === "") { res = "Falta el nombre completo del usuario.<br>"; }
    if (_('cboHorario').value === 0){ res += "Falta el horario para el empleado.<br>"; }
    if (res !== "") {
        Swal.fire('Error!',"Se encontraron algunos errores:<br><br>" + res + "<br>Favor de corregirlos antes de guardar.",'error');
        return false;
    } else {
        _('btnagregausuario').disabled = true;
        $.ajax({
            type: 'post',
            url: 'empleado_nuevo.php',
            data: {
                guardar: 1,
                id: Base64.decode(idestacion),
                estacion: Base64.decode(nombreestacion),
                nombre: _('txtNombre').value,
                horario: _('cboHorario').value
            },
            success: function (data) {
                _('contenido').innerHTML = data;
            }
        });
    }
}

function abrepuerta(codigo, nombre){
    codigo = Base64.decode(codigo);
    nombre = Base64.decode(nombre);
    Swal.fire({
        title: 'Quieres abrir ' + nombre + ' ?',
        showCancelButton: true,
        confirmButtonText: 'Abrir puerta !',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No, dejala cerrada.',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: 'select.php?tipo=2&codigo='+codigo,
                success: function (data) {
                    data = JSON.parse(data);
                    data = data.estatus;
                    switch (Number(data)){
                        case 0:
                            $.ajax({
                                url: 'select.php?tipo=1&codigo='+codigo+'&pos='+posLatLng,
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
}

function getPuertaStatus(){
    $("label[id^='lblstatus_']").each(function() {
        var elemento = $(this).attr('id');
        $.ajax({
            url: 'select.php?tipo=2&codigo='+elemento.split('lblstatus_')[1],
            success: function (data) {
                data = JSON.parse(data);
                if (data.hardware === "ESP01") {
                    switch (Number(data.estatus)) {
                        case 0:
                            $('#' + elemento).removeClass('text-danger');
                            $('#' + elemento).addClass('text-success');
                            _(elemento).innerHTML = "En espera";
                            break;
                        case 1:
                            $('#' + elemento).removeClass('text-success');
                            $('#' + elemento).addClass('text-danger');
                            _(elemento).innerHTML = "En movimiento";
                            break;
                        case 2:
                            $('#' + elemento).removeClass('text-success');
                            $('#' + elemento).addClass('text-danger');
                            _(elemento).innerHTML = "ERROR: Sin conexion WIFI";
                            break;
                    }
                } else if (data.hardware === "ESP02") {
                        switch (Number(data.estatus)) {
                            case 0:
                                $('#' + elemento).removeClass('text-success');
                                $('#' + elemento).addClass('text-danger');
                                _(elemento).innerHTML = "Inactivo";
                                break;
                            case 1:
                                $('#' + elemento).removeClass('text-danger');
                                $('#' + elemento).addClass('text-success');
                                _(elemento).innerHTML = "Activo";
                                break;
                            case 2:
                                $('#' + elemento).removeClass('text-success');
                                $('#' + elemento).addClass('text-danger');
                                _(elemento).innerHTML = "ERROR: Sin conexion WIFI";
                                break;
                        }
                }


                let divSenElem = 'divsenal_'+elemento.split('lblstatus_')[1];
                switch (Number(data.wifi)){
                    case 0:
                        _(divSenElem).innerHTML = "<label class=\" text-md text-danger\"><i class=\"fas fa-skull-crossbones\"></i></label>";
                        break;
                    case 1:
                        _(divSenElem).innerHTML = "<label class=\" text-xs text-danger\">" + data.dbi + " dBm&nbsp;&nbsp;&nbsp;</label><label class=\" text-xs text-danger\">)</label>";
                        break;
                    case 2:
                        _(divSenElem).innerHTML = "<label class=\" text-xs text-danger\">" + data.dbi + " dBm&nbsp;&nbsp;&nbsp;</label><label class=\" text-xs text-warning\">)</label><label class=\" text-sm text-warning\">)</label>";
                        break;
                    case 3:
                        _(divSenElem).innerHTML = "<label class=\" text-xs text-danger\">" + data.dbi + " dBm&nbsp;&nbsp;&nbsp;</label><label class=\" text-xs text-success\">)</label><label class=\" text-sm text-success\">)</label><label class=\" text-md text-success\">)</label>";
                        break;
                }
            }
        });
    });
}

function bloquearUsuario(codigo,bloqueado){
    $.ajax({
        url: 'select.php?tipo=11',
        data: {
            bloqueado: bloqueado,
            codigo: codigo
        },
        type: 'post',
        success: function (data) {
            if (Number(data) === 1) {
                _('imgqr').src = "../common_files/img/blocked.png";
            } else {
                _('imgqr').src = "../common_files/clases/img_qr.php?codigo=" + codigo;
            }
            _('txtbloqueado').value = (Number(bloqueado) === 1)?0:1;
            if (Number(bloqueado)===1){
                $('#btnbloquear').removeClass('btn-danger');
                $('#btnbloquear').addClass('btn-primary');
                _('btnbloquear').innerHTML = "Habilitar usuario <i class=\"fas fa-unlock-alt\"></i>";
            } else {
                $('#btnbloquear').removeClass('btn-primary');
                $('#btnbloquear').addClass('btn-danger');
                _('btnbloquear').innerHTML = "Bloquear usuario <i class=\"fas fa-user-lock\"></i>";
            }
        }
    });
}

function eliminarUsuario(codigo,idestacion,idusuario){
    Swal.fire({
        title: 'Quieres eliminar este usuario ?',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No.',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: 'select.php?tipo=16',
                data: {
                    idestacion: idestacion,
                    codigo: codigo,
                    idusuario: idusuario
                },
                type: 'post',
                success: function (data) {
                    menu_inicio();
                }
            });
        }
    });
}

function setAdminUsuario(estacion,usuario,admin){
    $.ajax({
        url: 'select.php?tipo=12',
        data: {
            admin: admin,
            idestacion: estacion,
            usuario: usuario
        },
        type: 'post',
        success: function (data) {
            _('txtadmin').value = (Number(admin) === 1)?0:1;
            if (Number(admin)===0){
                $('#btnadmin').removeClass('btn-danger');
                $('#btnadmin').addClass('btn-primary');
                _('btnadmin').innerHTML = "Hacer administrador <i class=\"fas fa-user-cog\"></i>";
            } else {
                $('#btnadmin').removeClass('btn-primary');
                $('#btnadmin').addClass('btn-danger');
                _('btnadmin').innerHTML = "Quitar administrador <i class=\"fas fa-user-cog\"></i>";
            }
        }
    });
}

function procesar_lista_nuevo_usuario(usuarios_restantes){
    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
    if (regex.test(_('archnuevousuario').value.toLowerCase())) {
        var fileUpload = _("archnuevousuario");
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            //For Browsers other than IE.
            if (reader.readAsBinaryString) {
                reader.onload = function (e) {
                    ProcessExcel(e.target.result,usuarios_restantes);
                };
                reader.readAsBinaryString(fileUpload.files[0]);
            } else {
                //For IE Browser.
                reader.onload = function (e) {
                    var data = "";
                    var bytes = new Uint8Array(e.target.result);
                    for (var i = 0; i < bytes.byteLength; i++) {
                        data += String.fromCharCode(bytes[i]);
                    }
                    ProcessExcel(data,usuarios_restantes);
                };
                reader.readAsArrayBuffer(fileUpload.files[0]);
            }
        } else {
            Swal.fire('Error!',"El navegador no soporta esta funcion.",'error');
        }
    } else {
        Swal.fire('Error!',"Archivo no reconocido, si el nombre del archivo contiene 'ñ' por favor cambie el nombre del archivo. Debe subir un archivo .XLSX (Excel) con el listado de predios de avios.",'error');
    }
}

function ProcessExcel(data,usuarios_restantes) {
    //Read the Excel File data.
    var workbook = XLSX.read(data, {
        type: 'binary',
        skipHeader: false
    });
    var tabla = _('lstListaImportar');
    var excelRows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
    for (let i=0;i<excelRows.length;i++) {
        if (usuarios_restantes >= tabla.rows.length) {
            let row = tabla.insertRow(tabla.getElementsByTagName("tr").length);
            row.insertCell(0);
            row.insertCell(1);
            row.insertCell(2);
            row.insertCell(3);
            for (let key in excelRows[i]) {
                switch (key) {
                    case "NOMBRE":
                        row.cells[0].innerHTML = excelRows[i][key];
                        break;
                    case "CORREO":
                        row.cells[1].innerHTML = excelRows[i][key];
                        break;
                    case "TEL":
                        row.cells[2].innerHTML = excelRows[i][key];
                        break;
                }
            }
            row.cells[3].innerHTML = "";
        }
    }
    if (usuarios_restantes < excelRows.length){
        _('lblerrorImporta').innerHTML = (excelRows.length - usuarios_restantes) + " Usuarios no importados por falta de espacio.";
    }
    if (tabla.rows.length>1){
        _('btnagregausuariolista').disabled = "";
    }
}

async function creaUsuariosLista(idestacion,nombreestacion){
    _('btnagregausuariolista').disabled = true;
    var tabla = _('lstListaImportar');
    for (let linea=1;linea<tabla.rows.length;linea++){
        tabla.rows[linea].cells[3].innerHTML = "<i class=\"fa fa-spin fa-spinner\"></i>";
        await $.ajax({
            type: 'post',
            url: 'select.php?tipo=6',
            data: {
                email: tabla.rows[linea].cells[1].innerText
            },
            success: function (data) {
                let idusuario = 0;
                if (Number(data)>0){ idusuario = data; }
                $.ajax({
                    type: 'post',
                    url: 'usuario_nuevo.php',
                    data: {
                        email: tabla.rows[linea].cells[1].innerText,
                        guardar: 1,
                        id: Base64.decode(idestacion),
                        idusuario: idusuario,
                        estacion: Base64.decode(nombreestacion),
                        nombre: tabla.rows[linea].cells[0].innerText,
                        whatsapp: "52" + tabla.rows[linea].cells[2].innerText
                    },
                    success: function (data) {
                        tabla.rows[linea].cells[3].innerHTML = "<i class=\"fa fa-check\"></i>";
                    }
                });
            }
        });
    }
}

async function registroCasaNuevo(idestacion){
    await $.ajax({
        type: 'post',
        url: 'select.php?tipo=18',
        data: {
            email: _('txtemail').value,
            fecha: _('dtfechainicio').value + ' ' + _('dthorainicio').value,
            telefono: _('txttelefono').value,
            nombre: _('txtnombre').value,
            fecha_fin: _('dtfechafin').value + ' ' + _('dthorafin').value,
            idestacion: idestacion
        },
        success: function (data) {
            if (Number(data)>0){
                swal.fire("Guardado", "Hecho, se guardo el registro !", 'success');
                menu_inicio();
            } else {
                swal.fire('Error', "Ups, a ocurrido un error y no se guardo el registro. Por favor intenta mas tarde.", 'error');
            }
        }
    });
}

function selCalendarioDia(fecha){
    if (selCalendarioInicio === "") {
        selCalendarioInicio = Base64.decode(fecha);
        _('dtfechainicio').value = selCalendarioInicio;
        console.log(selCalendarioInicio);
        let celdaFecha = $('#' + selCalendarioInicio);
        celdaFecha.removeClass('activo');
        celdaFecha.addClass('selCalendarioFecha');
    } else{
        let dias = (Date.parse(Base64.decode(fecha) + 'T00:00:00Z')-Date.parse(selCalendarioInicio + 'T00:00:00Z')) / 24 / 60 / 60 / 1000;
        if (dias > 0) {
            selCalendarioFin = Base64.decode(fecha);
            _('dtfechafin').value = selCalendarioFin;
            console.log(dias);
            let celdaFecha = $('#' + selCalendarioFin);
            celdaFecha.removeClass('activo');
            celdaFecha.addClass('selCalendarioFecha');

            for (let i = 1; i < dias; i++) {

                console.log('#' + addDays(selCalendarioInicio, i + 1));
                let celdaRango = $('#' + addDays(selCalendarioInicio, i + 1));
                celdaRango.removeClass('activo');
                celdaRango.addClass('selCalendarioFechaRango');
            }
        }
    }
}

function remCalendarioDia(fecha){
    if (selCalendarioInicio === fecha) {
        let dias = (Date.parse(selCalendarioFin + 'T00:00:00Z')-Date.parse(selCalendarioInicio + 'T00:00:00Z')) / 24 / 60 / 60 / 1000;
        for (let i = 0; i < dias+1; i++) {
            let celdaRango = $('#' + addDays(selCalendarioInicio, i + 1));
            celdaRango.removeClass('selCalendarioFechaRango');
            celdaRango.removeClass('selCalendarioFecha');
            celdaRango.addClass('activo');
        }
        selCalendarioInicio = "";
        selCalendarioFin = "";
        _('dtfechainicio').value = "";
        _('dtfechafin').value = "";
    } else{

        let dias = (Date.parse(selCalendarioFin + 'T00:00:00Z')-Date.parse(selCalendarioInicio + 'T00:00:00Z')) / 24 / 60 / 60 / 1000;
        for (let i = 1; i < dias+1; i++) {
            let celdaRango = $('#' + addDays(selCalendarioInicio, i + 1));
            celdaRango.removeClass('selCalendarioFechaRango');
            celdaRango.removeClass('selCalendarioFecha');
            celdaRango.addClass('activo');
        }
        selCalendarioFin = "";
        _('dtfechafin').value = "";

    }
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    return moment(result).format('YYYY-MM-DD');
}

function verUbicacionEntrada(latlng){
    var url = "https://maps.google.com/?q=" + latlng;
    window.open(url);
}

function sigMesCasa(fecha,idestacion,selec){
    if (selec){
        if (selCalendarioInicio === "") {
            selCalendarioInicio = Base64.decode(fecha);
            _('dtfechainicio').value = selCalendarioInicio;
        }
    }
    ajaxpage('select.php?tipo=17&fecha='+fecha+'&idestacion='+idestacion+'&selec='+selec,'divcalendario_'+idestacion);
}