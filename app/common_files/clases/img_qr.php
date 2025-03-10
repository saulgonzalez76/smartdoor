<?php
/**
 *
 * @filesource   image.php
 * @created      24.12.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 *
 * ayuda de generador lineal
 * https://briangrinstead.com/gradient/
 */

namespace chillerlan\QRCodeExamples;
use chillerlan\QRCode\{Output\QRImage, QRCode, QROptions};
require_once '../../vendor/autoload.php';
$d = 0;
$codigo = filter_input(INPUT_GET, 'codigo');
if (!is_null(filter_input(INPUT_GET, 'd'))) {$d = filter_input(INPUT_GET, 'd');}
$gzip = true;
$options = new QROptions([
    'version'      => 7,
    'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
    'imageBase64'  => false,
    'eccLevel'     => QRCode::ECC_L,
    'addQuietzone' => true,
    'svgOpacity'   => 1.0,
    'svgDefs'      => '
		<linearGradient id="g1" x1="106%" y1="94%" x2="0%" y2="0%">
			<stop offset="0%" style="stop-color:rgb(0,88,255);stop-opacity:1;" />
	        <stop offset="100%" style="stop-color:rgb(255,0,0);stop-opacity:1;" />
		</linearGradient>
		<style>rect{shape-rendering:crispEdges}</style>',
    'moduleValues' => [
        // finder
        1536 => 'url(#g1)', // dark (true)  #093480   #BD2E02
        6    => '#fff', // light (false)
        // alignment
        2560 => 'url(#g1)',
        10   => '#fff',
        // timing
        3072 => 'url(#g1)',
        12   => '#fff',
        // format
        3584 => 'url(#g1)',
        14   => '#fff',
        // version
        4096 => 'url(#g1)',
        16   => '#fff',
        // data
        1024 => 'url(#g1)',
        4    => '#fff',
        // darkmodule
        512  => 'url(#g1)',
        // separator
        8    => '#fff',
        // quietzone
        18   => '#fff',
    ],
]);
$qrcode = (new QRCode($options))->render($codigo);
if ($d == 0) {
    header('Content-type: image/svg+xml');
    if ($gzip === true) {
        header('Vary: Accept-Encoding');
        header('Content-Encoding: gzip');
        $qrcode = gzencode($qrcode, 9);
    }
}
echo $qrcode;