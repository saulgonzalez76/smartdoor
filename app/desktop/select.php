<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */
if(!isset($_SESSION)) { session_start(); }
include '../common_files/clases/calendario.php';
$arrMesLetra = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');


include('email.php');
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$tipo=0;
if (null !== (filter_input(INPUT_GET,'tipo'))) { $tipo = filter_input(INPUT_GET,'tipo'); }
switch ($tipo) {
    case 1:
        $codigo = filter_input(INPUT_GET, 'codigo');
        $pos = filter_input(INPUT_GET, 'pos');
        $nombre = $clsBaseDatos->validaRegistro($codigo);
        if ($nombre == "") {
            // ABRE PUERTA AL DARLE CLIC A LA IMAGEN
            echo $clsBaseDatos->registro($codigo,$pos);
        } else { echo $nombre; }
        break;
    case 2:
        $codigo = filter_input(INPUT_GET, 'codigo');
        echo $clsBaseDatos->getPuertaStatus($codigo);
        break;
    case 3:
        // crea un identificador unico para el dispositivo, debo cambiarlo cada 7 dias
        echo $clsBaseDatos->keygen(32);
        break;
    case 4:
        $id = filter_input(INPUT_GET, 'id');
        echo $clsBaseDatos->seguridad_token($id);
        break;
    case 5:
        // datos de grafica de uso del mes
        $nombres = [];
        $cantidad = [];
        $cantidad2 = [];
        $estacion = base64_decode(filter_input(INPUT_POST, 'id'));
        $sth = $clsBaseDatos->grafica_uso($estacion,date("Y-m"));
        while ($row = $sth->fetch()){
            array_push($nombres,$clsBaseDatos->usuarios_nombre($row[0]));
            array_push($cantidad,$row[1]);
            array_push($cantidad2,$clsBaseDatos->grafica_uso_usuario($estacion,date("Y-m", strtotime("first day of previous month")),$row[3]));
        }
        echo json_encode(array("categorias"=>$nombres,"valores"=>$cantidad,"valores2"=>$cantidad2,"meses"=>array($arrMesLetra[date("n", strtotime("first day of previous month"))-1],$arrMesLetra[date("n")-1])));
        break;
    case 6:
        $email = filter_input(INPUT_POST, 'email');
        echo $clsBaseDatos->valida_email($email);
        break;
    case 7:
        // regresa todas las estaciones donde el usuario tiene acceso, idestacion y ubicacion en json
        $retorno = [];
        $sth_puertas = $clsBaseDatos->usuarios_listado_puertas_web();
        while ($row_puerta = $sth_puertas->fetch(PDO::FETCH_NAMED)){
            array_push($retorno,array("idestacion"=>$row_puerta['idpuerta'],"ubicacion"=>$row_puerta['ubicacion']));
        }
        echo json_encode($retorno);
        break;
    case 8:
        // guarda un evento con sus invitados
        $retorno = [];
        $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
        $nombre = filter_input(INPUT_POST, 'nombre');
        $fecha = filter_input(INPUT_POST, 'fecha');
        $hora = filter_input(INPUT_POST, 'hora');
        $json_invitados = json_decode(filter_input(INPUT_POST, 'json_invitados'),true);
        $idevento = $clsBaseDatos->evento_nuevo($nombre,$fecha,$hora,$idestacion);
        for($i=0;$i<sizeof($json_invitados);$i++){
            $codigo = $clsBaseDatos->getTokenInvitado($json_invitados[$i]['nombre'],$fecha,$hora,$idestacion,$json_invitados[$i]['correo'],$json_invitados[$i]['telefono'],$idevento);
            $nombre_usuario = $clsBaseDatos->usuarios_nombre($_SESSION['usuario']['idusuario']);
            if ($json_invitados[$i]['correo'] !== ""){
                // envia correo al invitado con qr
                enviaCorreo(correoInvita($nombre,$nombre_usuario,$fecha,$hora,str_replace("=","",base64_encode($codigo))),$json_invitados[$i]['correo'],$json_invitados[$i]['nombre'],"SmartDoor - ".$nombre_usuario." te a enviado una invitacion.",false,"no-reply");
            }
            array_push($retorno,["nombre"=>$json_invitados[$i]['nombre'],
                "correo"=>$json_invitados[$i]['correo'],
                "telefono"=>$json_invitados[$i]['telefono'],
                "codigo"=>$codigo]);
        }
        echo json_encode($retorno);
        break;
    case 9:
        // regresa el historico de invitaciones de una estacion por usuario
        $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
        $idusuario = filter_input(INPUT_POST, 'idusuario');
        $retorno = [];
        $evento = [];
        $invitados = [];
        $idevento = 0;
        $sth = $clsBaseDatos->invitados_historico($idestacion,$idusuario);
        while ($row = $sth->fetch(PDO::FETCH_NAMED)){
            if ($idevento !== $row['idregistro']){
                $idevento = $row['idregistro'];
                if (sizeof($evento) > 0) {
                    $evento["invitados"] = $invitados;
                    array_push($retorno,$evento);
                    $evento = [];
                    $invitados = [];
                }
            }
            if (sizeof($evento) == 0) {
                $evento = ["idregistro"=>$row['idregistro'],"evento"=>$row['evento'],"fecha"=>$row['fecha'],"hora"=>$row['hora'],"invitados"=>""];
            }
            array_push($invitados,["nombre"=>$row['nombre'],"email"=>$row['email'],"telefono"=>$row['telefono']]);
        }
        if (sizeof($invitados) > 0) {
            $evento["invitados"] = $invitados;
            array_push($retorno,$evento);
        }
        echo json_encode($retorno);
        break;
    case 10:
        $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
        $lunes = filter_input(INPUT_POST, 'lunes');
        $martes = filter_input(INPUT_POST, 'martes');
        $miercoles = filter_input(INPUT_POST, 'miercoles');
        $jueves = filter_input(INPUT_POST, 'jueves');
        $viernes = filter_input(INPUT_POST, 'viernes');
        $sabado = filter_input(INPUT_POST, 'sabado');
        $domingo = filter_input(INPUT_POST, 'domingo');
        $clsBaseDatos->horarios_nuevo($lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo,$idestacion);
        break;
    case 11:
        $bloqueado = filter_input(INPUT_POST, 'bloqueado');
        $codigo = filter_input(INPUT_POST, 'codigo');
        echo $clsBaseDatos->usuario_bloquear($codigo,$bloqueado);
        break;
    case 12:
        $admin = filter_input(INPUT_POST, 'admin');
        $idestacion = filter_input(INPUT_POST, 'idestacion');
        $usuario = filter_input(INPUT_POST, 'usuario');
        if ($admin == 1) {
            echo $clsBaseDatos->usuarios_setAdmin($idestacion,$usuario);
        } else {
            echo $clsBaseDatos->usuarios_delAdmin($idestacion,$usuario);
        }
        break;
    case 13:
        $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
        $idusuario = filter_input(INPUT_POST, 'idusuario');
        $monto = filter_input(INPUT_POST, 'monto');
        $status = filter_input(INPUT_POST, 'status');
        $paypal_json = filter_input(INPUT_POST, 'paypal_json');
        $sku = filter_input(INPUT_POST, 'sku');
        $cantidad = filter_input(INPUT_POST, 'cantidad');

        $index_plan = -1;
        for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
            if ($_SESSION['usuario']['plan_pago'][$i]["idestacion"] == $idestacion){ $index_plan = $i; break; }
        }
        switch (explode("_",$sku)[2]){
            case "idplan":
                // si el sku es de plan de servicio entonces...
                // actualizo la session para modificar el nuevo vencimiento y habilitar el acceso
                $_SESSION['usuario']['plan_pago'][$index_plan]['plan_vigente'] = 1;
                $_SESSION['usuario']['plan_pago'][$index_plan]['pagos_ultimo'] = date("Y-m-d");
                $periodo = $_SESSION['usuario']['plan_pago'][$index_plan]['plan_periodo'];
                $_SESSION['usuario']['plan_pago'][$index_plan]['plan_vencimiento'] = date('Y-m-d', strtotime("+$periodo months", strtotime( date("Y-m-d",strtotime($_SESSION['usuario']['plan_pago'][$index_plan]['pagos_ultimo'])))));
                echo $clsBaseDatos->pagos_nuevo($idestacion,$idusuario,$monto,$status,$paypal_json);
                break;
            case "visitas":
                // si el sku es de pago de visitas ...
                // actualizo la session para modificar la cantidad de visitas pagadas
                $_SESSION['usuario']['plan_pago'][$i]['plan_visitas'] += $cantidad;
                echo $clsBaseDatos->pagos_visitas($idestacion,$idusuario,$cantidad,$status,$paypal_json,$monto);
                break;
            case "empleados":
                // si el sku es de pago de empleados ...
                break;
        }
        break;
    case 14:
        // regresa los planes de servicio de un tipo de instalacion
        $tiposervicio = filter_input(INPUT_POST, 'tiposervicio');
        echo $clsBaseDatos->planes_servicio($tiposervicio);
        break;
    case 15:
        // espera hasta que se abre la puerta o pasan 30 seg para cancelar
        $idregistro = filter_input(INPUT_GET, 'idregistro');
        $hora = time() + (30); // 30 seg
        $abierto = false;
        while ((!$abierto) && ($hora > time())) {
            $abierto = $clsBaseDatos->puerta_abierta($idregistro);
        }
        if (!$abierto){
            $clsBaseDatos->puerta_cancelar_apertura($idregistro);
        }
        echo ($abierto)?1:0;
        break;
    case 16:
        // elimina un usuario de una puerta de acceso
        $idusuario = filter_input(INPUT_POST, 'idusuario');
        $idestacion = base64_decode(filter_input(INPUT_POST, 'idestacion'));
        $codigo = base64_decode(filter_input(INPUT_POST, 'codigo'));
        echo $clsBaseDatos->usuarios_del($codigo,$idestacion,$idusuario);
        break;
    case 17:
        // cambia de mes en el calendario de casa
        $fecha = base64_decode(filter_input(INPUT_GET, 'fecha'));
        $idestacion = filter_input(INPUT_GET, 'idestacion');
        $selec = filter_input(INPUT_GET, 'selec');
        $calendar = new Calendar(date("Y-m-d",strtotime($fecha)),$idestacion);
        $calendar->selecc = $selec;
        // array de eventos
        echo $calendar;
        break;
    case 18:
        // guarda registro de casa smartbreak
        $fecha = filter_input(INPUT_POST, 'fecha');
        $idestacion = filter_input(INPUT_POST, 'idestacion');
        $nombre = filter_input(INPUT_POST, 'nombre');
        $email = filter_input(INPUT_POST, 'email');
        $telefono = filter_input(INPUT_POST, 'telefono');
        $fecha_fin = filter_input(INPUT_POST, 'fecha_fin');
        echo $clsBaseDatos->registro_casa($idestacion,$nombre,$telefono,$email,$fecha,$fecha_fin);
        break;
    case 19:
        // datos de grafica de uso x hora del dia
        $retorno = ["categorias"=>["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23"],"valores"=>[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]];
        $estacion = base64_decode(filter_input(INPUT_GET, 'id'));
        $sth = $clsBaseDatos->grafica_uso_hora($estacion);
        while ($row = $sth->fetch()){
            $retorno['valores'][$row[0]] = $row[1];
        }
        echo json_encode($retorno);
        break;

}

