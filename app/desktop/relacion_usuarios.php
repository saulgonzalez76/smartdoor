<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
include("../common_files/clases/seguridad.php");

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

$idusuario = 0;
if (null !== (filter_input(INPUT_GET, 'idusuario'))) {
    $idusuario = filter_input(INPUT_GET, 'idusuario');
}

$encontrado = false;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++) {
    if ($_SESSION['usuario']['plan_pago'][$i]["tipo_instalacion"] == 2) {
        $encontrado = true;
        break;
    }
}
if (!$encontrado) { ?>
    <h3 class="text-center">Uso exclusivo para fraccionamientos !</h3>
<?php exit; } ?>

<?php
$idfraccionamiento_tmp = 0;
for ($i=0;$i<sizeof($_SESSION['usuario']['plan_pago']);$i++){
    if ($_SESSION['usuario']['plan_pago'][$i]["tipo_instalacion"] == 2){
        $sthestacion = $clsBaseDatos->estacion_nombre($_SESSION['usuario']['plan_pago'][$i]["idestacion"],"info");
        $row_estacion = $sthestacion->fetch(PDO::FETCH_NAMED);
        $nombre_fracc = $clsBaseDatos->nombre_fraccionamiento($_SESSION['usuario']['plan_pago'][$i]["idestacion"]);
        $idfraccionamiento_tmp = $clsBaseDatos->id_fraccionamiento($_SESSION['usuario']['plan_pago'][$i]["idestacion"]);
        if ($idfraccionamiento !== $idfraccionamiento_tmp) {
            $idfraccionamiento = $idfraccionamiento_tmp;
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title"><?= $nombre_fracc; ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if($clsBaseDatos->usuarios_esAdmin($_SESSION['usuario']['plan_pago'][$i]["idestacion"],$_SESSION['usuario']['idusuario']) > 0){ ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="txtmonto">Usuario:</label>
                                            <select  class="form-control select2" id="cbousuario" onchange="ajaxpage('relacion_usuarios.php?idusuario=' + this.value,'contenido');">
                                                        <option value="0">Selecciona</option>
                                                        <?php
                                                        $sth_usuario = $clsBaseDatos->estacion_usuarios($_SESSION['usuario']['plan_pago'][$i]["idestacion"]);
                                                        while ($row_usuario = $sth_usuario->fetch(PDO::FETCH_NAMED)){ ?>
                                                            <option value="<?= $row_usuario['idregistro']; ?>" <?php if (($_SESSION['usuario']['plan_pago'][$i]["idclientepuerta"] == $row_usuario['idregistro']) && ($idusuario == 0)) { echo "selected"; } ?>><?= $row_usuario['nombre']; ?></option>
                                                        <?php } ?>
                                            </select>
                                        </div>
                                    </div><br>
                                </div>
                            </div>

                            <?php
                                $sth_relacion = $clsBaseDatos->usuarios_relacion_lista($idfraccionamiento);
                            ?>
                            <table id="lstrelacion_<?= str_replace("=","",base64_encode($_SESSION['usuario']['plan_pago'][$i]["idestacion"])); ?>" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Relación</th>
                                    <th>Acción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while ($row_relacion = $sth_relacion->fetch(PDO::FETCH_NAMED)){
                                    $accion = "";
                                    if($idusuario !== $row_relacion['idusuario']){
                                        // si no es el mismo usuario del seleccionado, entonces puede relacionar
                                        $accion = "1";

                                    }
                                    ?>
                                    <tr>
                                        <td><?= $row_relacion['nombre']; ?></td>
                                        <td><?= $row_relacion['relacion']; ?></td>
                                        <td></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>



                        <?php } else { ?><label>No administras esta estación !</label><?php } ?>
                    </div>
                </div>
            </div>
        </div>
<?php } } } ?>