<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de detalle factura
 */
class detalleFactura{
    public $id_unico;
    public $factura;
    public $concepto_tarifa;
    public $valor;
    public $cantidad;
    public $iva;
    public $impoconsumo;
    public $ajuste_peso;
    public $valor_total_ajustado;
    public $detallecomprobante;

    private $mysqli;
    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(detalleFactura $data){
        try {
            $sql = "INSERT INTO gp_detalle_factura(
                                    factura,
                                    concepto_tarifa,
                                    valor,
                                    cantidad,
                                    iva,
                                    impoconsumo,
                                    ajuste_peso,
                                    valor_total_ajustado,
                                    detallecomprobante
                                ) VALUES (
                                    $data->factura,
                                    $data->concepto_tarifa,
                                    $data->valor,
                                    $data->cantidad,
                                    $data->iva,
                                    $data->impoconsumo,
                                    $data->ajuste_peso,
                                    $data->valor_total_ajustado,
                                    $data->detallecomprobante
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoId($factura){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gp_detalle_factura WHERE factura = $factura";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(detalleFactura $data){
        try {
            $sql = "UPDATE gp_detalle_factura
                    SET    concepto_tarifa      = $data->concepto_tarifa,
                           cantidad             = $data->cantidad,
                           valor                = $data->valor,
                           iva                  = $data->iva,
                           impoconsumo          = $data->impoconsumo,
                           ajuste_peso          = $data->ajuste_peso,
                           valor_total_ajustado = $data->valor_total_ajustado
                    WHERE  id_unico             = $data->id_unico";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_detalle_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res = true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function desplazmientoDetallesCompleto($id_unico){
        try {
            $sql = "SELECT     dtc.detallecomprobantepptal  AS 'detalle_pp',
                               dtf.detallecomprobante       AS 'cuenta_debito',
                               dta.id_unico                 AS 'cuenta_credito',
                               dti.id_unico                 AS 'cuenta_iva_1',
                               dtv.id_unico                 AS 'cuenta_iva_2',
                               dtm.id_unico                 AS 'cuenta_impo_1',
                               dto.id_unico                 AS 'cuenta_impo_2'
                    FROM       gp_detalle_factura dtf
                    LEFT JOIN  gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                    LEFT JOIN  gf_detalle_comprobante dta ON dta.detalleafectado    = dtc.id_unico
                    LEFT JOIN  gf_detalle_comprobante dti ON dti.detalleafectado    = dta.id_unico
                    LEFT JOIN  gf_detalle_comprobante dtv ON dtv.detalleafectado    = dti.id_unico
                    LEFT JOIN  gf_detalle_comprobante dtm ON dtm.detalleafectado    = dtv.id_unico
                    LEFT JOIN  gf_detalle_comprobante dto ON dto.detalleafectado    = dtm.id_unico
                    WHERE      dtf.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function desplazmientoDetallesMinimo($id_unico){
        try {
            $sql = "SELECT    dtc.detallecomprobantepptal AS 'detalle_pp',
                              dtf.detallecomprobante      AS 'cuenta_debito',
                              dta.id_unico                AS 'cuenta_credito'
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                    LEFT JOIN gf_detalle_comprobante dta ON dta.detalleafectado    = dtc.id_unico
                    WHERE     dtf.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetalleContable($id_unico){
        try {
            $id  = 0;
            $sql = "SELECT detallecomprobante FROM gp_detalle_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarDetallesFactura($id_unico){
        try {
            $sql = "DELETE FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerListados($factura){
        try {
            $sql = "SELECT    dtf.id_unico,
                              cnp.id_unico,
                              cnp.nombre,
                              dtf.cantidad,
                              dtf.valor,
                              dtf.iva,
                              dtf.impoconsumo,
                              dtf.ajuste_peso,
                              fat.numero_factura,
                              dtf.valor_total_ajustado
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gp_factura fat  ON fat.id_unico = dtf.factura
                    LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
                    WHERE     dtf.factura = $factura";
            $res = $this->mysqli->query($sql);
            return $res;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetallesFactura($factura){
        try {
            $sql = "SELECT    dtf.id_unico,
                              cnp.id_unico,
                              dtf.cantidad,
                              dtf.valor,
                              dtf.iva,
                              dtf.impoconsumo,
                              dtf.ajuste_peso,
                              fat.numero_factura,
                              dtf.valor_total_ajustado
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gp_factura fat  ON fat.id_unico = dtf.factura
                    LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
                    WHERE     dtf.factura = $factura";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_all($res, MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoF($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT concepto_financiero FROM gp_concepto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoRB($concepto){
        try {
            $sql = "SELECT id_unico, rubro FROM gf_concepto_rubro WHERE concepto = $concepto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
            }
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function obtenerConceptoRubro($concepto){
        try {
            @session_start();
            $panno = $_SESSION['anno'];
            $dia = 0;
            $tipo_cartera = 0;
            $crt = "SELECT * FROM `gp_tipo_cartera` where dia_final >= $dia and dia_inicial <= $dia";
            $res = $this->mysqli->query($crt);
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_row($res);
                $tipo_cartera  = $row[0];
            }
            $sql = "SELECT cf.concepto_rubro, 
                cf.rubro_fuente 
                FROM gp_configuracion_concepto cf
                LEFT JOIN gf_concepto_rubro cr ON cf.concepto_rubro = cr.id_unico 
                LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico 
                LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                WHERE cf.concepto = $concepto
                AND cf.tipo_cartera =$tipo_cartera
                AND cf.parametrizacionanno = $panno";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
            }
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    

    public function obtnerRubroFuente($rubro){
        try {
            $id_ = 0;
            $sql = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rubro";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConfigRubroCuenta($concepto_rubro){
        try {
            $sql = "SELECT cuenta_debito,
                           cuenta_credito,
                           cuenta_iva,
                           cuenta_impoconsumo
                    FROM   gf_concepto_rubro_cuenta
                    WHERE  concepto_rubro = $concepto_rubro";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function destruirRelacionDetalles($factura){
        try {
            $sql = "UPDATE  gp_detalle_factura
                    SET     detallecomprobante = NULL
                    WHERE   factura            = $factura";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function relacionarDetalleComprobante($id_unico, $detalle){
        try {
            $sql = "UPDATE  gp_detalle_factura
                    SET     detallecomprobante = $detalle
                    WHERE   id_unico           = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}