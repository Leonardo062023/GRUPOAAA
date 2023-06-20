<?php
/**
 * controllerGFPlanInventario.php
 *
 * Archivo de control para registro, modificación y eliminado de la tabla elemento ficha
 *
 * @author Alexander Numpaque
 * @package Plan Inventario
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFPlanInventario.php 001 2017-05-26 Alexander Numpaque$
 */

session_start();
$compania = $_SESSION['compania'];
$param    = $_SESSION['anno'];
if(!empty($_REQUEST['action'])){
    $action =  $_REQUEST['action'];
}elseif ($_GET['action']) {
    $action =  $_REQUEST['action'];
}

require ('../json/registrar_GF_PLAN_INVENTARIOJson.php');

if($action == 'insert') {
    $codigo = $_REQUEST['codigo'];
    $nombre = $_REQUEST['nombre'];
    $movimiento = $_REQUEST['movimiento'];
    $tipoInv = $_REQUEST['tipoInv'];
    $undFact = $_REQUEST['undFact'];
    if(empty($_REQUEST['predecesor'])){
        $predecesor = NULL;
    }else{
        $predecesor = $_REQUEST['predecesor'];
    }
    $tipoAct = $_REQUEST['tipoAct'];

    if (empty($_REQUEST['sltFicha'])) {
        $ficha = NULL;
    }else{
        $ficha = $_REQUEST['sltFicha'];
    }

    if(empty($_REQUEST['chkCapacidad'])){
       $xCantidad = NULL;
    }else{
        $xCantidad = $_REQUEST['chkCapacidad'];
    }

    if(empty($_REQUEST['chkConcepto'])){
        $xConcepto = NULL;
    }else{
        $xConcepto = $_REQUEST['chkConcepto'];
    }
    
    if(empty($_REQUEST['codigoBarras'])){
        $codigoB = NULL;
    }else{
        $codigoB = $_REQUEST['codigoBarras'];
    }

    $padre = $_REQUEST['sltPlanPadre'];
    $result = gf_plan_inventario::save_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $compania, $predecesor, $ficha, $xCantidad, $xConcepto, $codigoB);
    if($result){
        $x = true;
        gf_plan_inventario::save_plan_p($codigo, $padre);
        if($xConcepto == 1){
            $id      = gf_plan_inventario::obtenerIDRegistro($codigo);
            $xExiste = gf_plan_inventario::existeConcepto($id);
            if(empty($xExiste) || $xExiste === 0){
                $data = new gf_plan_inventario();
                $data->tipo_concepto       = 1;
                $data->nombre              = $nombre;
                $data->tipo_operacion      = 1;
                $data->plan_inventario     = intVal($id);
                $data->factor_base         = NULL;
                $data->compania            = intVal($compania);
                $x = gf_plan_inventario::guardarConceptoFactura($data);
            }
        }
        if($result && $x){
            echo 1;
        }else{
            echo 2;
        }
    }else{
        echo 2;
    }
}else if($action == 'modify') {
    $id_unico  = $_REQUEST['id'];
    $codigo = $_REQUEST['codigo'];
    $nombre = $_REQUEST['nombre'];
    $movimiento = $_REQUEST['movimiento'];
    $tipoInv = $_REQUEST['tipoInv'];
    $undFact = $_REQUEST['undFact'];
    $ivaDesc = $_REQUEST['ivaDesc'];
    if(empty($_REQUEST['predecesor'])){
        $predecesor = NULL;
    }else{
        $predecesor = $_REQUEST['predecesor'];
    }
    $tipoAct = $_REQUEST['tipoAct'];
    if (empty($_REQUEST['sltFicha'])) {
        $ficha = NULL;
    }else{
        $ficha = $_REQUEST['sltFicha'];
    }
    $padre = $_REQUEST['sltPlanPadre'];
    $planAso = $_REQUEST['planAso'];

    if(empty($_REQUEST['chkCapacidad'])){
        $xCantidad = "0";
    }else{
        $xCantidad = $_REQUEST['chkCapacidad'];
    }

    if(empty($_REQUEST['chkConcepto'])){
        $xConcepto = "0";
    }else{
        $xConcepto = $_REQUEST['chkConcepto'];
    }
    if(empty($_REQUEST['codigoBarras'])){
        $codigoB = NULL;
    }else{
        $codigoB = $_REQUEST['codigoBarras'];
    }

    $result = gf_plan_inventario::modify_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $predecesor, $ficha, $id_unico, $xCantidad, $xConcepto,$codigoB,$ivaDesc);

    if($result==true){
        $x = true;
        gf_plan_inventario::modify_plan_p($planAso, $padre, $id_unico);
        if($xConcepto == 1){
            $xExiste = gf_plan_inventario::existeConcepto($id_unico);
            if(empty($xExiste) || $xExiste === 0) {
                $data = new gf_plan_inventario();
                $data->tipo_concepto       = 1;
                $data->nombre              = $nombre;
                $data->tipo_operacion      = 1;
                $data->plan_inventario     = $id_unico;
                $data->concepto_financiero = NULL;
                $data->formula             = NULL;
                $data->factor_base         = NULL;
                $data->parametrizacionanno = $param;
                $x = gf_plan_inventario::guardarConceptoFactura($data);
            }
        }else{
            gf_plan_inventario::eliminarConceptos($id_unico);
        }
        if($result && $x){
            echo 1;
        }else{
            echo 2;
        }
    }else{
        echo 2;
    }
}elseif($action == 'delete') {
    $id_unico = $_REQUEST['id_unico'];
    $result = gf_plan_inventario::delete_data($id_unico);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}
