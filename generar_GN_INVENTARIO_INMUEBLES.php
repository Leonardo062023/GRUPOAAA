<?php
#06/10/2021 - Elkin O- Se crea el formulario para inventario inmuebles.
require 'head.php';
require 'Conexion/conexion.php';

$compania = $_SESSION['compania'];
?>
    <title>Inventario Inmuebles</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0px 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }


        #form .form-group{
            margin-bottom: 10px;
        }
        
    </style>


</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Inventario General De Inmuebles </h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST" target="_selft" enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>                     
                        <div class="form-group">
                            <label for="txtFechaInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 ">
                                <input type="text" class="form-control" id="txtFechaInicial" name="txtFechaInicial" title="Seleccione fecha inicial" placeholder="Fecha Inicial" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaFinal" name="txtFechaFinal"  title="Seleccione fecha final" placeholder="Fecha Final" required>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="sltProductoInicial" class="control-label col-sm-5"><strong class="obligado">*</strong>Producto Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left" >
                                <select name="sltProductoInicial" id="sltProductoInicial" class="select2 form-control text-left" title="Seleccione producto Inicial" required >
                                    <?php
                            
                                    echo "<option value=\"\">Producto Inicial</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        pln.tipoinventario  = 4
                                            AND        pln.compania        = $compania
                                            AND        pes.valor          != ' '
                                            ORDER BY   pr.id_unico ASC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                       echo "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="sltProductoFinal" class="control-label col-sm-5"><strong class="obligado">*</strong>Producto Final:</label>
                         <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltProductoFinal" id="sltProductoFinal" class="select2 form-control text-left" title="Seleccione producto final" required>
                                    <?php
                                    
                                    echo "<option value=\"\">Producto Final</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        pln.tipoinventario  = 4
                                            AND        pes.valor          != ' '
                                            AND        pln.compania        = $compania
                                            ORDER BY   pr.id_unico DESC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                        echo "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                                <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="submit" id="btnPdf" class="btn btn-primary glyphicon glyphicon-print"></button>
                                </div>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="submit" id="btnExc" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Excel">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script src="dist/jquery.validate.js"></script>
            <script src="js/bootstrap-notify.js"></script>
            <script type="text/javascript" src="js/md5.js"></script>
            <script src="js/select/select2.full.js"></script>
           
          
          <script>
      $(".select2").select2();

           
           $("#btnPdf").click(function(){
               $("#form").attr("action","informes_almacen/generar_INF_INVENTARIO_INMUEBLES.php");
           });

           $("#btnExc").click(function(){
               $("#form").attr("action","informes_almacen/generar_INF_INVENTARIO_INMUEBLES_EXCEL.php");
           });

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
                        yearSuffix: ''
                    };
                    $.datepicker.setDefaults($.datepicker.regional['es']);
                    $("#txtFechaInicial").datepicker({changeMonth: true}).val();
                    $("#txtFechaFinal").datepicker({changeMonth: true}).val();
                });

                $("#txtFechaFinal").change(function(){
                    var fechaInicial = $("#txtFechaInicial").val();
                    var fechaFinal   = $("#txtFechaFinal").val();

                    if(fechaFinal < fechaInicial){
                        $("#txtFechaFinal").parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                        $("#txtFechaFinal").val("");
                    }else{
                        $("#txtFechaFinal").parents(".col-sm-5").addClass("has-success").removeClass('has-error');
                    }
                });

                $().ready(function() {
                    var validator = $("#form").validate({
                        ignore: "",
                        errorElement:"em",
                        errorPlacement: function(error, element){
                            error.addClass('help-block');
                        },
                        highlight: function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                            }else{
                                $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                            }
                        
                        },
                        unhighlight:function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                            }else{
                                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                            }
                           
                        }
                    });
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>