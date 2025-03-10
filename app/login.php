<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */


if (!isset($_SESSION)) {
    session_start();
}
require_once 'common_files/clases/session_config.php';


?>
<!doctype html>
<html lang="es">
    <head>
        <?php    include 'common_files/meta_tags.php'; ?>
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="common_files/css/sign-in.css?<?= time(); ?>" rel="stylesheet">
        <link href="common_files/css/index.css" rel="stylesheet">

        <script src="common_files/java/sweetalert.js?<?= time(); ?>" type="text/javascript"></script>
        <script src="common_files/java/base64.js"></script>
        <?php if (getenv('APPLICATION_ENV') === "development") { ?>
            <script src="common_files/java/login.js?<?= time(); ?>"></script>
        <?php } else { ?>
            <script src="common_files/java/login.min.js?<?= time(); ?>"></script>
        <?php } ?>
        <script>
            var dominio = "<?= $_SERVER['HTTP_HOST']; ?>";
        </script>

    </head>
    <body>
    <div class="container" id="divcookie" style="display: none;">
        <div class="row text-center">
            <h2>Este sistema utiliza cookies, es necesario habilitarlas para poder accesar.</h2>
            <br><br>
            <div class="row text-center"><button class="btn btn-danger btn-lg" onclick="window.location.replace('login.php');">Refrescar</button></div>
            <br><br>
<h1><a href="https://support.apple.com/es-mx/guide/safari/sfri11471/mac">Mac OS</a> </h1><br>
            <h1>Android</h1><br>
            <h4 class="text-danger">Paso 1.</h4>
            <p>De clic en la imagen del candado a un lado de la direccion de la pagina en su navegador.</p>
            <img src="common_files/img/cook_1.png" style="width: 80%"><br>

            <br><h4 class="text-danger">Paso 2.</h4>
            <p>De clic en la sección "Cookies".</p>
            <img src="common_files/img/cook_2.png" style="width: 80%"><br>

            <br><h4 class="text-danger">Paso 3.</h4>
            <p>De clic "Configuración".</p>
            <img src="common_files/img/cook_3.png" style="width: 80%"><br>

            <br><h4 class="text-danger">Paso 4.</h4>
            <p>Seleccione una opción, se recomienda la segunda "Bloquear cookies de terceros en modo incógnito".</p>
            <img src="common_files/img/cook_4.png" style="width: 80%"><br>

            <br><h4 class="text-danger">Paso 5. Opcional</h4>
            <p>Si desea dejar las cookies bloqueadas, puede agregar la direccion web.</p>
            <img src="common_files/img/cook_5.png" style="width: 80%"><br>

            <br><h4 class="text-danger">Paso 6. Opcional</h4>
            <p>Escriba "smartdoor.mx" y de clic en "Agregar".</p>
            <img src="common_files/img/cook_6.png" style="width: 80%"><br>
        </div>
    </div>


    <div class="container" id="divlogin" style="display: inline;">
        <div style="width: 100%; height: 100%; overflow: hidden; background: url('common_files/img/logo.png') center center; background-size: 383px 152px; left: 0; top: 0; z-index: -1; opacity: 0.2; position: absolute;"></div>
        <div class="row text-center">
            <div class="container">
                <div class="row"><br></div>
                <div class="row">
                    <input type="text" id="login" name="login" class="form-control" required="" autofocus="" placeholder="Correo electrónico">
                </div>
                <div class="row"><br></div>
                <div class="row">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required="">
                </div>
                <div class="row"><br></div>
                <div class="row">
                    <button id="btnlogin" class="btn btn-lg btn-primary btn-block" onclick="login();">Accesar</button>
                    <button id="btnreset" style="display: none;" class="btn btn-lg btn-primary btn-block" onclick="resetPass();">Recuperar contraseña</button>
                </div><br><br>
                <div class="row">
                    <label style="font-size: medium; color: #fff200;" onclick="this.style = 'display: none;'; document.getElementById('password').style = 'display: none;'; document.getElementById('btnreset').style = 'display:inline;'; document.getElementById('btnlogin').style = 'display:none;';">Olvidé mi contraseña !</label>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                Version <b><?= date("ymd",filectime(__FILE__)); ?></b>
            </div>
            <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>
        </div>
    </div>
    <?php // echo http://smartdoor.localhost/?id=ODQ6Y2M6YTg6YWY6Yzc6NzA= ; ?>

        <script src="common_files/java/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="common_files/java/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
