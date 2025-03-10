<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
include '../common_files/clases/calendario.php';
$clsBaseDatos = new Base_Datos();

$idestacion = "";
if (!is_null(filter_input(INPUT_GET, 'idestacion'))) { $idestacion = base64_decode(filter_input(INPUT_GET, 'idestacion')); }
$calendar = new Calendar(date("Y-m-d"),base64_encode($idestacion));
?>

<div class="card card-default card-solid">
    <div class="card-header with-border">
        <h3 class="card-title">Registro de actividad para: <?= $idestacion; ?></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Nuevo registro</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label>Selecciona el rango de fechas.</label>
                        </div>
                        <div class="row">
                            <?php
                            $calendar->selecc = true;
                            $calendar->eventos = $clsBaseDatos->eventos_casa(base64_encode($idestacion));
                            echo $calendar;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Entrada</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-danger" onclick="remCalendarioDia(_('dtfechainicio').value);";><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Fecha:</label>
                                <input type="date" disabled class="form-control" id="dtfechainicio" />
                            </div>
                            <div class="col-md-6">
                                <label>Hora:</label>
                                <input type="time" class="form-control" value="14:00" id="dthorainicio" "/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Salida</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-danger" onclick="remCalendarioDia(_('dtfechafin').value);";><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Fecha:</label>
                                <input type="date" disabled class="form-control" id="dtfechafin" />
                            </div>
                            <div class="col-md-6">
                                <label>Hora:</label>
                                <input type="time" class="form-control" value="14:00" id="dthorafin" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-default card-solid">
                    <div class="card-header with-border">
                        <h3 class="card-title">Datos</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label>Nombre:</label>
                            <input type="text" class="form-control" id="txtnombre"/>
                        </div>
                        <div class="row">
                            <label>em@il:</label>
                            <input type="email" class="form-control" id="txtemail"/>
                        </div>
                        <div class="row">
                            <label>Teléfono:</label>
                            <input type="tel" class="form-control" id="txttelefono"/>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <div class="card-footer">
        <button id="btnGuardar" class="btn float-right btn-primary" onclick="registroCasaNuevo('<?= base64_encode($idestacion); ?>');">Guardar&nbsp;&nbsp;&nbsp;<i class="fas fa-save"></i> </button>
    </div>
</div>
