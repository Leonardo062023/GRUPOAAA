<?php
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
@$id = $_GET['idE'];
@$tipoN = $_GET['tipo'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ',t.nombreuno,  t.nombredos,  t.apellidouno, t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE e.id_unico = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";

if(empty($idT))
{
    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    $a="inline-block";
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltEmpleado-error, #sltConcepto-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body{
        font-size: 11px;
    }
    
   /* Estilos de tabla*/
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
<script>


$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<script src="js/jquery-ui.js"></script>
<script>

        $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();        
});
</script>
   <title>Registrar Incapacidad</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Incapacidad</h2>
                    <a href="<?php echo 'listar_GN_INCAPACIDAD.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarIncapacidadJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                            <!--------------------------------------------------------------------------------------------------------------------- -->
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <?php 
                                        if(empty($idT))
                                        {
                                            $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT_WS(' ',t.nombreuno,  t.nombredos,  t.apellidouno, t.apellidodos )
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                                                    $idTer = "";
                                        }
                                        else
                                        {
                                            $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT_WS(' ',t.nombreuno,  t.nombredos,  t.apellidouno, t.apellidodos )
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != '$idT'";
                                            $idTer = $idT;
                                        }
                                        $empleado = $mysqli->query($emp);
                                        
                                    ?>
                            
                                    <label for="sltEmpleado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                            			echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                          
                                    <!--------------------------------------------------------------------- -->
                                    <?php 
                                        $tip   = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE id_unico = $tipoN ";
                                        $tipon = $mysqli->query($tip);
                                        $nov   = mysqli_fetch_row($tipon);
                                    ?>
                                    <input type="hidden" name="txtidTip" id="txtidTip" value="<?php echo $nov[0]; ?>" title="Tipo Novedad" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Tipo Novedad:</label>
                                    <input name="sltTipo" id="sltTipo" title="Tipo Novedad" value="<?php echo $nov[1]; ?>" readonly="readonly" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2">                      
                          
                                    <?php 
                                        $es = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE clase = '10'";
                                        $est = $mysqli->query($es);
                                    ?>
                            
                                    <label for="sltConcepto" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Concepto:</label>
                                    <select name="sltConcepto" id="sltConcepto" title="Seleccione Concepto" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2" required="required">
                                        <option value="">Concepto</option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($est))
                                            {
                                                echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                            }
                                        ?>                                                          
                                    </select>
                                </div>
                                <!--------------------------------------------------------------------------------------------------- -->                              
                                <div class="form-group form-inline" >
                            
                                    <label for="txtNumeroI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Incapacidad:</label>
                                    <input  name="txtNumeroI" id="txtNumeroI" title="Ingrese Número Incapacidad" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Número Incapacidad">                              
                            
                                    <label for="txtNumeroA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Aprobación:</label>
                                    <input  name="txtNumeroA" id="txtNumeroA" title="Ingrese Número Aprobación" type="text" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Número Aprobación">  
                            
                                    <label for="txtNumeroI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Días:</label>
                                    <input  name="txtNumeroD" id="txtNumeroD" title="Ingrese Número Días" type="number" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2">  
                                </div>      
                                <!--------------------------------------------------------------------------------------------------- -->                              
                                <div class="form-group form-inline" >
                            
                                    <label for="txtDiagnostico" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Diagnóstico:</label>
                                    <input  name="txtDiagnostico" id="txtDiagnostico" title="Ingrese Diagnóstico" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" onkeypress="return txtValida(event,'car')" placeholder="Diagnóstico">
                                    
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                            
                                    <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                                    <input name="sltFechaI" id="sltFechaI" title="Ingrese la Fecha Inicial" type="text" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2"  onchange="javaScript:fechaInicial();"  placeholder="Ingrese la fecha" >  
                            
                                    <label for="sltFechaA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Aprobación:</label>
                                    <input name="sltFechaA" id="sltFechaA" title="Ingrese la Fecha Aprobación" type="text" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2"    disabled ="true" placeholder="Ingrese la fecha" >  
                                                        
                                </div> 
                                
                                <div class="form-group form-inline" >
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                            
                                    <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                                    <input name="sltFechaF" id="sltFechaF" title="Ingrese la Fecha Final" type="text" style="width: 14%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2"  onchange="javaScript:fechaInicial();"  placeholder="Ingrese la fecha" >  

                                    <?php
                                        $estin = "SELECT a.id_unico, CONCAT(a.numradicado,' - ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos ) "
                                                . "FROM gn_accidente a LEFT JOIN gn_empleado e ON a.empleado = e.id_unico "
                                                . "LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE a.empleado  = '$idTer'";
                                        $esin  = $mysqli->query($estin);
                                    ?>
                                    <label for="sltAccidente" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Accidente:</label>
                                    <select name="sltAccidente" id="sltAccidente" title="Seleccione Accidente" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="">Accidente</option>
                                        <?php 
                                            while($rowEs = mysqli_fetch_row($esin))
                                            {
                                                echo "<option value=".$rowEs[0].">".$rowEs[1]."</option>";
                                            }
                                        ?>                                                          
                                    </select> 
                            
                                    <label for="No" class="col-sm-10 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:-35px; width:40px; margin-bottom: -10px;margin-left: -45px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>  
                                </div>
                            </div>
                        </form>
                    </div> 
                </div>    
                
                <?php
                    if($nov[0] == 5){
                ?>
                        <script type="text/javascript">

                            $("#sltAccidente").prop("disabled", false);

                        </script>
              <?php }else{ ?>
                  
                      <script type="text/javascript">

                            $("#sltAccidente").prop("disabled", true);

                        </script> 
                <?php        
                    }        
                ?>
                        
                <script type="text/javascript">
                    $("#sltEmpleado").change(function(){
                        var tip = $("#txtidTip").val();
                        var emp = $("#sltEmpleado").val();
                        document.location = 'registrar_GN_INCAPACIDAD.php?tipo='+tip+'&idE='+emp; 
                    });
                </script>        
                <div class="col-sm-8 col-sm-2" style="margin-top:-23px">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                </th>
                            </tr>
                    
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">EMPLEADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_INCAPACIDAD.php">ESTADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_CONCEPTO.php">CONCEPTO</a>
                                </td>
                            </tr>
                        </tbody>        
                    </table>
                </div>
                <!---------------------------------------------------------------------------------------------------->                        
                
                <div class="form-group form-inline" style="margin-top:5px;">
                
                    <?php require_once './menu.php'; 
                        
                        if(!empty($idTer)){
                            
                            $sql = "SELECT      i.id_unico,
                                                i.numeroinc,
                                                i.fechainicio,
                                                i.numerodias,
                                                i.empleado,
                                                e.id_unico,
                                                e.tercero,
                                                t.id_unico,
                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                i.fechaaprobacion,
                                                i.numeroaprobacion,
                                                i.accidente,
                                               
                                                i.diagnostico,
                                                i.tiponovedad,
                                                tn.id_unico,
                                                tn.nombre,
                                                a.numradicado
                                    FROM gn_incapacidad i
                                    LEFT JOIN	gn_empleado e             ON i.empleado    = e.id_unico
                                    LEFT JOIN   gf_tercero t              ON e.tercero     = t.id_unico
                                    LEFT JOIN   gn_accidente a            ON i.accidente   = a.id_unico
                                    LEFT JOIN   gn_tipo_novedad tn        ON i.tiponovedad = tn.id_unico
                                    WHERE i.empleado = $idTer";
                            $resultado = $mysqli->query($sql);
                            $nres = mysqli_num_rows($resultado);
                        }else{
                            $nres = 0;
                        }    
                    ?>
                    
                    <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 5px;">
                        <div class="table-responsive" >
                            <div class="table-responsive" >
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td class="cabeza" style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>
                                            <!-- Actualización 24 / 02 10:40 No es necesario mostrar el nombre del empleado
                                            <td class="cabeza"><strong>Empleado</strong></td>
                                            -->
                                            <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                            <td class="cabeza"><strong>Accidente</strong></td>
                                            <td class="cabeza"><strong>No. Incapacidad</strong></td>
                                            <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                            <td class="cabeza"><strong>No. Días</strong></td>
                                            <td class="cabeza"><strong>No. Aprobación</strong></td>
                                            <td class="cabeza"><strong>Fecha Aprobación</strong></td>
                                            <td class="cabeza"><strong>Diagnóstico</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th class="cabeza" width="7%"></th>                                        
                                            <!-- Actualización 24 / 02 10:40 No es necesario mostrar el nombre del empleado
                                            <th class="cabeza">Empleado</th>
                                            -->
                                            <th class="cabeza">Tipo Novedad</th>
                                            <th class="cabeza">Accidente</th>
                                            <th class="cabeza">No. Incapacidad</th>
                                            <th class="cabeza">Fecha Inicio</th>
                                            <th class="cabeza">No. Días</th>
                                            <th class="cabeza">No. Aprobación</th>
                                            <th class="cabeza">Fecha Aprobación</th>
                                            <th class="cabeza">Diagnóstico</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <?php
                                            if($nres > 0){
                                                while ($row = mysqli_fetch_row($resultado)) {                                         
                                                    $infi      = $row[2];
                                                    if(!empty($row[2])||$row[2]!=''){
                                                        $infi      = trim($infi, '"');
                                                        $fecha_div = explode("-", $infi);
                                                        $anioi     = $fecha_div[0];
                                                        $mesi      = $fecha_div[1];
                                                        $diai      = $fecha_div[2];
                                                        $infi      = $diai.'/'.$mesi.'/'.$anioi;
                                                    }else{

                                                        $infi =  '';
                                                    }
                                        
                                                    $infa      = $row[9];
                                                    if(!empty($row[9])|| $row[9]!=''){
                                                        $infa      = trim($infa, '"');
                                                        $fecha_div = explode("-", $infa);
                                                        $aniofa    = $fecha_div[0];
                                                        $mesfa     = $fecha_div[1];
                                                        $diafa     = $fecha_div[2];
                                                        $infa      = $diafa.'/'.$mesfa.'/'.$aniofa;
                                                    }else{

                                                        $infa = '';
                                                    }

                                                         
                                                    $inid   = $row[0];
                                                    $inni   = $row[1];
                                                    #$infi   = $row[2];
                                                    $innd   = $row[3];
                                                    $inemp  = $row[4];
                                                    $empid  = $row[5];
                                                    $empter = $row[6];
                                                    $terid  = $row[7];
                                                    $ternom = $row[8];
                                                    #$infa   = $row[9];
                                                    $inna   = $row[10];
                                                    $inest  = $row[11];
                                                    #$eiid   = $row[12];
                                                    #$einom  = $row[13];
                                                    $indiag = $row[12];
                                                    $intn   = $row[13];
                                                    $tnid   = $row[14];
                                                    $tnnom  = $row[15];
                                                    $radiac = $row[16];
                                        ?>
                                                    <tr>
                                                        <td style="display: none;"><?php echo $row[0]?></td>
                                                        <td>
                                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                            </a>
                                                            <a href="modificar_GN_INCAPACIDAD.php?id=<?php echo md5($row[0]);?>">
                                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                            </a>
                                                        </td>                                        
                                                        <!-- Actualización 24 / 02 10:40 No es necesario mostrar el nombre del empleado
                                                        <td class="campos"><?php #echo $ternom?></td>
                                                        -->
                                                        <td class="campos"><?php echo $tnnom?></td>                   
                                                        <td class="campos"><?php echo $radiac?></td>                   
                                                        <td class="campos"><?php echo $inni?></td>                   
                                                        <td class="campos"><?php echo $infi?></td>                
                                                        <td class="campos"><?php echo $innd?></td>                
                                                        <td class="campos"><?php echo $inna?></td>                
                                                        <td class="campos"><?php echo $infa?></td>                
                                                        <td class="campos"><?php echo $indiag?></td>  
                                          <?php }
                                            }
                                          ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
               
            </div>
           
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Incapacidad?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
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


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarIncapacidadJson.php?id="+id,
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
      function recargar()
      {
        window.location.reload();     
      }
  </script>     
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){ 
         reload();
        //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
        //window.location='../listar_GN_ACCIDENTE.php';
        window.history.go(-1);        
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        window.history.go(-1);
      });    
  </script>
</div>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaA').value;
        var fechaff= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaA");
        fi.disabled=false;
      
       
            $( "#sltFechaA" ).datepicker( "destroy" );
            $( "#sltFechaA" ).datepicker({ changeMonth: true, minDate: fechain});
        

           
           
}
    </script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#sltConcepto").select2();
        $("#sltEmpleado").select2();
        $("#sltAccidente").select2();
    </script>
</body>
</html>
