<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con = new ConexionPDO();
$anno = $_SESSION['anno'];?>
<title>Informe Facturación</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #p-error, #tipo-error, #fechaI-error,#fechaF-error {
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
<script>
    $(function(){
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaI").datepicker({changeMonth: true}).val();
        $("#fechaF").datepicker({changeMonth: true}).val();


    });
</script>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Informe Facturación</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_servicios/INF_FACTURAS_CONCEPTO.php" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Informe Por:</label>
                            <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo" style="height: auto " required>
                                <option value="">Informe Por</option>
                                <option value="1">Fechas</option>
                                <option value="2">Periodo</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-top: -5px; display: none" id="periodo" >
                            <label for="p" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            <select name="p" id="p" class="form-control select2" title="Seleccione Periodo" style="height: auto;  width: 26% " >
                                <?php 
                                    echo '<option value="">Periodo</option>';
                                    $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
                                        WHERE p.anno = $anno ORDER BY p.fecha_inicial DESC");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i]['descripcion'].'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group" style="display: none" id="fechas" >
                            <div class="form-group" style="">
                                <label for="fechaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha Inicial:</label>
                                <input class="form-control col-sm-2" name="fechaI" id="fechaI" title="Seleccione Fecha Inicial" style=" width: 26% " >
                            </div>
                            <div class="form-group" style="">
                                <label for="fechaF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha Final:</label>
                                <input class="form-control col-sm-2" name="fechaF" id="fechaF" title="Seleccione Fecha Final" style="width: 26%" >
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="uso" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Uso:</label>
                            <select name="uso" id="uso" class="form-control select2" title="Seleccione Uso" style="height: auto " >
                                <?php 
                                    echo '<option value="">Uso</option>';
                                    $tr = $con->Listar("SELECT * FROM gp_uso ORDER BY id_unico asc");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="estrato" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Estrato:</label>
                            <select name="estrato" id="estrato" class="form-control select2" title="Seleccione Estrato" style="height: auto " >
                                <option value="">Estrato</option>
                            </select>
                        </div>
                            <div class="form-group form-inline  col-md-12 col-lg-12" style="margin-left: 5px; margin-top: 10px">
                                <label for="sector2" class="col-sm-5 control-label"></label>
                                <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></button>    
                            </div>
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
            $("#p").select2();
            $("#uso").select2();
            $("#tipo").select2();
            $("#estrato").select2();
        
        $("#tipo").change(function(){
            let tipo = $("#tipo").val();
            if(tipo==1){
                $("#periodo").css("display", "none");
                $("#fechas").css("display", "block");
                $("#fechaI").attr("required", "true");
                $("#fechaF").attr("required", "true");
                $("#p").removeAttr("required");
            } else {
                $("#periodo").css("display", "block");
                $("#fechas").css("display", "none");
                $("#fechaI").removeAttr("required");
                $("#fechaF").removeAttr("required");
                $("#p").attr("required", "true");

            }
        })
        $("#uso").change(function(){
            let form_data = {
                uso:$("#uso").val(),
                action:1
            };
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_InformesJson.php",
                data: form_data,
                success: function (data) {
                    let option = '<option value="">Estrato</option>';
                    option = option+data;
                    $("#estrato").html(option);
                }
            })
        })
    </script>
    <?php require_once 'footer.php'?>  
</div>
</body>
</html>