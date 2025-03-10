<?php
/**
 * Desarrollado por Saul Gonzalez Villafranca 
 */

if(!isset($_SESSION)) { session_start(); }
include('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

if (!isset($_SESSION['usuario']['nombre'])) { header('Location: login.php'); }

include("common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
$errorMessage = "";
if (null !== (filter_input(INPUT_POST,'login'))) { $nick = filter_input(INPUT_POST,'login'); }
if (null !== (filter_input(INPUT_POST,'password'))) { $pass = filter_input(INPUT_POST,'password'); }
if (isset($nick) && isset($pass)) {
    $privilegio = $clsBaseDatos->login($nick,$pass);
    switch ($privilegio){
        case -1:
            header('Location: desktop/cambio_pass.php?usuario=' . $nick);
            exit;
            break;
        case 0:
            //login incorrecto
            $errorMessage = 'Usuario y/o contrase&ntilde;a incorrectos.';
            break;
        case 1:
            header('Location: desktop/');
            exit;
            break;
    }
}
// }
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php    include 'common_files/meta_tags.php'; ?>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="common_files/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="common_files/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">


  <!-- Google Font -->
</head>
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
<body class="text-center">
<div style="position: relative; width: 100%; height: 100%; padding-top: 300px;">
<div id="container" style="background-color: #01052d; width: 100%; height: 100%; overflow: hidden;
                background: url('common_files/img/logo.png') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;
                z-index: -1;opacity: 0.2;
                position: absolute;"></div>
  <div class="lockscreen-name"><?= $_SESSION['usuario']['nombre']; ?></div>
  <div class="lockscreen-item">
    <div class="lockscreen-image">
      <img src="common_files/img/usuarios/<?= $_SESSION['usuario']['idusuario']; ?>.png" onError="this.onerror=null;this.src='common_files/img/0.png';">
    </div>
    <form class="lockscreen-credentials" action="lock.php" method="post">
      <div class="input-group">
          <input type="hidden" name="login" value="<?= $_SESSION['usuario']['nickname']; ?>">
        <input type="password" class="form-control" name="password" placeholder="password">

        <div class="input-group-btn">
          <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
        </div>
      </div>
    </form>
  </div>
  <div class="help-block text-center">
    Ingresa tu contrase&ntilde;a para restablecer tu sesion
  </div>
  <div class="text-center">
    <a href="login.php">Inicia sesion con un usuario diferente</a>
  </div>
</div>
<div id="footer">
    <div class="container">
        <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>
        <label id="lblfecha" class="float-right text-danger text-uppercase text-xs"></label><input type="hidden" id="txtHora" name="txtHora" value="">
    </div>
</div>

<script src="common_files/java/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
