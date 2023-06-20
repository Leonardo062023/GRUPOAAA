<?php
// require_once 'head.php';
// require_once('Conexion/conexion.php');
// $id=$_GET['id'];
// //Busqueda registro a modificar
// $bus= "SELECT t.id_unico, 
//     u.id_unico, 
//     u.nombre, 
//     p.id_unico, 
//     pa.anno, 
//     c.nombre, 
//     p.fecha_inicial, 
//     p.fecha_final, 
//     e.id_unico, 
//     e.nombre, 
//     tt.id_unico, 
//     tt.nombre, 
//     t.valor, 
//     t.porcentaje_iva, 
//     t.porcentaje_impoconsumo 
//     FROM gp_tarifa t 
//     LEFT JOIN gp_uso u ON t.uso= u.id_unico 
//     LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
//     LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
//     LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
//     LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
//     LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
//     WHERE md5(t.id_unico)='$id'";
// $bus= $mysqli->query($bus);
// $row= mysqli_fetch_row($bus);

// //uso
// $bUso= "SELECT id_unico, nombre FROM gp_uso WHERE id_unico != '$row[1]' ORDER BY nombre";
// $bUso = $mysqli->query($bUso);
// //PERIODO
// $bPeriodo = "SELECT p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final
//         FROM gp_periodo p 
//         LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
//         LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
//         WHERE p.id_unico != '$row[3]' ORDER BY pa.anno ASC";
// $bPeriodo = $mysqli->query($bPeriodo);

// //Estrato
// $bEstrato= "SELECT id_unico, nombre FROM gp_estrato WHERE id_unico != '$row[8]' ORDER BY nombre ASC ";
// $bEstrato= $mysqli->query($bEstrato);

// //TIPO TARIFA
// $bTipoT= "SELECT id_unico, nombre FROM gp_tipo_tarifa WHERE id_unico != '$row[10]' ORDER BY nombre ASC";
// $bTipoT= $mysqli->query($bTipoT);
?>

<?php
require_once 'head.php';
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$id = $_REQUEST['id'];
$id = base64_decode($id);
$action = $_REQUEST['action'];
$conexion = $_SESSION['conexion'];
$titulo = "";
if ($action == 1) {
    $titulo = "Modificar Tarifa";
    //Busqueda registro a modificar
    $row = $con->Listar("SELECT t.id_unico, 
    u.id_unico, 
    u.nombre, 
    p.id_unico, 
    pa.anno, 
    c.nombre, 
    p.fecha_inicial, 
    p.fecha_final, 
    e.id_unico, 
    e.nombre, 
    tt.id_unico, 
    tt.nombre, 
    t.valor, 
    t.porcentaje_iva, 
    t.porcentaje_impoconsumo 
    FROM gp_tarifa t 
    LEFT JOIN gp_uso u ON t.uso= u.id_unico 
    LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
    LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
    LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
    LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
    WHERE t.id_unico=$id");


    //uso
    if (!empty($row[0][1])) {
        $bUso = $con->Listar("SELECT id_unico, nombre FROM gp_uso WHERE id_unico != " . $row[0][1] . " ORDER BY nombre");
    } else {
        $bUso = $con->Listar("SELECT id_unico, nombre FROM gp_uso  ORDER BY nombre");
    }

    //PERIODO
    if (!empty($row[0][3])) {
        $bPeriodo = $con->Listar("SELECT p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final
        FROM gp_periodo p 
        LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
        WHERE p.id_unico != " . $row[0][3] . " ORDER BY pa.anno ASC");
    } else {
        $bPeriodo = $con->Listar("SELECT p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final
        FROM gp_periodo p 
        LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
        ORDER BY pa.anno ASC");
    }

    //Estrato
    if (!empty($row[0][8])) {
        $bEstrato = $con->Listar("SELECT id_unico, nombre FROM gp_estrato WHERE id_unico != " . $row[0][8] . " ORDER BY nombre ASC ");
    } else {
        $bEstrato = $con->Listar("SELECT id_unico, nombre FROM gp_estrato ORDER BY nombre ASC ");
    }

    //TIPO TARIFA
    if (!empty($row[0][10])) {
        $bTipoT = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_tarifa WHERE id_unico != " . $row[0][10] . " ORDER BY nombre ASC");
    } else {
        $bTipoT = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_tarifa ORDER BY nombre ASC");
    }
} else {
    $titulo = "Registrar Tarifa";
    $row = $con->Listar("SELECT t.id_unico, 
    u.id_unico, 
    u.nombre, 
    p.id_unico, 
    pa.anno, 
    c.nombre, 
    p.fecha_inicial, 
    p.fecha_final, 
    e.id_unico, 
    e.nombre, 
    tt.id_unico, 
    tt.nombre, 
    t.valor, 
    t.porcentaje_iva, 
    t.porcentaje_impoconsumo 
    FROM gp_tarifa t 
    LEFT JOIN gp_uso u ON t.uso= u.id_unico 
    LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
    LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
    LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
    LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico");

    //uso
    $bUso = $con->Listar("SELECT id_unico, nombre FROM gp_uso ORDER BY nombre");

    //PERIODO
    $bPeriodo = $con->Listar("SELECT p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final
        FROM gp_periodo p 
        LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
        ORDER BY pa.anno ASC");

    //Estrato
    $bEstrato = $con->Listar("SELECT id_unico, nombre FROM gp_estrato ORDER BY nombre ASC ");

    //TIPO TARIFA
    $bTipoT = $con->Listar("SELECT id_unico, nombre FROM gp_tipo_tarifa ORDER BY nombre ASC");
}
?>
<title><?php echo $titulo ?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<style>
    .error-message {
        color: #155180;
        font-weight: normal;
        font-style: italic;
        display: none;
    }
</style>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top:-10px" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <!--Ingresa la información-->
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0] ?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="uso" class="col-sm-5 control-label">Uso:</label>
                                <?php if ($action == 1) { ?>
                                    <select id="uso" name="uso" class="select2_single form-control" title="Seleccione Uso">
                                        <?php if (!empty($row[0][1])) { ?>
                                            <option value="<?php echo $row[0][1] ?>"><?php echo ucwords(strtolower($row[0][2])) ?></option>
                                        <?php } else { ?>
                                            <option value="">Uso</option>
                                        <?php } ?>
                                        <?php for ($rowUso = 0; $rowUso < count($bUso); $rowUso++) { ?>
                                            <option value="<?php echo $bUso[$rowUso][0] ?>"><?php echo ucwords(strtolower($bUso[$rowUso][1])) ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select id="uso" name="uso" class="select2_single form-control" title="Seleccione Uso">
                                        <option value="">Uso</option>
                                        <?php for ($rowUso = 0; $rowUso < count($bUso); $rowUso++) { ?>
                                            <option value="<?php echo $bUso[$rowUso][0] ?>"><?php echo ucwords(strtolower($bUso[$rowUso][1])) ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>

                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="periodo" class="col-sm-5 control-label">Periodo:</label>
                                <?php if ($action == 1) { ?>
                                    <select id="periodo" name="periodo" class="select2_single form-control" title="Seleccione periodo">
                                        <?php if (!empty($row[0][3])) { ?>
                                            <option value="<?php echo $row[0][3] ?>"><?php echo $row[0][4] . ' - ' . ucwords(strtolower($row[0][5])) . ' - ' . date("d/m/Y", strtotime($row[0][6])) . ' - ' . date("d/m/Y", strtotime($row[0][7])); ?></option>
                                        <?php } else { ?>
                                            <option value="">Periodo</option>
                                        <?php } ?>
                                        <?php for ($rowPer = 0; $rowPer < count($bPeriodo); $rowPer++) { ?>
                                            <option value="<?php echo $bPeriodo[$rowPer][0] ?>"><?php echo $bPeriodo[$rowPer][1] . ' - ' . ucwords(strtolower($bPeriodo[$rowPer][2])) . ' - ' . date("d/m/Y", strtotime($bPeriodo[$rowPer][3])) . ' - ' . date("d/m/Y", strtotime($bPeriodo[$rowPer][4])); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select id="periodo" name="periodo" class="select2_single form-control" title="Seleccione periodo">
                                        <option value="">Periodo</option>
                                        <?php for ($rowPer = 0; $rowPer < count($bPeriodo); $rowPer++) { ?>
                                            <option value="<?php echo $bPeriodo[$rowPer][0] ?>"><?php echo $bPeriodo[$rowPer][1] . ' - ' . ucwords(strtolower($bPeriodo[$rowPer][2])) . ' - ' . date("d/m/Y", strtotime($bPeriodo[$rowPer][3])) . ' - ' . date("d/m/Y", strtotime($bPeriodo[$rowPer][4])); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="estrato" class="col-sm-5 control-label">Estrato:</label>
                                <?php if ($action == 1) { ?>
                                    <select id="estrato" name="estrato" class="select2_single form-control" title="Seleccione estrato">
                                        <?php if (!empty($row[0][8])) { ?>
                                            <option value="<?php echo $row[0][8] ?>"><?php echo ucwords(strtolower($row[0][9])) ?></option>
                                        <?php } else { ?>
                                            <option value="">Estrato</option>
                                        <?php } ?>
                                        <?php
                                        for ($rowEst = 0; $rowEst < count($bEstrato); $rowEst++) { ?>
                                            <option value="<?php echo $bEstrato[$rowEst][0] ?>"><?php echo ucwords(strtolower($bEstrato[$rowEst][1])); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <select id="estrato" name="estrato" class="select2_single form-control" title="Seleccione estrato">
                                        <option value="">Estrato</option>
                                        <?php
                                        for ($rowEst = 0; $rowEst < count($bEstrato); $rowEst++) { ?>
                                            <option value="<?php echo $bEstrato[$rowEst][0] ?>"><?php echo ucwords(strtolower($bEstrato[$rowEst][1])); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="tipoT" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Tarifa:</label>
                                <?php if ($action == 1) { ?>
                                    <select name="tipoT" id="tipoT" class="select2_single form-control" title="Seleccione Tipo Tarifa">
                                        <?php if (!empty($row[0][10])) { ?>
                                            <option value="<?php echo $row[0][10] ?>"><?php echo ucwords(strtolower($row[0][11])); ?></option>
                                        <?php } else { ?>
                                            <option value="">Tipo tarifa</option>

                                        <?php } ?>
                                        <?php
                                        for ($rowTipo = 0; $rowTipo < count($bTipoT); $rowTipo++) {  ?>
                                            <option value="<?php echo $bTipoT[$rowTipo][0] ?>"><?php echo ucwords(strtolower($bTipoT[$rowTipo][1]));
                                                                                            } ?></option>
                                    </select>
                                <?php } else { ?>
                                    <select name="tipoT" id="tipoT" class="select2_single form-control" title="Seleccione Tipo Tarifa">
                                        <option value="">Tipo Tarifa</option>
                                        <?php
                                        for ($rowTipo = 0; $rowTipo < count($bTipoT); $rowTipo++) {  ?>
                                            <option value="<?php echo $bTipoT[$rowTipo][0] ?>"><?php echo ucwords(strtolower($bTipoT[$rowTipo][1]));
                                                                                            } ?></option>
                                    </select>
                                <?php } ?>
                                <span id="perfil-error" class="error-message col-sm-5 control-label" style="margin-top: -18px;">Seleccione el tipo tarifa</span>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="valor" id="valor" value="<?php echo $row[0][12] ?>" class="form-control" maxlength="17" title="Ingrese el valor" placeholder="Valor" onkeypress="return txtValida(event,'decimales')" required>
                                <?php } else { ?>
                                    <input type="text" name="valor" id="valor" class="form-control" maxlength="17" title="Ingrese el valor" placeholder="Valor" onkeypress="return txtValida(event,'decimales')" required>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="porcIva" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje IVA:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="porcIva" id="porcIva" value="<?php echo $row[0][13] ?>" class="form-control" maxlength="5" title="Ingrese el porcentaje Iva" placeholder="Porcentaje IVA" onkeypress="return validarNum1(event, true)" required>
                                <?php } else { ?>
                                    <input type="text" name="porcIva" id="porcIva" class="form-control" maxlength="5" title="Ingrese el porcentaje Iva" placeholder="Porcentaje IVA" onkeypress="return validarNum1(event, true)" required>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="porcIm" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje Impoconsumo:</label>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="porcIm" id="porcIm" value="<?php echo $row[0][14] ?>" class="form-control" maxlength="5" title="Ingrese el porcentaje impoconsumo" placeholder="Porcentaje Impoconsumo" onkeypress="return validarNum2(event, true)" required>
                                <?php } else { ?>
                                    <input type="text" name="porcIm" id="porcIm" class="form-control" maxlength="5" title="Ingrese el porcentaje impoconsumo" placeholder="Porcentaje Impoconsumo" onkeypress="return validarNum2(event, true)" required>
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
</body>

</html>
<script src="js/select/select2.full.js"></script>
<script type="text/javascript">
    function agregar() {
        var tipoSelect = document.getElementById("tipoT");
        var tipoError = document.getElementById("perfil-error");
        if (tipoSelect.value === "") {
            tipoError.style.display = "block";
        } else {
            tipoError.style.display = "none";
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificar_GP_TARIFAJson.php?action=2",
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
                            document.location = 'LISTAR_GP_TARIFA.php';
                        })

                    } else if(response==3) {
                        $("#mensaje").html('No se ha podido agregar información. Este registro ya existe.');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function() {
                            $("#modalMensajes").modal("hide");
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
    }

    function modificar() {
        var tipoSelect = document.getElementById("tipoT");
        var tipoError = document.getElementById("perfil-error");
        if (tipoSelect.value === "") {
            tipoError.style.display = "block";
        } else {
            tipoError.style.display = "none";
            jsShowWindowLoad('Modificando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificar_GP_TARIFAJson.php?action=3",
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
                            document.location = 'LISTAR_GP_TARIFA.php';
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
    }
</script>
<script>
    $(document).ready(function() {
        $(".select2_single").select2({
            allowClear: true
        });
    });
</script>
<script>
    var validarNum1 = function(event) {
        event = event || window.event;
        var charCode = event.keyCode || event.which;
        var first = (charCode <= 57 && charCode >= 48);
        var numero = document.getElementById('porcIva').value;
        var char = parseFloat(String.fromCharCode(charCode));
        var num = parseFloat(numero + char);
        var com = parseFloat(100);

        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if (dec <= 3) {
            if (num < com) {
                if (charCode == 46) {
                    var element = event.srcElement || event.target;
                    if (element.value.indexOf('.') == -1) {
                        return (charCode = 46);
                    } else {
                        return first;
                    }
                } else {
                    return first;
                }
            } else {
                if (num <= com) {
                    return first;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }



    }
</script>
<script>
    var validarNum2 = function(event) {
        event = event || window.event;
        var charCode = event.keyCode || event.which;
        var first = (charCode <= 57 && charCode >= 48);
        var numero = document.getElementById('porcIm').value;
        var char = parseFloat(String.fromCharCode(charCode));
        var num = parseFloat(numero + char);
        var com = parseFloat(100);

        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if (dec <= 3) {
            if (num < com) {
                if (charCode == 46) {
                    var element = event.srcElement || event.target;
                    if (element.value.indexOf('.') == -1) {
                        return (charCode = 46);
                    } else {
                        return first;
                    }
                } else {
                    return first;
                }
            } else {
                if (num <= com) {
                    return first;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }



    }
</script>