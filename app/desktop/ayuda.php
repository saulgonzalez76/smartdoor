
<table id="tabla_busqueda" class="table table-bordered table-hover">
<thead><tr><th><font size="4">Ayuda:</font></th></tr></thead><tbody>
            <tr><td>
    <?php 
    if (null !== (filter_input(\INPUT_GET,'esaxc'))) { ?>
        <label>En caso de ser contrato de AxC es necesario contar con toda la informacion de los campos. Si es un contrato fuera del esquema de AxC de ASERCA entonces seleccione "Libre".</label>
    <?php } 
    if (null !== (filter_input(\INPUT_GET,'precio'))) { ?>
        <label>
            Precio del grano a tratar, por tonelada.
        </label>
        <p>En este campo se requiere que tenga 8 digitos, puede agregar ceros a la izq para completar algun precio, ejemplo: $003,200.75  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php } 
    if (null !== (filter_input(\INPUT_GET,'toneladas'))) { ?>
        <label>
            Toneladas que conforman el contrato.
        </label>
        <p>En este campo se requiere que tenga 8 digitos, puede agregar ceros a la izq para completar el dato, ejemplo: 003,000.00  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php }
    if (null !== (filter_input(\INPUT_GET,'servicios'))) { ?>
        <label>
            Precio de los servicios.
        </label>
        <p>En este campo se requiere que tenga 5 digitos, puede agregar ceros a la izq para completar el dato, ejemplo: 050.00  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'numcontrato'))) { ?>
        <label>
            Numero de contrato ante ASERCA.
        </label>
        <p>Si cuenta con el sistema de escritorio, este dato es requerido para elavorar las liquidaciones y desplegar el numero de contrato que tiene ante ASERCA.</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'preciojulio'))) { ?>
        <label>
            Precio en USD que tiene el grano en la publicacion de ASERCA al mes de julio.
        </label>
        <p>Este valor en conjunto con el precio de bases conforman el precio final del contrato. Es importante introducir el valor correcto.</p>
        <p>En este campo se requiere que tenga 6 digitos, puede agregar ceros a la izq para completar el dato, ejemplo: 0,050.00  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'fechacontrato'))) { ?>
        <label>
            Fecha en la que se realizo la compra del contrato de AxC ante ASERCA.
        </label>
        <p>Fecha en la que se compraron las coberutras call o put ante ASERCA, esta fecha esta ligado al precio del contrato.</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'soloput'))) { ?>
        <label>
            Seleccione si se contrato con la opcion de "Solo Put o Call" ante ASERCA.
        </label>
        <p>Esto sirve para que el sistema de liquidacion sepa cuanto tiene que pagar al contrato, por defaul el solo put paga al alza si el precio esta arriba de lo contratado, de lo contrario paga al precio contratado (para esto tendria que hacer un tramite en ASERCA y ceder las pocisiones al comprador).</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'bases'))) { ?>
        <label>
            Precio en USD que tiene las bases para el grano en la publicacion de ASERCA.
        </label>
        <p>Este valor en conjunto con el precio de julio conforman el precio final del contrato. Es importante introducir el valor correcto.</p>
        <p>En este campo se requiere que tenga 4 digitos, puede agregar ceros a la izq para completar el dato, ejemplo: 05.00  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'coberturacall'))) { ?>
        <label>
            Folio de la cobertura (call o put) asignada por ASERCA.
        </label>
        <p>Este folio sirve para monitorear la utilidad de la cobertura. (en la pagina web de ASERCA)</p>
    <?php } 
        if (null !== (filter_input(\INPUT_GET,'costocobertura'))) { ?>
        <label>
            Monto total pagado de cobertura ante ASERCA.
        </label>
        <p>El sistema de escritorio esta diseñado para cobrarle al productor por el total de toneladas contratadas en ASERCA, este dato ayuda a calcular el monto que se debe recuperar por tonelada contratada.</p>
<p>En este campo se requiere que tenga 10 digitos, puede agregar ceros a la izq para completar el dato, ejemplo: 01,000,005.00  <--- anteponiendo un cero (0) al inicio del campo.</p>
    <?php }
        if (null !== (filter_input(\INPUT_GET,'txtchofer'))) { ?>
        <label>
            Nombre del chofer.
        </label>
        <p>Empieza a escribir y aparecera una lista de choferes aqui.</p>
    <?php }
        if (null !== (filter_input(\INPUT_GET,'txttransporte'))) { ?>
        <label>
            Transporte.
        </label>
        <p>Empieza a escribir el nombre del chofer, si tenemos coincidencias aparecera una lista aqui.</p>
    <?php }
        if (null !== (filter_input(\INPUT_GET,'txtorigen'))) { ?>
        <label>
            Origen del producto.
        </label>
        <p>Empieza a escribir el nombre del chofer, si tenemos coincidencias aparecera una lista aqui.</p>
    <?php }
        if (null !== (filter_input(\INPUT_GET,'txtcolor'))) { ?>
        <label>
            Color del transporte.
        </label>
        <p>Empieza a escribir el nombre del chofer, si tenemos coincidencias aparecera una lista aqui.</p>
    <?php }
        if (null !== (filter_input(\INPUT_GET,'txtplacas'))) { ?>
        <label>
            Placas del transporte.
        </label>
        <p>Empieza a escribir el nombre del chofer, si tenemos coincidencias aparecera una lista aqui.</p>
    <?php }
    if (null !== (filter_input(\INPUT_GET,'txtPlacasCajaEmbarque'))) { ?>
        <label>
            Placas de la caja del transporte.
        </label>
        <p>Empieza a escribir el nombre del chofer, si tenemos coincidencias aparecera una lista aqui.</p>
    <?php } ?>


                </td></tr>
</tbody></table>
