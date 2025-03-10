<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }

include("../common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
if (null !== (filter_input(INPUT_GET,'usuario'))) { $nick = filter_input(INPUT_GET, 'usuario'); }
if (null !== (filter_input(INPUT_POST,'usuario'))) { $nick = filter_input(INPUT_POST, 'usuario'); }
if (null !== (filter_input(INPUT_POST,'password'))) {
    $pass = filter_input(INPUT_POST, 'password');
    $idusuario = $_SESSION['usuario']['idusuario'];
    if ($idusuario == ""){
        header('Location: /');
        exit;
    }
    $clsBaseDatos->cambio_password($idusuario, $pass);
    header('Location: ../login.php');
    exit;
}

if (null !== (filter_input(INPUT_GET,'r'))) {
    $nick = $_SESSION['usuario']['email'];
}

?>
<!doctype html>
<html lang="es">
    <?php    include '../common_files/meta_tags.php'; ?>
    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../common_files/css/sign-in.css" rel="stylesheet">

    <script type="text/javascript">
        function checkform() {
            if (document.cambio_pass.password.value != document.cambio_pass.password2.value) {
                alert("Contraseña no coincide, verifique mayusculas o minusculas.");
                return false;
            } else { 
                if (document.cambio_pass.password.value.length >= 6){
                    return true; 
                } else {
                    alert("Contraseña muy corta, necesita ser al menos 6 caracteres.");
                    return false;
                }
            }
        }
    </script>
    <style>
        #footer{
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 35px;
            border-top: 2px solid #0d6aad;
            vertical-align: center;
            padding-top: 8px;
            background-color: #f5f1f1;
        }
    </style>
</head>

<body class="text-center">
<div id="container" style="background-color: #01052d; width: 100%; height: 100%; overflow: hidden;
                background: url('../common_files/img/logo.png') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                z-index: -1;opacity: 0.2;
                position: absolute;"></div>
    <form class="form-signin" name="cambio_pass" action="cambio_pass.php" method="post" onSubmit="return checkform()">
        <h2 class="text-danger">Debe cambiar la contrase&ntilde;a por defecto.</h2><br><br>
        <input type="hidden" value="<?= $nick; ?>" name="usuario" id="usuario">

                <input type="password" id="password" name="password" class="form-control" required="" placeholder="Contraseña" autofocus="">
                <br>
                <input type="password" id="password2" name="password2" class="form-control" placeholder="Confirmar contraseña" required="">
                <br>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar Contrase&ntilde;a</button>
            </table>
    </form>
<div id="footer">
    <div class="container">
        <div class="pull-right hidden-xs">
            Version <b><?= date("ymd",filectime(__FILE__)); ?></b>
        </div>
        <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>    </div>
</div>

                <script src="../common_files/java/jquery.min.js"></script>
<script src="../common_files/java/popper.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../common_files/java/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

