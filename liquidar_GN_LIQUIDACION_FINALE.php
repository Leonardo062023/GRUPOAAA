<?php

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];
@$fec = $_GET['fec'];
@$id_p = $_GET['idP'];



$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
$a2 = "none";
if(empty($id_T))
{
    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    
}
if(empty($id_p))
{
     
}
else
{
    
    $a="inline-block";
}
//'3'
@$empl_sim=$_GET['sltemp'];
//'30/11/218'
@$fecha_retiro=$_GET['fec'];
//"inline-block"
@$a2=$_GET['op'];
if(empty($a2))
{
     $a2="none";
}
else
{
    
    $a2="inline-block";
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
    label #sltEmpleado-error, #sltPeriodo-error, #fechaR-error {
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
               
        $("#fechaR").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Cesantías Retroactivas</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Cesantías Retroactivas</h2>
                    
                    <div class="client-form contenedorForma" style="margin-top: -6px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNomina/registrar_Liquidacion_Final_GN.php">                              
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top:-25px; height: 100px;">
                                <?php 
                                    $emp = "SELECT                         
                                                    e.id_unico,
                                                    e.tercero,
                                                    t.id_unico,
                                                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) 
                                    FROM gn_empleado e
                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico where t.compania = 1 ";
                               
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Empleado:
                                </label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 210px;height: 30px" class="form-control col-sm-2">
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
                            $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico  WHERE tpn.id_unico = 16 AND p.liquidado !=1 AND p.parametrizacionanno = '$vig'";

                            $periodo = $mysqli->query($per);
                        ?>
                        <label for="sltPeriodo"  class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Periodo:</label>
                        <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 210px;height: 30px" class="form-control col-sm-2">
                            <option value="">Periodo</option>
                            <?php 
                                while($rowE = mysqli_fetch_row($periodo))
                                {
                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                }
                            ?>       
                        </select>
                        <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top: 0px; width:100px; margin-bottom: -15px;margin-left: 10px ;">Liquidar</button>  

                            </div>
                        </form> 
                    </div>
                </div>
            </div>

           
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>


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
        var fechafi= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
      
       
            $( "#sltFechaF" ).datepicker( "destroy" );
            $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
    
                   
}


function fechaDisfrute(){
        var fechain= document.getElementById('sltFechaID').value;
        var fechafi= document.getElementById('sltFechaFD').value;
          var fi = document.getElementById("sltFechaFD");
        fi.disabled=false;
      
       
            $( "#sltFechaFD" ).datepicker( "destroy" );
            $( "#sltFechaFD" ).datepicker({ changeMonth: true, minDate: fechain});
        

           
           
}
</script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
         $("#sltEmpleado").select2();
        </script>
<script type="text/javascript"> 
         $("#sltPeriodo").select2();
</script>
        
</body>
</html>
