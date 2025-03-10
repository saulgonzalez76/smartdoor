<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

$_SESSION['LAST_ACTIVITY'] = time();

class Base_Datos {
    private $conexion;
    private const JWT_KEY = "smartdoor_private_key_here";
    private const SESSTIMEOUT = 604800; // 7 dias

    public function __construct(){
        if(!isset($this->conexion)) {
            if ((isset($_SESSION['DB_HOST'])) && (isset($_SESSION['DB_DATABASE'])) && (isset($_SESSION['DB_USERNAME'])) && (isset($_SESSION['DB_PASSWORD']))) {
                $this->conexion = new PDO ("mysql:host=" . $_SESSION['DB_HOST'] . ";dbname=" . $_SESSION['DB_DATABASE'], $_SESSION['DB_USERNAME'], $_SESSION['DB_PASSWORD']);
            }
        }
    }

	public function logout() {
		$sql = "update tblLogin set conectado = 0, session = '', strkey = '',strkey_timeout = '1900-01-01 00:00:00' where idusuario = " . $_SESSION['usuario']['idusuario'];
                $sth = $this->conexion->prepare($sql);
                $sth->execute();
		return 0;
	}

    public function getKey() {
        $sql = "select strkey from tblLogin where idusuario = " . $_SESSION['usuario']['idusuario'];
        $sth = $this->conexion->prepare($sql);
        $sth->execute(); $row = $sth->fetch(PDO::FETCH_NAMED);
        return $row['strkey'];
    }

	public function cambio_password($idusuario,$password) {
		$sql = "update tblLogin set pass = sha2(:pass,256), pass_renew = 0 where idusuario = $idusuario";
		$sth = $this->conexion->prepare($sql);
		$sth->execute(["pass"=>$password]);
		return 0;
	}

    public function validaRegistro ($codigo){
        // si es puerta no se espera el 1.1 minutos para la apertura, solo para portones
//        $sql = "select tblLogin.nombre from tblRegistro, tblLogin, tblClientePuerta where tblClientePuerta.codigo = :codigo and tblRegistro.idestacion = tblClientePuerta.idestacion and tblRegistro.hora > CURRENT_TIMESTAMP - INTERVAL 1.1 MINUTE and tblClientePuerta.idregistro = tblRegistro.idusuario and tblLogin.idusuario = tblClientePuerta.idusuario";
        $sql = "select tblLogin.nombre from tblRegistro, tblLogin, tblClientePuerta, tblEstacion where tblClientePuerta.codigo = :codigo and tblRegistro.idestacion = tblClientePuerta.idestacion and (tblRegistro.sync = 0 or tblRegistro.sync = 2) and tblRegistro.hora > CURRENT_TIMESTAMP - INTERVAL 1.1 MINUTE and tblClientePuerta.idregistro = tblRegistro.idusuario and tblLogin.idusuario = tblClientePuerta.idusuario and tblEstacion.idestacion = tblClientePuerta.idestacion and tblEstacion.es_puerta = 0";
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["codigo"=>$codigo]); $nombre = "";
        if ($row = $sth->fetch()){
            $nombre = $row[0];
        }
        return $nombre;
    }

    public function getPuertaStatus($codigo){
        // regresa el status de la puerta, 0 = en espera, 1 = en movimiento, 2 = sin conexion wifi
        $retorno = 0;
        $senal_wifi = 0;
        $dbi = 0;
        $sql = "select tblEstacion.status, ((current_timestamp - INTERVAL 5 MINUTE) > tblEstacion.ping) as actualizado, tblEstacion.wifi_signal, tblEstacion.hardware from tblEstacion, tblClientePuerta where tblClientePuerta.codigo = :codigo and tblEstacion.idestacion = tblClientePuerta.idestacion";
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["codigo"=>$codigo]);
        if ($row = $sth->fetch()){
            $retorno = $row[0];
            if (($retorno == 0) && ($row[1] > 0)) {
                $retorno = 2;
                $senal_wifi = 0;
            } else {
                $senal_wifi = 3;
                $dbi = $row['wifi_signal'];
                if ($dbi < -69) { $senal_wifi = 2; }
                if ($dbi < -79) { $senal_wifi = 1; }
            }
            return json_encode(["estatus"=>$retorno,"wifi"=>$senal_wifi,"dbi"=>$dbi,"hardware"=>$row['hardware']]);
        }
        return -1;
    }

    public function registro($codigo,$pos) {
        $idregistro = 0;
        $sql = "select tblEstacion.idestacion, tblClientePuerta.idregistro from tblClientePuerta, tblEstacion where tblClientePuerta.codigo = :codigo and tblEstacion.idestacion = tblClientePuerta.idestacion";
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["codigo"=>$codigo]);
        if ($row = $sth->fetch()){
            $sql = "insert into tblRegistro values (0,'" . $row[0] . "'," . $row[1] . ",now(),2,'$pos',now())";
            $sth = $this->conexion->prepare($sql);
            $sth->execute();
            $idregistro = $this->conexion->lastInsertId();
        }
        return $idregistro;
    }

    public function registro_totales_todo(): int {
        $sth = $this->conexion->prepare("select count(*) from tblRegistro"); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function registro_totales_particulares(): int {
        $sth = $this->conexion->prepare("select count(*) from tblEstacion, tblPlan where (tblPlan.idtipo = 4 or tblPlan.idtipo = 1) and tblEstacion.idplanpago = tblPlan.idplan"); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function registro_totales_negocio(): int {
        $sth = $this->conexion->prepare("select count(*) from tblEstacion, tblPlan where (tblPlan.idtipo = 3 or tblPlan.idtipo = 5) and tblEstacion.idplanpago = tblPlan.idplan"); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function registro_totales_fraccionamiento(): int {
        $sth = $this->conexion->prepare("select count(*) from tblEstacion, tblPlan where tblPlan.idtipo = 2 and tblEstacion.idplanpago = tblPlan.idplan"); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function getTokenInvitado($nombre,$fecha,$hora,$idestacion,$correo,$telefono,$idevento) {
        $idestacion = strtoupper($idestacion);
        $fecha_inicio = date("Y-m-d H:i:s",strtotime($fecha . " " . $hora));
        $codigo = $this->keygen(29);
        $sql = "insert into tblClientePuerta values (0,0,:idestacion,'$codigo','" . $fecha_inicio . "',date_add('$fecha_inicio', INTERVAL 4 HOUR),0,$idevento,0,:nombre,'',:email,:telefono,1,0,0,'1900-01-01')";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"nombre"=>$nombre,"email"=>$correo,"telefono"=>$telefono]);
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Registro de invitado $nombre para el dia " . $fecha . " a las " . $hora,$idestacion);
        return $codigo;
    }

    public function empleados_registro($nombre,$idestacion,$idhorario) {
        $idestacion = strtoupper($idestacion);
        $codigo = $this->keygen(29);
        $sql = "insert into tblClientePuerta values (0,0,:idestacion,'$codigo',now(),now(),0,0,$idhorario,:nombre,'','','',1,0,0,'1900-01-01')";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"nombre"=>$nombre]);
        $sql = "insert into tblUsuarioEmpleado values (0," . $_SESSION['usuario']['idusuario'] . "," . $this->conexion->lastInsertId() .")";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Registro de empleado $nombre",$idestacion);
        return $codigo;
    }

    public function usuario_bloquear($codigo,$bloqueado) {
        $sql = "update tblClientePuerta set bloqueado = $bloqueado where codigo = :codigo";
        $sth = $this->conexion->prepare($sql); $sth->execute(["codigo"=>$codigo]);

        $sql = "select nombre,idestacion from tblClientePuerta where codigo = :codigo";
        $sth = $this->conexion->prepare($sql); $sth->execute(["codigo"=>$codigo]); $row = $sth->fetch(PDO::FETCH_NAMED);

        if ($bloqueado == 1) {
            $this->transaccion($_SESSION['usuario']['nombre'] . ": Bloqueo de usuario " . $row['nombre'], $row['idestacion']);
        } else {
            $this->transaccion($_SESSION['usuario']['nombre'] . ": Elimina bloqueo de usuario " . $row['nombre'], $row['idestacion']);
        }
        return $bloqueado;
    }

    public function invitados_historico($idestacion,$idusuario) {
        $idestacion = strtoupper($idestacion);
        $sql = "select tblEvento.idregistro, tblEvento.nombre as evento, tblEvento.fecha, tblEvento.hora, tblClientePuerta.nombre, tblClientePuerta.email, tblClientePuerta.telefono from tblClientePuerta, tblEvento where tblClientePuerta.idestacion = :idestacion and tblEvento.idusuario = $idusuario and tblEvento.idregistro = tblClientePuerta.idevento order by  tblEvento.idregistro desc";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function cliente_nuevo($email,$nombre,$whatssapp,$idestacion,$idusuario){
        $idestacion = strtoupper($idestacion);
        // crear un usuario nuevo en tbllogin
        if ($idusuario == 0) {
            $password = $this->keygen(5);
            $idusuario = $this->usuarios_nuevo($nombre, $password, $email, '', $whatssapp);
        }
        if ($idusuario > 0) {
            $codigo = $this->keygen(29);
            $confirmado = ($this->usuarios_valida_confirmado($email) > 0) ? 1 : 0;
            $sql = "insert into tblClientePuerta values (0,$idusuario,'$idestacion','$codigo',now(),now(),1,0,0,:nombre,'',:email,:telefono,$confirmado,0,0,'".date("Y-m-t")."')";
            $sth = $this->conexion->prepare($sql);
            $sth->execute(["email" => $email, "telefono" => $whatssapp, "nombre" => $nombre]);

            $this->transaccion($_SESSION['usuario']['nombre'] . ": Nuevo usuario " . $nombre, $idestacion);
            return $idusuario . ";" . $password . ";" . $codigo;
        } else {
            return "";
        }
    }

    public function busca_codigo($codigo) {
        $retorno = [];
        $sql = "select
            tblEvento.nombre as nombre_evento,
            tblClientePuerta.fecha_hora,
            tblClientePuerta.vigencia,
            concat(tblEstacion.nombre_puerta,' de ',tblEstacion.nombre_estacion) as nombre_estacion,
            tblEstacion.ubicacion,
            tblLogin.nombre,
            now() > tblClientePuerta.fecha_hora as iniciado,
            tblClientePuerta.vigencia < now() as terminado
        from tblClientePuerta, tblEstacion, tblEvento, tblLogin
        where
            tblClientePuerta.codigo = :codigo and
            (tblClientePuerta.fecha_hora < now() or now() < tblClientePuerta.vigencia) and
            tblEstacion.idestacion = tblClientePuerta.idestacion and
            tblEvento.idregistro = tblClientePuerta.idevento and
            tblLogin.idusuario = tblEvento.idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["codigo"=>$codigo]); $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch(PDO::FETCH_NAMED)) {
           $retorno = ["iniciado"=>$row['iniciado'],"terminado"=>$row['terminado'],"evento"=>$row['nombre_evento'],"inicio"=>$row['fecha_hora'],"vigencia"=>$row['vigencia'],"estacion"=>$row['nombre_estacion'],"ubicacion"=>$row['ubicacion'],"nombre"=>$row['nombre']];
        }
        return json_encode($retorno);
    }

    public function valida_email($email) {
        // falta validar el correo del usuario, que confirme el correo con una liga, falta poder agregar esa cuenta al usuario existente
        $sql = "select * from tblLogin where correo = :email";
        $sth = $this->conexion->prepare($sql); $sth->execute(["email"=>$email]);
        $id =0;
        if($row = $sth->fetch()){
            $id = $row[0];
        }
        return $id;
    }

    public function horarios_nuevo($lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo,$idestacion) {
        $idestacion = strtoupper($idestacion);
        $sql = "insert into tblHorarioPuerta values (0,:lunes,:martes,:miercoles,:jueves,:viernes,:sabado,:domingo,:idestacion)";
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["lunes"=>$lunes,"martes"=>$martes,"miercoles"=>$miercoles,"jueves"=>$jueves,"viernes"=>$viernes,"sabado"=>$sabado,"domingo"=>$domingo,"idestacion"=>$idestacion]);
        $retorno = $this->conexion->lastInsertId();
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Nuevo horario.",$idestacion);
        return $retorno;
    }

    public function horarios_listado($idestacion) {
        $idestacion = strtoupper($idestacion);
        $sql = "select * from tblHorarioPuerta where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function horarios_uso($idhorario) {
        $sql = "select concat(tblEstacion.nombre_estacion,' ', tblEstacion.nombre_puerta) as nombre from tblClientePuerta, tblEstacion where tblClientePuerta.idhorario = $idhorario and tblEstacion.idestacion = tblClientePuerta.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $sth->setFetchMode(PDO::FETCH_NUM);
        $retorno = "";
        while ($row = $sth->fetch()){
            if ($retorno !== "") {$retorno .= ", ";}
            $retorno .= $row[0];
        }
        return $retorno;
    }

    public function busca_codigo_vigencia($codigo) {
        $retorno = "";
        $sql = "select tblLogin.nombre, tblClientePuerta.fecha_hora, tblEstacion.nombre_puerta, tblEstacion.nombre_estacion, tblEstacion.ubicacion from tblLogin, tblClientePuerta, tblEstacion where tblClientePuerta.codigo = :codigo and tblClientePuerta.fecha_hora > now() and tblEstacion.idestacion = tblClientePuerta.idestacion and tblLogin.idusuario = tblClientePuerta.idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["codigo"=>$codigo]); $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch()) {
            $retorno = $row[0] . ";" . $row[1] . ";" . $row[2] . ";" . $row[3] . ";" . $row[4];
        }
        return $retorno;
    }

    public function setJWT($idusuario,$nombre,$email,$key){
        $payload = [
            "data" => [
                "idusuario" => $idusuario,
                "nombre" => $nombre,
                "email" => $email
            ],
            "sub" => $key,
            "exp" => time() + self::SESSTIMEOUT // duracion de una semana
        ];
        $token = jwt_encode($payload, self::JWT_KEY, 'HS256');
        return $token;
    }

    private function login_registro($idusuario,$phoneinfo) {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblLoginRegistro values (0,$idusuario,'".date("Y-m-d")."','".date("H:i:s")."',:pinfo)";
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["pinfo"=>$phoneinfo]);
        return 0;
    }

    public function loginJWT($token,$key,$phoneinfo){
        $privilegio = "0;";
        try {
            $decoded_token = jwt_decode($token, self::JWT_KEY, ['algorithm' => 'HS256']);
            $sql = "select idusuario from tblLogin where strkey_timeout > now() and tblLogin.idusuario = " . $decoded_token["data"]["idusuario"];
            $sth = $this->conexion->prepare($sql); $sth->execute();
            if ($sth->rowCount() > 0) {
                $sql = "select idusuario,correo,pass_renew,nombre,fecha_acceso, distribuidor from tblLogin where tblLogin.idusuario = " . $decoded_token["data"]["idusuario"];
                $sth = $this->conexion->prepare($sql); $sth->execute();
                $sth->setFetchMode(PDO::FETCH_NAMED);
                if ($row = $sth->fetch()) {
                    // en un futuro quitare las sessiones, se reemplazan por JWT
                    $_SESSION['usuario']['idusuario'] = $row['idusuario'];           //id del trabajador o usuario del sistema
                    $_SESSION['usuario']['nickname'] = $row['correo'];              //login del sistema
                    $_SESSION['usuario']['email'] = $row['correo'];               //email del usuario
                    $_SESSION['usuario']['nombre'] = $row['nombre'];      //nombre completo del usuario
                    $_SESSION['usuario']['distribuidor'] = $row['distribuidor'];      //id de la tabla de distribuidores, si es un distribuidor
                    $_SESSION['usuario']['fecha_acceso'] = $row['fecha_acceso'];      //fecha de ultimo acceso del usuario
                    $_SESSION['usuario']['sessionid'] = session_id();
                    $_SESSION['usuario']['img'] = "../common_files/img/usuarios/" . $row['idusuario'] . ".png";
                    $_SESSION['config']['timeout'] = self::SESSTIMEOUT;
                    $cambia_pass = $row['pass_renew'];
                    $session = session_id();                    //session del servidor
                    //actualizo para guardar que si esta conectado el usuario
                    $sql = "update tblLogin set tblLogin.conectado = 1, fecha_acceso = now() where tblLogin.idusuario = " . $_SESSION['usuario']['idusuario'];
                    $sth = $this->conexion->prepare($sql);
                    $sth->execute();
                    $privilegio = "1;" . $token;
                    if ($cambia_pass == 1) { $privilegio = "-1;" . $token; }
                    // configuracion de pagos para cada estacion
                    $_SESSION['usuario']['plan_pago'] = $this->usuarios_plan_pago();
                    $this->login_registro($_SESSION['usuario']['idusuario'],$phoneinfo);
                }
            } else {
                $privilegio = "2;$sql";
            }
        } catch (ExpiredSignatureException $e) {
            // Expired token
            $privilegio = "0;";
        }
        return $privilegio;
    }

	public function login($usuario,$password,$key,$phoneinfo) {
        $privilegio = "0;";
		$sql = "select idusuario,correo,pass_renew,nombre,fecha_acceso, distribuidor from tblLogin where tblLogin.correo = :usuario and tblLogin.pass = sha2(:password,256)";
		$sth = $this->conexion->prepare($sql);
		$sth->execute(["usuario"=>$usuario,"password"=>$password]);
		$sth->setFetchMode(PDO::FETCH_NAMED);
		if ($row = $sth->fetch()) {
            // en un futuro quitare las sessiones, se reemplazan por JWT
            $_SESSION['usuario']['idusuario'] = $row['idusuario'];           //id del trabajador o usuario del sistema
            $_SESSION['usuario']['nickname'] = $usuario;              //login del sistema
            $_SESSION['usuario']['email'] = $row['correo'];               //email del usuario
            $_SESSION['usuario']['nombre'] = $row['nombre'];      //nombre completo del usuario
            $_SESSION['usuario']['distribuidor'] = $row['distribuidor'];      //id de la tabla de distribuidores, si es un distribuidor
            $_SESSION['usuario']['fecha_acceso'] = $row['fecha_acceso'];      //fecha de ultimo acceso del usuario
            $_SESSION['usuario']['sessionid'] = session_id();
            $_SESSION['usuario']['img'] = "../common_files/img/usuarios/" . $row['idusuario'] . ".png";
            $cambia_pass = $row['pass_renew'];                     //si es la primera ves que entra tiene que cambiar el password
            $_SESSION['config']['timeout'] = self::SESSTIMEOUT;
            $session = session_id();                    //session del servidor
            if (!is_null($key))
                $strJWT = $this->setJWT($row['idusuario'], $row['nombre'], $row['correo'], $key);

            //actualizo para guardar que si esta conectado el usuario
            $sql = "update tblLogin set strkey_timeout = '" . date("Y-m-d H:i:s", time() + self::SESSTIMEOUT) . "', tblLogin.conectado = 1, fecha_acceso = now() where tblLogin.idusuario = " . $_SESSION['usuario']['idusuario'];
            $sth = $this->conexion->prepare($sql);
            $sth->execute();
            if ($cambia_pass == 1) {
                $privilegio = "-1;" . $strJWT;
            } else {
                $privilegio = "1;" . $strJWT;
            }
            // configuracion de pagos para cada estacion
            $_SESSION['usuario']['plan_pago'] = $this->usuarios_plan_pago();
            $this->login_registro($_SESSION['usuario']['idusuario'],$phoneinfo);
        }
        return $privilegio;
	}

    public function estacion_cliente_admin($idestacion){
        $idestacion = strtoupper($idestacion);
        // para saber si el usuario actual es administrador de la estacion
        $sql = "select idestacion from tblEstacionAdmin where idestacion = :estacion and idusuario = " . $_SESSION['usuario']['idusuario'];
        $sth = $this->conexion->prepare($sql);
        $sth->execute(["estacion"=>$idestacion]);
        return ($sth->rowCount()>0);
    }

    public function estacion_admin(){
        // devuelve la lista de estaciones administradas por el usuario
        $sql = "select tblEstacion.idestacion,tblEstacion.nombre_estacion,tblEstacion.nombre_puerta from tblEstacion, tblEstacionAdmin where tblEstacionAdmin.idusuario = " . $_SESSION['usuario']['idusuario'] . " and tblEstacion.idestacion = tblEstacionAdmin.idestacion";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        return $sth;
    }

    public function login_activity($session = "") {
        $this->conexion->exec("set names utf8");
        if ($_SESSION['usuario']['idusuario'] > 0) {
            if ($session == "") { $session = session_id(); }
            $sql = "update tblLogin set conectado = 1, session = '" . $session . "', fecha_acceso = '" . date('Y-m-d H:i:s') . "' where idusuario = " . $_SESSION['usuario']['idusuario'];
            $sth = $this->conexion->prepare($sql);
            $sth->execute();
        }
        return 0;
    }

	public function usuarios_actualiza_datos($idusuario,$nombre,$email) {
		$sql = "update tblLogin set nombre = :nombre, correo = :email where idusuario = $idusuario";
		$sth = $this->conexion->prepare($sql); $sth->execute(["nombre"=>$nombre,"email"=>$email]);
		return "0";
	}

    public function estacion_puertas($idestacion) {
        $idestacion = strtoupper($idestacion);
        $sql = "select idestacion, nombre_puerta from tblEstacion where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function estacion_usuarios($idestacion) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select tblClientePuerta.idregistro, tblLogin.nombre from tblClientePuerta, tblLogin where tblClientePuerta.idestacion = :idestacion and tblLogin.idusuario = tblClientePuerta.idusuario order by tblLogin.nombre";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function estacion_ubicacion() {
        // regresa todos los puntos gps donde estan instalados smartdoor
        $sql = "select E.idestacion,
                E.ubicacion,
                E.fecha_install,
                E.ping,
                E.hardware,
                E.version,
                E.nombre_estacion,
                E.nombre_puerta,
                E.tiempo_apertura,
                E.pin_apertura,
                E.status,
                E.idplanpago,
                E.reset_wifi,
                E.wifi_signal,
                E.es_puerta,
                E.fraccionamientos_pago_prorroga,
                E.check_status,
                E.wifi_ssid,
                E.wifi_pass,((current_timestamp - INTERVAL 5 MINUTE) > E.ping) as actualizado, (select ifnull(count(*),0) from tblRegistro where idestacion = E.idestacion and sync = 0 ) as aperturas, (select ifnull(count(*),0) from tblRegistro where idestacion = E.idestacion and sync = 3 ) as errores from tblEstacion E";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        $datos = [];
        while ($row = $sth->fetch(PDO::FETCH_NAMED)){
            $datos[] = $row;
        }
        return json_encode($datos);
    }

    public function keygen($length=10) {
        $key = '';
        list($usec, $sec) = explode(' ', microtime());
        mt_srand((float) $sec + ((float) $usec * 100000));
        $inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));
        for($i=0; $i<$length; $i++) {
            $key .= $inputs[mt_rand(0,61)];
        }
        $sql = "select * from tblClientePuerta where codigo = '$key'";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        if ($sth->rowCount() > 0){ return $this->keygen(); }
        return $key;
    }

    public function estacion_asignado($idestacion) {
        $idestacion = strtoupper($idestacion);
        // devuelve el id del admin si la estacion esta asignada a alguien, sirve para setup.php
        $sql = "select ifnull(idusuario,-1) from tblEstacionAdmin where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        $row = $sth->fetch();
        return $row[0];
    }

    public function estacion_valida_nueva($idestacion): int {
        $idestacion = strtoupper($idestacion);
        $sql = "select * from tblPingEstaciones where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth->rowCount();
    }

    public function estacion_nuevo($idestacion,$idusuario,$nombre_estacion,$nombre_puerta,$latlng,$nombre,$fbid,$email,$whatsapp,$confirmado,$planservicio,$es_puerta) {
        // devuelve el id de la estacion recien creada
        $idestacion = strtoupper($idestacion);
        if ($confirmado == 0){ $confirmado = ($this->usuarios_valida_confirmado($email)>0)?1:0; }
        $sql = "select * from tblEstacion where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        if ($sth->rowCount() == 0) {
            $sth_plan_tipo = $this->planes_detalle($planservicio);
            $fracc_dias_prorroga = 0;
            if ($row_fracc_dias = $sth_plan_tipo->fetch(PDO::FETCH_NAMED)){
                if ($row_fracc_dias['idtipo'] == 2){
                    // meto dias por default, cambiar esto en una configuracion
                    $fracc_dias_prorroga = 10;
                }
            }
            $sql = "insert into tblEstacion values (:idestacion,:latlng,'" . date("Y-m-d") . "','1900-01-01 00:00:00','ESP01',0,:nombre_estacion,:nombre_puerta,1,0,0,'{}',$planservicio,0,$es_puerta,$fracc_dias_prorroga,0,0,'','')";
            $sth = $this->conexion->prepare($sql);
            $sth->execute(["idestacion" => $idestacion, "nombre_estacion" => $nombre_estacion, "nombre_puerta" => $nombre_puerta,"latlng"=>$latlng]);
            $this->usuarios_setAdmin($idestacion,$idusuario);
            $codigo = $this->keygen(29);
            $sql = "insert into tblClientePuerta values (0,$idusuario,'$idestacion','$codigo',now(),now(),1,0,0,:nombre,'$fbid',:email,:telefono,$confirmado,0,0,'".date("Y-m-t")."')";
            $sth = $this->conexion->prepare($sql);
            $sth->execute(["nombre"=>$nombre,"telefono"=>$whatsapp,"email"=>$email]);
            return $codigo;
        } else {
            return 0;
        }
    }

    public function evento_nuevo($nombre,$fecha,$hora,$idestacion) {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblEvento values (0,".$_SESSION['usuario']['idusuario'].",:idestacion,:nombre,:fecha,:hora)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"nombre"=>$nombre,"fecha"=>$fecha,"hora"=>$hora]);
        $retorno = $this->conexion->lastInsertId();
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Nuevo evento " . $nombre . " el día " . $fecha . " a las " . $hora,$idestacion);
        return $retorno;
    }

    public function usuarios_setAdmin($idestacion,$idusuario) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblEstacionAdmin values (:idestacion,$idusuario)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Nuevo admin " . $this->usuarios_nombre($idusuario),$idestacion);
        return 1;
    }

    public function usuarios_delAdmin($idestacion,$idusuario) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "delete from tblEstacionAdmin where idestacion = :idestacion and idusuario = $idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Elimina admin " . $this->usuarios_nombre($idusuario),$idestacion);
        return 1;
    }

    public function usuarios_del($codigo,$idestacion,$idusuario) {
        $this->conexion->exec("set names utf8");
        $sql = "delete from tblClientePuerta where codigo = :codigo";
        $sth = $this->conexion->prepare($sql); $sth->execute(["codigo"=>$codigo]);
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Elimina acceso de usuario " . $this->usuarios_nombre($idusuario),$idestacion);
        return 1;
    }

    public function usuarios_cantAdmin($idestacion) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select ifnull(count(*),0) from tblEstacionAdmin where idestacion = :idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]); $row = $sth->fetch();
        return $row[0];
    }

    public function usuarios_nfc_tag_guarda($idusuario,$tag) {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblNfcTag values (:tag,$idusuario)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["tag"=>$tag]);
        return 0;
    }

    public function usuarios_plan_pago(): array {
        $sql = "select tblPlan.*, tblEstacion.fecha_install, tblEstacion.idestacion, tblClientePuerta.idregistro as idclientepuerta from tblClientePuerta, tblEstacion, tblPlan, tblLogin where tblLogin.idusuario = " . $_SESSION['usuario']['idusuario'] . " and tblClientePuerta.idusuario = tblLogin.idusuario and tblEstacion.idestacion = tblClientePuerta.idestacion and tblPlan.idplan = tblEstacion.idplanpago";
        $this->conexion->exec("set names utf8");
        $sth = $this->conexion->prepare($sql); $sth->execute();
        $arrJson = [];
        while ($row = $sth->fetch(PDO::FETCH_NAMED)) {
            $tmpArr = [
                "idestacion" => $row['idestacion'],
                "idclientepuerta" => $row['idclientepuerta'],
                "plan_nombre" => $row['nombre'],
                "plan_descripcion" => $row['descripcion'],
                "plan_usuarios" => $row['usuarios'],
                "plan_visitas" => $row['visitas_habilitado'],
                "plan_empleados" => $row['empleados'],
                "plan_empleados_por_usuario" => $row['empleados_por_usuario'],
                "plan_administradores" => $row['administradores'],
                "plan_costo_empleados" => $row['costo_empleado'],
                "plan_costo_visitas" => $row['costo_visitas'],
                "plan_pago_x_usuario" => $row['pago_x_usuario'],
                "plan_costo_plan" => $row['monto'],
                "plan_periodo" => $row['periodo_pago'],
                "uso_usuarios" => 0,
                "uso_visitas" => 0,
                "uso_empleados" => 0,
                "uso_administradores" => 0,
                "fecha_install" => $row['fecha_install'],
                "idplan" => $row['idplan'],
                "plan_vigente" => 1,
                "plan_vencimiento" => "",
                "pagos_ultimo" => "",
                "tipo_instalacion" => $row['idtipo']
            ];

            if ($row['periodo_pago'] === 0){
                // plan de demo, no tiene vencimiento
                $tmpArr['plan_vencimiento'] = "2100-01-01";
                $tmpArr['pagos_ultimo'] = "1900-01-01";
                $tmpArr['plan_vigente'] = 1;
            } else {
                // todos los demas planes tienen vigencia
                $tmpArr['pagos_ultimo'] = $this->pagos_ultimo_pago($row['idestacion']);
                $periodo = $tmpArr['plan_periodo'];
                $tmpArr['plan_vencimiento'] = date('Y-m-d', strtotime("+1 months", strtotime( date("Y-m-d",strtotime($tmpArr['fecha_install'])))));

                if ($tmpArr['pagos_ultimo'] != "1900-01-01"){
                    $tmpArr['plan_vencimiento'] = date('Y-m-d', strtotime("+$periodo months", strtotime( date("Y-m-d",strtotime($tmpArr['pagos_ultimo'])))));
                }
                $tmpArr['plan_vigente'] = (new DateTime(date("Y-m-d")) < new DateTime($tmpArr['plan_vencimiento']))?1:0;
            }

            $sth_tmp = $this->conexion->prepare("select ifnull(sum(visitas),0) from tblPagosServicios where idestacion = '" . $row['idestacion'] . "' and idusuario = " . $_SESSION['usuario']['idusuario']);
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['plan_visitas'] += $row_tmp[0];

            $sth_tmp = $this->conexion->prepare("select count(*) from tblEstacionAdmin where idestacion = '" . $row['idestacion'] . "'");
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['uso_administradores'] = $row_tmp[0];

            $sth_tmp = $this->conexion->prepare("select count(*) from tblClientePuerta where permanente = 1 and idestacion = '" . $row['idestacion'] . "'");
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['uso_usuarios'] = $row_tmp[0];

            // visitas del usuario
            $sth_tmp = $this->conexion->prepare("select count(*) from tblClientePuerta, tblEvento where tblClientePuerta.fecha_hora like '%" . date("Y-m") . "%' and tblClientePuerta.idestacion = '" . $row['idestacion'] . "' and tblEvento.idregistro = tblClientePuerta.idevento and tblEvento.idusuario = " . $_SESSION['usuario']['idusuario']);
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['uso_visitas'] = $row_tmp[0];

            // visitas de todos los usuarios de la estacion
            $sth_tmp = $this->conexion->prepare("select count(*) from tblClientePuerta, tblEvento where tblClientePuerta.fecha_hora like '%" . date("Y-m") . "%' and tblClientePuerta.idestacion = '" . $row['idestacion'] . "' and tblEvento.idregistro = tblClientePuerta.idevento and tblEvento.idusuario != " . $_SESSION['usuario']['idusuario']);
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['uso_visitas'] += $row_tmp[0];


            $sth_tmp = $this->conexion->prepare("select count(*) from tblClientePuerta where idhorario > 0 and idestacion = '" . $row['idestacion'] . "'");
            $sth_tmp->execute(); $row_tmp = $sth_tmp->fetch();
            $tmpArr['uso_empleados'] = $row_tmp[0];
            array_push($arrJson,$tmpArr);
        }

        return $arrJson;
    }

    public function pagos_ultimo_pago($idestacion): string{
        $retorno = "1900-01-01";
        $this->conexion->exec("set names utf8");
        $sql = "select fecha from tblPagos where idestacion = :idestacion order by idpago desc limit 1";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        if ($row = $sth->fetch()){
            return $row[0];
        }
        return $retorno;
    }

    public function usuarios_esAdmin($idestacion,$idusuario) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select * from tblEstacionAdmin where idestacion = :idestacion and idusuario = $idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth->rowCount();
    }

    public function usuarios_listado_puertas() {
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.nombre_puerta, tblEstacion.nombre_estacion, tblLogin.nombre, tblEstacion.idestacion, tblLogin.idusuario, tblClientePuerta.bloqueado from tblLogin, tblEstacion, tblClientePuerta, tblEstacionAdmin where tblEstacionAdmin.idusuario = ".$_SESSION['usuario']['idusuario']." and tblEstacion.idestacion = tblEstacionAdmin.idestacion and tblClientePuerta.idestacion = tblEstacion.idestacion and tblClientePuerta.permanente = 1 and tblLogin.idusuario = tblClientePuerta.idusuario order by tblEstacion.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function usuarios_listado_puertas_empleados($idestacion) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.nombre_puerta, tblEstacion.nombre_estacion, tblLogin.nombre as patron, tblEstacion.idestacion, tblClientePuerta.nombre as empleado, tblClientePuerta.idregistro
from tblEstacion, tblClientePuerta, tblUsuarioEmpleado, tblLogin
where tblEstacion.idestacion = :idestacion and
      tblClientePuerta.idestacion = tblEstacion.idestacion and
      tblClientePuerta.permanente = 0 and
      tblClientePuerta.idevento = 0 and
      tblClientePuerta.idhorario > 0 and
      tblUsuarioEmpleado.idclientepuerta = tblClientePuerta.idregistro and
      tblLogin.idusuario = tblUsuarioEmpleado.idusuario order by tblEstacion.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function usuarios_listado_puertas_web() {
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.idestacion, tblLogin.idusuario, tblClientePuerta.codigo as idpuerta, tblEstacion.nombre_estacion, tblEstacion.nombre_puerta as puerta, tblEstacion.ubicacion, tblClientePuerta.bloqueado,tblEstacion.fraccionamientos_pago_prorroga,tblClientePuerta.fraccionamientos_pago_corriente,tblEstacion.hardware from tblLogin, tblClientePuerta, tblEstacion where tblLogin.idusuario = ".$_SESSION['usuario']['idusuario']." and tblClientePuerta.idusuario = tblLogin.idusuario and tblEstacion.idestacion = tblClientePuerta.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function estacion_nombre($token,$tipo) {
        $this->conexion->exec("set names utf8");
        switch ($tipo){
            case "info":
                // nombre de la estacion usando el id de la estacion
                $sql = "select * from tblEstacion where idestacion = :idestacion";
                $sth = $this->conexion->prepare($sql);
                $sth->execute(["idestacion" => $token]);
                break;
            case "nfc":
                // nombre de la estacion usando el sticker NFC
                $sql = "select concat(tblEstacion.nombre_puerta,' de ',tblEstacion.nombre_estacion) as nombre, tblClientePuerta.bloqueado, tblLogin.nombre as usuario, tblClientePuerta.codigo from tblClientePuerta, tblEstacion, tblLogin, tblNfcTag where tblNfcTag.mac = :token and tblClientePuerta.idregistro = tblNfcTag.idclientepuerta and tblEstacion.idestacion = tblClientePuerta.idestacion and tblLogin.idusuario = tblClientePuerta.idusuario";
                $sth = $this->conexion->prepare($sql);
                $sth->execute(["token" => $token]);
                break;
        }
        return $sth;
    }

    public function grafica_uso($estacion,$fecha){
        $this->conexion->exec("set names utf8");
        $sql = "select C.idusuario, count(R.idregistro) as cantidad, (select substr(hora,1,7) from tblRegistro where hora like '$fecha%' and idregistro = R.idregistro) as fecha, C.idregistro from tblRegistro R, tblClientePuerta C where R.idestacion = '$estacion' and R.hora like '$fecha%' and C.idregistro = R.idusuario group by R.idusuario, fecha order by cantidad desc limit 10";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function grafica_uso_hora($estacion){
        // regresa el total de uso por hora de la estacion, de los ultimos 15 dias
        $this->conexion->exec("set names utf8");
        $sql = "SELECT   Hour(hora) AS Hours ,COUNT(*) AS cantidad
                FROM     tblRegistro
                RIGHT JOIN ( SELECT  0 AS Hour
                         UNION ALL SELECT  1 UNION ALL SELECT  2 UNION ALL SELECT  3
                         UNION ALL SELECT  4 UNION ALL SELECT  5 UNION ALL SELECT  6
                         UNION ALL SELECT  7 UNION ALL SELECT  8 UNION ALL SELECT  9
                         UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
                         UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
                         UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18
                         UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21
                         UNION ALL SELECT 22 UNION ALL SELECT 23
                  ) AS AllHours ON HOUR(hora) = Hour
                WHERE hora BETWEEN '". date("Y-m-d", strtotime(date("Y-m-d") . " -15 day")) ."' AND NOW() and idestacion = :idestacion and sync = 0
                GROUP BY Hours,Hour order by Hour";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$estacion]);
        return $sth;
    }

    public function grafica_uso_usuario($estacion,$fecha,$idusuario){
        $this->conexion->exec("set names utf8");
        $sql = "select count(R.idregistro) as cantidad, (select substr(hora,1,7) from tblRegistro where hora like '$fecha%' and idregistro = R.idregistro) as fecha from tblRegistro R where R.idestacion = '$estacion' and R.hora like '$fecha%' and R.idusuario = $idusuario group by R.idusuario, fecha";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        if ($row = $sth->fetch()) {
            return $row[0];
        } else {
            return 0;
        }
    }

    public function usuarios_puerta_datos($idusuario,$idestacion) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.nombre_estacion, tblEstacion.nombre_puerta, tblClientePuerta.codigo, tblLogin.nombre, tblClientePuerta.bloqueado from tblLogin, tblEstacion, tblClientePuerta  where tblEstacion.idestacion = :idestacion and tblLogin.idusuario = $idusuario and tblClientePuerta.idusuario = tblLogin.idusuario and tblClientePuerta.idestacion = tblEstacion.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function empleado_puerta_datos($idempleado,$idestacion) {
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.nombre_estacion, tblEstacion.nombre_puerta, tblClientePuerta.codigo, tblClientePuerta.nombre, tblClientePuerta.bloqueado from tblEstacion, tblClientePuerta  where tblEstacion.idestacion = :idestacion and tblClientePuerta.idregistro = $idempleado and tblClientePuerta.idestacion = tblEstacion.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function usuarios_email($email) {
        $this->conexion->exec("set names utf8");
        $sql = "select idusuario from tblLogin where correo = :correo";
        $sth = $this->conexion->prepare($sql); $sth->execute(["correo"=>$email]);
        $idusuario = 0;
        if ($row = $sth->fetch()) {
            $idusuario = $row[0];
        }
        return $idusuario;
    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------
     USUARIOS - CREAR UN USUARIO NUEVO PARA EL SISTEMA     */
    public function usuarios_nuevo($nombre,$password,$email,$fbid,$whatsapp) {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblLogin values (0,:email,:nombre,'','',0,0,1,'1900-01-01 00:00:00','$whatsapp','$fbid','','1900-01-01 00:00:00',0)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["nombre"=>$nombre,"email"=>$email]);
        $idusuario = $this->conexion->lastInsertId();
        $sql = "update tblLogin set pass = sha2(:password,256) where idusuario = $idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["password"=>$password]);
        return $idusuario;
    }

    public function usuarios_confirma_email($email) {
        $this->conexion->exec("set names utf8");
        $sql = "update tblClientePuerta set permanente = 1, confirmado = 1 where email = :email";
        $sth = $this->conexion->prepare($sql); $sth->execute(["email"=>$email]);
        return 1;
    }

    public function usuarios_valida_confirmado($email) {
        $this->conexion->exec("set names utf8");
        $sql = "select * from tblClientePuerta where email = :email and confirmado = 1";
        $sth = $this->conexion->prepare($sql); $sth->execute(["email"=>$email]);
        return $sth->rowCount();
    }

    public function usuarios_nombre($idusuario) {
        $this->conexion->exec("set names utf8");
        if ($idusuario == 0){ return "Invitado"; }
        $sql = "select nombre from tblLogin where idusuario = $idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function usuarios_nombre_clientepuerta($idusuario) {
        $this->conexion->exec("set names utf8");
        $sql = "select nombre from tblClientePuerta where idregistro = $idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $row = $sth->fetch();
        return $row[0];
    }

    public function refreshqr(){
        $this->conexion->exec("set names utf8");
        $sql = "select idregistro from tblClientePuerta where permanente = 1";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        while ($row = $sth->fetch()){
            $codigo = $this->keygen(29);
            $sth_qr = $this->conexion->prepare("update tblClientePuerta set codigo = '$codigo' where idregistro = " . $row[0]); $sth_qr->execute();
        }
        return 1;
    }

    public function reporte_entradas($idestacion, $anio, $mes): PDOStatement{
        $idestacion = strtoupper($idestacion);
        $this->conexion->exec("set names utf8");
        $sql = "select tblRegistro.hora, tblLogin.nombre, tblClientePuerta.nombre as empleado, tblRegistro.ubicacion, tblRegistro.idregistro from tblRegistro, tblClientePuerta, tblLogin
            where tblRegistro.idestacion = :idestacion and tblRegistro.hora like '$anio-$mes%' and tblClientePuerta.idregistro = tblRegistro.idusuario and tblLogin.idusuario = tblClientePuerta.idusuario order by tblRegistro.hora desc";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function planes_servicio($tiposervicio): String{
        $data = [];
        $this->conexion->exec("set names utf8");
        $sql = "select * from tblPlan where idtipo = $tiposervicio";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_NAMED)) {
            array_push($data,["idplan"=>$row['idplan'],"descripcion"=>$row['descripcion'],"nombre"=>$row['nombre'],"periodo"=>$row['periodo_pago'],"monto"=>$row['monto'],"usuarios"=>$row['usuarios'],"administradores"=>$row['administradores'],"empleados"=>$row['empleados'],"pago_x_usuario"=>$row['pago_x_usuario'],"visitas"=>$row['visitas_habilitado']]);
        }
        return json_encode($data);
    }

    public function planes_detalle($idplan): PDOStatement{
        $this->conexion->exec("set names utf8");
        $sql = "select * from tblPlan where idplan = $idplan";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function pagos_nuevo($idestacion,$idusuario,$monto,$status,$paypal_json): int{
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblPagos values (0,:idestacion, :idusuario, '".date("Y-m-d H:i:s")."',:monto,:status,:paypaljson)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idusuario"=>$idusuario,"status"=>$status,"paypaljson"=>$paypal_json,"monto"=>$monto]);
        $retorno = $this->conexion->lastInsertId();
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Pago del servicio SmartDoor " . $this->usuarios_nombre($idusuario) . " por $ " . number_format($monto,2) . " Gracias por su pago !",$idestacion);
        return $retorno;
    }

    public function puerta_abierta($idregistro): bool{
        $this->conexion->exec("set names utf8");
        $sql = "select count(*) from tblRegistro where idregistro = $idregistro and sync = 2";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $row= $sth->fetch();
        return ($row[0]==0);
    }

    public function puerta_cancelar_apertura($idregistro): bool{
        $this->conexion->exec("set names utf8");
        $sql = "update tblRegistro set sync = 3 where idregistro = $idregistro";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return 1;
    }

    public function pagos_visitas($idestacion,$idusuario,$cantidad,$status,$paypal_json,$monto): int{
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblPagosServicios values (0,:idestacion, :idusuario,$cantidad,0, '".date("Y-m-d H:i:s")."',:monto,:status,:paypaljson)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idusuario"=>$idusuario,"status"=>$status,"paypaljson"=>$paypal_json,"monto"=>$monto]);
        $retorno = $this->conexion->lastInsertId();
        $this->transaccion($_SESSION['usuario']['nombre'] . ": Pago de $cantidad visitas para SmartDoor " . $this->usuarios_nombre($idusuario) . " por $ " . number_format($monto,2) . " Gracias por su pago !",$idestacion);
        return $retorno;
    }

    public function reemplazo_estacion($idestacion, $idnuevo){
        $idestacion = strtoupper($idestacion);
        $sql = "update tblClientePuerta set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblEstacion set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblEstacionAdmin set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblEstacionDistribuidor set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblEvento set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblHorarioPuerta set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblPagos set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblPagosServicios set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);

        $sql = "update tblRegistro set idestacion = :idnuevo where idestacion = :idestacion ";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"idnuevo"=>$idnuevo]);
        return 1;
    }

    public function transaccion($desc,$idestacion){
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblTransaccion values (0,:idestacion,:desc,now())";
        $sth = $this->conexion->prepare($sql); $sth->execute(["desc"=>$desc,"idestacion"=>$idestacion]);
        return 1;
    }

    public function transaccion_reporte($idestacion, $anio, $mes): PDOStatement{
        $this->conexion->exec("set names utf8");
        $sql = "select fecha, descripcion from tblTransaccion where idestacion = :idestacion and fecha like '$anio-$mes%'";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function distribuidor_estaciones_disponibles(): PDOStatement{
        $this->conexion->exec("set names utf8");
        $sql = "select idestacion from tblEstacionDistribuidor where iddistribuidor = ".$_SESSION['usuario']['distribuidor']." and idestacion not in (select idestacion from tblEstacion)";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function distribuidor_estaciones_instaladas(): PDOStatement{
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.* from tblEstacionDistribuidor, tblEstacion where tblEstacionDistribuidor.iddistribuidor = ".$_SESSION['usuario']['distribuidor']." and tblEstacionDistribuidor.idestacion = tblEstacion.idestacion";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

    public function pagos_fraccionamientos_nuevo($idclientepuerta,$monto,$fecha,$idadmin,$idestacion): int{
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblPagosFraccionamientos values (0,:idestacion, $idclientepuerta, :fecha,:monto,$idadmin)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion,"monto"=>$monto,"fecha"=>$fecha]);
        $retorno = $this->conexion->lastInsertId();
        $sql = "update tblClientePuerta set fraccionamientos_pago_corriente = '".date("Y-m-t",strtotime($fecha))."' where idregistro = $idclientepuerta";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        $nombreusuario = $this->usuarios_nombre_clientepuerta($idclientepuerta);
        $this->transaccion("Pago de $nombreusuario",$idestacion);
        return $retorno;
    }

    public function pagos_fraccionamientos_eliminar($idregistro): int{
        $this->conexion->exec("set names utf8");
        $sql = "select idclientepuerta,idestacion from tblPagosFraccionamientos where idregistro = $idregistro";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $row=$sth->fetch();
        $nombreusuario = $this->usuarios_nombre_clientepuerta($row[0]);
        $idestacion = $row[1];
        $sql = "delete from tblPagosFraccionamientos where idregistro = $idregistro";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        $this->transaccion("Elimina pago #$idregistro de $nombreusuario",$idestacion);
        return 1;
    }

    public function pagos_fraccionamientos_reporte($idestacion,$idclientepuerta,$anio,$mes): PDOStatement {
        $this->conexion->exec("set names utf8");
        $fechainicio = $anio . "-" . $mes . "-";
        if ($mes == 0){ $fechainicio = $anio . "-"; }
        $sql = "select tblPagosFraccionamientos.fecha_pago, tblPagosFraccionamientos.monto_pago, tblLogin.nombre, tblPagosFraccionamientos.idregistro from tblPagosFraccionamientos, tblLogin where tblPagosFraccionamientos.idestacion = :idestacion and tblPagosFraccionamientos.idclientepuerta = $idclientepuerta and tblPagosFraccionamientos.fecha_pago like '%".$fechainicio."%' and tblLogin.idusuario = tblPagosFraccionamientos.confirmado_por";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function pagos_fraccionamientos_reporte_admin($idestacion,$anio,$mes): PDOStatement {
        $this->conexion->exec("set names utf8");
        $fechainicio = $anio . "-" . $mes . "-";
        if ($mes == 0){ $fechainicio = $anio . "-"; }
        $sql = "select P.fecha_pago, P.monto_pago, L.nombre as usuario, P.idregistro,
                   (select nombre from tblLogin where idusuario = P.confirmado_por) as nombre
            from tblPagosFraccionamientos P, tblLogin L, tblClientePuerta C
            where
                  P.idestacion = :idestacion and P.fecha_pago like '%".$fechainicio."%' and
                  C.idregistro = P.idclientepuerta and
                  L.idusuario = C.idusuario";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        return $sth;
    }

    public function latencia($idestacion): float {
        $this->conexion->exec("set names utf8");
        $sql = "select avg(latencia) as latencia from (select (fecha_update - hora) as latencia from tblRegistro where idestacion = :idestacion and sync = 0 order by idregistro desc limit 10) L";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        if ($row = $sth->fetch()){
            if (($row[0] > -1) && ($row[0] < 99)) {
                return $row[0];
            }
        }
        return 0;
    }

    public function registro_casa($idestacion,$nombre,$telefono,$email,$fecha,$fecha_fin): int {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblRegistroCasa values (0,:idestacion,:fecha_inicio,:fecha_fin,:nombre,:telefono,:email)";
        $sth = $this->conexion->prepare($sql); $sth->execute(["fecha_fin"=>$fecha_fin,"fecha_inicio"=>$fecha,"idestacion"=>base64_decode($idestacion),"nombre"=>$nombre,"telefono"=>$telefono,"email"=>$email]);
        return $this->conexion->lastInsertId();
    }

    public function eventos_casa($idestacion): array {
        $this->conexion->exec("set names utf8");
        $sql = "select *, datediff(fecha_fin,fecha_inicio) as dias from tblRegistroCasa where idestacion = :idestacion and fecha_fin > now()";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>base64_decode($idestacion)]);
        $retorno = [];
        while ($row = $sth->fetch(PDO::FETCH_NAMED)){
            array_push($retorno,[$row['nombre'],$row['fecha_inicio'],$row['dias']+1,$row['idregistro']]);
        }
        return $retorno;
    }

    public function nombre_fraccionamiento($idestacion): string {
        $this->conexion->exec("set names utf8");
        $sql = "select F.nombre from tblFraccionamiento F, tblEstacionFraccionamiento E where E.idestacion = :idestacion and F.idregistro = E.idfraccionamiento";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        $retorno = "Desconocido";
        if ($row = $sth->fetch(PDO::FETCH_NAMED)){
            $retorno = $row['nombre'];
        }
        return $retorno;
    }

    public function id_fraccionamiento($idestacion): int {
        $this->conexion->exec("set names utf8");
        $sql = "select F.idregistro from tblFraccionamiento F, tblEstacionFraccionamiento E where E.idestacion = :idestacion and F.idregistro = E.idfraccionamiento";
        $sth = $this->conexion->prepare($sql); $sth->execute(["idestacion"=>$idestacion]);
        $retorno = 0;
        if ($row = $sth->fetch(PDO::FETCH_NAMED)){
            $retorno = $row['idregistro'];
        }
        return $retorno;
    }

    public function usuarios_relacion_lista($idfraccionamiento): PDOStatement {
        $this->conexion->exec("set names utf8");
        $sql = "select Usuarios.idusuario, Usuarios.nombre, ifnull((select tblLogin.nombre from tblLogin, tblRelacionUsuario where tblRelacionUsuario.idusuario_relacion = Usuarios.idusuario and tblLogin.idusuario = tblRelacionUsuario.idusuario),\"Ninguno\") as relacion from
            (select distinct
                   tblLogin.idusuario,
                   tblLogin.nombre
            from tblClientePuerta, tblLogin, tblEstacionFraccionamiento
            where
                  tblEstacionFraccionamiento.idfraccionamiento = $idfraccionamiento and
                  tblClientePuerta.idestacion = tblEstacionFraccionamiento.idestacion and
                  tblLogin.idusuario = tblClientePuerta.idusuario) Usuarios order by Usuarios.nombre";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        return $sth;
    }

} // FIN DE CLASE
