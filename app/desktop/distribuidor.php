<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

if(!isset($_SESSION)) { session_start(); }
if ($_SESSION['usuario']['distribuidor'] == 0){
    include('usuario_desktop.php');
    exit;
}

include("../common_files/clases/seguridad.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
?>
<div class="row">
    <div class="col-sm-12">
        <div class="card card-default card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Estaciones disponibles</h3>
            </div>
            <div class="card-body">
                <table id="lstentradas" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>QR</th>
                        <th>Liga</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sth = $clsBaseDatos->distribuidor_estaciones_disponibles();
                    while ($row = $sth->fetch(PDO::FETCH_NAMED)) { $url = "https://smartdoor.mx?id=" . str_replace("=","",base64_encode($row['idestacion'])) ?>
                        <tr>
                            <td><img src="../common_files/clases/img_qr.php?codigo=<?= $url; ?>" width="150"></td>
                            <td><?= $url; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="row">
<div class="col-sm-12">
    <div class="card card-default card-solid">
        <div class="card-header with-border">
            <h3 class="card-title">Estaciones instaladas</h3>
        </div>
        <div class="card-body">
            <table id="lstentradas" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nombre Estación</th>
                    <th>Nombre Puerta</th>
                    <th>Ubicación</th>
                    <th>Fecha Instalado</th>
                    <th>Plan de servicio</th>
                    <th>Precio</th>
                    <th>Periodo</th>
                    <th>Fecha Corte</th>
                    <th>Ultimo Pago</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sth = $clsBaseDatos->distribuidor_estaciones_instaladas();
                while ($row = $sth->fetch(PDO::FETCH_NAMED)) {
                    $sth_plan = $clsBaseDatos->planes_detalle($row['idplanpago']);
                    $row_planes = $sth_plan->fetch(PDO::FETCH_NAMED);


                    $pagos_ultimo = $clsBaseDatos->pagos_ultimo_pago($row['idestacion']);
                    $periodo = $row_planes['periodo_pago'];
                    $plan_vencimiento = date('Y-m-d', strtotime("+1 months", strtotime( date("Y-m-d",strtotime($row['fecha_install'])))));

                    if ($pagos_ultimo != "1900-01-01"){
                        $plan_vencimiento = date('Y-m-d', strtotime("+$periodo months", strtotime( date("Y-m-d",strtotime($pagos_ultimo)))));
                    }
                    $plan_vigente = (new DateTime(date("Y-m-d")) < new DateTime($plan_vencimiento))?1:0;
                    ?>
                    <tr>
                        <td><?= $row['nombre_estacion']; ?></td>
                        <td><?= $row['nombre_puerta']; ?></td>
                        <td><?= $row['ubicacion']; ?></td>
                        <td><?= $row['fecha_install']; ?></td>
                        <td><?= $row_planes['nombre']; ?></td>
                        <td>$ <?= number_format($row_planes['monto'],2); ?></td>
                        <td><?php switch ($row_planes['periodo_pago']) {case 0: echo "<i class=\"fas fa-infinity\"></i>"; break;case 1: echo "Mensual"; break;case 12: echo "Anual"; break; }; ?></td>
                        <td><label <?php if($plan_vigente == 0) { echo "class=\"text-danger\""; } ?>><?= $plan_vencimiento; ?></label></td>
                        <td><?= $pagos_ultimo; ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>