<?php
$mac = getopt("i:");
$tag = getopt("t:");
if (($mac == "") && ($tag == "")) {
	echo "llamar usando:\n php nfc_liga.php -i{direccion mac}\n";
	echo " php nfc_liga.php -t{direccion mac de tag}\n";
} else {
	if ((!isset($mac['i'])) && (!isset($tag['t']))) {
		echo "llamar usando:\n php nfc_liga.php -i{direccion mac}\n";
		echo " php nfc_liga.php -t{direccion mac de tag}\n";
	} else {
		if (isset($mac['i'])){
			echo "https://smartdoor.mx?id=" . str_replace("=","",base64_encode($mac['i'])) . "\n";
		}
	
		if (isset($tag['t'])){
			echo "https://smartdoor.mx?t=" . str_replace("=","",base64_encode($tag['t'])) . "\n";
		}
	}
}
exit;
