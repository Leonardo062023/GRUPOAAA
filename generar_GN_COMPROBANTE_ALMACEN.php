<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 

$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
 ?>
<title>Comprobantes  Almacén</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #sltctai-error, #sltctaf-error, #sltClase-error  {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
</style>


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
        $("#fechaini").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Comprobantes  Almacén </h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                          <div class="clasMo" style="margin-top: -5px"> 
                            <label for="sltClase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Clase:</label>
                            <select  name="sltClase" id="sltClase" class="select2_single form-control" title= "Seleccione clase" style="height: 30px" required>
                                <option value="">Clase Movimiento</option>
                                
                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "clase_movimiento.php",
                                                            success: function(response){
                                                                $('.clasMo select').html(response).fadeIn();
                                                                $('#sltClase').css('display','none');
                                                            }
                                                        });
                                                    });
                                                </script>  
                                              

                            </select>
                         </div> 
                        </div>
                     <div class="form-group" style="margin-top: -5px">
                            <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Movimiento Inicial:</label>
                            <select  name="sltTci" id="sltTci" class="select2_single form-control" title= "Seleccione Tipo Movimiento inicial" style="height: 30px" required>
                                <option value="">Tipo Movimiento Inicial</option>
                                <?php 
                                $tci= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_movimiento m 
                                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                                WHERE t.compania = $compania
                                ORDER BY t.sigla ASC";
                                $rsTci = $mysqli->query($tci);
                                
                                while ($filaTci = mysqli_fetch_row($rsTci)) { 
                                echo "<option value=".$filaTci[1].">".$filaTci[1].'-'.ucwords(mb_strtolower($filaTci[2]))."</option>";
                                } 
                                ?>

                            </select>
                            <script type="text/javascript">
                                      
                                     $("#sltClase").change(function(){
                                         console.log("hola");
                                         var opcion = '<option value="" >Tipo Movimiento Inicial</option>';
                                         console.log(opcion);
                                         var form_data = {
                                             is_ajax: 1,
                                             id_clase: +$("#sltClase").val()
                                         };
                                         $.ajax({
                                             type: "POST",
                                             url: "movimiento_inicial.php",
                                             data: form_data,
                                             success: function(response){
                                                 opcion += response;
                                                 $("#sltTci").html(opcion).focus();
                                                 
                                             }
                                         });
                                     });
                           
                                </script>

                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltTcf" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Movimiento Final:</label>
                            <select name="sltTcf" class="select2_single form-control" id="sltTcf" title="Seleccione Tipo Movimiento final" style="height: 30px"  required >
                                <option value="">Tipo Movimiento Final</option>
                                <?php 
                                $tcf= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_movimiento m 
                                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                                WHERE t.compania = $compania ORDER BY t.sigla DESC";
                                $rsTcf = $mysqli->query($tcf);
                                while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                <option value="<?php echo $filaTcf[1];?>"><?php echo ($filaTcf[1].' - '. ucwords(mb_strtolower($filaTcf[2]))); ?></option>
                                <?php } ?>
                            </select>
                            <script type="text/javascript">
                                      
                                      $("#sltClase").change(function(){
                                          console.log("hola");
                                          var opcion = '<option value="" >Tipo Movimiento Final</option>';
                                          console.log(opcion);
                                          var form_data = {
                                              is_ajax: 1,
                                              id_clase: +$("#sltClase").val()
                                          };
                                          $.ajax({
                                              type: "POST",
                                              url: "movimiento_final.php",
                                              data: form_data,
                                              success: function(response){
                                                  opcion += response;
                                                  $("#sltTcf").html(opcion).focus();
                                                  
                                              }
                                          });
                                      });
                            
                                 </script>

                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                             <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha:</label>
                             <input class="form-control" type="text" name="fechaini" id="fechaini"  value="<?php echo date("Y-m-d");?>" required>
                        </div>

                       
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>             
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
    </script>
    <script type="text/javascript"> 
         $("#sltClase").select2(); 
         $("#sltTci").select2(); 
         
    </script>
      <script>
    function reporteExcel(){
       $('form').attr('action', 'informes_almacen/generar_INF_COMPROBANTE_ALMACEN.php');
    }
    </script>
</div>
<?php require_once 'footer.php' ?>  
</body>
</html>