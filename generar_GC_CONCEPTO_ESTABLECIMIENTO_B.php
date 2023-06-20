<?php
    ###################################################################################################
    #
    #12/02/2019 creado por LORENA MORENO
    #
    ####################################################################################################

    require_once ('head_listar.php');
    require_once ('./Conexion/conexion.php');
    #session_start();

    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];

    @$id = $_GET['idE'];
  
?>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
            label #sltFechaI-error, #sltBancoI-error {
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
                $("#sltFechaF").datepicker({changeMonth: true}).val();


        });
        </script>
        <script src="js/jquery-ui.js"></script>

        <title>Informe de Concepto de Uso del Suelo de Establecimientos</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
       
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Concepto de Uso del Suelo de Establecimientos</h2>
                    
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">        
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  target="_blank">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php 
                            $uniE = " SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                            $Ueje = $mysqli->query($uniE);
                            ?>
                            <div class="form-group " style="margin-top: -5px">
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $("#datepicker").datepicker();  
                                    });
                                </script>
        
                                <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                <input style="width:13%;" class="col-sm-2 col-md-2 col-lg-2 input-sm" required readonly type="text" name="sltFechaI" id="sltFechaI"  onchange="fechaInicial();" placeholder="Ingrese la fecha" title="Ingrese la fecha Inicial" >                            
                                <label for="sltFechaF" class="col-sm-2 col-md-2 col-lg-2 control-label">Fecha Final:</label>
                                <input  class="col-sm-2 input-sm" type="text" name="sltFechaF" id="sltFechaF" readonly disabled="true" placeholder="Ingrese la fecha"  style="width: 13%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2"> 
                                 <div class="col-sm-2 col-md-2 col-lg-2 control-label">
                                     <button  class="btn sombra btn-primary" title="Generar reporte PDF" onclick="reportePdf1()" style="margin-top: -2px; "><li class="fa fa-file-pdf-o"></li></button>

                                 </div>   
                             </div>
                         
                        </form>      
                    </div>
                </div>
            </div>                                    
        </div>
        <div>

            <!--Script que dan estilo al formulario-->

            <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            
        </div>

        <script>
            function reporteExcel1(){
                $('form').attr('action', 'informesComercio/generar_INF_CONCEPTO_USO_XLS.php');
            }   

            function reportePdf1(){
                $('form').attr('action', 'informesComercio/generar_INF_CONCEPTO_USO_BRB.php');
            }
        </script>
        <script src="js/select/select2.full.js"></script>

        <script type="text/javascript"> 
            $("#sltTipo").select2();
                     
        </script>
        <script>
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaI').value;
                var fechafi= document.getElementById('sltFechaF').value;
                var fi = document.getElementById("sltFechaF");
                fi.disabled=false;
       
                $( "#sltFechaF" ).datepicker( "destroy" );
                $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>
        <?php require_once './footer.php'; ?>
    </body>
</html>