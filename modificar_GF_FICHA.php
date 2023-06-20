<?php
/*require_once 'head.php';
require_once('Conexion/conexion.php');
//consulta para cargar la informacion guardada con ese id
$id = " ";
$queryCond = "";
if (isset($_GET["id"])) {
    $id = (($_GET["id"]));
    $queryCond = "SELECT id_unico, descripcion FROM gf_ficha  WHERE md5(id_unico) = '$id'";
}
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);
$_SESSION['url'] = "modificar_GF_FICHA.php?id=" . (($_GET["id"]));*/
?>
<?php
require_once('Conexion/conexionPDO.php');
require_once 'head.php';
$con = new ConexionPDO();

$action = $_REQUEST['action'];
$titulo = "";

if ($action == 1) {
    $titulo = "Modificar Ficha";
    if (isset($_GET["id"])) {
        $id = base64_decode($_GET["id"]);
        $row = $con->Listar("SELECT id_unico, descripcion FROM gf_ficha  WHERE id_unico = '$id'");
    }
    $_SESSION['url'] = "modificar_GF_FICHA.php?id=" . (($_GET["id"]));
} else {
    $titulo = "Registrar Ficha";
    $_SESSION['url'] = "modificar_GF_FICHA.php?id=" . (($_GET["id"]));
}

?>
<!--Titulo de la página-->
<title><?php echo $titulo ?></title>
<style>
    .disabled-link {
        pointer-events: none;
        /* Evita que se pueda hacer clic en el enlace */
        color: gray;
        /* Cambia el color del enlace a gris para simular que está desactivado */
        text-decoration: none;
        /* Elimina cualquier decoración del enlace */
        cursor: default;
        /* Cambia el cursor a predeterminado para indicar que no se puede hacer clic */
    }
</style>

</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <!--titulo de formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px"><?php echo $titulo ?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php if ($action == 1) { ?>
                                <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">
                            <?php } ?>
                            <!--Carga los datos para la modificación-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="150" title="Ingrese la descripción" onkeypress="return txtValida(event,'car')" placeholder="Descripción" value="<?php echo ucwords(strtolower($row[0][1])); ?>" required>
                                <?php } else { ?>
                                    <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="150" title="Ingrese la descripción" onkeypress="return txtValida(event,'car')" placeholder="Descripción" value="" required>
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
            <div class="col-sm-8 col-sm-1">
                <table class="tablaC table-condensed" style="margin-top: -22px;">
                    <thead>
                        <tr>
                        <tr>
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>
                        </tr>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="margin-bottom: 1px;" id="div">
                                    <?php if ($action == 1) { ?>
                                        <a class="btn btnConsultas" href="registrar_GF_FICHA_INVENTARIO.php?ficha=<?php echo $id; ?>" id="linkMovE">
                                            FICHA<br />INVENTARIO
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btnConsultas disabled link" href="registrar_GF_FICHA_INVENTARIO.php?ficha=<?php echo $id; ?>" id="linkMovE">
                                            FICHA<br />INVENTARIO
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
    <script>
        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "controller/controllerGFFicha.php?action=insert",
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
                            document.location = 'listar_GF_FICHA.php';
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
                url: "controller/controllerGFFicha.php?action=modify",
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
                            document.location = 'listar_GF_FICHA.php';
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