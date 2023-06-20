<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];

switch ($action) {
    case 1:
        $id = $_REQUEST['id'];
        // $query = "DELETE FROM gp_concepto WHERE id_unico = $id";
        // $resultado = $mysqli->query($query);
        $sql_cons = "DELETE FROM gp_concepto WHERE id_unico=:id_unico";
        $sql_dato = array(
            array(":id_unico", $id)
        );
        $resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
    case 2:
        $tipoC      = $_REQUEST['TipoConcepto'];
        $tipoO      = $_REQUEST['TipoOperacion'];
        $nombre     = $_REQUEST['nombre'];
        if (empty($_REQUEST['planInventario'])) {
            $planI  = NULL;
        } else {
            $planI  = $_REQUEST['planInventario'];
        }
        if (empty($_REQUEST['factorBase'])) {
            $factorB  = NULL;
        } else {
            $factorB  = $_REQUEST['factorBase'];
        }

        if (empty($_REQUEST['alojamiento'])) {
            $alojamiento  = NULL;
        } else {
            $alojamiento  = $_REQUEST['alojamiento'];
        }

        if (empty($_REQUEST['concepto_asociado'])) {
            $concepto_asociado  = NULL;
        } else {
            $concepto_asociado  = $_REQUEST['concepto_asociado'];
        }

        if (empty($_REQUEST['ajuste'])) {
            $ajuste  = NULL;
        } else {
            $ajuste  = $_REQUEST['ajuste'];
        }

        if (empty($_REQUEST['traduccion'])) {
            $traduccion = NULL;
        } else {
            $traduccion = $_REQUEST['traduccion'];
        }

        // $insert = "INSERT INTO gp_concepto (tipo_concepto, 
        // nombre, tipo_operacion, 
        // plan_inventario, factor_base,  compania,
        // alojamiento, concepto_asociado, 
        // ajuste, traduccion) 
        // VALUES($tipoC, '$nombre', $tipoO, $planI,  
        // $factorB,  $compania, 
        // $alojamiento, $concepto_asociado, '$ajuste', '$traduccion' 
        // )";
        // $resultado = $mysqli->query($insert);
        $sql_cons  = "INSERT INTO gp_concepto (tipo_concepto, nombre, tipo_operacion, plan_inventario, factor_base, compania, alojamiento,
        concepto_asociado, ajuste, traduccion) 
        VALUES (:tipo_concepto, :nombre, :tipo_operacion, :plan_inventario, :factor_base, :compania, :alojamiento, :concepto_asociado, 
        :ajuste, :traduccion)";
        $sql_dato = array(
            array(":tipo_concepto", $tipoC),
            array(":nombre", $nombre),
            array(":tipo_operacion", $tipoO),
            array(":plan_inventario", $planI),
            array(":factor_base", $factorB),
            array(":compania", $compania),
            array(":alojamiento", $alojamiento),
            array(":concepto_asociado", $concepto_asociado),
            array(":ajuste", $ajuste),
            array(":traduccion", $traduccion),

        );

        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
    case 3:
        $id  = $_REQUEST['id'];
        $tipoC      = $_REQUEST['TipoConcepto'];
        $tipoO      = $_REQUEST['TipoOperacion'];
        $nombre     = $_REQUEST['nombre'];
        if (empty($_REQUEST['planInventario'])) {
            $planI  = NULL;
        } else {
            $planI  = $_REQUEST['planInventario'];
        }
        if (empty($_REQUEST['factorBase'])) {
            $factorB  = NULL;
        } else {
            $factorB  = $_REQUEST['factorBase'];
        }

        if (empty($_REQUEST['alojamiento'])) {
            $alojamiento  = NULL;
        } else {
            $alojamiento  = $_REQUEST['alojamiento'];
        }

        if (empty($_REQUEST['concepto_asociado'])) {
            $concepto_asociado  = NULL;
        } else {
            $concepto_asociado  = $_REQUEST['concepto_asociado'];
        }

        if (empty($_REQUEST['ajuste'])) {
            $ajuste  = NULL;
        } else {
            $ajuste  = $_REQUEST['ajuste'];
        }

        if (empty($_REQUEST['traduccion'])) {
            $traduccion = NULL;
        } else {
            $traduccion = $_REQUEST['traduccion'];
        }

        $sql_cons = "UPDATE gp_concepto 
            SET tipo_concepto=:tipo_concepto, nombre=:nombre, tipo_operacion=:tipo_operacion, plan_inventario=:plan_inventario, factor_base=:factor_base, 
            alojamiento=:alojamiento, concepto_asociado=:concepto_asociado, ajuste=:ajuste, traduccion=:traduccion
            WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":tipo_concepto", $tipoC),
            array(":nombre", $nombre),
            array(":tipo_operacion", $tipoO),
            array(":plan_inventario", $planI),
            array(":factor_base", $factorB),
            array(":alojamiento", $alojamiento),
            array(":concepto_asociado", $concepto_asociado),
            array(":ajuste", $ajuste),
            array(":traduccion", $traduccion),
            array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
    default:
        break;
}

?>