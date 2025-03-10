<?php
$siteUrl = "http://" . $_SERVER['HTTP_HOST'];
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $siteUrl = "https://" . $_SERVER['HTTP_HOST'];

?>
<!doctype html>
<html lang="es">
<head>
    <?php    include $siteUrl. '/common_files/meta_tags.php'; ?>
    <!-- Bootstrap core CSS -->
    <link href="<?= $siteUrl; ?>/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?= $siteUrl; ?>/common_files/css/sign-in.css" rel="stylesheet">
    <style>
        .copyr {
            position:fixed;
            bottom: 10px;
            right: 10px;
        }

    </style>

</head>
<body class="text-center" style="background-color:  #01052d;">
<div class="copyr">
    <!-- stuff -->
    <label>Derechos Reservados &#9400; SparanSoft.mx</label>
    <h7><a href="<?= $siteUrl; ?>/politica_priv.html" target="_blank">Pol&iacute;tica de privacidad</a></h7>
</div>

<div style="height: 500px;"><img style="max-width: 100%; max-height: 100%; display: block;" src="<?= $siteUrl; ?>/common_files/img/logo.png"></div><br><br>
<label style="color: white;">Tenemos un error en el sistema ...</label>

<script src="<?= $siteUrl; ?>/common_files/java/jquery.min.js"></script>
<script src="<?= $siteUrl; ?>/common_files/java/popper.js"></script>
<script src="<?= $siteUrl; ?>/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= $siteUrl; ?>/common_files/java/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
