<?php require_once('Conexion/conexion.php');
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$action = $_REQUEST['action'];
$con = new ConexionPDO();
require_once 'head.php';
$id_param = " ";
$action = $_REQUEST['action'];
if (isset($_GET["id_param"])) {
    $id_param = base64_decode(($_REQUEST["id_param"]));
    $row = $con->Listar("SELECT P.Id_Unico, 
    P.Anno, 
    P.SalarioMinimo, 
    P.MinDepreciacion, 
    P.UVT, 
    P.CajaMenor, 
    E.Id_Unico, 
    E.Nombre,
    P.minimacuantia, 
    P.menorcuantia, 
    P.menorcuantia_m, 
    P.mayorcuantia 
FROM gf_parametrizacion_anno P 
LEFT JOIN gf_estado_anno E ON P.EstadoAnno = E.Id_Unico 
WHERE P.Id_Unico ='$id_param'");
}

$estadoA = $con->Listar("SELECT Id_Unico, Nombre FROM gf_estado_anno ORDER BY Nombre ASC");

?>
<?php if ($action == 1) { ?>
    <title>Modificar Parametrizacion Año</title>
<?php }

?>
<?php if ($action == 2) { ?>
    <title>Registrar Parametrizacion Año</title>
<?php }


?>

</head>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px;">
            <?php if ($action == 1) { ?>
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Parametrización Año</h2>
            <?php }

            ?>
            <?php if ($action == 2) { ?>
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Parametrización Año</h2>
            <?php }


            ?>

            <a href="listar_GF_PARAMETRIZACION_ANNO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[0][1] ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class=" client-form">
                <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="datoAnno" id="datoAnno" value="<?php echo $row[0][1]; ?>" />
                    <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <?php if ($action == 1) { ?>
                            <input type="number" name="valor" id="valor" class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event, 'num')" placeholder="Año" value="<?php echo $row[0][1] ?>" required>
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="valor" id="valor" class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event, 'num')" placeholder="Año" required>
                        <?php }


                        ?>

                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="salariom" class="col-sm-5 control-label">Salario Mínimo:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="salariom" id="salariom" class="form-control" maxlength="19" title="Ingrese el salario mínimo" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Salario mínimo" value="<?php echo $row[0][2] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="salariom" id="salariom" class="form-control" maxlength="19" title="Ingrese el salario mínimo" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Salario mínimo">
                        <?php }


                        ?>


                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="minimod" class="col-sm-5 control-label">Mínimo Depreciación:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="minimod" id="minimod" class="form-control" maxlength="19" title="Ingrese el mínimo depreciación" onkeypress="return txtValida(event, 'dec', 'minimod', '2')" placeholder="Mínimo depreciación" value="<?php echo $row[0][3] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="minimod" id="minimod" class="form-control" maxlength="19" title="Ingrese el mínimo depreciación" onkeypress="return txtValida(event, 'dec', 'minimod', '2')" placeholder="Mínimo depreciación">
                        <?php }


                        ?>

                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="uvt" class="col-sm-5 control-label">UVT:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="uvt" id="uvt" class="form-control" maxlength="19" title="Ingrese UVT" onkeypress="return txtValida(event, 'dec', 'uvt', '2')" placeholder="UVT" value="<?php echo $row[0][4] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="uvt" id="uvt" class="form-control" maxlength="19" title="Ingrese UVT" onkeypress="return txtValida(event, 'dec', 'uvt', '2')" placeholder="UVT">
                        <?php }


                        ?>

                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="cajam" class="col-sm-5 control-label">Caja Menor:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name<input type="text" name="cajam" id="cajam" class="form-control" maxlength="19" title="Ingrese caja menor" onkeypress="return txtValida(event, 'dec', 'cajam', '2')" placeholder="Caja menor" value="<?php echo $row[0][5] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="cajam" id="cajam" class="form-control" maxlength="19" title="Ingrese caja menor" onkeypress="return txtValida(event, 'dec', 'cajam', '2')" placeholder="Caja menor">
                        <?php }


                        ?>

                    </div>
                    <div class="form-group">
                        <label for="estadoA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Año:</label>
                        <?php if ($action == 1) { ?>
                            <select name="estadoA" id="estadoA" class="form-control" title="Seleccione el estado año" required>
                                <?php
                                if (empty($row[0][7])) {
                                    echo '<option value=""> - </option>';
                                }
                                for ($i = 0; $i < count($estadoA); $i++) {
                                    if ($row[0][7] == $$estadoA[$i][0]) {    ?>
                                        <option value="<?php echo $estadoA[$i][0] ?>"><?php echo ucwords((strtolower($estadoA[$i][1]))); ?></option>
                                        <?php } else {
                                        if (($estadoA[$i][1]) == NULL) { ?>
                                            <option></option>
                                            <option value="<?php echo $estadoA[$i][0] ?>"><?php echo ucwords((strtolower($estadoA[$i][1]))); ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $estadoA[$i][0] ?>"><?php echo ucwords((strtolower($estadoA[$i][1]))); ?></option>
                                <?php }
                                    }
                                }
                                ?>
                            </select>
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <select name="estadoA" id="estadoA" class="form-control" title="Seleccione el estado año" required>
                                <option value="">Estado Año</option>
                                <?php for ($i = 0; $i < count($estadoA); $i++) { ?>
                                    <option value="<?php echo $estadoA[$i][0] ?>"><?php echo ucwords((strtolower($estadoA[$i][1])));
                                                                                } ?></option>;
                            </select>
                        <?php }


                        ?>

                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="min_c" class="col-sm-5 control-label">Mínima Cuantía:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="min_c" id="cajam" class="form-control" title="Ingrese Mínima Cuantía" onkeypress="return txtValida(event, 'dec', 'min_c', '2')" placeholder="Mínima Cuantía" value="<?php echo $row[0][8] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="min_c" id="cajam" class="form-control" title="Ingrese Mínima Cuantía" onkeypress="return txtValida(event, 'dec', 'min_c', '2')" placeholder="Mínima Cuantía">
                        <?php }


                        ?>

                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="menor_c" class="col-sm-5 control-label">Menor Cuantía:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="menorc" id="menorc" class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorc', '2')" placeholder="Menor Cuantía Desde" style="width: 150px; display:inline-block" value="<?php echo $row[0][9] ?>">
                            <input type="text" name="menorcm" id="menorcm" class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorcm', '2')" placeholder="Menor Cuantía Hasta" style="width: 150px; display:inline-block" value="<?php echo $row[0][10] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="menorc" id="menorc" class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorc', '2')" placeholder="Menor Cuantía Desde" style="width: 150px; display:inline-block">
                            <input type="text" name="menorcm" id="menorcm" class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorcm', '2')" placeholder="Menor Cuantía Hasta" style="width: 150px; display:inline-block">
                        <?php }


                        ?>

                    </div>
                    <div class=" form-group" style="margin-top: -10px;">
                        <label for="mayorc" class="col-sm-5 control-label">Mayor Cuantía:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="mayorc" id="mayorc" class="form-control" maxlength="19" title="Ingrese Mayor Cuantía" onkeypress="return txtValida(event, 'dec','mayorc', '2')" placeholder="Mayor Cuantía" value="<?php echo $row[0][11] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="mayorc" id="mayorc" class="form-control" maxlength="19" title="Ingrese Mayor Cuantía" onkeypress="return txtValida(event, 'dec','mayorc', '2')" placeholder="Mayor Cuantía">
                        <?php }


                        ?>

                    </div>
                    <div align="center" style="margin-top: -10px;">
                    <?php if ($action == 1) { ?>
                        <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        <?php }


                        ?>
                        
                    </div>
                    <div class="texto" style="display:none"></div>
                    <input type="hidden" name="MM_insert">
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>

<div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este Año ya existe.¿Desea actualizar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" id="ver2">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function modificar() {
        jsShowWindowLoad('Modificando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "Json/modificarParamAnnoJson.php?action=3",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                jsRemoveWindowLoad();
                console.log(response);
                if (response == 1) {
                    $("#mensaje").html('Información Modificada Correctamente');
                    $("#modalMensajes").modal("show");

                    document.location = 'listar_GF_PARAMETRIZACION_ANNO.php';

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
            url: "Json/modificarParamAnnoJson.php?action=2",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                jsRemoveWindowLoad();
                console.log(response);
                if (response == 1) {
                    $("#mensaje").html('Información Modificada Correctamente');
                    $("#modalMensajes").modal("show");

                    document.location = 'listar_GF_PARAMETRIZACION_ANNO.php';

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

    function existente() {
        var anno = document.form.valor.value;
        var result = '';

        if (anno == null || anno == '' || anno == "Año") {

            $("#myModal2").modal('show'); //consulta si el campo tiene algun valor, pero como es en el mdificar siempre va tener un dato, no se necesita

        } else { //se hace un envio por POST tomando el valor del camppo y consultando y como resultado me imprime un campo oculto con el ID y un modal preguntando si deseo cargar los datos.

            $.ajax({
                data: {
                    "anio": anno
                },
                type: "POST",
                url: "consultarParametrizacion.php",
                success: function(data) {

                    var res = data.split(";");

                    if (res[1] == 'true1') {
                        $('.texto').html(data);
                        $("#myModal1").modal('show');

                    }
                }
            });
        }
    }
</script>

<script type="text/javascript">
    $('#ver1').click(function() {
        var id = document.getElementById("id").value;
        console.log(id);
        document.location = 'modificar_GF_PARAMETRIZACION_ANNO.php?id_param=' + id;
    });
</script>

<script type="text/javascript">
    $('#ver2').click(function() {
        var anio = document.form.datoAnno.value;
        $("#valor").val(anio)
    });
</script>

</body>

</html>