<?php
require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';

$con = new ConexionPDO();
$action = $_REQUEST['action'];
$titulo = "";
$id = $_SESSION['id_tercero'];
//echo "el id es " . $id;

if ($_SESSION['perfil'] == "N") {
    //Consulta para el listado de registro de la tabla gf_tercero para naturales.
    $busq = $con->Listar("SELECT t.NombreUno || ' ' || t.NombreDos || ' ' || t.ApellidoUno || ' ' || t.ApellidoDos AS NOMBRE,
    ti.Nombre || ': ' || t.NumeroIdentificacion AS identificacion
    FROM gf_tercero t
    LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
    WHERE t.Id_Unico = $id");
} elseif ($_SESSION['perfil'] == "J") {
    //Consulta para el listado de registro de la tabla gf_tercero para jur�dicos.
    $busq = $con->Listar("SELECT t.razonsocial, ti.Nombre || ': ' || t.NumeroIdentificacion AS identificacion
    FROM gf_tercero t
    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
    WHERE t.Id_Unico = $id");
}

$datosTercero = $busq[0][0] . ' (' . $busq[0][1] . ')';

//TIPO DIRECCION
$Tdire = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_direccion ORDER BY Nombre ASC");

// CIUDAD
$ciu = $con->Listar("SELECT c.id_unico, c.nombre, d.nombre FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento = d.id_unico ORDER BY c.nombre ASC");

//DATOS TABLA
$direccion = $con->Listar("SELECT d.id_unico, d.direccion, td.Nombre, c.nombre, dep.nombre, d.tipo_direccion, d.ciudad_direccion, d.tercero 
    FROM gf_direccion d 
    LEFT JOIN gf_tipo_direccion td ON d.tipo_direccion = td.Id_Unico 
    LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico
    LEFT JOIN gf_departamento dep ON dep.id_unico= c.departamento 
    WHERE d.tercero = $id");

?>

<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>

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
<style>
    label#direccion-error,
    #tipodireccion-error,
    #ciudad-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }
</style>
<title>Registrar Dirección</title>

</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Registrar Dirección</h2>
                <a href="<?php echo "modificar_GF_BANCO_JURIDICA.php?action=1&id_bancoJur=" . base64_encode($id); ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(strtolower(($datosTercero))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" method="POST" class="form-inline" enctype="multipart/form-data" action="javascript:agregar()">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="tercero" value="<?php echo $id ?>">
                        <div class="form-group form-inline" style="margin-top:-20px; margin-left: -7px; width: 900px">

                            <label for="direccion" class=" control-label col-sm-2" style="width:88px; margin-top:20px"><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                            <input type="text" name="direccion" id="direccion" style="width:150px; margin-top: 15px;height: 30px" title="Ingrese la dirección" class="form-control col-sm-1" onkeypress="return txtValida(event,'direccion')" maxlength="150" placeholder="Dirección" required="required" />
                            <label for="tipodireccion" class="control-label col-sm-2" style="width:83px; margin-top: 15px;"><strong style="color:#03C1FB;">*</strong>Tipo Dirección:</label>
                            <select name="tipodireccion" id="tipodireccion" class=" select2_single form-control" title="Seleccione tipo dirección" required="required" style="width:150px; margin-top: 15px;height: 30px;">
                                <option value="">Tipo Dirección</option>
                                <?php
                                for ($t = 0; $t < count($Tdire); $t++) { ?>
                                    <option value="<?php echo $Tdire[$t][0] ?>"><?php echo ucwords((strtolower($Tdire[$t][1])));
                                                                            } ?></option>;
                            </select>
                            <input type="hidden" id="ciudad" name="ciudad" required="required" title="Seleccione ciudad">
                            <label for="ciudad" style="margin-left: 19px;"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <select name="ciudad2" id="ciudad2" class="select2_single form-control" title="Seleccione ciudad" required="required" onchange="llenar();" style="width:150px; margin-top: 15px;height: 30px;">
                                <option value="">Ciudad</option>
                                <?php
                                for ($rowC = 0; $rowC < count($ciu); $rowC++) {  ?>
                                    <option value="<?php echo $ciu[$rowC][0] ?>"><?php echo ucwords((strtolower($ciu[$rowC][1] . ' - ' . $ciu[$rowC][2])));
                                                                                } ?>
                                    </option>;
                            </select>

                            <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 15px;">Guardar</button>

                            <input type="hidden" name="MM_insert">
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Dirección</strong></td>
                                    <td class="cabeza"><strong>Tipo dirección</strong></td>
                                    <td class="cabeza"><strong>Ciudad</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Dirección</th>
                                    <th>Tipo dirección</th>
                                    <th>Ciudad</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                for ($i = 0; $i < count($direccion); $i++) {
                                ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $direccion[$i][0]; ?></td>
                                        <td align="center" class="campos">
                                            <a href="#" onclick="javascript:eliminarItem(<?php echo $direccion[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a onclick="javascript:modificarModal(<?php echo "'" . ucwords(strtolower($direccion[$i][1])) . "'," . $direccion[$i][5] . ',' . $direccion[$i][6] . ',' . $direccion[$i][0] . ',' . $direccion[$i][7]; ?>);"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(strtolower(($direccion[$i][1]))); ?></td>
                                        <td class="campos"><?php echo ucwords(strtolower(($direccion[$i][2]))); ?></td>
                                        <td class="campos"><?php echo ucwords(strtolower($direccion[$i][3] . ' - ' . $direccion[$i][4])); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-sm-2 text-center" align="center" style="margin-top:-15px;">
                <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
                <div align="center">
                    <a href="registrar_GF_TIPO_DIRECCION.php" class="btn btn-primary btnInfo">TIPO DIRECCIÓN</a>
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

    <script>
        function llenar() {
            var ciudad = document.getElementById('ciudad2').value;
            document.getElementById('ciudad').value = ciudad;
        }
    </script>

    <!--- MODIFICAR !-->
    <?php
    //TIPO DIRECCION
    $Tdire = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_direccion ORDER BY Nombre ASC");

    // CIUDAD
    $ciud = $con->Listar("SELECT c.id_unico, c.nombre, d.nombre FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento = d.id_unico ORDER BY c.nombre ASC");
    ?>
    <div class="modal fade" id="myModalUpdate" role="dialog" align="center">
        <div class="modal-dialog">

            <form name="form1" id="form1" method="POST" action="javascript:modificar()">
                <input type="hidden" name="idm" id="idm">
                <input type="hidden" name="tercerom" id="tercerom">
                <div class="modal-content client-form1">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                    </div>
                    <div class="modal-body " align="center">
                        <div class="form-group" align="left">
                            <label style="margin-left:160px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                            <input style="display:inline-block; width:250px; font-size: 0.9em; height: 30px;" type="text" name="direccionM" id="direccionM" title="Ingrese la dirección" class="form-control" onkeypress="return txtValida(event,'direccion')" maxlength="150" placeholder="Dirección" style=" height: 55px;width:250px;" required>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label align="right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Dirección:</label>
                            <select style="display:inline-block; width:250px; padding: 5px;  height:32px; font-size:0.9em;" name="tipod" id="tipod" class="select2_single form-control" title="Seleccione tipo dirección" required>
                                <?php for ($rowD = 0; $rowD < count($Tdire); $rowD++) { ?>
                                    <option value="<?php echo $Tdire[$rowD][0]; ?>">
                                        <?php echo ucwords((strtolower($Tdire[$rowD][1]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-top: 13px;">
                            <label align="right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px;  text-align-last:left;" name="ciudadm" id="ciudadm" class="select2_single form-control" title="Seleccione ciudad" required>
                                <?php for ($c = 0; $c < count($ciud); $c++) { ?>
                                    <option value="<?php echo $ciud[$c][0]; ?>">
                                        <?php echo ucwords((strtolower($ciud[$c][1] . ' - ' . $ciud[$c][2]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="hidden" id="id" name="id">
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
                        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--  MODAL para los mensajes del  modificar -->
    <div class="modal fade" id="myModal5" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Informaci&oacute;n modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal6" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La informaci&oacute;n no se ha podido modificar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!--  Mensajes de la opción  eliminar -->
    <div class="modal fade" id="myModal" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Dirección?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal1" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Informaci&oacute;n eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la informaci&oacute;n, el registro seleccionado est&aacute; siendo utilizada por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
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
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- MODIFICAR!-->
    <script type="text/javascript">
        function modificarModal(direccion, tipod, ciudad, id, tercero) {
            document.getElementById('direccionM').value = direccion;
            $("#tipod").val(tipod);
            $("#ciudadm").val(ciudad);
            document.getElementById('idm').value = id;
            document.getElementById('tercerom').value = tercero;
            $("#myModalUpdate").modal('show');
        }

        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificar_GF_DIRECCION_TERCEROJson.php?action=2",
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
                            document.location = 'GF_DIRECCION_TERCERO.php';
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
            var formData = new FormData($("#form1")[0]);

            $.ajax({
                type: 'POST',
                url: "json/modificar_GF_DIRECCION_TERCEROJson.php?action=3",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    if (response == 1) {
                        $("#myModalUpdate").modal('hide');
                        $("#myModal5").modal('show');
                        $("#ver5").click(function() {

                            $("#myModal5").modal('hide');
                            window.location.reload();
                        })
                    } else {
                        $("#myModal6").modal('show');
                        $("#ver6").click(function() {
                            $("#myModal6").modal('hide');
                        });

                    }
                }
            });
        }
    </script>

    <!-- Funci�n para la opcion eliminar -->
    <script type="text/javascript">
        function eliminarItem(id) {
            $("#myModal").modal('show');
            $("#ver").click(function() {
                jsShowWindowLoad('Eliminando Datos ...');
                $("#mymodal").modal('hide');
                var form_data = {
                    action: 1,
                    id: id
                };
                $.ajax({
                    type: 'POST',
                    url: "Json/modificar_GF_DIRECCION_TERCEROJson.php?action==1",
                    data: form_data,
                    success: function(response) {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if (response == 1) {
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#myModal1").modal('show');
                        } else {
                            $("#mensaje").html('No se puede eliminar la información, ya que la actividad posee Seguimiento(s)');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function() {
                                $("#modalMensajes").modal("hide");
                            })
                        }
                        $("#ver1").click(function() {
                            document.location = "GF_DIRECCION_TERCERO.php";
                        });
                        $("#ver2").click(function() {
                            document.location = "GF_DIRECCION_TERCERO.php";
                        });
                    }
                });
            });
        }
    </script>
</body>

</html>