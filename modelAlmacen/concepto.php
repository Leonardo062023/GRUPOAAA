<?php
require_once('./Conexion/ConexionPDO.php');
class concepto
{

    private $con;

    public $id_unico;
    public $tipo_concepto;
    public $nombre;
    public $tipo_opereacion;
    public $plan_inventario;
    public $concepto_financiero;
    public $formula;
    public $factor_base;

    public function __construct()
    {
        $this->con = new ConexionPDO();
    }

    public function getIdUnico()
    {
        return $this->id_unico;
    }

    public function setIdUnico($id_unico)
    {
        $this->id_unico = $id_unico;
    }

    public function getTipoConcepto()
    {
        return $this->tipo_concepto;
    }

    public function setTipoConcepto($tipo_concepto)
    {
        $this->tipo_concepto = $tipo_concepto;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getTipoOpereacion()
    {
        return $this->tipo_opereacion;
    }

    public function setTipoOpereacion($tipo_opereacion)
    {
        $this->tipo_opereacion = $tipo_opereacion;
    }

    public function getPlanInventario()
    {
        return $this->plan_inventario;
    }

    public function setPlanInventario($plan_inventario)
    {
        $this->plan_inventario = $plan_inventario;
    }

    public function getConceptoFinanciero()
    {
        return $this->concepto_financiero;
    }

    public function setConceptoFinanciero($concepto_financiero)
    {
        $this->concepto_financiero = $concepto_financiero;
    }

    public function getFormula()
    {
        return $this->formula;
    }

    public function setFormula($formula)
    {
        $this->formula = $formula;
    }

    public function getFactorBase()
    {
        return $this->factor_base;
    }

    public function setFactorBase($factor_base)
    {
        $this->factor_base = $factor_base;
    }


    public function registrar(concepto $data)
    {
        try {
            $sql_cons  = "INSERT INTO gp_concepto (tipo_concepto, nombre, tipo_operacion, plan_inventario, concepto_financiero, formula,factor_base) 
            VALUES (:tipo_concepto, :nombre, :tipo_opereacion, :plan_inventario, :concepto_financiero, :formula, :factor_base)";
            $sql_dato = array(
                array(":tipo_concepto", $data->tipo_concepto),
                array(":nombre", $data->nombre),
                array(":tipo_opereacion", $data->tipo_opereacion),
                array(":plan_inventario", $data->plan_inventario),
                array(":concepto_financiero", $data->concepto_financiero),
                array(":formula", $data->formula),
                array(":factor_base", $data->factor_base),
            );
            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(concepto $data)
    {
        try {
            $sql_cons = "UPDATE gp_concepto 
            SET tipo_concepto=:tipo_c, nombre=:nombre, tipo_opereacion=: tipo_o, plan_inventario=:plan, concepto_financiero=:concepto,
            formula=:formula, factor_base=:facor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":tipo_c", $data->tipo_concepto),
                array(":nombre", $data->nombre),
                array(":tipo_o", $data->tipo_opereacion),
                array(":plan", $data->plan_inventario),
                array(":concepto", $data->concepto_financiero),
                array(":formula", $data->formula),
                array(":facor", $data->factor_base),
                array(":id_unico", $data->id_unico),
            );

            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);

            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico)
    {
        try {
            $sql_cons = "DELETE FROM gp_concepto WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico)
            );

            $resp = $this->con->InAcEl($sql_cons, $sql_dato);

            if (empty($resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoFinanciero($id_unico)
    {
        try {
            $id  = 0;
            $res = $this->con->Listar("SELECT concepto_financiero FROM gp_concepto WHERE id_unico = $id_unico");
            if (count($res) > 0) {
                $id  = $res[0][0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptoPlan()
    {
        try {
            $plan_inventario = base64_decode($this->plan_inventario);
            $res = $this->con->Listar("SELECT con.id_unico, con.nombre, tar.valor, tar.id_unico, tar.porcentaje_iva, tar.porcentaje_impoconsumo, tpt.nombre
            FROM gp_concepto con
            LEFT JOIN gp_concepto_tarifa cpt ON con.id_unico = cpt.concepto
            LEFT JOIN gp_tarifa tar ON cpt.tarifa = tar.id_unico
            LEFT JOIN gp_tipo_tarifa tpt ON tar.tipo_tarifa = tpt.id_unico
            WHERE con.plan_inventario = $plan_inventario
            AND tar.id_unico IS NOT NULL");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptosPlanId()
    {
        try {
            $plan_inventario = base64_decode($this->plan_inventario);
            $res = $this->con->Listar("SELECT DISTINCT con.id_unico, con.nombre
            FROM gp_concepto con
            LEFT JOIN gp_concepto_tarifa cpt ON con.id_unico = cpt.concepto
            LEFT JOIN gp_tarifa tar ON cpt.tarifa = tar.id_unico
            WHERE con.plan_inventario = $plan_inventario");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoPl()
    {
        try {
            $id = base64_decode($this->id_unico);
            $res = $this->con->Listar("SELECT con.plan_inventario
            FROM gp_concepto con
            LEFT JOIN gp_concepto_tarifa cpt ON con.id_unico = cpt.concepto
            LEFT JOIN gp_tarifa tar ON cpt.tarifa = tar.id_unico
            WHERE con.id_unico = $id");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptosFinanciera($param)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gf_concepto WHERE  parametrizacionanno = $param");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoOrden($orden, $param)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, UPPER(nombre) FROM gp_concepto WHERE parametrizacionanno = $param ORDER BY id_unico $orden");
            //Se realizo el cambio del script de gp_concepto ya que estaba desactualizado y no contenia parametrizacionano
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataCompania($id)
    {
        try {
            $res = $this->con->Listar("SELECT UPPER(razonsocial), numeroidentificacion, ruta_logo FROM gf_tercero WHERE id_unico = $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptos($cptI, $cptF)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, UPPER(nombre) FROM gp_concepto WHERE id_unico BETWEEN $cptI AND $cptF");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDatosConcepto($concpto)
    {
        try {
            $res = $this->con->Listar("SELECT TO_CHAR(gpg.fecha_pago, 'DD/MM/YYYY'), gpg.numero_pago,
            CASE
            WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
            ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
            END,
            gdf.valor, gdf.cantidad, gpf.descripcion
            FROM gp_detalle_pago gdp
            LEFT JOIN gp_detalle_factura gdf ON gdp.detalle_factura = gdf.id_unico
            LEFT JOIN gp_pago gpg ON gdp.pago = gpg.id_unico
            LEFT JOIN gp_factura gpf ON gdf.factura = gpf.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            WHERE gdf.concepto_tarifa = $concpto");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
