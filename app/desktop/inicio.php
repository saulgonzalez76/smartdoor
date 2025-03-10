<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth = $clsBaseDatos->usuarios_listado_puertas();

$estacion = "";
$idestacion = "";
$cantidad = 0;


?>
<div class="row">
    <form id="frmfotoavatarusuarioadmin" action="#" method="POST" enctype="multipart/form-data">
        <input type="file" id="archavatarusradmin" name="archavatarusradmin" style="display: none">
    </form>
<?php

while ($row = $sth->fetch(PDO::FETCH_NAMED)){
    if (($idestacion !== $row['idestacion']) && ($clsBaseDatos->estacion_cliente_admin($row['idestacion']))){
        $cantidad ++;
        if ($idestacion !== ""){ ?>
            <div class="row">
                <h3>Lista de empleados:</h3>
            </div>
            <?php
            $sth_empleados = $clsBaseDatos->usuarios_listado_puertas_empleados($idestacion);
            while ($row_empleados = $sth_empleados->fetch(PDO::FETCH_NAMED)){ ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row elevation-2">
                            <div class="col-lg-3">
                                <img src="../common_files/img/0.png" alt="User Image" style="height: 75px;">
                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-8" style="cursor: pointer;" onclick="ajaxpage('usuario_ver_qr.php?idempleado=<?= $row_empleados['idregistro']; ?>&idpuerta=<?= $idestacion; ?>','contenido');">
                                <h3 class="users-list-name" title="<?= $row_empleados['empleado']; ?> empleado de: <?= $row_empleados['patron'] ?>"><?= $row_empleados['empleado']; ?> empleado de: <label><?= $row_empleados['patron'] ?></label></h3>
                            </div>
                        </div><br>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="card-footer text-center">
            <button type="button" class="btn btn-warning float-left" onclick="ajaxpage('empleado_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">Nuevo Empleado</button>
            <button type="button" class="btn btn-primary float-right" onclick="ajaxpage('usuario_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">Nuevo Usuario</button>
        </div>
    </div>
    </div>
        <?php }
        $estacion = $row['nombre_estacion'];
        $idestacion = $row['idestacion'];
    ?>
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header with-border">
                <h3 class="card-title">Usuarios para: <?= $row['nombre_puerta'] . " en " . $estacion; ?></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body no-padding">
                    <?php } ?>
                    <div class="col-lg-12">
                        <?php
                        $imgusuario = "../common_files/img/0.png";
                        if (file_exists("../common_files/img/usuarios/".$row['idusuario'].".png")){
                            $imgusuario = "../common_files/img/usuarios/".$row['idusuario'].".png";
                        }
                        ?>
                        <div class="row elevation-2">
                            <div class="col-lg-3">
                                <img src="<?= $imgusuario . "?" . filemtime($imgusuario); ?>" class="float-left" style="height: 75px;" alt="User Image" id="imgavatar<?= $row['idusuario']; ?>" onclick="cambiaavatar('imgavatar<?= $row['idusuario']; ?>','fotousuarioadmin',<?= $row['idusuario']; ?>,'archavatarusradmin');archavatarusradmin.click();">
                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-8" style="cursor: pointer;" onclick="ajaxpage('usuario_ver_qr.php?idcliente=<?= $row['idusuario']; ?>&idpuerta=<?= $row['idestacion']; ?>','contenido');">
                                <h3><?php if($row['bloqueado'] == 1) { ?><h4 class="text-danger float-right mid"> <?php } else { if ($clsBaseDatos->usuarios_esAdmin($idestacion,$row['idusuario'])){ ?><h4 class="text-primary float-right"> <?php } else { ?><h4 class="float-right"> <?php }} ?><?= $row['nombre']; ?></h4></h3>
                            </div>
                        </div>
                    </div><br>

<?php }
if ($estacion !== ""){ ?>

                <div class="row">
                    <h3>Lista de empleados:</h3>
                </div>
                        <?php
                        $sth_empleados = $clsBaseDatos->usuarios_listado_puertas_empleados($idestacion);
                        while ($row_empleados = $sth_empleados->fetch(PDO::FETCH_NAMED)){ ?>
                            <div class="row">
                                <div class="col-lg-12">
                                        <div class="row elevation-2">
                                            <div class="col-lg-3">
                                                <img src="../common_files/img/0.png" alt="User Image" style="height: 75px;">
                                            </div>
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-8" style="cursor: pointer;" onclick="ajaxpage('usuario_ver_qr.php?idempleado=<?= $row_empleados['idregistro']; ?>&idpuerta=<?= $idestacion; ?>','contenido');">
                                                <h3 class="users-list-name" title="<?= $row_empleados['empleado']; ?> empleado de: <?= $row_empleados['patron'] ?>"><?= $row_empleados['empleado']; ?> empleado de: <label><?= $row_empleados['patron'] ?></label></h3>
                                            </div>
                                        </div><br>
                                </div>
                            </div>
                        <?php } ?>
            </div>
            <div class="card-footer text-center">
                <button type="button" class="btn btn-warning float-left" onclick="ajaxpage('empleado_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">Nuevo Empleado</button>
                <button type="button" class="btn btn-primary float-right" onclick="ajaxpage('usuario_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">Nuevo Usuario</button>
            </div>
        </div>
    </div>
<?php }
if ($cantidad == 0){ ?>
    <h1 class="text-danger">No administras ningúna estación !</h1>
<?php }
?>
</div>
