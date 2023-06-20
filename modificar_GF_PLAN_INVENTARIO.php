<?php
/*require('Conexion/conexion.php');
require('head.php');
require_once('./modelAlmacen/inventario.php');
@session_start();
$compania = $_SESSION['compania'];
$plan = new inventario();
    list($id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre, $unidad, $unidf, $pred, $tipa, $tanombre, $fid_unico, $fnombre, $numHij, $xCantidad, $xConcepto, $ivaDesc)
        = array(0, 0, "", "", 0, "", 0, 0, 0, 0, "", 0, 0, 0, 0, 0);
    if (!empty($_GET["id_plan_inv"])) {
        $id_plan_inv = $_GET["id_plan_inv"];
        $queryPlanInv = "SELECT    pi.id_unico, pi.codi, pi.Nombre, pi.tienemovimiento, 
                    pi.tipoinventario, ti.nombre, pi.unidad, uf.nombre,
                    pi.predecesor, pi.tipoactivo, ta.nombre, f.id_unico, 
                    f.descripcion, pi.xCantidad, pi.xFactura, 
                    pi.codigo_barras, pi.iva_descontable
          FROM      gf_plan_inventario AS pi
          LEFT JOIN gf_tipo_inventario AS ti ON pi.tipoinventario = ti.id_unico
          LEFT JOIN gf_unidad_factor   AS uf ON pi.unidad         = uf.id_unico
          LEFT JOIN gf_tipo_activo     AS ta ON pi.tipoactivo     = ta.id_unico
          LEFT JOIN gf_ficha           AS f  ON pi.ficha          = f.id_unico
          WHERE     md5(pi.id_unico) = '$id_plan_inv'";
        $resultado = $mysqli->query($queryPlanInv);
        $row = mysqli_fetch_row($resultado);
        list(
            $id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre,  $unidad,
            $unidf, $pred, $tipa, $tanom, $fid_unico, $fnombre, $xCantidad, $xConcepto, $codigo_barras, $ivaDesc
        ) =
            array(
                $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8],
                $row[9], $row[10], $row[11], $row[12], $row[13], $row[14], $row[15], $row[16]
            );*/
?>
<?php
@session_start();
require_once('Conexion/conexionPDO.php');
require_once 'head.php';
require_once('./modelAlmacen/inventario.php');

$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$action = $_REQUEST['action'];
$titulo = "";
$conexion = $_SESSION['conexion'];
//echo base64_decode($_REQUEST["id_plan_inv"]);
if ($action == 1) {
    $titulo = "Modificar Plan Inventario";
    $plan = new inventario();
    list(
        $id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre, $unidad, $unidf, $pred, $tipa,
        $tanombre, $fid_unico, $fnombre, $numHij, $xCantidad, $xConcepto, $ivaDesc
    ) = array(0, 0, "", "", 0, "", 0, 0, 0, 0, "", 0, 0, 0, 0, 0);
    if (!empty($_REQUEST["id_plan_inv"])) {
        $id_plan_inv = base64_decode($_REQUEST["id_plan_inv"]);
        $row  = $con->Listar("SELECT pi.id_unico, pi.codi, pi.Nombre, pi.tienemovimiento, pi.tipoinventario, ti.nombre, pi.unidad, uf.nombre, pi.predecesor,
        pi.tipoactivo,ta.nombre, f.id_unico, f.descripcion, pi.xCantidad, pi.xFactura, pi.codigo_barras, pi.iva_descontable
        FROM gf_plan_inventario pi
        LEFT JOIN gf_tipo_inventario ti ON pi.tipoinventario = ti.id_unico
        LEFT JOIN gf_unidad_factor uf ON pi.unidad = uf.id_unico
        LEFT JOIN gf_tipo_activo ta ON pi.tipoactivo = ta.id_unico
        LEFT JOIN gf_ficha f ON pi.ficha = f.id_unico
        WHERE pi.id_unico = $id_plan_inv ");

        list(
            $id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre,  $unidad,
            $unidf, $pred, $tipa, $tanom, $fid_unico, $fnombre, $xCantidad, $xConcepto, $codigo_barras, $ivaDesc
        ) =
            array(
                $row[0][0], $row[0][1], $row[0][2], $row[0][3], $row[0][4], $row[0][5], $row[0][6], $row[0][7], $row[0][8],
                $row[0][9], $row[0][10], $row[0][11], $row[0][12], $row[0][13], $row[0][14], $row[0][15], $row[0][16]
            );
    }
} else {
    $titulo = "Registrar Plan Inventario";

    $row  = $con->Listar("SELECT pi.id_unico, pi.codi, pi.Nombre, pi.tienemovimiento, pi.tipoinventario, ti.nombre, pi.unidad, uf.nombre, pi.predecesor,
        pi.tipoactivo,ta.nombre, f.id_unico, f.descripcion, pi.xCantidad, pi.xFactura, pi.codigo_barras, pi.iva_descontable
        FROM gf_plan_inventario pi
        LEFT JOIN gf_tipo_inventario ti ON pi.tipoinventario = ti.id_unico
        LEFT JOIN gf_unidad_factor uf ON pi.unidad = uf.id_unico
        LEFT JOIN gf_tipo_activo ta ON pi.tipoactivo = ta.id_unico
        LEFT JOIN gf_ficha f ON pi.ficha = f.id_unico");
}
?>
<title><?php echo $titulo ?></title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<link rel="stylesheet" href="css/desing.css">
<style type="text/css" media="screen">
    #form>.form-group {
        margin-bottom: 10px !important;
    }

    .client-form input[type="text"] {
        width: 100%;
    }
</style>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-8 col-md-8 col-lg-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin: 0px 4px 5px;"><?php echo $titulo; ?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" cl1ass="client-form col-sm-12 col-md-12 col-lg-12">
                    <?php if ($action == 1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
                        <?php } else { ?>
                            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">

                            <?php } ?>
                            <p align="center" style="margin-bottom: 10px; margin-top: 4px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?php echo $id_unico; ?>">
                            <input type="hidden" name="id_predec" id="id_predec" value="<?php echo base64_decode($pred); ?>">
                            <div class="form-group predc">
                                <label for="predecesor" class="col-sm-2 col-md-2 col-lg-2 control-label">Predecesor:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php
                                    if ($action == 1) {
                                        switch ($conexion) {
                                            case 1:
                                                $row_pr = $con->Listar("SELECT gfpi.id_unico, CONCAT(gfpi.codi, ' ', gfpi.nombre) AS plan
                                                FROM gf_plan_inventario gfpi
                                                WHERE gfpi.id_unico = $pred
                                                ORDER BY gfpi.codi ASC");
                                                break;
                                            case 2:
                                                $row_pr =  $con->Listar("SELECT gfpi.id_unico, gfpi.codi || ' ' || gfpi.nombre AS plan
                                                FROM gf_plan_inventario gfpi
                                                WHERE gfpi.id_unico = $pred
                                                ORDER BY gfpi.codi ASC");
                                                break;
                                            default:
                                                break;
                                        }
                                    ?>
                                        <select name="predecesor" id="predecesor" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese el predecesor">
                                            <option value="<?php echo $row_pr[0][0] ?>"> <?php echo ucwords(mb_strtolower($row_pr[0][1])) ?></option>

                                            <?php
                                            $res_pr = $con->Listar("SELECT gfpi.id_unico, gfpi.codi || ' ' || gfpi.nombre AS plan
                                            FROM gf_plan_inventario gfpi
                                            LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico
                                            WHERE gfpi.tienemovimiento = 1 AND gfpi.id_unico != $pred AND gfpi.compania = $compania
                                            ORDER BY gfpi.codi ASC");

                                            for ($i = 0; $i < count($res_pr); $i++) { ?>
                                                <option value="<?php echo $res_pr[$i][0] ?>"> <?php echo ucwords(mb_strtolower($res_pr[$i][1])) ?> </option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="predecesor" id="predecesor" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese el predecesor">
                                            <option value="">Predecesor</option>;

                                            <?php
                                            switch ($conexion) {
                                                case 1:
                                                    $res_pr = $con->Listar("SELECT gfpi.id_unico, CONCAT(gfpi.codi, ' ', gfpi.nombre) AS plan 
                                                    FROM gf_plan_inventario gfpi
                                                    LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico
                                                    WHERE gfpi.tienemovimiento = 1 AND gfpi.compania = $compania
                                                    ORDER BY gfpi.codi ASC");
                                                    break;
                                                case 2:
                                                    $res_pr = $con->Listar("SELECT gfpi.id_unico, gfpi.codi || ' ' || gfpi.nombre AS plan 
                                                    FROM gf_plan_inventario gfpi
                                                    LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico
                                                    WHERE gfpi.tienemovimiento = 1 AND gfpi.compania = $compania
                                                    ORDER BY gfpi.codi ASC");
                                                    break;
                                                default:
                                                    break;
                                            }


                                            for ($row_pr = 0; $row_pr < count($res_pr); $row_pr++) { ?>
                                                <option value="<?php echo $res_pr[$row_pr][0] ?>"> <?php echo ucwords(mb_strtolower($res_pr[$row_pr][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="codigo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Código:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" placeholder="Código" value="<?php echo $codi ?>">
                                    <?php } else { ?>
                                        <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" placeholder="Código" required>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nombre" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($nombre)); ?>" required>
                                    <?php } else { ?>
                                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                                    <?php } ?>
                                </div>
                                <label for="movimiento" class="col-sm-3 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Movimiento:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php
                                    if ($action == 1) {
                                        switch ($tienemovimiento) {
                                            case '1': ?>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="si" value="2" /> Sí&nbsp &nbsp </label>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="no" value="1" checked /> No </label>
                                            <?php
                                                break;
                                            case '2': ?>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="si" value="2" checked /> Sí&nbsp &nbsp </label>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="no" value="1" /> No </label>";
                                            <?php
                                                break;
                                            default: ?>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="si" value="2" /> Sí&nbsp &nbsp</label>
                                                <label class="radio-inline"><input type="radio" name="movimiento" id="no" value="1" /> No </label>
                                        <?php break;
                                        }
                                    } else { ?>
                                        <label class="radio-inline"><input type="radio" name="movimiento" id="si" value="2" /> Sí&nbsp &nbsp</label>
                                        <label class="radio-inline"><input type="radio" name="movimiento" id="no" value="1" /> No </label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipoInv" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Inventario:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="tipoInv" id="tipoInv" class="form-control select2" <?php if ($numHij != 0) {
                                                                                                                echo 'title="El tipo inventario no puede ser modificado, el registro seleccionado está siendo utilizado por otra dependencia."';
                                                                                                            } else {
                                                                                                                echo 'title="Ingrese el tipo de inventario"';
                                                                                                            } ?> required>
                                            <?php

                                            $row_t = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico = $tipoinventario"); ?>
                                            <option value="<?php echo $row_t[0][0] ?>"><?php echo ucwords(mb_strtolower($row_t[0][1])) ?></option>

                                            <?php
                                            if (!empty($tipoinventario)) {
                                                $res_t = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico != $tipoinventario");
                                            } else {
                                                $res_t = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario");
                                            }
                                            for ($rt = 0; $rt < count($res_t); $rt++) { ?>
                                                <option value="<?php $res_t[$rt][0] ?>"><?php echo ucwords(mb_strtolower($res_t[$rt][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="tipoInv" id="tipoInv" class="form-control select2" <?php if ($numHij != 0) {
                                                                                                                echo 'title="El tipo inventario no puede ser modificado, el registro seleccionado está siendo utilizado por otra dependencia."';
                                                                                                            } else {
                                                                                                                echo 'title="Ingrese el tipo de inventario"';
                                                                                                            } ?> required>
                                            <option value="">Tipo Inventario</option>
                                            <?php
                                            $res_t = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario");
                                            for ($r = 0; $r < count($res_t); $r++) { ?>
                                                <option value="<?php echo $res_t[$r][0] ?>"><?php echo ucwords(mb_strtolower($res_t[$r][1])) ?></option>
                                            <?php  } ?>
                                        </select>
                                    <?php } ?>

                                </div>
                                <label for="tipoAct" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Activo:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php if ($action == 1) { ?>
                                        <select name="tipoAct" id="tipoAct" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese la unidad factor" required>
                                            <?php $row_ta = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico = $tipa ORDER BY nombre ASC"); ?>
                                            <option value="<?php echo $row_ta[0][0] ?>"><?php echo ucwords(mb_strtolower($row_ta[0][1])) ?></option>
                                            <?php
                                            if (!empty($tipa)) {
                                                $res_ta = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico != $tipa ORDER BY nombre ASC");
                                            } else {
                                                $res_ta = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC");
                                            }
                                            for ($x = 0; $x < count($res_ta); $x++) { ?>
                                                <option value="<?php echo $res_ta[$x][0] ?>"><?php echo ucwords(mb_strtolower($res_ta[$x][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="tipoAct" id="tipoAct" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese la unidad factor" required>
                                            <option value="">Tipo Activo</option>
                                            <?php
                                            $res_ta = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC");
                                            for ($row_ta = 0; $row_ta < count($res_ta); $row_ta++) { ?>
                                                <option value="<?php echo $res_ta[$row_ta][0] ?>"><?php echo ucwords(mb_strtolower($res_ta[$row_ta][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="button" id="btn-asignar" class="btn btn-primary borde-sombra" title="Asignar a hijos"><span class="glyphicon glyphicon-sort-by-alphabet"></span></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="undFact" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Unidad Factor:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <select name="undFact" id="undFact" class="form-control select2" title="Ingrese la unidad factor" required>
                                            <?php $row_u = $con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico = $unidad ORDER BY nombre ASC"); ?>
                                            <option value="<?php echo $row_u[0][0] ?>"><?php echo ucwords(mb_strtolower($row_u[0][1])) ?></option>
                                            <?php
                                            if (!empty($unidad)) {
                                                $res_u = $con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico != $unidad ORDER BY nombre ASC");
                                            } else {
                                                $res_u = $con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC");
                                            }
                                            for ($ru = 0; $ru < count($res_u); $ru++) { ?>
                                                <option value="<?php echo $res_u[$ru][0] ?>"><?php echo ucwords(mb_strtolower($res_u[$ru][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="undFact" id="undFact" class="form-control select2" title="Ingrese la unidad factor" required>
                                            <option value="">Unidad Factor</option>
                                            <?php
                                            $res_u = $con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC");
                                            for ($row_u = 0; $row_u < count($res_u); $row_u++) { ?>
                                                <option value="<?php echo $res_u[$row_u][0] ?>"><?php echo ucwords(mb_strtolower($res_u[$row_u][1])) ?></option>;
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="sltFicha" class="col-sm-2 col-md-2 col-lg-2 control-label">Ficha:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php if ($action == 1) { ?>
                                        <select name="sltFicha" id="sltFicha" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese ficha">
                                            <option value="<?php echo $fid_unico ?>"> <?php echo ucwords(mb_strtolower($fnombre)) ?> </option>
                                            <?php
                                            if (!empty($fid_unico)) {
                                                $res_Fi = $con->Listar("SELECT id_unico,descripcion FROM gf_ficha WHERE id_unico != $fid_unico ORDER BY id_unico");
                                            } else {
                                                $res_Fi = $con->Listar("SELECT id_unico,descripcion FROM gf_ficha ORDER BY id_unico");
                                            }
                                            for ($rf = 0; $rf < count($res_Fi); $rf++) { ?>
                                                <option value="<?php echo $res_Fi[$rf][0] ?>"><?php echo ucwords(strtolower($res_Fi[$rf][1])) ?></option>';
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select name="sltFicha" id="sltFicha" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese ficha">
                                            <option value="">Ficha</option>";
                                            <?php
                                            $res_Fi = $con->Listar("SELECT id_unico,descripcion FROM gf_ficha ORDER BY id_unico");
                                            for ($rowf = 0; $rowf < count($res_Fi); $rowf++) { ?>
                                                <option value="<?php echo $res_Fi[$rowf][0] ?>"><?php echo ucwords(strtolower($res_Fi[$rowf][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $padre = $con->Listar("SELECT plan_padre,id_unico FROM gf_plan_inventario_asociado WHERE plan_hijo =  $id_unico"); ?>
                                <input value="<?php echo $padre[0][1] ?>" name="planAso" type="hidden" class="hidden">;
                                <label for="sltPlanPadre" class="col-sm-2 col-md-2 col-lg-2 control-label">Plan Inventario Padre:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) { ?>
                                        <?php
                                        switch ($conexion) {
                                            case 1:
                                                $planPadre = $con->Listar("SELECT id_unico, CONCAT(codi, ' - ', nombre) 
                                                FROM gf_plan_inventario where id_unico= " . $padre[0][0] . " ORDER by id_unico");
                                                break;
                                            case 2:
                                                $planPadre = $con->Listar("SELECT id_unico, codi || ' - ' || nombre 
                                                from gf_plan_inventario where id_unico= " . $padre[0][0] . " order by id_unico");
                                                break;
                                            default:
                                                # code...
                                                break;
                                        }
                                         ?>
                                        <select name="sltPlanPadre" id="sltPlanPadre" class="form-control select2" title="Selccione plan inventario padre">
                                            <option value="<?php echo $planPadre[0][0] ?>"> <?php echo $planPadre[0][1] ?></option>

                                            <?php
                                            if (!empty($planPadre[0][0])) {
                                                switch ($conexion) {
                                                    case 1:
                                                        $resultPlan = $con->Listar("SELECT id_unico, CONCAT(codi, ' - ', nombre) 
                                                        FROM gf_plan_inventario where id_unico!= " . $row[0][0] . " AND id_unico != " . $planPadre[0][0] . " 
                                                        AND compania = $compania  order by id_unico");
                                                        break;
                                                    case 2:
                                                        $resultPlan = $con->Listar("SELECT id_unico, codi || ' - ' || nombre 
                                                        from gf_plan_inventario where id_unico!= " . $row[0][0] . " and id_unico != " . $planPadre[0][0] . " 
                                                        AND compania = $compania  order by id_unico");
                                                        break;
                                                    default:
                                                        break;
                                                }

                                            } else {
                                                switch ($conexion) {
                                                    case 1:
                                                        $resultPlan = $con->Listar("SELECT id_unico, CONCAT(codi, ' - ', nombre) from gf_plan_inventario where id_unico!= " . $row[0][0] . " and compania = $compania order by id_unico");
                                                        break;
                                                    case 2:
                                                        $resultPlan = $con->Listar("SELECT id_unico, codi || ' - ' || nombre from gf_plan_inventario where id_unico!= " . $row[0][0] . " and compania = $compania order by id_unico");
                                                        break;
                                                    default:
                                                        break;
                                                }
                                            }
                                            for ($campo = 0; $campo < count($resultPlan); $campo++) { ?>
                                                <option value="<?php echo $resultPlan[$campo][0] ?>"><?php echo ucwords(strtolower($resultPlan[$campo][1])) ?></option>
                                            <?php } ?>
                                        </select>

                                    <?php } else { ?>
                                        <select name="sltPlanPadre" id="sltPlanPadre" class="form-control select2" title="Selccione plan inventario padre">
                                            <option value="">Plan Inventario</option>
                                            <?php
                                            switch ($conexion) {
                                                case 1:
                                                    $resultPlan = $con->Listar("SELECT id_unico, CONCAT(codi, ' - ', nombre) from gf_plan_inventario where id_unico != " . $row[0][0] . " and compania = $compania order by id_unico");
                                                    break;
                                                case 2:
                                                    $resultPlan = $con->Listar("SELECT id_unico, codi || ' - ' || nombre from gf_plan_inventario where id_unico != " . $row[0][0] . " and compania = $compania order by id_unico");
                                                    break;
                                                default:
                                                    # code...
                                                    break;
                                            }
                                            for ($c = 0; $c < count($resultPlan); $c++) { ?>
                                                <option value="<?php echo $resultPlan[$c][0] ?>"><?php echo ucwords(strtolower($resultPlan[$c][1])) ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <label for="movimiento" class="col-sm-2 col-md-2 col-lg-2 control-label">Indicador Capacidad:</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php if ($action == 1) {
                                        if ($xCantidad == 1) { ?>
                                            <label class="checkbox-inline"><input type="checkbox" name="chkCapacidad" id="chkCapacidad" value="1" style="margin-top: -5px;" checked></label>
                                        <?php } else { ?>
                                            <label class="checkbox-inline"><input type="checkbox" name="chkCapacidad" id="chkCapacidad" value="1" style="margin-top: -5px;"></label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label class="checkbox-inline"><input type="checkbox" name="chkCapacidad" id="chkCapacidad" value="1" style="margin-top: -5px;"></label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="chkConcepto" class="control-label col-sm-2 col-lg-2">Concepto facturable?</label>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <?php
                                    if ($action == 1) {
                                        if ($xConcepto == 1) { ?>
                                            <label class="checkbox-inline"><input type="checkbox" name="chkConcepto" id="chkConcepto" value="1" style="margin-top: -5px;" checked></label>
                                        <?php } else { ?>
                                            <label class="checkbox-inline"><input type="checkbox" name="chkConcepto" id="chkConcepto" value="1" style="margin-top: -5px;"></label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <label class="checkbox-inline"><input type="checkbox" name="chkConcepto" id="chkConcepto" value="1" style="margin-top: -5px;"></label>
                                    <?php } ?>
                                </div>
                                <label for="codigoBarras" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Código Barras:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php if ($action == 1) { ?>
                                        <input type="text" name="codigoBarras" id="codigoBarras" class="form-control" placeholder="Código Barras" value="<?php echo $codigo_barras ?>">
                                    <?php } else { ?>
                                        <input type="text" name="codigoBarras" id="codigoBarras" class="form-control" placeholder="Código Barras" required>
                                    <?php } ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="ivaDesc" class="col-sm-3 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Iva Descontable:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <?php if ($action == 1) {
                                        switch ($ivaDesc) {
                                            case '2': ?>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="si" value="1" /> Sí&nbsp &nbsp </label>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="no" value="2" checked /> No </label>
                                            <?php
                                                break;
                                            case '1': ?>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="si" value="1" checked /> Sí&nbsp &nbsp </label>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="no" value="2" /> No </label>
                                            <?php
                                                break;
                                            default: ?>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="si" value="1" /> Sí&nbsp &nbsp</label>
                                                <label class="radio-inline"><input type="radio" name="ivaDesc" id="no" value="2" /> No </label>
                                        <?php break;
                                        }
                                        ?>
                                    <?php } else { ?>
                                        <label class="radio-inline"><input type="radio" name="ivaDesc" id="si" value="1" /> Sí&nbsp &nbsp</label>
                                        <label class="radio-inline"><input type="radio" name="ivaDesc" id="no" value="2" /> No </label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">

                                <label for="no" class="col-sm-9 col-md-9 col-lg-9 control-label"></label>
                                <div class="col-sm-2 col-md-2 col-lg-2 text-right">
                                    <button type="submit" class="btn btn-primary borde-sombra"><?php echo $titulo ?></button>
                                </div>
                            </div>
                            </form>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-2 col-md-2 col-lg-2">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h2 class="titulo" align="center" style="font-size:17px; margin-top: 0px;">Información adicional</h2>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GS_TIPO_ELEMENTO.php" class="btn btn-primary btnInfo">TIPO DE ELEMENTO</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GF_UNIDAD_FACTOR.php" class="btn btn-primary btnInfo">UNIDAD FACTOR</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GF_TIPO_ACTIVO.php" class="btn btn-primary btnInfo">TIPOS DE ACTIVO</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <?php if ($action == 1) { ?>
                        <a class="btn btn-primary btnInfo" href="modificar_GF_ELEMENTO_FICHA.php?action=2">ELEMENTO FICHA</a>
                    <?php } else { ?>
                        <a class="btn btn-primary btnInfo" disabled>ELEMENTO FICHA</a>
                    <?php } ?>
                </div>
                <?php
                if ($xConcepto == 1) {
                    $html = "";
                    $html .= "\n\t<div class=\"col-sm-12 col-md-12 col-lg-12\">";
                    $html .= "\n\t\t<a class=\"btn btn-primary btnInfo\" href=\"access.php?controller=Inventario&action=vistaConceptos&plan=" . base64_encode($id_plan_inv) . "\">ESTABLECER PRECIO</a>";
                    $html .= "\n\t</div>";
                    echo $html;
                }
                ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Este predecesor ya no puede tener más hijos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Este código ya existe.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Acept" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_asignado" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Información asignada correctamente a hijos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btn-asig" class="btn btn-default" data-dismiss="modal">Aceptar</button>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript">
        $(".select2").select2();

        $("#predecesor").change(function(e) {
            const padre = e.target.value;
            if (padre != "" || padre != 0) {
                $.get("access.php?controller=Inventario&action=obnterCodigoHijo&padre=" + padre, function(data) {
                    $("#codigo").val(data);
                });
            } else {
                $('#codigo').val("").removeAttr("readonly");
                $('#si').prop('disabled', false);
                $('#no').prop('disabled', false);
                $('.radios input').attr({
                    'title': 'Seleccione si tiene movimiento o no.'
                });
                $('#si').prop('checked', false);
                $('#no').prop('checked', true);
            }

            $.post("access.php?controller=Inventario&action=obtenerDatosPadre", {
                plan: padre
            }, function(data) {
                var res = JSON.parse(data);
                if (jQuery.isEmptyObject(res) == false) {
                    $("#tipoInv").val(res[5]).trigger("change");
                    $("#undFact").val(res[6]).change();
                    $("#tipoAct").val(res[8]).change();
                }
            });
        });

        $("#btn-asignar").click(function(e) {
            var tipo = $("#tipoAct").val();
            var codigo = $("#codigo").val();
            var result = false;
            if (tipo != "" && codigo != "") {
                $.post('access.php?controller=Inventario&action=asignar_tipo&codigo=' + codigo + '&tipo=' + tipo,
                    function(data, textStatus, xhr) {
                        result = JSON.parse(data);
                        if (result == true) {
                            $("#modal_asignado").modal("show");
                        }
                    }
                );
            }
        });

        $('#ver1').click(function() {
            window.location.reload();
        });

        $('#Acept').click(function() {
            window.location.reload();
        });

        var codigo = $("#codigo").val();
        if (codigo.length == 5) {
            $("#btn-asignar").css('display', 'block');
        } else {
            $("#btn-asignar").css('display', 'none');
        }

        var validator = $("#form").validate({
            ignore: "",
            errorElement: "em",
            errorPlacement: function(error) {
                error.addClass('help-block');
            },
            highlight: function(element) {
                var elem = $(element);
                if (elem.hasClass('select2-offscreen')) {
                    $("#s2id_" + elem.attr("id")).addClass('has-error').removeClass('has-success');
                } else {
                    $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                }
                if ($(element).attr('type') === 'radio') {
                    $(element.form).find("input[type=radio]").each(function(which) {
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                        $(this).addClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                    $(element).addClass("has-error");
                }
            },
            unhighlight: function(element) {
                var elem = $(element);
                if (elem.hasClass('select2-offscreen')) {
                    $("#s2id_" + elem.attr("id")).addClass('has-success').removeClass('has-error');
                } else {
                    $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
                }
                if ($(element).attr('type') === 'radio') {
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

        $("#chkConcepto").click(function(e) {
            var btn = $(this);
            if (btn.is(':checked')) {
                $("#sltConceptoF").attr("readonly", false);
            } else {
                $("#sltConceptoF").attr("readonly", true);
            }
        });

        <?php
        if ($xConcepto == 1) {
            $html = "";
            $html .= "$('#sltConceptoF').removeAttr('readonly');";
            echo $html;
        }
        ?>
    </script>
    <script>
        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "controller/controllerGFPlanInventario.php?action=insert",
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
                            document.location = 'GF_PLAN_INVENTARIO.php';
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
                url: "controller/controllerGFPlanInventario.php?action=modify",
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
                            document.location = 'GF_PLAN_INVENTARIO.php';
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
    <?php require('footer.php'); ?>
</body>

</html>