<?php
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
?>
    <title>Existencias - Inventario</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: -20px">
                <h2 align="center" class="tituloform">Existencias de Inventario</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <?php
                            $ein= "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' ', UPPER(pi.nombre)) AS codele
                                   FROM            gf_detalle_movimiento dm
                                   LEFT JOIN       gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE           pi.compania = $compania
                                   ORDER BY        pi.id_unico ASC";
                            $rsEin = $mysqli->query($ein);
                            ?>
                            <label for="Ein" class="col-sm-5 control-label">
                                <strong style="color:#03C1FB;">*</strong>Elemento Inicial:
                            </label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltEin" id="sltEin" class="form-control select" title="Seleccione Elemento inicial" style="height: 30px" required>
                                    <?php
                                    echo "<option value=''>Elemento Inicial</option>";
                                    while ($filaEin = mysqli_fetch_row($rsEin)){
                                        echo "<option value='$filaEin[0]'>$filaEin[1]</option>";
                                    }
                                    ?>
                                 </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $efn = "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' ', UPPER(pi.nombre)) AS codele
                                   FROM      gf_detalle_movimiento dm
                                   LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE     pi.compania = $compania
                                   ORDER BY pi.id_unico DESC";
                            $rsEfn = $mysqli->query($efn);
                            ?>
                            <label for="Efn" class="col-sm-5 control-label">
                                <strong style="color:#03C1FB;">*</strong>Elemento Final:
                            </label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltEfn" id="sltEfn" class="form-control select" title="Seleccione Elemento final" style="height: 30px" required>
                                    <?php
                                    echo "<option value=''>Elemento Final</option>";
                                    while ($filaEfn = mysqli_fetch_row($rsEfn)){
                                        echo "<option value='$filaEfn[0]'>$filaEfn[1]</option>";
                                    }
                                    ?>
                             </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Fecha Final:
                            </label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" type="text" name="fechaini" id="fechaini" placeholder="Fecha Final" autocomplete="off" style="width: 350px;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="sltInforme" class="col-sm-5 control-label">
                                <strong style="color:#03C1FB;">*</strong>Informe Por:
                            </label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                            <select name="sltInforme" id="sltInforme" class="form-control select" title="Seleccione el tipo de informe" style="height: 30px" required onchange="javascript:generarI()">
                                <option value="">Tipo Informe</option>              
                                <option value="0">Informe Completo</option>              
                                <option value="1">Informe Conteo Fisico</option>
                                <option value="2">Informe Por Grupos</option>
                            </select>
                            </div>
                        </div>
                     
                        <div class="form-group" style="margin-top: -0px;">
                            <label for="Efn" class="col-sm-5 col-md-5 col-lg-5 control-label">Tipo Archivo:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="" class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo" value="1" checked="checked"required onclick="javascript:generarI()">PDF</label>
                                <label for="" class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo" value="2" onclick="javascript:generarI()">EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Efn" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-primary"><span class="fa fa-send"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php' ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-notify.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        $(".select2, #sltEfn, #sltEin,#sltInforme").select2();
        function generarI(){
            console.log('aca');
            console.log($("#optArchivo").val());
            console.log(document.getElementById("optArchivo").checked==true);
            var opcion = document.getElementById('sltInforme').value;
            if(document.getElementById("optArchivo").checked==true){
                switch(opcion){
                    case('0'):
                        $('form').attr('action', 'informes/generar_INF_EXISTENCIAS_INVENTARIO.php');                       
                    break;
                    case('1'):
                        $('form').attr('action', 'informes/generar_INF_EXISTENCIAS_INVENTARIO_CON.php');    
                    break;
                    case('2'):
                        $('form').attr('action', 'informes_almacen/generar_INF_EXISTENCIAS_INVENTARIO_GRUPO.php?t=1');
                    break;
                }
            } else {
                switch(opcion){
                    case('0'):
                        $('form').attr('action', 'informes/generar_INF_EXIS_INVENTARIO_EXCEL.php');   
                    break;
                    case('1'):
                        $('form').attr('action', 'informes/generar_INF_EXIS_INVENTARIO_EXCEL_CON.php');    
                    break;
                    case('2'):
                        $('form').attr('action', 'informes_almacen/generar_INF_EXISTENCIAS_INVENTARIO_GRUPO.php?t=2');
                    break;
                }
            }
        }



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
            $("#fechaini").datepicker({changeMonth: true}).val();
        });
    </script>
</body>
</html>