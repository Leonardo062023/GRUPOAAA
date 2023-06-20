<?php
###################################################################################################
#
#04/04/2017 creado por Nestor B 
#23/05/2017 --- Nestor B --- se modificó la consulta que trae los periodos que aún no han sido cerrados
#16/06/2017 --- Nestor B --- se agregó la validacion cuando los selects son vacíos
####################################################################################################

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero,CONCAT_WS(' ', t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos )  , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
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
    label #sltEmpleado-error, #sltPeriodo-error {
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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Liquidación Bonificación de Dirección</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Bonificación de Dirección</h2>
                    <a href="<?php echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/LiquidarBonDirJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!--------------------------------------------------------------------------------------------------------------------- -->
                            <div class="form-group form-inline" style="margin-top:-25px">
                                <?php 
                                    if(empty($idT))
                                    {
                                        $emp = "SELECT                         
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS(' ', t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos ) 
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                            WHERE e.id_unico !=2";
                                        $idTer = "";
                                    }
                                    else
                                    {
                                        $emp = "SELECT                      
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS(' ', t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos ) 
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                                        $idTer = $idT;
                                    }
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 160px;height: 30px" class="form-control col-sm-1">
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
                                    $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) "
                                            . "FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico "
                                            . "WHERE p.liquidado !=1 AND p.tipoprocesonomina = 6 AND p.parametrizacionanno = '$vig'";

                                    $periodo = $mysqli->query($per);
                                ?>

                                <label for="sltPeriodo" class="col-sm-2 control-label"><strong class="obligado">*</strong>Periodo:</label>
                                <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 150px;height: 30px" class="form-control col-sm-1">
                                    <option value="">Periodo</option>
                                    <?php 
                                        while($rowE = mysqli_fetch_row($periodo))
                                        {
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>       
                                </select>
                          
                                <label for="No" class="col-sm-2 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top: -3px; width:100px; margin-bottom: -10px;margin-left: 10px ;">Liquidar</button>    
                            </div>
                        </form>  
                    </div>
            </div>
        </div>
    </div>
    
    <?php require_once './footer.php'; ?>

</body>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
    $("#sltEmpleado").select2();
    $("#sltPeriodo").select2();
</script>

</html>


       