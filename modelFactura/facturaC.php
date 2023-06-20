<?php
require_once('../Conexion/ConexionPDO.php');
/**
 * Modelo de facturación, es decir de la tabla factura
 */
class factura
{
    public $id_unico;
    public $numero_factura;
    public $tipofactura;
    public $tercero;
    public $fecha_factura;
    public $fecha_vencimiento;
    public $centrocosto;
    public $descripcion;
    public $estado_factura;
    public $responsable;
    public $parametrizacionanno;

    private $con;

    public function __construct()
    {
        $this->con = new ConexionPDO();
    }

    public function obtnerFacturas($fecha_incial, $fecha_final)
    {
        try {
            $res = $this->con->Listar("SELECT tipofactura, id_unico, numero_factura, tercero
                    FROM   gp_factura
                    WHERE  (fecha_factura BETWEEN '$fecha_incial' AND '$fecha_final')");
            for ($row = 0; $row < count($res); $row++) {
                $facturas[] = " " . $res[$row][0] . " , " . $res[$row][1] . ", " . $row[2] . ", " . $res[$row][3] . " ";
            }
            return $facturas;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCompania($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT UPPER(CASE
            WHEN ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos IS NULL
                OR ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos = ''
                THEN ter.razonsocial
                ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END) AS NOMBRE,
            UPPER(ti.nombre) || ' :' || ter.numeroidentificacion AS IDENT,
            ter.ruta_logo,
            ter.digitoverficacion
            FROM gf_tercero ter
            LEFT JOIN gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
            WHERE ter.id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function valorDetallesFactura($id_unico)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT valor FROM gp_detalle_factura WHERE factura = $id_unico");
            for ($row = 0; $row < count($res); $row++) {
                $xxx += $res[$row][0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function obtnerComprobantesCntP($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT      cnt.id_unico as cnt, ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura        = tpg.id_unico
                    AND         tpc.id_unico          = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante   = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante  = tcp.id_unico
                    AND         pg.numero_factura     = ptal.numero
                    AND         pg.numero_factura     = cnt.numero
                    AND         pg.id_unico           =  $id_unico");
            return $row;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function obtnerValoresDetalleCnt($id_unico)
    {
        try {
            $xxx = 0;
            $row = $this->con->Listar("SELECT SUM(valor) FROM gf_detalle_comprobante WHERE comprobante = $id_unico AND naturaleza = 2");
            $xxx += $row[0][0];
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValoresPptal($id_unico)
    {
        try {
            $xxx = 0;
            $row = $this->con->Listar("SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_unico");
            $xxx += $row[0][0];
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerComprobantesDesdeDetalles($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT    dtc.comprobante,dtp.comprobantepptal
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc       ON dtc.id_unico                = dtf.detallecomprobante
                    LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico
                    WHERE     dtf.factura = $id_unico AND dtc.detallecomprobantepptal IS NOT NULL");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorTotalFacturIva($id_unico)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT (valor * cantidad) + iva FROM gp_detalle_factura WHERE factura = $id_unico");
            for ($row=0; $row < count($res); $row++) { 
                $xxx += $res[$row][0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerMaxNumeroF($tipo)
    {
        try {
            $row = $this->con->Listar("SELECT MAX(numero_factura) FROM gp_factura WHERE tipofactura = $tipo");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFechaTipoNumero($tipo, $numero)
    {
        try {
            $row = $this->con->Listar("SELECT fecha_factura FROM gp_factura WHERE tipofactura = $tipo AND numero_factura = $numero");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function contarFacturasTipo($tipo)
    {
        try {
            $row = $this->con->Listar("SELECT COUNT(*) FROM gp_factura WHERE tipofactura = $tipo");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerNumeroFactura($id_unico)
    {
        try {
            $row = "SELECT numero_factura FROM gp_factura WHERE id_unico = $id_unico";
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
