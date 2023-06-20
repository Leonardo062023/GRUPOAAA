<?php
require_once('Conexion/conexionPDO.php');
require_once 'head.php';

$con = new ConexionPDO();
$id = $_GET['id'];
$action = $_REQUEST['action'];
$titulo = "";

if ($action == 1) {
    $titulo = "Modificar Chequera";
    if (isset($id)) {
        $id = base64_decode(($id));
        $_SESSION['url'] = 'Modificar_GF_DEPENDENCIA.php?id=' . $id;
        $row = $con->Listar("SELECT
            c.id_unico, 
            c.numerochequera, 
            c.numeroinicial, 
            c.numerofinal, 
            ec.id_unico, 
            ec.nombre, 
            cb.id_unico, 
            cb.numerocuenta, 
            cb.descripcion  
            FROM gf_chequera c 
            LEFT JOIN gf_estado_chequera ec ON c.estadochequera= ec.id_unico 
            LEFT JOIN gf_cuenta_bancaria cb ON cb.id_unico=c.cuentabancaria  
            WHERE c.id_unico= '$id'");
        //Estado Chequera
        $es = $con->Listar("SELECT id_unico, nombre FROM gf_estado_chequera WHERE id_unico != " . $row[0][4] . " ORDER BY nombre ASC");

        //Cuenta bancaria
        $cue = $con->Listar("SELECT id_unico, numerocuenta, descripcion FROM gf_cuenta_bancaria WHERE id_unico != " . $row[0][6] . " ORDER BY numerocuenta ASC");
    }
} else {
    $titulo = "Registrar Chequera";
    //Estado Chequera
    $es = $con->Listar("SELECT id_unico, nombre FROM gf_estado_chequera ORDER BY nombre ASC");
    //Cuenta bancaria
    $cue = $con->Listar("SELECT id_unico, numerocuenta, descripcion FROM gf_cuenta_bancaria ORDER BY numerocuenta ASC");
}
?>

<title>Modificar Chequera</title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo; ?> </h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <a href="LISTAR_GF_CHEQUERA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>

                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" action="javascript:modificar()" enctype="multipart/form-data">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" action="javascript:agregar()" enctype="multipart/form-data">
                            <?php } ?>

                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <input type="hidden" id="id" name="id" value="<?php echo $row[0][0] ?>">

                                <!-- Número de chequera-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="numero" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Chequera:</label>
                                    <?php if ($action == 1) { ?>
                                        <input style="display:inline" type="text" name="numero" id="numero" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número de chequera" placeholder="Número Chequera" required="required" value="<?php echo $row[0][1]; ?>">
                                    <?php } else { ?>
                                        <input style="display:inline" type="text" name="numero" id="numero" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número de chequera" placeholder="Número Chequera" required="required">
                                    <?php } ?>
                                </div>

                                <!-- Número inicial-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="numeroI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Inicial:</label>
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="numeroI" id="numeroI" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número inicial" placeholder="Número Inicial" required="required" value="<?php echo $row[0][2]; ?>">
                                    <?php } else { ?>
                                        <input type="text" name="numeroI" id="numeroI" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número inicial" placeholder="Número Inicial" required="required">
                                    <?php } ?>

                                </div>

                                <!-- Número final-->
                                <div class="form-group" id="numeroFinal" name="numeroFinal" style="margin-top: -10px;">
                                    <label for="numeroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Final:</label>

                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="numeroF" id="numeroF" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número final" placeholder="Número Final" required="required" value="<?php echo $row[0][3]; ?>">
                                    <?php } else { ?>
                                        <input type="text" name="numeroF" id="numeroF" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número final" placeholder="Número Final" required="required">
                                    <?php } ?>
                                </div>

                                <!-- Estado chequera-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                                    <?php if ($action == 1) { ?>
                                        <select name="estado" id="estado" title="Seleccione estado" required="required">
                                            <option value="<?php echo $row[0][4] ?>"><?php echo ucwords(strtolower($row[0][5])) ?></option>
                                            <?php for ($e = 0; $e < count($es); $e++) {  ?>
                                                <option value="<?php echo $es[$e][0]; ?>"><?php echo ucwords(strtolower($es[$e][1])); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="estado" id="estado" title="Seleccione estado" required="required">
                                            <option value="">Seleccione Estado</option>
                                            <?php for ($e = 0; $e < count($es); $e++) {  ?>
                                                <option value="<?php echo $es[$e][0]; ?>"><?php echo ucwords(strtolower($es[$e][1])); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>

                                <!-- Cuenta bancaria-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Bancaria:</label>
                                    <?php if ($action == 1) { ?>
                                        <select name="cuenta" id="cuenta" title="Seleccione cuenta bancaria" required="required">
                                            <option value="<?php echo $row[0][6] ?>"><?php echo ucwords(strtolower($row[0][7] . ' - ' . $row[0][8])); ?></option>
                                            <?php for ($c = 0; $c < count($cue); $c++) { ?>
                                                <option value="<?php echo $cue[$c][0]; ?>"><?php echo ucwords(strtolower($cue[$c][1] . ' - ' . $cue[$c][2])); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="cuenta" id="cuenta" title="Seleccione cuenta bancaria" required="required">
                                            <option value="">Seleccione Estado</option>
                                            <?php for ($c = 0; $c < count($cue); $c++) { ?>
                                                <option value="<?php echo $cue[$c][0]; ?>"><?php echo ucwords(strtolower($cue[$c][1] . ' - ' . $cue[$c][2])); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>

                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>

                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;"><?php echo $titulo ?></button>

                                </div>
                                <input type="hidden" name="MM_insert">
                            </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_single").select2({

                allowClear: true
            });


        });

        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificar_GF_CHEQUERAJson.php?action=2",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información agregada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
                            document.location = 'LISTAR_GF_CHEQUERA.php';
                        })

                    } else {
                        $("#mensaje").html('No Se Ha Podido Agregar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
                        })

                    }
                }
            });
        }

        function modificar() {
            jsShowWindowLoad('Modificando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificar_GF_CHEQUERAJson.php?action=3",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);

                    if (response == 1) {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
                            document.location = 'LISTAR_GF_CHEQUERA.php';
                        })
                    } else {
                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
                        })

                    }
                }
            });
        }
    </script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>