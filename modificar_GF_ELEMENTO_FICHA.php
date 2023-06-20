<?php
require_once('Conexion/conexionPDO.php');
require_once 'head.php';
$con = new ConexionPDO();

$action = $_REQUEST['action'];
$titulo = "";
$id_elemento_ficha = $_REQUEST['id_elemento_ficha'];

if ($action == 1) {
    $titulo = "Modificar Elemento Ficha";
    if (isset($_GET["id_elemento_ficha"])) {
        $id_elemento_ficha = base64_decode($id_elemento_ficha);
        $row = $con->Listar("SELECT ef.Id_Unico id, ef.Nombre Nombre, ef.TipoDato idTipoDato, td.Nombre tipoDato
                                    FROM gf_elemento_ficha ef, gf_tipo_dato td 
                                    WHERE ef.TipoDato=td.Id_Unico
                                    AND (ef.Id_Unico)= $id_elemento_ficha");
    }
    $tipoDato = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_dato WHERE Id_Unico != " . $row[0][2] . " ORDER BY Nombre ASC");
} else {
    $titulo = "Registrar Elemento Ficha";
    $row = $con->Listar("SELECT ef.Id_Unico id, ef.Nombre Nombre, ef.TipoDato idTipoDato, td.Nombre tipoDato
                                    FROM gf_elemento_ficha ef, gf_tipo_dato td 
                                    WHERE ef.TipoDato=td.Id_Unico");
    $tipoDato = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_dato WHERE Id_Unico != " . $row[0][2] . " ORDER BY Nombre ASC");
}

?>
<title><?php echo $titulo ?></title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<script type="text/javascript" src="js/select2.js"></script>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Elemento Ficha</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo $row[0][1] ?>" required>
                                <?php } else { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="tipoDato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo de Dato:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="tipoDato" id="tipoDato" style="width:27%; margin-right: -130px;" class="select2_single" title="Seleccione el tipo de dato" required>
                                        <option value="<?php echo $row[0][2] ?>"><?php echo $row[0][3] ?></option>
                                        <?php for ($rowTd = 0; $rowTd < count($tipoDato); $rowTd++) { ?>
                                            <option value="<?php echo $tipoDato[$rowTd][0] ?>"><?php echo ucwords(utf8_encode(strtolower($tipoDato[$rowTd][1])));
                                                                                            } ?></option>;
                                    </select>
                                <?php } else { ?>
                                    <select name="tipoDato" id="tipoDato" style="width:27%; margin-right: -130px;" class="select2_single " title="Seleccione el tipo de dato" required>
                                        <option value="">Seleccione el tipo de dato</option>
                                        <?php for ($rowTd = 0; $rowTd < count($tipoDato); $rowTd++) { ?>
                                            <option value="<?php echo $tipoDato[$rowTd][0] ?>" style="width:69% "><?php echo ucwords(utf8_encode(strtolower($tipoDato[$rowTd][1])));
                                                                                                                } ?></option>;
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
    <?php require_once 'footer.php'; ?>
    <script src="js/select/select2.full.js"></script>

    <script>
        $(document).ready(function() {
            $(".select2_single").select2({

                allowClear: true
            });
        });
    </script>
    <script>
        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "controller/controllerGFElementoFicha.php?action=insert",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información agregada correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
                            document.location = 'GF_ELEMENTO_FICHA.php';
                        })

                    } else {
                        $("#mensaje").html('No se ha podido agregar información');
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
                url: "controller/controllerGFElementoFicha.php?action=modify",
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
                            document.location = 'GF_ELEMENTO_FICHA.php';
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
    <script type="text/javascript">
        $(".select2").select2();

        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                errorElement: "em",
                errorPlacement: function(error, element) {
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass) {
                    var elem = $(element);
                    if (elem.hasClass('select2-offscreen')) {
                        $("#s2id_" + elem.attr("id")).addClass('has-error').removeClass('has-success');
                    } else {
                        $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                    }
                    if ($(element).attr('type') == 'radio') {
                        $(element.form).find("input[type=radio]").each(function(which) {
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight: function(element, errorClass, validClass) {
                    var elem = $(element);
                    if (elem.hasClass('select2-offscreen')) {
                        $("#s2id_" + elem.attr("id")).addClass('has-success').removeClass('has-error');
                    } else {
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                    }
                    if ($(element).attr('type') == 'radio') {
                        $(element.form).find("input[type=radio]").each(function(which) {
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });
    </script>
</body>

</html>