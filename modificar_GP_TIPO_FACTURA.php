<?php
// require_once('head.php');
// require_once('Conexion/conexion.php');
// $compania = $_SESSION['compania'];
// $sql = "SELECT tpf.id_unico,tpf.nombre, UPPER(tpf.prefijo), 
//     cf.id_unico, cf.nombre,	
//     tpc.id_unico, UPPER(tpc.sigla), tpc.nombre, 
//     tr.id_unico, tr.nombre, 
//     tm.id_unico, UPPER(tm.sigla), tm.nombre,
//     tpf.sigue_consecutivo,
//     tpf.servicio,
//     tpf.xDescuento,
//     tpf.automatico, 
//     tc.id_unico, tc.nombre, 
//     tpf.facturacion_e 
// FROM gp_tipo_factura tpf 
// LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpf.tipo_comprobante 
// LEFT JOIN gp_tipo_pago tr ON tr.id_unico = tpf.tipo_recaudo 
// LEFT JOIN gp_clase_factura cf ON tpf.clase_factura = cf.id_unico
// LEFT JOIN gf_tipo_movimiento tm ON tpf.tipo_movimiento = tm.id_unico 
// LEFT JOIN gf_tipo_cambio tc ON tpf.tipo_cambio = tc.id_unico 
// WHERE  md5(tpf.id_unico) ='" . $_GET['id'] . "'";
// $resultado = $mysqli->query($sql);
// $rowdd = mysqli_fetch_row($resultado);
?>
<?php
@session_start();
require_once('head.php');
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$id = $_REQUEST['id'];
$id = base64_decode($id);
$compania = $_SESSION['compania'];
$action = $_REQUEST['action'];
$conexion = $_SESSION['conexion'];
$titulo = "";

if ($action == 1) {
    $titulo = "Modificar Tipo Factura";
    $rowdd = $con->Listar("SELECT tpf.id_unico,tpf.nombre, UPPER(tpf.prefijo), 
    cf.id_unico, cf.nombre,	
    tpc.id_unico, UPPER(tpc.sigla), tpc.nombre, 
    tr.id_unico, tr.nombre, 
    tm.id_unico, UPPER(tm.sigla), tm.nombre,
    tpf.sigue_consecutivo,
    tpf.servicio,
    tpf.xDescuento,
    tpf.automatico, 
    tc.id_unico, tc.nombre, 
    tpf.facturacion_e 
    FROM gp_tipo_factura tpf 
    LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpf.tipo_comprobante 
    LEFT JOIN gp_tipo_pago tr ON tr.id_unico = tpf.tipo_recaudo 
    LEFT JOIN gp_clase_factura cf ON tpf.clase_factura = cf.id_unico
    LEFT JOIN gf_tipo_movimiento tm ON tpf.tipo_movimiento = tm.id_unico 
    LEFT JOIN gf_tipo_cambio tc ON tpf.tipo_cambio = tc.id_unico 
    WHERE  tpf.id_unico = $id");
} else {
    $titulo = "Registrar Tipo Factura";
    // $rowdd = $con->Listar("SELECT tpf.id_unico,tpf.nombre, UPPER(tpf.prefijo), 
    // cf.id_unico, cf.nombre,	
    // tpc.id_unico, UPPER(tpc.sigla), tpc.nombre, 
    // tr.id_unico, tr.nombre, 
    // tm.id_unico, UPPER(tm.sigla), tm.nombre,
    // tpf.sigue_consecutivo,
    // tpf.servicio,
    // tpf.xDescuento,
    // tpf.automatico, 
    // tc.id_unico, tc.nombre, 
    // tpf.facturacion_e 
    // FROM gp_tipo_factura tpf 
    // LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpf.tipo_comprobante 
    // LEFT JOIN gp_tipo_pago tr ON tr.id_unico = tpf.tipo_recaudo 
    // LEFT JOIN gp_clase_factura cf ON tpf.clase_factura = cf.id_unico
    // LEFT JOIN gf_tipo_movimiento tm ON tpf.tipo_movimiento = tm.id_unico 
    // LEFT JOIN gf_tipo_cambio tc ON tpf.tipo_cambio = tc.id_unico");
}
?>
<title><?php echo $titulo ?></title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<link rel="stylesheet" href="css/desing.css">
<style>
    .client-form input[type='text'] {
        width: 100%;
    }

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
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin: -2px 4px 5px;"><?php echo $titulo ?></h2>
                <a href="listar_GP_TIPO_FACTURA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <?php if ($action == 1) { ?>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $rowdd[0][1]; ?></h5>
                <?php } else { ?>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Registrar Tipo Factura</h5>
                <?php } ?>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -5px" class="client-form">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
                            <?php } ?>
                            <p align="center" style="margin-bottom: 15px; margin-top: 25px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <?php echo '<input type="hidden" name="id" id="id" value="' . $rowdd[0][0] . '">'; ?>
                            <div class="form-group">
                                <label for="nombre" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required value="<?php echo $rowdd[0][1]; ?>">
                                    <?php } else { ?>
                                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
                                    <?php } ?>
                                </div>
                                <label for="prefijo" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Prefijo:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="prefijo" id="prefijo" class="form-control" maxlength="10" title="Ingrese el prefijo" onkeypress="return txtValida(event,'car_sin')" placeholder="Prefijo" style="text-transform:uppercase;" required value="<?php echo $rowdd[0][2]; ?>">
                                    <?php } else { ?>
                                        <input type="text" name="prefijo" id="prefijo" class="form-control" maxlength="10" title="Ingrese el prefijo" onkeypress="return txtValida(event,'car_sin')" placeholder="Prefijo" style="text-transform:uppercase;" required>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="sltClase" class="col-sm-2 col-md-2 col-lg-2 control-label">Clase Factura:</label>
                                <div class="col-sm-4 col-md-4 col-md-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="sltClase" id="sltClase" class="form-control">
                                            <?php if (empty($rowdd[0][3])) {
                                                $idc = 0; ?>
                                                <option value=""> - </option>
                                            <?php } else {
                                                $idc = $rowdd[0][3]; ?>
                                                <option value="<?php echo $rowdd[0][3] ?>"><?php echo $rowdd[0][4] ?></option>
                                            <?php }
                                            $html = "";
                                            $row  = $con->Listar("SELECT id_unico, nombre FROM gp_clase_factura 
                                        WHERE id_unico != $idc ORDER BY nombre ASC");
                                            foreach ($row as $fila) {
                                                $html .= "<option value='$fila[0]'>$fila[1]</option>";
                                            }
                                            echo $html;
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="sltClase" id="sltClase" class="form-control">
                                            <option value="">Clase Factura</option>
                                            <?php
                                            $html = "";
                                            $row = $con->Listar("SELECT id_unico, nombre FROM gp_clase_factura
                                         ORDER BY nombre ASC");
                                            foreach ($row as $fila) {
                                                $html .= "<option value='$fila[0]'>$fila[1]</option>";
                                            }
                                            echo $html;
                                            ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="tipoC" class="col-sm-1 col-md-1 col-lg-1 control-label">Tipo comprobante:</label>
                                <div class="col-sm-4 col-md-4 col-md-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control">
                                            <?php
                                            if (empty($rowdd[0][5])) {
                                                $idtc = 0; ?>
                                                <option value=""> - </option>
                                            <?php } else {
                                                $idtc = $rowdd[0][5]; ?>
                                                <option value="<?php echo $rowdd[0][5] ?>"><?php echo  $rowdd[0][6] . ' - ' . $rowdd[0][7] ?></option>';
                                            <?php }
                                            $resultT = $con->Listar("SELECT id_unico,nombre,sigla 
                                        FROM gf_tipo_comprobante 
                                        WHERE  niif != 1 AND compania = $compania AND id_unico != $idtc AND clasecontable in (9,10,15,16)");
                                            for ($t = 0; $t < count($resultT); $t++) { ?>
                                                <option value="<?php echo $resultT[$t][0] ?>"><?php echo $resultT[$t][2] . " - " . ucwords(mb_strtolower($resultT[$t][1])) ?></option>";
                                            <?php }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control">
                                            <option value="">Tipo Comprobante</option>
                                            <?php
                                            $resultT = $con->Listar("SELECT id_unico,nombre,sigla 
                                        FROM gf_tipo_comprobante 
                                        WHERE  niif != 1 AND compania = $compania AND clasecontable in (9,10,15,16)");
                                            for ($t = 0; $t < count($resultT); $t++) { ?>
                                                <option value="<?php echo $resultT[$t][0] ?>"><?php echo $resultT[$t][2] . " - " . ucwords(mb_strtolower($resultT[$t][1])) ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipoR" class="col-sm-2 col-md-2 col-lg-2 control-label">Tipo Recaudo:</label>
                                <div class="col-sm-4 col-md-4 col-md-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="tipoR" id="tipoR" title="Tipo Recaudo" class="form-control">
                                            <?php
                                            if (empty($rowdd[0][8])) {
                                                $idR = 0; ?>
                                                <option value=""> - </option>
                                            <?php } else {
                                                $idR = $rowdd[0][8]; ?>
                                                <option value="<?php echo $rowdd[0][8] ?>"><?php echo $rowdd[0][9] ?></option>
                                            <?php }
                                            $resultT = $con->Listar("SELECT id_unico,UPPER(nombre) FROM gp_tipo_pago 
                                        WHERE compania = $compania AND id_unico != $idR");
                                            for ($rowT = 0; $rowT < count($resultT); $rowT++) { ?>
                                                <option value="<?php echo $resultT[$rowT][0] ?>"><?php echo $resultT[$rowT][1] ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="tipoR" id="tipoR" title="Tipo Recaudo" class="form-control">
                                            <option value="">Tipo Recaudo</option>
                                            <?php
                                            $resultT = $con->Listar("SELECT id_unico,UPPER(nombre) FROM gp_tipo_pago 
                                        WHERE compania = $compania");
                                            for ($rowT = 0; $rowT < count($resultT); $rowT++) { ?>
                                                <option value="<?php echo $resultT[$rowT][0] ?>"><?php echo $resultT[$rowT][1] ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="sltMov" class="col-sm-1 col-md-1 col-lg-1 control-label">Tipo Movimiento:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="sltMov" id="sltMov" title="Tipo Movimiento" class="form-control">
                                            <?php
                                            $html = "";
                                            if (empty($rowdd[0][10])) {
                                                $idtm = 0; ?>
                                                <option value=""> - </option>
                                            <?php } else {
                                                $idtm = $rowdd[0][10]; ?>
                                                <option value="<?php echo $rowdd[0][10] ?>"><?php echo $rowdd[0][11] . ' - ' . $rowdd[0][12] ?></option>
                                            <?php }
                                            switch ($conexion) {
                                                case 1:
                                                    $rst = $con->Listar("SELECT id_unico, CONCAT_WS(' ', UPPER(sigla), nombre) 
                                                FROM gf_tipo_movimiento WHERE clase = 3 
                                                AND compania = $compania AND id_unico != $idtm ORDER BY sigla ASC");

                                                    break;
                                                case 2:
                                                    $rst = $con->Listar("SELECT id_unico, UPPER(sigla) || ' ' || nombre AS nombre_completo
                                                FROM gf_tipo_movimiento
                                                WHERE clase = 3
                                                AND compania = $compania
                                                AND id_unico != $idtm
                                                ORDER BY sigla ASC;
                                                ");
                                                    break;
                                                default:
                                                    break;
                                            }
                                            for ($row = 0; $row < count($rst); $row++) {
                                                $html .= "<option value='$rst[$row][0]'>$rst[$row][1]</option>";
                                            }
                                            echo $html;
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="sltMov" id="sltMov" title="Tipo Movimiento" class="form-control">
                                            <?php
                                            $html = ""; ?>
                                            <option value="">Tipo Movimiento</option>
                                            <?php
                                            switch ($conexion) {
                                                case 1:
                                                    $rst = $con->Listar("SELECT id_unico, CONCAT_WS(' ', UPPER(sigla), nombre)
                                                FROM gf_tipo_movimiento WHERE clase = 3
                                                AND compania = $compania ORDER BY sigla ASC");
                                                    break;
                                                case 2:
                                                    $rst = $con->Listar("SELECT id_unico, UPPER(sigla) || ' ' || nombre AS nombre_completo
                                                FROM gf_tipo_movimiento
                                                WHERE clase = 3
                                                AND compania = $compania
                                                ORDER BY sigla ASC");
                                                    break;
                                                default:
                                                    # code...
                                                    break;
                                            }
                                            for ($row = 0; $row < count($rst); $row++) {
                                                $html .= "<option value='" . $rst[$row][0] . "'> " . $rst[$row][1] . "</option>";
                                            }
                                            echo $html; ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="consecutivo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Sigue Consecutivo:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <?php if ($rowdd[0][13] == 1) { ?>
                                            <label for="consecutivo1" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo1" value="1" checked>Sí</label>
                                            <label for="consecutivo2" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo2" value="2">No</label>
                                        <?php } else { ?>
                                            <label for="consecutivo1" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo1" value="1">Sí</label>
                                            <label for="consecutivo2" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo2" value="2" checked>No</label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label for="consecutivo1" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo1" value="1">Sí</label>
                                        <label for="consecutivo2" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo2" value="2">No</label>
                                    <?php } ?>
                                </div>
                                <label for="servicio" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Servicio:</label>
                                <div class="col-sm-4 col-md-4 col-md-4">
                                    <?php if ($action == 1) { ?>
                                        <?php if ($rowdd[0][14] == 1) { ?>
                                            <label for="serv1" class="radio-inline"><input type="radio" name="serv" id="serv1" value="1" checked>Sí</label>
                                            <label for="serv2" class="radio-inline"><input type="radio" name="serv" id="serv2" value="2">No</label>
                                        <?php } else { ?>
                                            <label for="serv1" class="radio-inline"><input type="radio" name="serv" id="serv1" value="1">Sí</label>
                                            <label for="serv2" class="radio-inline"><input type="radio" name="serv" id="serv2" value="2" checked>No</label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label for="serv1" class="radio-inline"><input type="radio" name="serv" id="serv1" value="1">Sí</label>
                                        <label for="serv2" class="radio-inline"><input type="radio" name="serv" id="serv2" value="2">No</label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-md-4 col-lg-4" style="margin-top: -12px; margin-left: 5px">
                                    <span id="perfil-error" class="error-message"><b>*Seleccione el consecutivo</b></span>
                                </div>
                                <div class="col-sm-4 col-md-4 col-md-4 ">
                                    <span id="perfil-error1" class="error-message" style="margin-top: -12px; margin-left: 48%;"><b>*Seleccione el servicio</b></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lblDescuento" class="col-sm-2 col-md-2 col-lg-2 control-label">Aplica descuento?</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <?php if (empty($rowdd[0][15])) { ?>
                                            <input type="checkbox" name="optXDescuento" id="optXDescuento" value="1" disabled>
                                        <?php } else { ?>
                                            <input type="checkbox" name="optXDescuento" id="optXDescuento" value="1" checked="true">
                                        <?php } ?>
                                    <?php } else { ?>
                                        <input type="checkbox" name="optXDescuento" id="optXDescuento" value="1">
                                    <?php } ?>
                                </div>
                                <label for="lblDescuento" class="col-sm-1 col-md-1 col-lg-1 control-label">Automático?</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <?php if ($rowdd[0][16] == 1) { ?>
                                            <label for="optAutS" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutS" value="1" checked>Si</label>
                                            <label for="optAutN" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutN" value="2">No</label>
                                        <?php } else { ?>
                                            <label for="optAutS" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutS" value="1">Si</label>
                                            <label for="optAutN" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutN" value="2" checked>No</label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label for="optAutS" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutS" value="1">Si</label>
                                        <label for="optAutN" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutN" value="2">No</label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sltTipoCambio" class="col-sm-2 col-md-2 col-lg-2 control-label">Tipo Cambio:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="sltTipoCambio" id="sltTipoCambio" title="Tipo Cambio" class="form-control">
                                            <?php
                                            $html = "";
                                            if (empty($rowdd[0][17])) {
                                                $idtc = 0;
                                                $html .= '<option value=""> - </option>';
                                            } else {
                                                $idtc = $rowdd[0][17];
                                                $html .= '<option value="' . $rowdd[0][17] . '">' . $rowdd[0][18] . '</option>';
                                                $html .= "<option value=''>Tipo Cambio</option>";
                                            }

                                            $rst = $con->Listar("SELECT id_unico,sigla,  nombre FROM gf_tipo_cambio WHERE id_unico != $idtc");
                                            for ($row = 0; $row < count($rst); $row++) {
                                                $html .= "<option value='" . $rst[$row][0] . "'>" . $rst[$row][1] . " - " . $rst[$row][2] . "</option>";
                                            }
                                            echo $html;
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="sltTipoCambio" id="sltTipoCambio" title="Tipo Cambio" class="form-control">
                                            <?php
                                            $html = "";
                                            $html .= "<option value=''>Tipo Cambio</option>";
                                            $rst = $con->Listar("SELECT id_unico,sigla,  nombre FROM gf_tipo_cambio");
                                            for ($row = 0; $row < count($rst); $row++) {
                                                $html .= "<option value='" . $rst[$row][0] . "'>" . $rst[$row][1] . " - " . $rst[$row][2] . "</option>";
                                            }
                                            echo $html;
                                            ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="consecutivo" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Facturación Electrónica:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <?php if ($rowdd[0][19] == 1) { ?>
                                            <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="1" checked>Sí</label>
                                            <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="2">No</label>
                                        <?php } else { ?>
                                            <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="1">Sí</label>
                                            <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="2" checked>No</label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="1">Sí</label>
                                        <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="2">No</label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                </div>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <span id="perfil-error2" class="error-message" style="margin-top: -12px; margin-left: 48%;"><b>*Seleccione la facturación</b></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="no" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                                <div class="col-sm-6 col-md-6 col-lg-6 text-right">
                                    <button type="submit" class="btn btn-primary borde-sombra"><?php echo $titulo ?></button>
                                </div>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/script.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript">
        function agregar() {
            var consecutivo1 = document.getElementById("consecutivo1");
            var consecutivo2 = document.getElementById("consecutivo2");
            var serv1 = document.getElementById("serv1");
            var serv2 = document.getElementById("serv2");
            // var optfe = document.getElementById("optfe");

            var perfilError = document.getElementById("perfil-error");
            var perfilError1 = document.getElementById("perfil-error1");
            // var perfilError2 = document.getElementById("perfil-error2");

            if ((consecutivo1.checked || consecutivo2.checked) && (serv1.checked || serv2.checked) ) {
                perfilError.style.display = "none";
                perfilError1.style.display = "none";
                // perfilError2.style.display = "none";
                jsShowWindowLoad('Agregando Datos ...');
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "json/modificarTipoFacturaJson.php?action=2",
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
                                document.location = 'listar_GP_TIPO_FACTURA.php';
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
            } else {
                perfilError.style.display = "block";
                perfilError1.style.display = "block";
                // perfilError2.style.display = "block";
            }
        }

        function modificar() {
            jsShowWindowLoad('Modificando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "json/modificarTipoFacturaJson.php?action=3",
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
                            document.location = 'listar_GP_TIPO_FACTURA.php';
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
    <script>
        $("#tipoC, .select, #sltMov, #sltClase, #sltTipoCambio").select2({
            allowClear: true
        });
        $("#tipoR").select2({
            allowClear: true
        });

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
                        $(elem).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
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
                        $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
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

        $("#sltClase").change(function(e) {
            let clase = e.target.value;
            if (clase == 3) {
                $("#optXDescuento").removeAttr("disabled");
            } else {
                $("#optXDescuento").attr("disabled", true);
            }

            let xClase = "";
            if (clase == 7) {
                xClase = 2;
            } else {
                xClase = 3;
            }

            $.get("access.php?controller=Devolutivos&action=tipoMovimientoClase", {
                    clase: xClase
                },
                function(data) {
                    $("#sltMov").html(data).trigger("change");
                }
            );
        });
    </script>
</body>

</html>