<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de Salida de almacén
 */
class salida{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerElementosInventario(){
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $sql = "SELECT DISTINCT pln.id_unico, CONCAT_WS(' - ',pln.codi, UPPER(pln.nombre))
                    FROM            gf_plan_inventario     pln
                    LEFT JOIN       gf_detalle_movimiento  dtm ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN       gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE           (pro.baja IS NULL OR pro.baja = 0)
                    AND             (tpm.clase = 2) 
                    AND             pln.compania = $compania 
                    ORDER BY        pln.codi ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadProductosPlan($id){
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $xxx = 0;
            $sql = "SELECT    dtm.cantidad
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND    pln.compania = $compania 
                    AND   (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            foreach ($row as $row) {
                $xxx += $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadProductosPlanSalida($id){
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $xxx = 0;
            $sql = "SELECT    dtm.cantidad
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND    pln.compania = $compania 
                    AND   (tpm.clase = 3)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            foreach ($row as $row) {
                $xxx += $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorProductoPlan($id){ 
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $xxx = 0;
            $sql = "SELECT    dtm.valor
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND    pln.compania = $compania 
                    AND   (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            $xxx = $row[0];
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerSaldoEntradaPlan($id){
        try {
            $xxx = 0;
            $sql = "SELECT    SUM((dtm.valor) * dtm.cantidad)
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND   (tpm.clase = 2)";
                $res = $this->mysqli->query($sql);
                $row = mysqli_fetch_row($res);
            if(count($row)> 0){
                    $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerSaldoSalidaPlan($id){
        try {
            $xxx = 0;
            $sql = "SELECT    SUM((dtm.valor) * dtm.cantidad)
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND   (tpm.clase = 3)";
                     $res = $this->mysqli->query($sql);
                     $row = $res->fetch_row();
            if(count($row)> 0){
                    $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarValorMaximoElemento($elemento){
        try {
            $xxx = 0;
            $sql = "SELECT    gdm.valor
                    FROM      gf_detalle_movimiento AS gdm
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento = gtp.id_unico
                    WHERE     gdm.planmovimiento = $elemento
                    AND       gdm.valor         != 0
                    ORDER BY  gdm.id_unico DESC
                    LIMIT     1";
                    $res = $this->mysqli->query($sql);
                    $row = $res->fetch_row();
            if(count($row)> 0){
                for ($i = 0; $i < count($row); $i++) {
                    $xxx = $row[$i][0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarDetallesRelacionadosPlan($id){
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $sql = "SELECT DISTINCT dtm.id_unico, dtm.cantidad
                    FROM            gf_detalle_movimiento  dtm
                    LEFT JOIN       gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN       gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE           (pln.id_unico = $id)
                    AND             (pro.baja IS NULL OR pro.baja = 0)
                    AND             pln.compania = $compania 
                    AND             (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarCantidadSalidaDetalleAsociado($id){
        @session_start();
        $compania = $_SESSION['compania'];
        try {
            $sql = "SELECT DISTINCT dtm.id_unico, dtm.cantidad
                    FROM            gf_detalle_movimiento dtm
                    LEFT JOIN       gf_plan_inventario pln ON dtm.planmovimiento = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto pro ON mpr.producto = pro.id_unico
                    LEFT JOIN       gf_movimiento mov ON dtm.movimiento = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    WHERE           (dtm.detalleasociado = $id)
                    AND             (pro.baja IS NULL OR pro.baja = 0)
                    AND             pln.compania = $compania 
                    AND             (tpm.clase = 3)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarDetalleSalida($cantidad, $valor, $iva, $movimiento, $detalleasociado, $planmovimiento){
        try {
            date_default_timezone_set('America/Bogota');
            $hora     = date('H:i:s');
            $sql = "INSERT INTO gf_detalle_movimiento(cantidad, valor, iva,hora,movimiento, detalleasociado, planmovimiento) VALUES($cantidad, $valor, $iva,'$hora', $movimiento, $detalleasociado, $planmovimiento)";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarDatosSalida($id_unico){
        try {
            $sql = "SELECT id_unico FROM gf_movimiento WHERE md5(id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}