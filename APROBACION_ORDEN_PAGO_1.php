<?php 
####################MODIFICACIONES########################### 
#11/05/2017 | ERICA G. | ARREGLO VALIDACIONES, MODIFICACIONES PARA VALIDAR CIERRE
#22/03/2017 | ERICA G. | ARCHIVO CREADO, MODIFICA LA FECHA, SOLO FOMVIDU
###############################################################################
  require_once('Conexion/conexion.php');
  require_once('estructura_apropiacion.php');
  require_once('estructura_saldo_obligacion.php');
  require_once 'head_listar.php'; 

  $numero = "";
  $fecha = "";
  $fechaVen = "";
  $descripcion = "";

  if(!empty($_SESSION['id_comp_pptal_OP']))
  {
    $queryGen = "SELECT detComP.id_unico, rub.nombre, detComP.valor, 
        rubFue.id_unico, fue.nombre, proy.nombre, detComP.tercero, detComP.proyecto, 
        detComP.comprobanteafectado, detcpa.comprobantepptal  
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      left join gf_tercero terc on terc.id_unico = detComP.tercero 
      left join gf_proyecto proy on proy.id_unico = detComP.proyecto 
      left join gf_detalle_comprobante_pptal detcpa ON detComP.comprobanteafectado = detcpa.id_unico 
      where detComP.comprobantepptal =".$_SESSION['id_comp_pptal_OP'];
    $resultado = $mysqli->query($queryGen);

    $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['id_comp_pptal_OP'];

    $comprobante = $mysqli->query($queryCompro);
    if(mysqli_num_rows($comprobante)>0) { 
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $terceroComp = $rowComp[8];

    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
  
    $fecha = $dia."-".$mes."-".$anio;
}
    //Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT id_unico, numero     
      FROM gf_comprobante_pptal 
      WHERE tipocomprobante = 6 
      AND estado = 1 
      AND id_unico != '".$_SESSION['id_comp_pptal_OP']."' 
      ORDER BY numero";
    $numeroSoli = $mysqli->query($queryNumSol);

  }

  $queryTipComPtal = "SELECT id_unico, codigo, nombre       
    FROM gf_tipo_comprobante_pptal 
    WHERE clasepptal = 16
    AND tipooperacion = 1 
    ORDER BY codigo";
  $tipoComPtal = $mysqli->query($queryTipComPtal);

  //Consulta para listado de Número Solicitud. // WHERE tipocomprobante = 6 era clase 14
   //SELECT comp.id_unico0, comp.numero1, comp.fecha2, comp.descripcion3 
  $querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
    FROM gf_comprobante_pptal  comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
    WHERE tipcomp.clasepptal = 15
    AND comp.estado = 3
    OR comp.estado = 4
    ORDER BY comp.numero";

  $SolAprob = $mysqli->query($querySolAprob);
  
   //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
  $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
    FROM gf_tercero ter 
    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico
    GROUP BY ter.id_unico";
  $tercero = $mysqli->query($queryTercero); 

  // Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);

?>

<title>Aprobación Orden Pago</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 


<script type="text/javascript">

 $(document).ready(function()
  {
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Anterior',
        nextText: 'Siguiente',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        changeYear:true
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    
    <?php if(empty($_SESSION['id_comp_pptal_OP'])) { ?>
        $("#fecha").datepicker({changeMonth: true}).val();
    <?php } else { 
        if(!empty($_SESSION['id_comp_pptal_OP'])) {
            ##BUSCAR FECHA COMPROBANTE 
            $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comp_pptal_OP'];
            $fc = $mysqli->query($fc);
            $fc = mysqli_fetch_row($fc);
            $fc = $fc[0];
            ##DIVIDIR FECHA
            $fecha_div = explode("-", $fc);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];
            ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
            $ci="SELECT
            cp.id_unico
            FROM
            gs_cierre_periodo cp
            LEFT JOIN
            gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
            LEFT JOIN
            gf_mes m ON cp.mes = m.id_unico
            WHERE
            pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
            $ci =$mysqli->query($ci);
            if(mysqli_num_rows($ci)>0){ ?>
                var fechaI = '<?php echo date("d/m/Y", strtotime($fecha));?>';
                $("#fecha").datepicker({changeMonth: true,minDate: fechaI}).val(); 
            <?php } else { ?> 
                var fechaI = '<?php echo date("d/m/Y", strtotime($fecha));?>';
                $("#fecha").datepicker({changeMonth: true,  minDate: fechaI}).val(fechaI);
            <?php } 
        } else { ?>
            var fechaI = '<?php echo date("d/m/Y", strtotime($fecha));?>';
            $("#fecha").datepicker({changeMonth: true,  minDate: fechaI}).val();
    <?php }
    }?>  
  });

</script>


<style type="text/css">
  .area
  { 
    height: auto !important;  
  }  

  /*Esto permite que el texto contenido dentro del div
  no se salga de las medidas del mismo.*/
  .acotado
  {
    white-space: normal;
  }

  table.dataTable thead th,table.dataTable thead td
  {
    padding: 1px 18px;
    font-size: 10px;
  }

  table.dataTable tbody td,table.dataTable tbody td
  {
    padding: 1px;
  }
  .dataTables_wrapper .ui-toolbar
  {
    padding: 2px;
    font-size: 10px;
  }

  .control-label
  {
    font-size: 12px;
  }

  .itemListado
  {
    margin-left:5px;
    margin-top:5px;
    width:150px;
    cursor:pointer;
  }

  #listado 
  {
    width:250px;
    height:120px;
    overflow: auto;
    background-color: white;
  }

</style>
 
 <!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

</head>
<body>

  <input type="hidden" id="id_comp_pptal_OP" value="<?php echo $_SESSION['id_comp_pptal_OP'];?>">

<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

   <!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Aprobación Orden Pago</h2>


<div class="col-sm-10"><!--   estaba 10 -->
  <div class="client-form contenedorForma col-sm-12"  > 
    
    <!-- Formulario de comprobante PPTAL -->
    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" onsubmit="return valida();" action="json/registrar_APROBACION_ORDEN_PAGOJson.php">

      <input type="hidden" value="obligacion" name="expedir">

      <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
      </p>

       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera Fila -->

       <div class="col-sm-3" align="left"> <!-- Tercero -->
            <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero">
            <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
            <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width:150px;" required>
              <?php 
              if(!empty($_SESSION['id_comp_pptal_OP'])){
                   if(!empty($_SESSION['nuevo_OP'])){
                       $bt = "SELECT IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                FROM gf_comprobante_pptal cp LEFT JOIN gf_tercero tr ON cp.tercero = tr.id_unico 
                                WHERE cp.id_unico =".$_SESSION['id_comp_pptal_OP'];
                       $bt=$mysqli->query($bt);
                       $bt = mysqli_fetch_row($bt);?>
                       <option value="<?php echo $bt[2]?>"><?php echo ucwords(mb_strtolower($bt[0])).' - '.$bt[1]?></option>
                       
                   <?php }  else {
                        $bt = "SELECT IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                FROM gf_comprobante_pptal cp LEFT JOIN gf_tercero tr ON cp.tercero = tr.id_unico 
                                WHERE cp.id_unico =".$_SESSION['id_comp_pptal_OP'];
                       $bt=$mysqli->query($bt);
                       $bt = mysqli_fetch_row($bt);?>
                       <option value="<?php echo $bt[2]?>"><?php echo ucwords(mb_strtolower($bt[0])).' - '.$bt[1]?></option>
                       <?php $bt1 = "SELECT IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                FROM gf_tercero tr WHERE tr.id_unico !=$bt[2] 
                                ORDER BY NOMBRE ASC ";
                       $bt1=$mysqli->query($bt1);
                       while ($rowT = mysqli_fetch_row($bt1)){ ?>
                          <option value="<?php echo $rowT[2]?>"><?php echo ucwords(mb_strtolower($rowT[0])).' - '.$rowT[1]?></option> 
                       <?php }
                    }
              }else { ?>
                    <option value="">Tercero</option>
                    <?php $bt = "SELECT IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                FROM gf_tercero tr 
                                ORDER BY NOMBRE ASC";
                       $bt=$mysqli->query($bt);
                       while ($rowT = mysqli_fetch_row($bt)){ ?>
                          <option value="<?php echo $rowT[2]?>"><?php echo ucwords(mb_strtolower($rowT[0])).' - '.$rowT[1]?></option> 
                       <?php }?>
                       
              <?PHP }
              ?>
            </select>
          </div> <!-- Fin Tercero -->

           <div class="col-sm-3" align="left"> <!-- Registro Presupuestal -->
            <label for="solicitudAprobada" class="control-label" ><strong style="color:#03C1FB;">*</strong>Registro Presupuestal:</label><br>
            <select name="solicitudAprobada" id="solicitudAprobada" class="form-control input-sm" title="Registro presupuestal" style="width:180px;">
              <?php
                if(!empty($_SESSION['id_comp_pptal_OP'])){
                    if(!empty($_SESSION['nuevo_OP']))
                   {
                     #####BUSCAR REGISTRO 
                       $selec = "SELECT DISTINCT
                            cpa.id_unico,
                            cpa.numero,
                            DATE_FORMAT(cpa.fecha,'%d/%m/%Y'),
                            cpa.descripcion 
                          FROM
                            gf_comprobante_pptal cp
                          LEFT JOIN
                            gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal
                          LEFT JOIN
                            gf_detalle_comprobante_pptal dca ON dca.id_unico = dcp.comprobanteafectado
                          LEFT JOIN
                            gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico
                          WHERE
                            cp.id_unico = ".$_SESSION['id_comp_pptal_OP'];
                       $odp = $mysqli->query($selec);
                       $odp = mysqli_fetch_row($odp);
                       echo '<option value="'.$odp[0].'">'.$odp[1].' '.$odp[2].' '.ucwords(mb_strtolower($odp[3])).'</option>';
                   } else {
                       echo '<option value="'.$id.'">'.$numero.' '.$fecha.' '.ucwords(mb_strtolower($descripcion)).'</option>';
                       $id_tercero = $terceroComp; 
                        $clase = 15;    

                     $queryComp ="SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
                                FROM gf_comprobante_pptal com
                                left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
                                WHERE tipoCom.clasepptal = $clase 
                                and tipoCom.tipooperacion = 1
                                and com.tercero =  $id_tercero and com.id_unico != $id";

                        $comprobanteP = $mysqli->query($queryComp);
                        $saldDisp = 0;
                        $totalSaldDispo = 0;
                        while ($row = mysqli_fetch_row($comprobanteP))
                        {
                         $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
                            FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
                            WHERE comP.id_unico = detComp.comprobantepptal 
                            AND comP.id_unico = ".$row[0];


                        $detCompro = $mysqli->query($queryDetCompro);
                        while($rowDetComp = mysqli_fetch_row($detCompro))
                        {

                                $saldDisp = $rowDetComp[1];

                                $queryDetAfe = "SELECT
                                  dcp.valor,
                                  tc.tipooperacion
                                FROM
                                  gf_detalle_comprobante_pptal dcp
                                LEFT JOIN
                                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                LEFT JOIN
                                  gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
                                WHERE
                                  dcp.comprobanteafectado =".$rowDetComp[0];
                                $detAfec = $mysqli->query($queryDetAfe);

                                while($rowDtAf = mysqli_fetch_row($detAfec))
                                {
                                    if($rowDtAf[1]==3){
                                          $saldDisp = $saldDisp - $rowDtAf[0];
                                    } else {
                                        if(($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)){
                                            $saldDisp = $saldDisp + $rowDtAf[0];
                                        } else {
                                            $saldDisp = $saldDisp - $rowDtAf[0];
                                        }
                                    }
                                }



                        }
                        $saldo = $saldDisp;

                                if($saldo > 0)
                                {
                                        $fecha_div = explode("-", $row[2]);
                                    $anio = $fecha_div[0];
                                    $mes = $fecha_div[1];
                                    $dia = $fecha_div[2];
                                    $fecha = $dia."/".$mes."/".$anio;

                                        echo '<option value="'.$row[0].'">'.$row[1].' '.$fecha.' '.ucwords(mb_strtolower($row[3])).' $'.number_format($saldo, 2, '.', ',').'</option>';
                                }
                        }

                   }
                } else { 
               ?>
             <option value="" >Registro Presupuestal</option>
                <?php } ?>
            </select>  
          </div><!-- Fin Solicitud aprobada -->

        <div class="col-sm-2" align="left"><!--  Fecha -->

          <label class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>

          <input class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" readonly="true" required="required">

        </div>

        <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botones nuevo -->
          <button type="button" id="btnNuevoComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Nuevo" >
            <li class="glyphicon glyphicon-plus"></li>
          </button> <!-- Nuevo -->
        </div>
        
        <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botones guardar -->
          <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
            <li class="glyphicon glyphicon-floppy-disk"></li>
          </button> <!--Guardar-->      
        </div> <!-- Fin Botones nuevo -->

        <div class="col-sm-1" style="margin-top: 22px;" ><!--Imprimir-->
          <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Imprimir">
            <li class="glyphicon glyphicon glyphicon-print"></li>
          </button> 
        </div>

         <?php 
             if(!empty($_SESSION['nuevo_OP']))
             {
           ?>
          <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnImprimir").click(function(){
                window.open('informesPptal/inf_Apr_Ord_Pag.php');
              });
            });
          </script>
          <?php 
           } else { ?>
            <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnImprimir").prop("disabled", true);
              $("#btnEnviar").prop("disabled", true);
            });
          </script> 
          <?php }
           ?>
        <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botón siguiente -->

              <button type="button" id="btnEnviar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Siguiente" ><li class="glyphicon glyphicon-arrow-right"></li></button> 

        </div> 
       </div> <!-- Fin de la primera fila -->


       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 8px;"> <!-- Segunda fila -->
        
           <div class="col-sm-3" style="margin-top: -5px;" > <!-- Buscar Aprobacion -->
                <label for="noDisponibilidad" class="control-label" style="margin-left:-60px"><right>Buscar Aprobación:</right></label>
           </div>
           <div class="col-sm-3" style="margin-top: -5px;" > <!-- Buscar Aprobacion -->
                <select class="select2_single form-control" name="buscarOrd" id="buscarOrd" style="width:250px">
                    <option value="">Aprobación Orden De Pago</option>
                    <?php $reg = "SELECT
                            cp.id_unico,
                            cp.numero,
                            cp.fecha,
                            tcp.codigo,
                            IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                              (tr.razonsocial),
                              CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                            tr.numeroidentificacion
                          FROM
                            gf_comprobante_pptal cp
                          LEFT JOIN
                            gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                          LEFT JOIN
                            gf_tercero tr ON cp.tercero = tr.id_unico 
                          WHERE tcp.clasepptal = 20 ORDER BY cp.numero DESC ";
                    $reg = $mysqli->query($reg); 
                    while ($row1 = mysqli_fetch_row($reg)) { 
                        $date= new DateTime($row1[2]);
                        $f= $date->format('d/m/Y');
                         $sqlValor = 'SELECT SUM(valor) 
                                FROM gf_detalle_comprobante_pptal 
                                WHERE comprobantepptal = '.$row1[0];
                        $valor = $mysqli->query($sqlValor);
                        $rowV = mysqli_fetch_row($valor);
                        $v=' $'.number_format($rowV[0], 2, '.', ','); ?>
                        <option value="<?php echo $row1[0]?>"><?php echo $row1[1].' '. mb_strtoupper($row1[3]).' '.$f.' '.ucwords(mb_strtolower($row1[4])).' '.$row1[5].$v?>
                    <?php }?>
                </select>
            </div>

          <div class="col-sm-5"></div>

            


  


  <script type="text/javascript">
    $(document).ready(function()
    {
      $("#btnEnviar").click(function()
      {
        var idComP = $("#id_comp_pptal_OP").val();

        var form_data = { estruc: 1, id_com: idComP };
        $.ajax({
          type: "POST",
          url: "estructura_modificar_eliminar_pptal.php",
          data: form_data,
          success: function(response)
          {
            if(response == 0)
            {
              siguiente();
            }
            else
            {
              $("#mdlYaHayAfec").modal('show');
            }
          }// Fin success.
        });// Fin Ajax;

      });

    });
</script>

<script type="text/javascript">
  
    function siguiente()
    {
      var idComP = $("#id_comp_pptal_OP").val();
      var form_data = { sesion: 'id_comp_pptal_CP', numero: idComP, nuevo: 'nuevo_CP', valN: 2};
      $.ajax({
        type: "POST",
        url: "estructura_seleccionar_pptal.php",
        data: form_data,
        success: function(response)
        {
          document.location = 'GENERAR_CUENTA_PAGAR.php'; // Dejar.
          //window.open('GENERAR_CUENTA_PAGAR.php'); // Comentar. Esto se usa solo para pruebas.
        }// Fin success.
      });// Fin Ajax;

    }

</script>

        </div> <!-- Fin segunda fila -->



        <!-- Script para cargar datos en el combo select Rubro a partir del lo que se seleccione en el combo select Concepto. -->
      <script type="text/javascript">

        $(document).ready(function()
        {
          $("#buscarOrd").change(function()
          { 
            traerNum();
            
          });
        });

      </script>

      <script type="text/javascript"> //Aquí $_SESSION['id_comp_pptal_OP']
      /*  $(document).ready(function()
        {
          $("#traerNum").click(function()
          { 

            if(($("#buscarOrd").val() != "") && ($("#buscarOrd").val() != 0) && ($("#seleccionar").val() != "") && ($("#seleccionar").val() != 0)) */
            function traerNum()
            {
              var form_data = {sesion: 'id_comp_pptal_OP', nuevo: 'nuevo_OP', numero: $("#buscarOrd").val(), valN: 1};
              $.ajax({
                type: "POST",
                url: "estructura_seleccionar_pptal.php",
                data: form_data,
                success: function(response)
                {
                  if(response == 1)
                  {
                    document.location.reload();
                  }
                                               
                }//Fin succes.
              }); //Fi
            } 

        /*  });
        }); */

      </script>

       <script type="text/javascript">
// Al dar click fuera del input buscar se limpia el input y se oculta el div de resultados.
        $(document).ready(function(){
 
          $(document).click(function(e){
            if(e.target.id!='buscarOrd')
              $('#buscarOrd').val('');
              $('#listado').fadeOut();
            });
 
        });

      </script>

   <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

             $(document).ready(function()
             {  
                $("#tercero").change(function()
                {
                 var opcion = '<option value="" >Registro Presupuestal</option>';

                  if(($("#tercero").val() == "")||($("#tercero").val() == 0))
                  { 
                    $("#solicitudAprobada").html(opcion);
                  }
                  else
                  {
                    var form_data = { id_tercero:+$("#tercero").val(), clase: 15 };
                    $.ajax({
                      type: "POST",
                      url: "estructura_tercero_comprobante_pptal.php",
                      data: form_data,
                      success: function(response)
                      {                          
                        if(response == "" || response == 0)
                        {
                          var noHay = '<option value="N" >No hay registro presupuestal</option>';
                          $("#solicitudAprobada").html(noHay).focus();
                        }
                        else
                        {
                          opcion += response;
                          $("#solicitudAprobada").html(opcion).focus();
                        }
                        
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asignación -->


<?php 
  if(!empty($_SESSION['id_comp_pptal_OP']))
  {

?>


<?php 
  }
?>






             <script type="text/javascript"> //Código JS para asignar un nuevo código de comprobante.

             $(document).ready(function()
             {  
                $("#tipoComPtal").change(function()
                {
                 

                  if(($("#tipoComPtal").val() == "")||($("#tipoComPtal").val() == 0))
                  { 
                    $("#noDisponibilidad").val("");
                  }
                  else
                  {
                    var form_data = { estruc: 3, id_tip_comp:+$("#tipoComPtal").val() };
                    $.ajax({
                      type: "POST",
                      url: "estructura_expedir_disponibilidad.php",
                      data: form_data,
                      success: function(response)
                      {                        
                        var numero = parseInt(response);
                        $("#noDisponibilidad").val(numero);
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asignar un nuevo código de comprobante. -->

          <!-- El número de solicitud seleccionado -->
          <input name="numero" type="hidden" value="<?php echo $numero; ?>">

<input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->

        <input type="hidden" name="MM_insert" >

      </form>

<!-- Al seleccionar un número de solcitud, cargará  --> 
<script type="text/javascript">

   $(document).ready(function()
     {  
        $("#solicitudAprobada").change(function() 
        {
          if(($("#solicitudAprobada").val() == "")||($("#solicitudAprobada").val() == 0))
          { 
            var form_data = { estruc: 11}; //Estructura Uno 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.
          }
          else if($("#solicitudAprobada").val() != "N")
          {
            var form_data = { estruc: 12, id_comp:+$("#solicitudAprobada").val() }; //Estructura Dos 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

          } //Cierre else.              
        });//Cierre change.

     });//Cierre Ready.

</script> <!-- Fin de recargar la página al seleccionar Solicitud nueva -->

  </div> <!-- Cierra clase client-form contenedorForma -->
</div> <!-- Cierra col-sm-10 -->


<?php 

  if(!empty($_SESSION['id_comp_pptal_OP']))
  {
?>
  <script type="text/javascript">

    $("#btnGuardarComp").prop("disabled", false);
    $("#btnNuevoComp").prop("disabled", false);
    $("#btnImprimir").prop("disabled", false);
    //$("#btnEnviar").prop("disabled", true);

    //$("#fecha").prop("disabled", true);
  </script>

<?php 
  }
  else
  {
?>

  <script type="text/javascript">

    $("#btnGuardarComp").prop("disabled", true);
    $("#btnNuevoComp").prop("disabled", true);
    $("#btnImprimir").prop("disabled", true);
    $("#btnEnviar").prop("disabled", true);

    $("#fecha").prop("disabled", false);
  </script>
<?php
  }

  if(!empty($_SESSION['nuevo_OP']))
  {
?>
 <script type="text/javascript">

    $("#btnGuardarComp").prop("disabled", true);
    //$("#btnEnviar").prop("disabled", false);

    //$("#fecha").prop("disabled", true);
  </script>

<?php
  }
?>
 
<script type="text/javascript">
  
     $(document).ready(function()
     { 
      $('#btnNuevoComp').click(function(){
         var form_data = { estruc: 11}; //Estructura Uno 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

      });
    });
    
  </script>

  <!-- select2 -->
  <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>

  <script>
  function llenar(){
      var tercero = document.getElementById('tercero').value;
      document.getElementById('terceroB').value= tercero;
  }
  </script>


<input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">

<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-10" style="margin-top: 5px;">
          <div class="table-responsive contTabla" >
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Rubro Presupuestal</strong></td>
                <td class="cabeza"><strong>Saldo Por Pagar</strong></td>
                <td class="cabeza"><strong>Valor Aprobado</strong></td>
              
               
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Rubro Presupuestal</th>
                <th>Saldo Por Pagar</th>
                <th>Valor Aprobado</th>

                                
              </tr>

            </thead>
            <tbody>
              
              <?php
                if(!empty($_SESSION['id_comp_pptal_OP']) && ($resultado == true))
                {
                   
                  while($row = mysqli_fetch_row($resultado))
                  {
                      
                      $valorPpTl = $row[2];
                  
                ?>
               <tr>
                <td class="oculto"><?php echo $row[0]?>
                  <input  id="id_det_com<?php echo $row[0];?>" type="hidden" value="<?php echo $row[0];?>" >
                </td>
                <td class="campos" > <!-- Botones modificar y eliminar -->
                    <?php 
                    ###BUSCAR EL CIERRE 
                    if(!empty($_SESSION['nuevo_OP']))
                    {
                    ##BUSCAR FECHA COMPROBANTE 
                    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comp_pptal_OP'];
                    $fc = $mysqli->query($fc);
                    $fc = mysqli_fetch_row($fc);
                    $fc = $fc[0];
                    ##DIVIDIR FECHA
                    $fecha_div = explode("-", $fc);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];

                    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
                    $ci="SELECT
                    cp.id_unico
                    FROM 
                    gs_cierre_periodo cp
                    LEFT JOIN
                    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
                    LEFT JOIN
                    gf_mes m ON cp.mes = m.id_unico
                    WHERE
                    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
                    $ci =$mysqli->query($ci);
                    if(mysqli_num_rows($ci)>0){ } else { ?> 
                    <div class="modElim" id="modElim" style="display:block">

                    <a class="" href="#<?php echo $row[0];?>" 
                      <?php 
                        if(!empty($_SESSION['nuevo_OP'])){echo 'onclick="javascript:eliminarDetComp('.$row[0].')"';}
                      ?>
                      >
                      <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                    </a>

                    <a class="" href="#<?php echo $row[0];?>"  
                      <?php 
                        if(!empty($_SESSION['nuevo_OP'])){echo 'onclick="javascript:modificarDetComp('.$row[0].')"';}
                      ?>
                      >
                      <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                    </a> 

                  </div>
                    <?php }  } ?>

                </td>

                <td class="campos" align="left" > <!-- Rubro presupuestal -->
                  <div class="acotado">
                    <?php echo ucwords(mb_strtolower($row[1]));?>
                  </div>
                </td>

                <td class="campos" align="right" > <!-- Saldo por pagar -->

                  <?php 
                  if(empty($_SESSION['nuevo_OP'])) {
                      
                      $saldoDisponible = valorRegistro($row[0], $row[3]) + modificacionRegistro($row[0], $row[3], 15) - afectacionRegistro3($row[0], $row[3], 15);
                      echo number_format($saldoDisponible, 2, '.', ',');
                      
                  } else {
                      
                      $saldoDisponible = valorRegistro($row[8], $row[3]) + modificacionRegistro($row[8],$row[3], 15) - afectacionRegistro3($row[8], $row[3], 15);
                      echo number_format($saldoDisponible, 2, '.', ',');
                      
                  }
                  
                  ?>
                  <input type="hidden" name="saldos<?php echo $row[0]?>" id="saldos<?php echo $row[0]?>" value="<?php echo $saldoDisponible?>">
 
                </td> <!-- Saldo por pagar -->

                <td class="campos" align="right" style="padding: 0px"> <!-- Valor aprobado -->

                  <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>">

                  <div id="divVal<?php echo $row[0];?>" style="margin-right: 10px;">
                    <?php  
                    $valorA=0;
                    if(empty($_SESSION['nuevo_OP'])){
                      $saldoDisponible = valorRegistro($row[0], $row[3]) + modificacionRegistro($row[0],$row[3], 15) - afectacionRegistro3($row[0], $row[3], 15);
                      echo number_format($saldoDisponible, 2, '.', ',');$
                      $valorA = $saldoDisponible;
                    } else {
                        echo number_format($valorPpTl, 2, '.', ','); 
                        $valorA =$valorPpTl;
                        
                    }
                    ?>
                     <input type="hidden" name="aprobados<?php echo $row[0]?>" id="aprobados<?php echo $row[0]?>" value="<?php echo $valorA?>"> 
                  </div>
                    <!-- Modificar los valores -->

                          <table id="tab<?php echo $row[0];?>" style="padding: 0px; background-color: transparent; background:transparent; margin: 0px;">
                            <tr>
                              <td style="padding: 0px;">

                              <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" maxlength="50" style="margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>');" value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>" required>

                            </td>

                            
                            <td style="padding: 3px;"> <!-- Botón guardar lo modificado. -->
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>','<?php echo $row[3];?>');" >
                                  <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                </a> 
                            </td>

                              <td style="padding: 3px;"> <!-- Botón cancelar modificación -->
                                <a href="#<?php echo $row[0];?>" onclick="javascript:cancelarModificacion(<?php echo $row[0];?>);" >
                                  <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                </a> 
                              </td>
                                
                            </tr>
                          </table>
                                
                    <script type="text/javascript">
                       var id = "<?php echo $row[0];?>";   

                       var idValorM = 'valorMod'+id;
                       var idTab = 'tab'+id;

                       $("#"+idTab).css("display", "none");

                    </script>

                </td> <!-- Fin celda Valor aprobado -->
                  
              </tr>
          <?php 
                  //}
                }
              }
          ?>

            </tbody>
          </table>

<?php 
  if(empty($_SESSION['nuevo_OP']))
  {
?>
  <script type="text/javascript">
    
    $('.modElim').click(function()
    {
      $("#ModalAlertNoMod").modal('show');
        
    });
    
  </script>
<?php
 }
?>

        </div> <!-- table-responsive contTabla -->
       
      </div> <!-- Cierra clase table-responsive contTabla  -->
      
    </div> <!-- Cierra clase col-sm-10 text-left -->
  </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->

<!-- Divs de clase Modal para las ventanillas de eliminar. -->
<div class="modal fade" id="myModal" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Detalle Solicitud?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!-- Fin Modales para eliminación -->

<!-- Divs de clase Modal para las ventanillas de modificar. -->

  <!-- Mensaje de modificación exitosa. -->
  <div class="modal fade" id="ModificacionConfirmada" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarConf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<!-- Error al modificar el valor al ser superior al saldo-->
  <div class="modal fade" id="myModalAlertMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es superior al saldo disponible.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

  <!-- Mensaje dato a modificar no es válido. -->
  <div class="modal fade" id="ModificacionNoValida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El dato a modificar no es válido.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarNoVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

 <!-- Mensaje de fallo en la modificación. -->
  <div class="modal fade" id="ModificacionFallida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarFall" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!-- Modales para modificación -->

<!-- Modal de alerta. El valor es mayor que el saldo.  -->
<div class="modal fade" id="myModalAlert" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es superior al saldo disponible.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de alerta. No se a seleccionado en el concepto.  -->
<div class="modal fade" id="myModalAlert2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione un concepto válido.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptCon" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
  <div class="modal fade" id="myModalAlertModInval" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValModInval" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
  <div class="modal fade" id="myModalAlertModSuperior" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor a modificar no puede ser superior al valor existente. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValModSup" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error de fecha --> 
  <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La fecha es menor a la del comprobante anterior. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error de fecha de vencimiento vacía --> 
  <div class="modal fade" id="ModalAlertFecVen" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La fecha está vacía. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptErrFecVen" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error generar el registro primero --> 
  <div class="modal fade" id="ModalAlertNoMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe generar primero el Registro Presupuestal.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptNoMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mdlYaHayAfec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Este comprobante ya tiene afectación.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnYaHayAfec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>


<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>

<?php require_once 'footer.php'; ?>

<script type="text/javascript">
  $('#AceptVal').click(function(){ 
    $("#valor").val('').focus();
  });
</script>

<script type="text/javascript">
  $('#AceptCon').click(function(){ 
    $("#valor").val('');
    $("#concepto").focus();
  });
</script>

<!-- Función para la eliminación del registro. -->
<script type="text/javascript">
      function eliminarDetComp(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GF_DETALLE_COMPROBANTE_PPTALJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>
  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'APROBACION_ORDEN_PAGO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'APROBACION_ORDEN_PAGO.php';
      });
    
  </script>

<!-- Fin funciones eliminar -->
<!-- Función para la modificación del registro. -->
<script type="text/javascript">

  function modificarDetComp(id)
  {
    if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != ""))
    {
      var cambiarTab = 'tab'+$("#idPrevio").val();
      var cambiarDiv = 'divVal'+$("#idPrevio").val();
      var cambiarOcul = 'valOcul'+$("#idPrevio").val();
      var cambiarMod = 'valorMod'+$("#idPrevio").val();

      var cambiarDivTerc = 'divTerc'+$("#idPrevio").val();
      var cambiarTabTerc = 'tabTerc'+$("#idPrevio").val();
      var cambiarDivProy = 'divProy'+$("#idPrevio").val();
      var cambiarTabProy = 'tabProy'+$("#idPrevio").val();

      if($("#"+cambiarTab).is(':visible'))
      {
            
        $("#"+cambiarTab).css("display", "none");
        $("#"+cambiarDiv).css("display", "block");
        $("#"+cambiarMod).val($("#"+cambiarOcul).val());

        $("#"+cambiarTabTerc).css("display", "none");
        $("#"+cambiarDivTerc).css("display", "block");

        $("#"+cambiarTabProy).css("display", "none");
        $("#"+cambiarDivProy).css("display", "block");
      }
    }
       
    var idValor = 'valorMod'+id;
    var idModi = 'modif'+id;

    var idDiv = 'divVal'+id;
    var idTabl = 'tab'+id;

    var idDivTerc = 'divTerc'+id;
    var idTablTerc = 'tabTerc'+id;

    var idDivProy = 'divProy'+id;
    var idTablProy = 'tabProy'+id;



    $("#"+idDiv).css("display", "none");
    $("#"+idTabl).css("display", "block");

    $("#"+idDivTerc).css("display", "none");
    $("#"+idTablTerc).css("display", "block");

    $("#"+idDivProy).css("display", "none");
    $("#"+idTablProy).css("display", "block");

    $("#idActual").val(id);

    if($("#idPrevio").val() != id)
      $("#idPrevio").val(id);

     
  }

</script>

<script type="text/javascript">
  function cancelarModificacion(id)
  {

    var idDiv = 'divVal'+id;
    var idTabl = 'tab'+id;
    var idValorM = 'valorMod'+id;
    var idValOcul = 'valOcul'+id;

    var idDivTerc = 'divTerc'+id;
    var idTablTerc = 'tabTerc'+id;

    var idDivProy = 'divProy'+id;
    var idTablProy = 'tabProy'+id;


    $("#"+idDiv).css("display", "block");
    $("#"+idTabl).css("display", "none");

    $("#"+idDivTerc).css("display", "block");
    $("#"+idTablTerc).css("display", "none");

    $("#"+idDivProy).css("display", "block");
    $("#"+idTablProy).css("display", "none");

    $("#"+idValorM).val($("#"+idValOcul).val());

  }
</script>



<script type="text/javascript">
  function guardarModificacion(id) //modificarDetComp(id)
  {
    var idDiv = 'divVal'+id;
    var idTabl = 'tab'+id;
    var idCampoValor = 'valorMod'+id;
    var idValOcul = 'valOcul'+id;

    var idCampoTerc = 'tercMod'+id;
    var idCampoProy = 'proyMod'+id;

    var valor = $("#"+idCampoValor).val();
        
    valor = valor.replace(/\,/g,''); //Elimina la coma que separa los miles.

    if( ($("#"+idCampoValor).val() == "") || ($("#"+idCampoValor).val() == 0))
    { 
      $("#ModificacionNoValida").modal('show');
      $("#"+idCampoValor).val($("#"+idValOcul).val());
    }
    else
    {
      var form_data = { id_val: id, valor: valor};
      $.ajax({
        type: "POST",
        url: "json/modificar_GF_DETALLE_COMPROBANTE_PPTALJson.php",
        data: form_data,
        success: function(response)
        {
          if(response != 0)
          {
            $("#ModificacionConfirmada").modal('show');
          }
          else
          {
            $("#ModificacionFallida").modal('show');
          }
        }
      });
    }

   }
  </script>

   <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
  <script type="text/javascript">

  function verificarValor(id_txt,id_rubFue)
  { 
    var saldo = $("#saldos"+id_txt).val();
    var aprob = $("#aprobados"+id_txt).val();
    
    var ver = parseFloat(saldo)+parseFloat(aprob);
    var valorMod = $("#valorMod"+id_txt).val();
    var validar = parseFloat(valorMod.replace(/\,/g,'')); //Elimina la coma que separa los miles.
    
    if(validar>ver){
        $("#myModalAlertMod").modal('show');
        
    } else {
        guardarModificacion(id_txt);
    }
                 
  }

</script>

<script type="text/javascript">
  function valida()
  {
    
    if($("#fecha").val() == "")
    {
      $("#ModalAlertFecVen").modal('show');
      return false;
    } else {
    
    return true;
    }

  }
</script>


  <script type="text/javascript">
      function modal()
      {
         $("#Modificacion").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#btnModificarConf').click(function()
      {
        document.location.reload();
      });
    
  </script>

<script type="text/javascript">
 //Si se ingresan valores diferentes a los numéricos en alguna de las casillas 
// de la lista para su modificación.
  $('#AceptValModInval').click(function()
  {
    var id_mod = "valorMod"+$("#idActual").val();
    var id_ocul = "valOcul"+$("#idActual").val();
    $("#"+id_mod).val($("#"+id_ocul).val()).focus();
  });
</script>

<script type="text/javascript">
  //Si se ingresan valores superiores a los valores para aprobar en alguna de las casillas 
  // de la lista para su modificación.
  $('#AceptValModSup').click(function()
  {
    var id_mod = "valorMod"+$("#idActual").val();
    var id_ocul = "valOcul"+$("#idActual").val();
    $("#"+id_mod).val($("#"+id_ocul).val()).focus();
  });
</script>

  

<script type="text/javascript">
    
  $('#AceptErrFec').click(function()
  {

    var fecha = new Date();
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1;

    if(dia < 10)
    {
      dia = "0" + dia;
    }

    if(mes < 10)
    {
      mes = "0" + mes;
    }

    var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
    $("#fecha").val(fecAct);
    $("#fechaVen").val("");

  });
    
</script>

<script type="text/javascript">
  
  $('#AceptErrFecVen').click(function(){
    $("#fecha").focus();
  });

</script>
<?php
    if(!empty($_SESSION['nuevo_OP']))
    {
  ?>
      <script type="text/javascript">

        $(document).ready(function()
        {
          var idComP = $("#id_comp_pptal_OP").val();

          var form_data = { estruc: 1, id_com: idComP };
          $.ajax({
            type: "POST",
            url: "estructura_modificar_eliminar_pptal.php",
            data: form_data,
            success: function(response)
            {
              if(response == 0)
              {
                $("#btnEnviar").prop("disabled", false);
              }
              else
              {
            $("#modElim").css('display','none');
            $("#btnEnviar").prop("disabled", true);
            $("#fecha").prop('disabled',true)
                
              }
            }// Fin success.
          });// Fin Ajax;
        });

        </script>
  <?php
  ##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comp_pptal_OP'];
    $fc = $mysqli->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
    $ci =$mysqli->query($ci);
    if(mysqli_num_rows($ci)>0){  ?>
        <script>
        $(document).ready(function(){
            console.log('djfh');
              $("#btnEnviar").prop("disabled", true);
              $("#btnEnviar").prop("disabled", true);
        });
        </script>  
     <?php    } else {  
  
    }}
  ?>
</body>
</html>