<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];
if ($action == 1) {
    $id_asoNat = "";
    $queryAsociadoNat = "";
    if (isset($_GET["id"])) {
        $id_asoNat = base64_decode(($_REQUEST["id"]));
        $row  = $con->Listar("SELECT T.Id_Unico,
                 TI.Id_Unico, 
                 TI.Nombre,
                 T.NumeroIdentificacion,
                 T.NombreUno,
                 T.NombreDos,
                 T.ApellidoUno,
                 T.ApellidoDos,         
                 TR.Id_Unico, 
                 TR.Nombre, 
                 T.email, 
                 z.id_unico, 
                 z.nombre, 
                 T.tarjeta_profesional 
        FROM gf_tercero T
        LEFT JOIN gf_tipo_identificacion TI  ON T.TipoIdentificacion = TI.Id_Unico
        LEFT JOIN gf_tipo_regimen TR  ON T.TipoRegimen = TR.Id_Unico 
        LEFT JOIN gf_zona z ON T.zona = z.id_unico 
        WHERE T.Id_Unico = '$id_asoNat'");
    }


    $_SESSION['id_tercero'] = $row[0][0];
    $_SESSION['perfil'] = "N"; //Natural.
    $_SESSION['url'] = "EDITAR_TERCERO_PROVEEDOR_NATURAL_2.php?id=" . (($_GET["id"]));
    $_SESSION['tipo_perfil'] = 'Asociado natural';
    #****** Tipo Identificación *********#
    $idt = 0;
    if (!empty($row[0][1])) {
        $idt = $row[0][1];
    }
    $ident  = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_identificacion 
        WHERE Id_Unico != $idt  ORDER BY Nombre ASC");

    #****** Tipo Régimen  *********#
    $idtr = 0;
    if (!empty($row[0][8])) {
        $idtr = $row[0][8];
    }
    $regimen = $con->Listar("SELECT Id_Unico, Nombre 
        FROM gf_tipo_regimen WHERE Id_Unico !=$idtr ORDER BY Nombre ASC");
}
if ($action == 2) {
    $_SESSION['perfil'] = "N"; //Natural.
    $_SESSION['url'] = "TerceroProveedorNatural2.php";

    #****** Tipo Identificación *********#
    $tipoI = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_identificacion ORDER BY Nombre ASC");

    #****** Tipo Régimen  *********#
    $regimen = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_regimen ORDER BY Nombre ASC");

    #****** Zona *********#
    $zona = $con->Listar("SELECT Id_Unico, Nombre 
  FROM gf_zona
  ORDER BY Nombre ASC");
}


?>
<?php if ($action == 1) { ?>
    <title>Modificar Proveedor Natural</title>
<?php }

?>
<?php if ($action == 2) { ?>
    <title>Registrar Proveedor Natural</title>
<?php }


?>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #tipoI-error,
    #numId-error,
    #correo-error,
    #primerN-error,
    #primerA-error {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
</style>
<script>
    $().ready(function() {
        var validator = $("#form").validate({
            ignore: "",
            errorPlacement: function(error, element) {
                $(element)
                    .closest("form")
                    .find("label[for='" + element.attr("id") + "']")
                    .append(error);
            },
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
</script>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="content row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px">
                <?php if ($action == 1) { ?>
                    <h2 align="center" class="tituloform">Modificar Proveedor Natural</h2>
                <?php }

                ?>
                <?php if ($action == 2) { ?>
                    <h2 align="center" class="tituloform">Registrar Proveedor Natural</h2>
                <?php }


                ?>

                <a href="LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[4] . ' ' . $row[6] ?></h5>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="perfil" value=5>
                        <input type="hidden" name="id" value="<?php echo $row[0][0]; ?>" />
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Identificación:</label>
                            <?php if ($action == 1) { ?>
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control" title="Seleccione el tipo identificación" required>
                                    <option value="<?php echo $row[0][1]; ?>"><?php echo ($row[0][2]); ?></option>
                                    <?php for ($i = 0; $i < count($ident); $i++) { ?>
                                        <option value="<?php echo $ident[$i][0] ?>"><?php echo ucwords((mb_strtolower($ident[$i][1])));
                                                                                } ?></option>;
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control" title="Seleccione el tipo identificación" required>
                                    <option value="">Tipo identificación</option>
                                    <?php for ($i = 0; $i < count($tipoI); $i++) { ?>
                                        <option value="<?php echo $tipoI[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoI[$i][1])));
                                                                                } ?></option>;
                                </select>
                            <?php }


                            ?>


                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <?php if ($action == 1) { ?>
                                <input type="number" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número identificación" onkeypress="return txtValida(event,'num')" value="<?php echo $row[0][3]; ?>" placeholder="Número identificación" required />
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="number" name="noIdent" id="noIdent" class="form-control" onblur="return existente()" onkeyup="this.value = this.value.slice(0,20)" maxlength="20" title="Ingrese el número identificación" onkeypress="return txtValida(event,'num')" placeholder="Número identificación" required>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Nombre:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[0][4]; ?>" placeholder="Primer Nombre" required>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')" placeholder="Primer Nombre" required>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoN" class="col-sm-5 control-label">Segundo Nombre:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[0][5]; ?>" placeholder="Segundo Nombre">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')" placeholder="Segundo Nombre">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Apellido:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[0][6]; ?>" placeholder="Primer Apellido" required>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')" placeholder="Primer Apellido" required>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoA" class="col-sm-5 control-label">Segundo Apellido:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[0][7]; ?>" placeholder="Segundo Apellido">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" placeholder="Segundo Apellido">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="tipoReg" class="col-sm-5 control-label">Tipo Régimen:</label>
                            <?php if ($action == 1) { ?>
                                <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Seleccione el tipo régimen">
                                    <?php
                                    if (!empty($row[0][8])) {
                                        echo '<option value="' . $row[0][8] . '">' . ucwords((mb_strtolower($row[0][9]))) . '</option>';
                                        echo '<option value=""> - </option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($regimen); $i++) {
                                        echo '<option value="' . $regimen[$i][0] . '">' . ucwords((mb_strtolower($regimen[$i][1]))) . '</option>';
                                    }
                                    ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Seleccione el tipo régimen">
                                    <option value="">Tipo Régimen</option>
                                    <?php for ($i = 0; $i < count($regimen); $i++) { ?>
                                        <option value="<?php echo $regimen[$i][0] ?>"><?php echo ucwords((mb_strtolower($regimen[$i][1])));
                                                                                    } ?></option>;
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <?php if ($action == 1) { ?>
                                <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" value="<?php echo $row[10] ?>">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <?php if ($action == 1) { ?>
                                <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            <?php }


                            ?>
                            
                        </div>
                        <input type="hidden" name="MM_insert">
                    </form>
                </div>
            </div>
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO CONTABLE
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_DIRECCION_TERCERO.php"><button class="btn btn-primary btnInfo" style="margin-bottom:10px">DIRECCIÓN</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO PRESUPUESTAL
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_CUENTA_BANCARIA_TERCERO.php"><button class="btn btnInfo btn-primary">CUENTA BANCARIA</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO<br />ALMACEN
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        TAREAS DE MANTENIMIENTO
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TELEFONO.php"><button class="btn btn-primary btnInfo">TELÉFONO</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        RETENCIONES EFECTUADAS
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php"><button class="btn btn-primary btnInfo">CONDICIÓN</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php"><button class="btn btn-primary btnInfo">PERFIL CONDICIÓN</button></a><br />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php require_once('footer.php'); ?>
        </div>
        <script src="js/select/select2.full.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/md5.js"></script>
        <script>
            $(document).ready(function() {
                $(".select2_single").select2({
                    allowClear: true
                });

            });

            function modificar() {
                jsShowWindowLoad('Modificando Datos ...');
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_tercerosJson.php?action=14",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if (response == 1) {
                            $("#mensaje").html('Información Modificada Correctamente');
                            $("#modalMensajes").modal("show");

                            document.location = 'LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php';

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

            function agregar() {
                jsShowWindowLoad('Agregando Datos ...');
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_tercerosJson.php?action=15",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if (response == 1) {
                            $("#mensaje").html('Información Modificada Correctamente');
                            $("#modalMensajes").modal("show");

                            document.location = 'LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php';

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
    </div>
</body>

</html>