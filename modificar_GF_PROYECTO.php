<!-- Llamado a la cabecera del formulario -->
<?php require_once('Conexion/conexion.php');

require_once 'head.php';
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$action = $_REQUEST['action'];
$con = new ConexionPDO();
$id_proy = base64_decode($_REQUEST["id_proy"]);
$row = $con->Listar("SELECT Id_Unico, Nombre, codigo, codigo_bpin FROM gf_proyecto  WHERE Id_Unico ='$id_proy'");

?>
<?php if ($action == 1) { ?>
    <title>Modificar Proyecto</title>
<?php }

?>
<?php if ($action == 2) { ?>
    <title>Registrar Proyecto</title>
<?php }


?>

</head>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <?php if ($action == 1) { ?>
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Proyecto</h2>
            <?php }

            ?>
            <?php if ($action == 2) { ?>
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Proyecto</h2>
            <?php }


            ?>

            <a href="listar_GF_PROYECTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>

            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $row[1]; ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $id_proy ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="codigo" id="codigo" class="form-control" maxlength="150" title="Ingrese el Código" onkeypress="return txtValida(event, 'num_car')" placeholder="Código" required value="<?= $row[0][2] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="codigo" id="codigo" class="form-control" maxlength="150" title="Ingrese el Código" onkeypress="return txtValida(event, 'num_car')" placeholder="Código" required>
                        <?php }


                        ?>
                        
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required value="<?= $row[0][1] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
                        <?php }


                        ?>
                        
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="codigobpin" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código BPIN:</label>
                        <?php if ($action == 1) { ?>
                            <input type="text" name="codigobpin" id="codigobpin" class="form-control" maxlength="150" title="Ingrese el Código BPIN" onkeypress="return txtValida(event, 'num_car')" placeholder="Código BPIN" required value="<?= $row[0][3] ?>">
                        <?php }

                        ?>
                        <?php if ($action == 2) { ?>
                            <input type="text" name="codigobpin" id="codigobpin" class="form-control" maxlength="150" title="Ingrese el Código BPIN" onkeypress="return txtValida(event, 'num_car')" placeholder="Código BPIN" required>
                        <?php }


                        ?>
                        
                    </div>
                    <div align="center">
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
    </div>
</div>
<?php require_once 'footer.php'; ?>
<script type="text/javascript">
    function modificar() {
        jsShowWindowLoad('Modificando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "Json/modificarProyectoJson.php?action=3",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                jsRemoveWindowLoad();
                console.log(response);
                if (response == 1) {
                    $("#mensaje").html('Información Modificada Correctamente');
                    $("#modalMensajes").modal("show");

                    document.location = 'listar_GF_PROYECTO.php';

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
            url: "Json/modificarProyectoJson.php?action=2",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                jsRemoveWindowLoad();
                console.log(response);
                if (response == 1) {
                    $("#mensaje").html('Información Modificada Correctamente');
                    $("#modalMensajes").modal("show");

                    document.location = 'listar_GF_PROYECTO.php';

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