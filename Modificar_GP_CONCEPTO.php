<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('jsonPptal/funcionesPptal.php');
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$tr         = tipo_cambio($compania);
$id         = " ";
$queryCond  = "";
$action = $_REQUEST['action'];

$id     = $_REQUEST["id"];
$_SESSION['url'] = 'Modificar_GP_CONCEPTO.php?id=' . $id;

$titulo = "";

$id = base64_decode($id);
if ($action == 1) {
    $titulo = "Modificar Concepto";
    $row = $con->Listar("SELECT c.id_unico, c.nombre,  tc.id_unico, tc.nombre,  
    top.id_unico,top.nombre, 
    pi.id_unico, pi.codi, pi.nombre, 
    fb.id_unico, fb.nombre, 
    c.alojamiento,
    ca.id_unico,
    ca.nombre,
    c.ajuste, 
    c.traduccion 
    FROM gp_concepto c 
    LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto=tc.id_unico 
    LEFT JOIN gp_tipo_operacion top ON c.tipo_operacion = top.id_unico 
    LEFT JOIN gf_plan_inventario pi ON c.plan_inventario = pi.id_unico 
    LEFT JOIN gp_factor_base fb ON c.factor_base = fb.id_unico 
    LEFT JOIN gp_concepto ca ON c.concepto_asociado = ca.id_unico 
    WHERE c.id_unico=$id");

    //Tipo concepto
    if (!empty($row[0][2])) {
        $tipoC = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_concepto WHERE id_unico != " . $row[0][2] . " ORDER BY nombre ASC");
    } else {
        $tipoC = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_concepto ORDER BY nombre ASC");
    }

    //Tipo Operación
    if (!empty($row[0][4])) {
        $tipoO = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_operacion WHERE id_unico != " . $row[0][4] . " ORDER BY nombre ASC");
    } else {
        $tipoO = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_operacion ORDER BY nombre ASC");
    }
} else {
    $titulo = "Registrar Concepto";
    $row = $con->Listar("SELECT c.id_unico, c.nombre,  tc.id_unico, tc.nombre,  
    top.id_unico,top.nombre, 
    pi.id_unico, pi.codi, pi.nombre, 
    fb.id_unico, fb.nombre, 
    c.alojamiento,
    ca.id_unico,
    ca.nombre,
    c.ajuste, 
    c.traduccion 
    FROM gp_concepto c 
    LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto=tc.id_unico 
    LEFT JOIN gp_tipo_operacion top ON c.tipo_operacion = top.id_unico 
    LEFT JOIN gf_plan_inventario pi ON c.plan_inventario = pi.id_unico 
    LEFT JOIN gp_factor_base fb ON c.factor_base = fb.id_unico 
    LEFT JOIN gp_concepto ca ON c.concepto_asociado = ca.id_unico");

    //Tipo concepto
    $tipoC = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_concepto ORDER BY nombre ASC");

    //Tipo Operación
    $tipoO = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_operacion ORDER BY nombre ASC");
}

//Plan inventario
if (empty($row[0][6])) {
    $planI = $con->Listar("SELECT id_unico, codi, nombre FROM gf_plan_inventario WHERE compania = $compania  ORDER BY nombre ASC");
} else {
    $planI = $con->Listar("SELECT id_unico, codi, nombre FROM gf_plan_inventario WHERE id_unico != " . $row[0][6] . " AND tienemovimiento = 1 AND compania = $compania ORDER BY codi ASC");
}

//Factor base
if (empty($row[0][9])) {
    $factorB = $con->Listar("SELECT id_unico, nombre FROM gp_factor_base ORDER BY nombre ASC");
} else {
    $factorB = $con->Listar("SELECT id_unico, nombre FROM gp_factor_base WHERE id_unico != " . $row[0][9] . " ORDER BY nombre ASC");
}

#Concepto Asociado 
if (empty($row[0][12])) {
    $concepto_a  = $con->Listar("SELECT id_unico, nombre FROM gp_concepto 
    WHERE  compania = $compania AND id_unico NOT IN (SELECT id_unico FROM gp_concepto WHERE concepto_asociado IS NOT NULL)");
} else {
    $concepto_a  = $con->Listar("SELECT id_unico, nombre FROM gp_concepto 
    WHERE id_unico != " . $row[0][12] . " AND compania = $compania AND id_unico NOT IN (SELECT id_unico FROM gp_concepto WHERE concepto_asociado IS NOT NULL)");
}
?>
<title><?php echo $titulo ?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
    label#TipoConcepto-error,
    #nombre-error,
    #TipoOperacion-error {
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
            rules: {
                param: {
                    required: true
                },
                mes: {
                    required: true
                },
                sltAnnio: {
                    required: true
                }
            }
        });

        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
</script>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px">
                <h2 align="center" class="tituloform"><?php echo $titulo ?></h2>
                <a href="LISTAR_GP_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Concepto:<?php echo  $row[0][5] ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="TipoConcepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="TipoConcepto" id="TipoConcepto" class="select2_single form-control" title="Seleccione Tipo Concepto" required="required">
                                        <?php if (!empty($row[0][3])) { ?>
                                            <option value="<?php echo $row[0][2] ?>"><?php echo ucwords(mb_strtolower($row[0][3])); ?></option>
                                        <?php } else { ?>
                                            <option value="">Seleccione el tipo concepto</option>
                                        <?php } ?>
                                        <?php
                                        for ($rowC = 0; $rowC < count($tipoC); $rowC++) {  ?>
                                            <option value="<?php echo $tipoC[$rowC][0] ?>"><?php echo ucwords((mb_strtolower($tipoC[$rowC][1])));
                                                                                        } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="TipoConcepto" id="TipoConcepto" class="select2_single form-control" title="Seleccione Tipo Concepto" required="required">
                                        <option value="">Seleccione el tipo concepto</option>
                                        <?php
                                        for ($rowC = 0; $rowC < count($tipoC); $rowC++) {  ?>
                                            <option value="<?php echo $tipoC[$rowC][0] ?>"><?php echo ucwords((mb_strtolower($tipoC[$rowC][1])));
                                                                                        } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[0][1])); ?>" required>
                                <?php } else { ?>
                                    <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="TipoOperacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="TipoOperacion" id="TipoOperacion" class="select2_single form-control" title="Seleccione Tipo Operación" required="required">
                                        <?php if (!empty($row[0][4])) { ?>
                                            <option value="<?php echo $row[0][4] ?>"><?php echo ucwords(mb_strtolower($row[0][5])); ?></option>
                                        <?php } else { ?>
                                            <option value="">Seleccione el tipo operación</option>
                                        <?php } ?>
                                        <?php
                                        for ($rowO = 0; $rowO < count($tipoO); $rowO++) {  ?>
                                            <option value="<?php echo $tipoO[$rowO][0] ?>"><?php echo ucwords((mb_strtolower($tipoO[$rowO][1])));
                                                                                        } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="TipoOperacion" id="TipoOperacion" class="select2_single form-control" title="Seleccione Tipo Operación" required="required">
                                        <option value="">Seleccione el tipo operación</option>
                                        <?php
                                        for ($rowO = 0; $rowO < count($tipoO); $rowO++) {  ?>
                                            <option value="<?php echo $tipoO[$rowO][0] ?>"><?php echo ucwords((mb_strtolower($tipoO[$rowO][1])));
                                                                                        } ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="planInventario" class="col-sm-5 control-label">Plan Inventario:</label>
                                <?php
                                if ($action == 1) { ?>
                                    <select name="planInventario" id="planInventario" class="select2_single form-control" title="Seleccione Plan Inventario">
                                        <option value="<?php echo $row[0][6] ?>"> <?php echo $row[0][7] . ' - ' . $row[0][8] ?> </option>
                                        <?php for ($rowI = 0; $rowI < count($planI); $rowI++) {  ?>
                                            <option value="<?php echo $planI[$rowI][0] ?>"><?php echo $planI[$rowI][1] . ' - ' . ucwords((mb_strtolower($planI[$rowI][2]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select name="planInventario" id="planInventario" class="select2_single form-control" title="Seleccione Plan Inventario">
                                        <option value=""> Seleccione el plan inventario </option>
                                        <?php for ($rowI = 0; $rowI < count($planI); $rowI++) {  ?>
                                            <option value="<?php echo $planI[$rowI][0] ?>"><?php echo $planI[$rowI][1] . ' - ' . ucwords((mb_strtolower($planI[$rowI][2]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top:-5px">
                                <label for="factorBase" class="col-sm-5 control-label">Factor Base:</label>
                                <?php if ($action == 1 && !empty($row[0][9])) { ?>
                                    <select name="factorBase" id="factorBase" class="select2_single form-control" title="Seleccione Factor Base">
                                            <option value="<?php echo $row[0][9] ?>"><?php echo $row[0][10] ?></option>
                                        <?php for ($rowF = 0; $rowF < count($factorB); $rowF++) {  ?>
                                            <option value="<?php echo $factorB[$rowF][0] ?>"><?php echo ucwords((mb_strtolower($factorB[$rowF][1]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select name="factorBase" id="factorBase" class="select2_single form-control" title="Seleccione Factor Base">

                                        <option value="">Seleccione el factor base</option>
                                        <?php for ($rowF = 0; $rowF < count($factorB); $rowF++) {  ?>
                                            <option value="<?php echo $factorB[$rowF][0] ?>"><?php echo ucwords((mb_strtolower($factorB[$rowF][1]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>

                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="alojamiento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Alojamiento:</label>
                                <div class="col-sm-4 col-md-4 col-md-4">
                                    <?php if ($action == 1) {
                                        if ($row[0][11] == 1) { ?>
                                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="1" checked>Sí</label>
                                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="2">No</label>
                                        <?php } else { ?>
                                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="1">Sí</label>
                                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="2" checked>No</label>
                                        <?php }
                                    } else { ?>
                                        <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="1">Sí</label>
                                        <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="2">No</label>
                                    <?php } ?>

                                </div>
                            </div>
                            <div class="form-group" style="margin-top:-5px;">
                                <label for="concepto_asociado" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Concepto Asociado:</label>

                                <?php if ($action == 1 && !empty($row[0][12])) { ?>
                                    <select name="concepto_asociado" id="concepto_asociado" class="select2_single form-control col-sm-1" title="Seleccione Concepto Asociado">
                                        <option value="<?php echo $row[0][12] ?>"><?php echo $row[0][13] ?></option>
                                        <option value=""> - </option>
                                        <?php
                                        for ($rowca = 0; $rowca < count($concepto_a); $rowca++) {  ?>
                                            <option value="<?php echo $concepto_a[$rowca][0] ?>"><?php echo ucwords((mb_strtolower($concepto_a[$rowca][1]))); ?></option>;
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select name="concepto_asociado" id="concepto_asociado" class="select2_single form-control col-sm-1" title="Seleccione Concepto Asociado">
                                        <option value="">Seleccione el concepto asociado</option>
                                        <option value=""> - </option>
                                        <?php
                                        for ($rowca = 0; $rowca < count($concepto_a); $rowca++) {  ?>
                                            <option value="<?php echo $concepto_a[$rowca][0] ?>"><?php echo ucwords((mb_strtolower($concepto_a[$rowca][1]))); ?></option>;
                                        <?php } ?>
                                    <?php } ?>
                                    </select>

                            </div>
                            <?php if ($tr != 0) { ?>
                                <div class="form-group" style="margin-top: -5px;">
                                    <label for="ajuste" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Ajuste:</label>
                                    <?php if ($action == 1) { ?>
                                        <input value="<?php echo $row[0][14] ?>" type="text" name="ajuste" id="ajuste" class="form-control" maxlength="100" title="Ingrese el Ajuste" placeholder="Ajuste">
                                    <?php } else { ?>
                                        <input type="text" name="ajuste" id="ajuste" class="form-control" maxlength="100" title="Ingrese el Ajuste" placeholder="Ajuste">
                                    <?php } ?>
                                </div>
                                <div class="form-group" style="margin-top: -15px;">
                                    <label for="traduccion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Traducción:</label>
                                    <?php if ($action == 1) { ?>
                                        <input value="<?php echo $row[0][15] ?>" type="text" name="traduccion" id="traduccion" class="form-control" onkeypress="return txtValida(event,'num_car')" title="Ingrese Traducción" placeholder="Traducción">
                                    <?php } else { ?>
                                        <input type="text" name="traduccion" id="traduccion" class="form-control" onkeypress="return txtValida(event,'num_car')" title="Ingrese Traducción" placeholder="Traducción">
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <input type="hidden" name="MM_insert">
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: -10px; margin-bottom: 0px; margin-left:0px"><?php echo $titulo ?></button>
                            </div>
                            </form>
                </div>
            </div>
            <div class="col-sm-6 col-sm-2" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -3px; ">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px; height:36px">Adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php if ($action == 1) { ?>
                                    <a href="GP_CONCEPTO_TARIFA.php?id=<?php echo base64_encode($id); ?>"><button class="btn btnInfo btn-primary">TARIFA</button></a><br />
                                <?php } else { ?>
                                    <a href="#"><button class="btn btnInfo btn-primary" disabled>TARIFA</button></a><br />
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>
</body>
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
<script type="text/javascript">
    function agregar() {
        jsShowWindowLoad('Agregando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "json/modificar_GP_CONCEPTOJson.php?action=2",
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
                        document.location = 'LISTAR_GP_CONCEPTO.php';
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
            url: "json/modificar_GP_CONCEPTOJson.php?action=3",
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
                        document.location = 'LISTAR_GP_CONCEPTO.php';
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