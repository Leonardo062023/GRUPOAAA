<?php
/*require_once 'head.php';
require_once('Conexion/conexion.php');
$compania = $_SESSION['compania'];
$id = $_GET['id'];
$bus = "SELECT tm.id_unico, tm.nombre, tm.costea, c.id_unico, c.nombre, te.id_unico, te.nombre, tp.id_unico, tp.nombre, f.id_unico, f.nombre, tm.sigla
        FROM gf_tipo_movimiento tm 
        LEFT JOIN gf_clase c ON tm.clase=c.id_unico 
        LEFT JOIN gs_tipo_elemento te ON tm.tipoelemento = te.id_unico 
        LEFT JOIN gs_tipo_persona tp ON tm.tipopersona=tp.id_unico 
        LEFT JOIN gf_tipo_documento f ON tm.tipo_documento = f.id_unico 
        WHERE md5(tm.id_unico) = '$id'";
$busqueda = $mysqli->query($bus);
$rowB =  mysqli_fetch_row($busqueda);
//Clase
$claseB = "SELECT id_unico, nombre FROM gf_clase WHERE id_unico != $rowB[3] ORDER BY nombre ASC";
$clase = $mysqli->query($claseB);
//Elemento
$elementoB = "SELECT id_unico, nombre FROM gs_tipo_elemento WHERE id_unico != $rowB[5] ORDER BY nombre ASC";
$elemento = $mysqli->query($elementoB);
//Persona
$persB = "SELECT id_unico, nombre FROM gs_tipo_persona WHERE id_unico != $rowB[7] ORDER BY nombre ASC";
$persona = $mysqli->query($persB);
//Formato
$formatoB = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE id_unico != '$rowB[9]' AND compania = $compania ORDER BY nombre ASC";
$formato = $mysqli->query($formatoB);*/
?>
<?php

require_once('Conexion/conexionPDO.php');
require_once 'head.php';
$con = new ConexionPDO();

$action = $_REQUEST['action'];
$compania = $_SESSION['compania'];
$id = $_GET['id'];
$titulo = "";

if ($action == 1) {
    $titulo = "Modificar Tipo Movimiento";
    $id = base64_decode($id);
    $rowB = $con->Listar("SELECT tm.id_unico, tm.nombre, tm.costea, c.id_unico, c.nombre, te.id_unico, te.nombre, tp.id_unico, tp.nombre, f.id_unico, f.nombre, tm.sigla
    FROM gf_tipo_movimiento tm 
    LEFT JOIN gf_clase c ON tm.clase=c.id_unico 
    LEFT JOIN gs_tipo_elemento te ON tm.tipoelemento = te.id_unico 
    LEFT JOIN gs_tipo_persona tp ON tm.tipopersona=tp.id_unico 
    LEFT JOIN gf_tipo_documento f ON tm.tipo_documento = f.id_unico 
    WHERE tm.id_unico = $id");

    //Clase
    $clase = $con->Listar("SELECT id_unico, nombre FROM gf_clase WHERE id_unico != " . $rowB[0][3] . " ORDER BY nombre ASC");

    //Elemento
    $elemento = $con->Listar("SELECT id_unico, nombre FROM gs_tipo_elemento WHERE id_unico != " . $rowB[0][5] . " ORDER BY nombre ASC");

    //Persona
    $persona = $con->Listar("SELECT id_unico, nombre FROM gs_tipo_persona WHERE id_unico != " . $rowB[0][7] . " ORDER BY nombre ASC");

    //Formato
    $formato = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_documento WHERE id_unico != " . $rowB[0][9] . " AND compania = $compania ORDER BY nombre ASC");
} else {
    $titulo = "Registrar Tipo Movimiento";

    //Clase
    $clase = $con->Listar("SELECT id_unico, nombre FROM gf_clase ORDER BY nombre ASC");

    //Elemento
    $elemento = $con->Listar("SELECT id_unico, nombre FROM gs_tipo_elemento ORDER BY nombre ASC");

    //Persona
    $persona = $con->Listar("SELECT id_unico, nombre FROM gs_tipo_persona ORDER BY nombre ASC");

    //Formato
    $formato = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_documento WHERE compania = $compania ORDER BY nombre ASC");
}
?>
<title><?php echo $titulo ?></title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px"><?php echo $titulo ?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 0px" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()" >
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" value="<?php echo $rowB[0][0] ?>" name="id" id="id">
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="txtSigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="txtSigla" id="txtSigla" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required value="<?php echo mb_strtoupper($rowB[0][11]) ?>">
                                <?php } else { ?>
                                    <input type="text" name="txtSigla" id="txtSigla" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required value="<?php echo $rowB[0][1] ?>">
                                <?php } else { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="costea" class="col-sm-5 control-label" style="margin-top: -7px"><strong style="color:#03C1FB;">*</strong>Costea:</label>
                                <?php if ($action == 1) { ?>
                                    <?php if ($rowB[0][2] == '1') { ?>
                                        <input type="radio" name="costea" id="costea" value="1" checked>Sí
                                        <input type="radio" name="costea" id="costea" value="2">No
                                    <?php } else { ?>
                                        <input type="radio" name="costea" id="costea" value="1">Sí
                                        <input type="radio" name="costea" id="costea" value="2" checked>No
                                    <?php } ?>
                                <?php } else { ?>
                                    <input type="radio" name="costea" id="costea" value="1">Sí
                                    <input type="radio" name="costea" id="costea" value="2">No
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="clase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="clase" id="clase" class="select2_single  form-control col-sm-1" title="Seleccione clase" required="required">
                                        <option value="<?php echo $rowB[0][3] ?>"><?php echo ucwords(mb_strtolower($rowB[0][4])) ?></option>
                                        <?php
                                        for ($rowClase = 0; $rowClase < count($clase); $rowClase++) {  ?>
                                            <option value="<?php echo $clase[$rowClase][0] ?>"><?php echo ucwords((mb_strtolower($clase[$rowClase][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="clase" id="clase" class="select2_single  form-control col-sm-1" title="Seleccione clase" required="required">
                                        <option value="">Selecciona la clase</option>
                                        <?php
                                        for ($rowClase = 0; $rowClase < count($clase); $rowClase++) {  ?>
                                            <option value="<?php echo $clase[$rowClase][0] ?>"><?php echo ucwords((mb_strtolower($clase[$rowClase][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="elemento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Elemento:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="elemento" id="elemento" class="select2_single form-control col-sm-1" title="Seleccione tipo elemento" required="required">
                                        <option value="<?php echo $rowB[0][5] ?>"><?php echo ucwords(mb_strtolower($rowB[0][6])) ?></option>
                                        <?php
                                        for ($rowElem = 0; $rowElem < count($elemento); $rowElem++) { ?>
                                            <option value="<?php echo $elemento[$rowElem][0] ?>"><?php echo ucwords((mb_strtolower($elemento[$rowElem][1])));
                                                                                                } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="elemento" id="elemento" class="select2_single form-control col-sm-1" title="Seleccione tipo elemento" required="required">
                                        <option value=""> Seleccione el tipo elemento </option>
                                        <?php
                                        for ($rowElem = 0; $rowElem < count($elemento); $rowElem++) { ?>
                                            <option value="<?php echo $elemento[$rowElem][0] ?>"><?php echo ucwords((mb_strtolower($elemento[$rowElem][1])));
                                                                                                } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="persona" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Persona:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="persona" id="persona" class="select2_single form-control col-sm-1" title="Seleccione tipo persona" required="required">
                                        <option value="<?php echo $rowB[0][7] ?>"><?php echo ucwords(mb_strtolower($rowB[0][8])) ?></option>
                                        <?php for ($rowPers = 0; $rowPers < count($persona); $rowPers++) { ?>
                                            <option value="<?php echo $persona[$rowPers][0] ?>"><?php echo ucwords((mb_strtolower($persona[$rowPers][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="persona" id="persona" class="select2_single form-control col-sm-1" title="Seleccione tipo persona" required="required">
                                        <option value="">Seleccione el tipo persona</option>
                                        <?php for ($rowPers = 0; $rowPers < count($persona); $rowPers++) { ?>
                                            <option value="<?php echo $persona[$rowPers][0] ?>"><?php echo ucwords((mb_strtolower($persona[$rowPers][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="formato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Documento:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="formato" id="formato" class="select2_single form-control col-sm-1" title="Seleccione tipo documento" required="required">
                                        <option value="<?php echo $rowB[0][9] ?>"><?php echo ucwords(mb_strtolower($rowB[0][10])) ?></option>
                                        <?php
                                        for ($rowForm = 0; $rowForm < count($formato); $rowForm++) { ?>
                                            <option value="<?php echo $formato[$rowForm][0] ?>"><?php echo ucwords((strtolower($formato[$rowForm][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="formato" id="formato" class="select2_single form-control col-sm-1" title="Seleccione tipo documento" required="required">
                                        <option value="">Seleccione el tipo documento</option>
                                        <?php
                                        for ($rowForm = 0; $rowForm < count($formato); $rowForm++) { ?>
                                            <option value="<?php echo $formato[$rowForm][0] ?>"><?php echo ucwords((strtolower($formato[$rowForm][1])));
                                                                                            } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px"><?php echo $titulo ?></button>
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
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
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
                url: "json/modificar_GF_TIPO_MOVIMIENTOJson.php?action=2",
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
                            document.location = 'LISTAR_GF_TIPO_MOVIMIENTO.php';
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
                url: "json/modificar_GF_TIPO_MOVIMIENTOJson.php?action=3",
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
                            document.location = 'LISTAR_GF_TIPO_MOVIMIENTO.php';
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
</body>

</html>