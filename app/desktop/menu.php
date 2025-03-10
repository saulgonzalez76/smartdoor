<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

?>

<li class="nav-item"><a class="nav-link active" id="menu1" href="javascript:menu_inicio();"><i class="fa fa-dashboard fa-home"></i> <p>Inicio</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu2" href="javascript:menu_invitar();"><i class="fa fa-dashboard fa-user-friends"></i> <p>Invitados</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu3" href="javascript:menu_horarios();"><i class="fa fa-dashboard fa-clock"></i> <p>Horarios</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu4" href="javascript:menu_reporte_entradas();"><i class="fa fa-dashboard fa-file-pdf"></i> <p>Reporte Entradas</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu5" href="javascript:menu_reporte_transacciones();"><i class="fa fa-dashboard fa-file-pdf"></i> <p>Reporte Transacciones</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu6" href="javascript:menu_pagos();"><i class="fa fa-dashboard fa-cash-register"></i> <p>Pagos</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu7" href="javascript:menu_relacion_usuario();"><i class="fa fa-dashboard fa-user-friends"></i> <p>Relación de usuarios</p></a></li>
<li class="nav-item"><a class="nav-link" id="menu8" href="javascript:menu_admin();"><i class="fa fa-dashboard fa-home"></i> <p>Admin</p></a></li>

<?php if ($_SESSION['usuario']['distribuidor'] > 0){
    // si es distribuidor, entonces muestro el panel de admin
    ?>
    <br>
    <li class="nav-item"><a class="nav-link text-bold" id="menu9" href="javascript:menu_distribuidor();"><i class="fas fa-user-shield"></i> <p>Panel Distribuidor</p></a></li>
<?php } ?>

