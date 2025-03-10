<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 *
 */

if(!isset($_SESSION)) { session_start(); }
require_once '../common_files/clases/session_config.php';


function busca_codigo($codigo,$id) {
    $conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);

    $sql = "select tblLogin.nombre from tblRegistro, tblLogin, tblClientePuerta, tblEstacion where tblClientePuerta.codigo = :codigo and tblRegistro.idestacion = tblClientePuerta.idestacion and (tblRegistro.sync = 0 or tblRegistro.sync = 2) and tblRegistro.hora > CURRENT_TIMESTAMP - INTERVAL 1.1 MINUTE and tblClientePuerta.idregistro = tblRegistro.idusuario and tblLogin.idusuario = tblClientePuerta.idusuario and tblEstacion.idestacion = tblClientePuerta.idestacion and tblEstacion.es_puerta = 0";
    $sth = $conexion->prepare($sql);
    $sth->execute(["codigo"=>$codigo]); $nombre = "";
    if ($row = $sth->fetch()){
        $nombre = $row[0];
    }

    if ($nombre == "") {
        $sql = "select 
       tblClientePuerta.idregistro, 
       tblEstacion.idestacion, 
       tblEstacion.tiempo_apertura, 
       tblEstacion.pin_apertura, 
       (now() < tblClientePuerta.vigencia) as vigente, 
       tblClientePuerta.permanente, 
       (now() > tblClientePuerta.fecha_hora) as fecha, 
       tblClientePuerta.idhorario, 
       tblEstacion.imgurl, 
       tblEstacion.ubicacion 
    from tblEstacion, tblClientePuerta where tblEstacion.idestacion = :idestacion and tblClientePuerta.idestacion = tblEstacion.idestacion and tblClientePuerta.codigo = :codigo";
        $sth = $conexion->prepare($sql);
        $sth->execute(["idestacion" => $id, "codigo" => $codigo]);
        $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch(PDO::FETCH_NAMED)) {
            $hoy = date("w");
            $horario = false;
            if ($row['idhorario'] > 0) {
                $sql_horario = "select * from tblHorarioPuerta where idhorario = " . $row['idhorario'];
                $sth_horario = $conexion->prepare($sql_horario);
                $sth_horario->execute();
                $row_horario = $sth_horario->fetch(PDO::FETCH_NAMED);
                switch ($hoy) {
                    case 0:
                        //domingo
                        $horario = ((strtotime(date(explode(",", $row_horario['domingo'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['domingo'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 1:
                        //lunes
                        $horario = ((strtotime(date(explode(",", $row_horario['lunes'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['lunes'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 2:
                        //martes
                        $horario = ((strtotime(date(explode(",", $row_horario['martes'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['martes'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 3:
                        //miercoles
                        $horario = ((strtotime(date(explode(",", $row_horario['miercoles'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['miercoles'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 4:
                        //jueves
                        $horario = ((strtotime(date(explode(",", $row_horario['jueves'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['jueves'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 5:
                        //viernes
                        $horario = ((strtotime(date(explode(",", $row_horario['viernes'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['viernes'])[1])) > strtotime(date("H:i:s"))));
                        break;
                    case 6:
                        //sabado
                        $horario = ((strtotime(date(explode(",", $row_horario['sabado'])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",", $row_horario['sabado'])[1])) > strtotime(date("H:i:s"))));
                        break;
                }
            }
            if (($row['permanente'] > 0) || ($horario) || (($row['vigente'] > 0) && ($row['fecha'] > 0))) {
                $idcliente = $row['idregistro'];
                $sql = "insert into tblRegistro values (0,:idestacion,$idcliente,now(),0,'" . $row['ubicacion'] . "',now())";
                $sth = $conexion->prepare($sql);
                $sth->execute(["idestacion" => $id]);
                $retorno = retornoJsonStr($row['pin_apertura'], ($row['tiempo_apertura'] * 1000),$row['imgurl'],$conexion->lastInsertId(),0,true);
                $conexion = null;
                $sth = null;
                return $retorno;
            }
        }
    }
    $conexion = null;
    $sth = null;
    return "";
}

function retornoJsonStr($pin,$tiempo,$img,$idregistro,$status,$qr): string{
    $jsonUrl = json_decode($img,true);
    $imgStr = "";
    $imgStrName = "";
    if (isset($jsonUrl['camaras'])) {
        foreach ($jsonUrl['camaras'] as $camara) {
            if ($imgStr != "") {
                $imgStr .= ",";
            }
            if ($imgStrName != "") {
                $imgStrName .= ",";
            }
            $imgStr .= $camara['url'];
            $imgStrName .= $camara['name'];
        }
    }

    if ($qr){
        // pin;tiempo;img;idregistro
        return $pin . ";" . $tiempo . ";" . $imgStr . ";" . $idregistro . ";" . $imgStrName;
    } else {
        // status;pin;tiempo;img;idregistro
        return $status . ";" . $pin . ";" . $tiempo . ";" . $imgStr . ";" . $idregistro . ";" . $imgStrName;
    }
}

function abrir_puerta($id) {
    $conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
    $sql = "select * from tblEstacion where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion"=>$id]);
    if ($row = $sth->fetch(PDO::FETCH_NAMED)) {
        $sql = "update tblRegistro set sync = 0, fecha_update = now() where idestacion = :idestacion";
        $sth_upd = $conexion->prepare($sql); $sth_upd->execute(["idestacion"=>$id]);
        // status;pin;tiempo;img;idregistro
        $retorno = retornoJsonStr($row['pin_apertura'],($row['tiempo_apertura'] * 1000),$row['imgurl'],"0",0,true);
        $conexion = null;
        $sth = null;
        return $retorno;
    }
    $conexion = null;
    $sth = null;
    return "";
}

function setPuerta_Status($id,$status) {
    $conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
    $sql = "select check_status from tblEstacion where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $id]);
    $row = $sth->fetch(PDO::FETCH_NAMED);
    $checkStatus = $row['check_status'];
    $sql = "select * from tblEstacion where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $id]);
    if ($sth->rowCount() == 0) {
        $sql = "update tblPingEstaciones set status = $status where idestacion = :idestacion";
    } else {
        if ($checkStatus > 0) {
            $sql = "update tblEstacion set status = $status, ping = now() where idestacion = :idestacion";
        } else {
            $sql = "update tblEstacion set status = 0, ping = now() where idestacion = :idestacion";
        }
    }
    $sth = $conexion->prepare($sql);
    $sth->execute(["idestacion" => $id]);
    $conexion = null;
    $sth = null;
    return "";
}

function checkResetWifi($id) {
    $conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
    $resp = 0;
    $sql = "select * from tblPingEstaciones where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion"=>$id]);
    if ($sth->rowCount() > 0) {
        $sql = "select reset_wifi from tblPingEstaciones where idestacion = :idestacion";
        $sth = $conexion->prepare($sql);
        $sth->execute(["idestacion" => $id]);
        if ($row = $sth->fetch()) {
            $resp = $row[0];
        }
        if ($resp > 0) {
            $sql = "update tblPingEstaciones set reset_wifi = 0 where idestacion = :idestacion";
            $sth = $conexion->prepare($sql);
            $sth->execute(["idestacion" => $id]);
        }
    } else {
        $sql = "select reset_wifi from tblEstacion where idestacion = :idestacion";
        $sth = $conexion->prepare($sql);
        $sth->execute(["idestacion" => $id]);
        if ($row = $sth->fetch()) {
            $resp = $row[0];
        }
        if ($resp > 0) {
            $sql = "update tblEstacion set reset_wifi = 0 where idestacion = :idestacion";
            $sth = $conexion->prepare($sql);
            $sth->execute(["idestacion" => $id]);
        }
    }
    $conexion = null;
    $sth = null;
    return $resp;
}

function abrir_puerta_check($id,$tiposmart,$wifi,$status_break,$wifi_ssid,$wifi_pass) {
    $conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
    $sql = "select * from tblEstacion where idestacion = :idestacion";
    $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $id]);
    if ($sth->rowCount() == 0) {
        // si la estacion esta en modo prueba entonces entra aqui
        $sql = "select * from tblPingEstaciones where idestacion = :idestacion";
        $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $id]);
        if ($row = $sth->fetch(PDO::FETCH_NAMED)){
            if ($tiposmart == 1) {
                // smartdoor
                if ($row['relay_check'] == 1) {
                    $sql = "update tblPingEstaciones set ping = now(), relay_check = 0, wifi_signal = $wifi, tiposmart = $tiposmart where idestacion = :idestacion";
                    $sth = $conexion->prepare($sql);
                    $sth->execute(["idestacion" => $id]);
                    // status;pin;tiempo;img;idregistro
                    $retorno = "1;0;1000;;0";
                    $conexion = null;
                    $sth = null;
                    return $retorno;
                }
            }
            if ($tiposmart == 2) {
                // smartbrake
                // relay prendido o apagado; pin de relay
                $retorno = $row['relay_check'] . ";0;" . $row['status'];

                $sql = "update tblPingEstaciones set ping = now(), status = $status_break, wifi_signal = $wifi, tiposmart = $tiposmart where idestacion = :idestacion";
                $sth = $conexion->prepare($sql);
                $sth->execute(["idestacion" => $id]);

                $conexion = null;
                $sth = null;
                return $retorno;
            }

        }
        $sql = "update tblPingEstaciones set ping = now(), wifi_signal = $wifi, tiposmart = $tiposmart where idestacion = :idestacion";
        $sth = $conexion->prepare($sql);
        $sth->execute(["idestacion" => $id]);
    } else {
        // si la estacion ya esta registrada, la elimino de la tabla ping
        $sth = $conexion->prepare("delete from tblPingEstaciones where idestacion = :idestacion");
        $sth->execute(["idestacion" => $id]);

        if ($tiposmart == 1) {
            // actualizo el ping de la estacion para saber si esta conectada
            $sth = $conexion->prepare("update tblEstacion set ping = now(), wifi_ssid = '$wifi_ssid', wifi_pass = '$wifi_pass', wifi_signal = $wifi, hardware = 'ESP0" . $tiposmart . "' where idestacion = :idestacion");
            $sth->execute(["idestacion" => $id]);

            $sql = "select check_status from tblEstacion where idestacion = :idestacion";
            $sth = $conexion->prepare($sql); $sth->execute(["idestacion" => $id]);
            if ($sth->rowCount() > 0) {
                $row = $sth->fetch(PDO::FETCH_NAMED);
                $checkStatus = $row['check_status'];
                if ($checkStatus == 0) {
                    $sql = "update tblEstacion set status = 0, ping = now() where idestacion = :idestacion";
                    $sth = $conexion->prepare($sql);
                    $sth->execute(["idestacion" => $id]);
                }
            }

            // busca si ay registros de entrada pendientes para abrir
            $sth = $conexion->prepare("select * from tblRegistro where sync = 2 and idestacion = :idestacion");
            $sth->execute(["idestacion" => $id]);
            if ($sth->rowCount() > 0) {
                $row_registro = $sth->fetch(PDO::FETCH_NAMED);
                $idregistro = $row_registro['idregistro'];
                $sql = "select * from tblEstacion where idestacion = :idestacion";
                $sth = $conexion->prepare($sql); $sth->execute(["idestacion"=>$id]);
                if ($row = $sth->fetch(PDO::FETCH_NAMED)) {
                    if ($row['version'] < 23) {
                        $conexion = null;
                        $sth = null;
                        return "1";
                    } else {
                        $sql = "update tblRegistro set sync = 0, fecha_update = now() where idregistro = $idregistro";
                        $sth_upd = $conexion->prepare($sql);
                        $sth_upd->execute();
                        // status;pin;tiempo;img;idregistro
                        $retorno = retornoJsonStr($row['pin_apertura'],($row['tiempo_apertura'] * 1000),$row['imgurl'],$idregistro,"1",false);

                        //$sth_log = $conexion->prepare("insert into tblLog values (0,'$id',now(),'$retorno')");
                        //$sth_log->execute();

                        $conexion = null;
                        $sth = null;
                        return $retorno;
                    }
                }
                $conexion = null;
                $sth = null;
                return "";
            }
        }
        if ($tiposmart == 2) {
            // actualizo el ping de la estacion para saber si esta conectada
            $sth = $conexion->prepare("update tblEstacion set ping = now(), status = $status_break, wifi_ssid = '$wifi_ssid', wifi_pass = '$wifi_pass', hardware = 'ESP0" . $tiposmart . "', wifi_signal = $wifi where idestacion = :idestacion");
            $sth->execute(["idestacion" => $id]);
        }

        // busca si ay registros de entrada pendientes para abrir
        $sth = $conexion->prepare("select * from tblRegistroCasa where now() between fecha_inicio and fecha_fin and idestacion = :idestacion");
        $sth->execute(["idestacion" => $id]);
        if ($sth->rowCount() > 0) {
            $sql = "select * from tblEstacion where idestacion = :idestacion";
            $sth = $conexion->prepare($sql); $sth->execute(["idestacion"=>$id]);
            if ($row = $sth->fetch(PDO::FETCH_NAMED)) {
                // status ; pin
                $retorno = "";
                if ($row['status'] == 0) {
                    $retorno = "1;" . $row['pin_apertura'];
                }
                $conexion = null;
                $sth = null;
                return $retorno;
            }
            $conexion = null;
            $sth = null;
            return "";
        } else {
            $conexion = null;
            $sth = null;
            return "0;0";
        }


    }
    $conexion = null;
    $sth = null;
    return "";
}
$abre = 0;
$strcodigo = "";
$tiposmart = 1;
$status_brake = 0;
$wifi_signal = 0;
$wifi_ssid = "";
$wifi_pass = "";
$json = json_decode(file_get_contents('php://input'),true);
if (!isset($json['id'])) { echo ""; exit; }
if (!isset($json['abre'])) { echo ""; exit; }
if (isset($json['tiposmart'])) {
    $tiposmart = $json['tiposmart'];
}
if (isset($json['ssid'])) {
    $wifi_ssid = $json['ssid'];
}
if (isset($json['ssidpass'])) {
    $wifi_pass = $json['ssidpass'];
}
if (isset($json['status_brake'])) {
    $status_brake = $json['status_brake'];
}
if (isset($json['wifi'])) {
    $wifi_signal = $json['wifi'];
}
$idEstacion = strtoupper($json['id']);
switch ($json['abre']) {
    case "0":
        // registro de entrada, al escanear el qr o al dar clic en el
        $codigo= $json['codigo'];
        $arrcodigo = explode(" ",$codigo);
        for ($i=0;$i<sizeof($arrcodigo);$i++){
            if ((($arrcodigo[$i] > 47) && ($arrcodigo[$i] < 58)) || (($arrcodigo[$i] > 64) && ($arrcodigo[$i] < 91)) || (($arrcodigo[$i] > 96) && ($arrcodigo[$i] < 123))) {
                $strcodigo .= strtoupper(chr($arrcodigo[$i]));
            }
        }
        $datos = busca_codigo($strcodigo,$idEstacion);
        break;
    case "1":
        // regresa el status de apertura al esp8266 para que abra
        $datos = abrir_puerta_check($idEstacion,$tiposmart,$wifi_signal,$status_brake,$wifi_ssid,$wifi_pass);
        break;
    case "2":
        // regrsa la info del pin y tiempo al esp8266
        $datos = abrir_puerta($idEstacion);
        break;
    case "3":
        // guarda el status y ping actual
        $datos = setPuerta_Status($idEstacion,$json['status']);
        break;
    case "4":
        // verifica si tiene que resetear el wifi on demand
        $datos = checkResetWifi($idEstacion);
        break;
}
echo $datos;
?>