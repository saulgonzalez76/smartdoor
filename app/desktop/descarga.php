<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

function enviaHeaders($archivo,$nombre){
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false); // required for certain browsers
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $nombre . '";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($archivo));
    readfile($archivo);
}

if (null !== (filter_input(\INPUT_GET, 'tipo'))) { $tipo = filter_input(\INPUT_GET, 'tipo'); }
switch ($tipo) {
    case 1:  // descarga archivos generados, pdf como facturas
        $archivo = base64_decode(filter_input(\INPUT_GET, 'archivo'));
        $nombre = base64_decode(filter_input(\INPUT_GET, 'nombre'));
        file_put_contents('/tmp/tmp_qr.svg',  "../common_files/clases/img_qr.php?d=1&codigo=".$archivo);

        $image = new Imagick();
        $image->readImageBlob(file_get_contents('/tmp/tmp_qr.svg'));
        $image->setImageFormat("png24");
        $image->resizeImage(1024, 768, imagick::FILTER_LANCZOS, 1);
        $image->writeImage('/tmp/tmp_qr.png');

        enviaHeaders('/tmp/tmp_qr.png', str_replace(" ","_",$nombre) . ".png");
        break;
}
exit;