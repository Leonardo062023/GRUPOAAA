<?php
// require_once('Conexion/conexion.php');
// require_once './head.php';
// $id = $_GET["id"];
// $queryCond = "SELECT id_unico, nombre FROM gp_tipo_concepto
//     WHERE md5(id_unico) = '$id'";

// $resul = $mysqli->query($queryCond);
// $row = mysqli_fetch_row($resul);
?>
<?php
require_once('Conexion/ConexionPDO.php');
require_once './head.php';
$con = new ConexionPDO();
$id = $_REQUEST['id'];
$id = base64_decode($id);
$action = $_REQUEST['action'];
$conexion = $_SESSION['conexion'];
$titulo = "";

if ($action == 1) {
    $titulo = "Modificar Tipo Concepto";
    $row = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_concepto WHERE id_unico = $id");
} else {
    $titulo = "Registrar Tipo Concepto";
    $row = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_concepto");
}
?>
<title><?php echo $titulo; ?></title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo; ?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <?php if ($action == 1) { ?>
                                <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="nombre" value="<?php echo ucwords((strtoupper($row[0][1]))) ?>" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                                <?php } else { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                                <?php } ?>
                            </div>

                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;"><?php echo $titulo; ?></button>
                            </div>
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
    <?php require_once './footer.php'; ?>
</body>
<script type="text/javascript">
    function agregar() {
        jsShowWindowLoad('Agregando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "json/modificarTipoConceptoJson.php?action=2",
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
                        document.location = 'listar_GP_TIPO_CONCEPTO.php';
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
            url: "json/modificarTipoConceptoJson.php?action=3",
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
                        document.location = 'listar_GP_TIPO_CONCEPTO.php';
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

</html>