
<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

function creaDir($ruta) {
    $oldmask = umask(0); mkdir($ruta, 0777, true); umask($oldmask);
}

if (null !== (filter_input(\INPUT_POST, 'fotousuario'))) {
    $idusuario = filter_input(\INPUT_POST, 'idusuario');
    if (!empty($_FILES)) {
        $tempFile = $_FILES['archavatarusr']['tmp_name'];
        $archivolocal = "../common_files/img/usuarios/" . $idusuario . ".png";
        imagepng(imagecreatefromstring(file_get_contents($tempFile)), $archivolocal,9);
    }
}

if (null !== (filter_input(\INPUT_POST, 'fotousuarioadmin'))) {
    $idusuario = filter_input(\INPUT_POST, 'idusuario');
    if (!empty($_FILES)) {
        $tempFile = $_FILES['archavatarusradmin']['tmp_name'];
        $archivolocal = "../common_files/img/usuarios/" . $idusuario . ".png";
        imagepng(imagecreatefromstring(file_get_contents($tempFile)), $archivolocal,9);
    }
}

?>


