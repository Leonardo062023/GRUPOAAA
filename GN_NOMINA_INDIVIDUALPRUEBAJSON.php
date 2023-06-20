<?php  

require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$anno = $_SESSION['anno'];
$con   = new ConexionPDO();
$rowe = $con->Listar("SELECT DISTINCT e.id_unico,tr.id_unico, IF(CONCAT_WS(' ',
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
        tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion 
        FROM gn_empleado e 
    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero tr ON tr.id_unico = e.tercero 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
    WHERE p.parametrizacionanno = $anno  
    ORDER BY e.id_unico ASC");

?>
<!--Titulo de la página-->
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>





label #sltmesi-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
}

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

   <style>
    .form-control {font-size: 12px;}
    
</style>

<title>Nómina Electrónica</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Enviar Nómina Electrónica</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNominaE/jsonpru.php">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                               <?php               
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                $filaAnnio = mysqli_fetch_row($rsannio);
                                $anio=$filaAnnio[0];
                                $anioN=$filaAnnio[1];
                                ?>
                        <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $anio;?>" > 
                        <input type="hidden" name="annio" id="annio" value="<?php echo $anioN;?>" >             
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes:</label>
                            <select required name="sltmesi" id="sltmesi" style="height: auto" class="select2_single form-control" title="Seleccione el mes" >
                                <option value="">Mes Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="Empleado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Empleado:</label>
                            <select required="required"  name="Empleado" id="Empleado" class="select2_single form-control" title="Seleccione Empleado">
                                <option value="">Empleado</option>
                                <?php for ($i = 0; $i < count($rowe); $i++) {
                                    echo '<option value="'.$rowe[$i][0].'">'.$rowe[$i][1].' - '.$rowe[$i][2].'</option>';
                                } ?>                                    
                            </select>
                        </div>
           
            <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
              <label for="no" class="col-sm-5 control-label"></label>
               <button type="submit" title="Enviar Nómina" onclick="alertaArchivo();" id="btnRequerido" class="btn btn-primary sombra" style=" margin-top: -10px; margin-left: 120px;" data-dismiss="modal" ><li class="glyphicon glyphicon-cloud-upload"></li></button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>      
    </div>
  </div>
</div>
 <div class="modal fade" id="modalRequerido" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Seleccione un archivo.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnRequerido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
 <script src="js/select/select2.full.js"></script>
 <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript"> 
      $(".select2_single").select2();
  </script>  
  <script>
           var form_data={action: 1, annio :$("#sltAnnio").val()};
           var optionMI ="<option value=''>Mes Inicial</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionMI =optionMI+response;
                  $("#sltmesi").html(optionMI).focus();              
              }
           });
        
</script>


<style>
#WindowLoad{
    position:fixed;
    top:0px;
    left:0px;
    z-index:3200;
    filter:alpha(opacity=80);
   -moz-opacity:80;
    opacity:0.80;
    background:#FFF;
}
</style>
<?php require_once 'footer.php';?>
</body>
</html>

