/*
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

let timeOut = 7;  //dias

window.onload = async function() {
    const cookey = await getCookie("k");
    const cootok = await getCookie("t");
    if ((cookey === "") || (cookey === null)) {
        const key = await getKey();
        setCookie("k", key, timeOut);
        setCookie("t", "", timeOut);
    } else {
        if (cootok !== "") {
            $.ajax({
                url: 'select.php?tipo=6',
                type: 'post',
                data: {
                    t: cootok,
                    k: cookey,
                    phoneinfo: ''
                },
                dataType: 'json',
                success: function (data) {
                    switch (Number(data.p)){
                        case -1:
                            location.href = "desktop/cambio_pass.php";
                            break;
                        case 1:
                            location.href = "desktop/";
                            break;
                    }
                }
            });
        }
    }
};

async function getKey(){
    const result = await $.ajax({
        url: 'select.php?tipo=4',
        success: function (data) {
            key = data;
        }
    });
    return key;
}

function resetPass(){
    $.ajax({
        type: 'post',
        url: 'select.php?tipo=3',
        data: {
            email: document.getElementById('login').value
        },
        success: function (data) {
            Swal.fire({
                title: 'Hecho !',
                text: "Enviamos un correo elecrónico, por favor sigue los pasos. Verifica Spam si acaso no esta en tu bandeja de entrada.",
                icon: 'success'
            }).then((result) => {
                location.href = "login.php";
            });
        }
    });
}

function setCookie(name, value, daysToLive) {
    var cookie = name + "=" + encodeURIComponent(value);
    var caduca = new Date();
    caduca.setDate(caduca.getDate() + daysToLive);
    if(typeof daysToLive === "number") {
        /* Sets the max-age attribute so that the cookie expires
        after the specified number of days */
        cookie += ";max-age=" + (daysToLive*24*60*60) + ";domain=" + dominio + ";samesite=strict;expires=" + caduca.toGMTString() + ";path=/";
        document.cookie = cookie;
    }
}

function delete_cookie( name, path, domain ) {
    if( getCookie( name ) ) {
        document.cookie = name + "=" +
            ((path) ? ";path="+path:"")+
            ((domain)?";domain="+domain:"") +
            ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
    }
}

function getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");

    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");

        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }

    // Return null if not found
    return null;
}

async function login(){
    const cookey = await getCookie("k");
    $.ajax({
        type: 'post',
        url: 'select.php?tipo=5',
        data: {
            email: document.getElementById('login').value,
            pass: document.getElementById('password').value,
            key: cookey,
            phoneinfo: ''
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            switch (Number(data.p)) {
                case -1:
                    // cambio de pass
                    setCookie("t",data.t,timeOut);
                    location.href = "desktop/cambio_pass.php";
                    break;
                case 0:
                    // login incorrecto
                    Swal.fire("Error","Login y/o usuario incorrectos.","error");
                    break;
                case 1:
                    //login
                    delete_cookie("k","/",dominio);
                    delete_cookie("t","/",dominio);
                    setCookie("k",cookey,timeOut);
                    setCookie("t",data.t,timeOut);
                    location.href = "desktop/";
                    break;
                case 2:
                    Swal.fire("Error","Usuario ya tiene cuenta abierta en otro dispositivo.","error");
                    break;
            }
        }
    });
}