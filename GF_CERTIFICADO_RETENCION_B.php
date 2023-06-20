<?php
require_once('Conexion/conexion.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
require_once('./jsonSistema/funcionCierre.php');
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];

?>

<title>Expedir Registro Presupuestal</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>



<style>
    label #fechaini-error, #fechafin-error, #sltTi-error, #sltTf-error, #opcion-error  {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}

body{
    font-size: 12px;
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

    <div class="container-fluid text-center"  >
        <div class="row content">
<?php require_once 'menu.php'; ?>

            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 

                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Certificado de Retención</h2>


                <div class="col-sm-12">
                    <div class="client-form contenedorForma col-sm-12"  style=""> 

                        <!-- Formulario de comprobante PPTAL -->
                        <form name="form" id="form" class="form-horizontal" method="POST" onsubmit="return valida();"  enctype="multipart/form-data" action="" target="_blank">

                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                            </p>
                         <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "text" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "text" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechafin" id="fechafin"  value="<?php echo date("d/m/Y");?>" title="Ingrese Fecha Final">
                        </div>
                         <div class="form-group" style="margin-top: 10px;">
                            <label for="terceroin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tercero Inicial:</label>
                           
                                <select  name="tercero" id="tercero" class="select2_single " title="Seleccione un tercero"  style="margin-left:-324px; width:300px " required>
                                        <?php
                                        
                                            $queryTercero = "SELECT IF(CONCAT_WS(' ',
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
                                    WHERE tr.compania=$compania
                                    ORDER BY tr.numeroidentificacion ASC LIMIT 20";
                                            $tercero1 = $mysqli->query($queryTercero);

                                            while ($tercero = mysqli_fetch_row($tercero1)) {
                                                if (empty($tercero[3])) {
                                                    ?>
                                                    <option value="<?php echo $tercero[1] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1]; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $tercero[1] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1] . '-' . $tercero[3]; ?></option>
                                                <?php }
                                            } ?>
                                           
                                          

                                    </select>
                        </div>

                         <div class="form-group" style="margin-top: 10px;">
                            <label for="tercerof" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tercero Final:</label>
                           
                                <select  name="tercero1" id="tercero1" class="select2_single" title="Seleccione un tercero"  style="margin-left:-324px; width:300px " required>
                                        <?php
                                        
                                            $queryTercero = "SELECT IF(CONCAT_WS(' ',
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
                                    WHERE tr.compania=$compania
                                    ORDER BY tr.numeroidentificacion DESC LIMIT 20";
                                            $tercero1 = $mysqli->query($queryTercero);

                                            while ($tercero = mysqli_fetch_row($tercero1)) {
                                                if (empty($tercero[3])) {
                                                    ?>
                                                    <option value="<?php echo $tercero[1] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1]; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $tercero[1] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1] . '-' . $tercero[3]; ?></option>
                                                <?php }
                                            } ?>
                                           
                                          

                                    </select>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="tercerof" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Informe:</label>
                            <select  required name="opcion" id="opcion" class="select2_single " style="margin-left:-324px; width:300px " title="Seleccione Informe">
                                <option value>Informe</option>
                                <option value ="1">General</option>
                                <option value ="2">Detallado</option>
                                
                            </select>
                             
                        </div>
                        
                        <div class="form-group text-center" style="margin-top:20px;">
                            <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                        </div>

                    </form>
          </div>
      </div>
</div>
      </div>
</div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>

    <script>
                        $(".select2_single").select2();
    </script> 


    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

 
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INF_Certificado_Retenciones1.php');
}
</script>
<script>
$('#s2id_autogen1_search').on("keydown", function(e) {
    let term = e.currentTarget.value;
    let form_data4 = {action: 12, term: term};
    console.log('tercero');
    $.ajax({
        type:"POST",
        url:"jsonPptal/gf_tercerosJson.php",
        data:form_data4,
        success: function(data){
            let option = '<option value=""> - </option>';
             option = option+data;
            $("#tercero").html(option);
                
        }
    }); 
});

$('#s2id_autogen2_search').on("keydown", function(e) {
    let term = e.currentTarget.value;
    let form_data4 = {action: 13, term: term};
    console.log('tercero1');
    $.ajax({
        type:"POST",
        url:"jsonPptal/gf_tercerosJson.php",
        data:form_data4,
        success: function(data){
            let option = '<option value=""> - </option>';
             option = option+data;
            $("#tercero1").html(option);
                
        }
    }); 
});





</script>

<?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>
</body>
</html>
<?php require_once 'footer.php'; ?>

