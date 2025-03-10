<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if ((isset($_FILES['imageFile']['name'])) && (null !== (filter_input(\INPUT_POST, 'idregistro')))) {
    $idregistro = filter_input(\INPUT_POST, 'idregistro');
    $camara = "";
    if (filter_input(\INPUT_POST, 'camara') !== null) { $camara = filter_input(\INPUT_POST, 'camara');}
    if (!is_dir("registro")) {
        $oldmask = umask(0); mkdir("registro", 0777, true); umask($oldmask);
    }
    if (!is_dir("registro/".$idregistro)) {
        $oldmask = umask(0); mkdir("registro/".$idregistro, 0777, true); umask($oldmask);
    }
    $direcctorio = "registro/".$idregistro;
    if($camara !== ""){
        if (!is_dir("registro/".$idregistro."/".$camara)) {
            $oldmask = umask(0); mkdir("registro/".$idregistro."/".$camara, 0777, true); umask($oldmask);
        }
        $direcctorio = "registro/".$idregistro."/".$camara;
    }
    $tempFile = $_FILES['imageFile']['tmp_name'];
    $archivos = array_diff(scandir($direcctorio), array('..', '.'));
    $archivolocal = $direcctorio . "/".(sizeof($archivos)+1).".png";
    imagepng(imagecreatefromstring(file_get_contents($tempFile)), $archivolocal);
}

?>


