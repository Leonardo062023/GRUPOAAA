<?php
require_once('./Conexion/ConexionPDO.php');
class concepto
{
    public $id_unico;
    public $tipo_concepto;
    public $nombre;
    public $tipo_opereacion;
    public $plan_inventario;
    public $concepto_financiero;
    public $formula;
    public $factor_base;

    private $con;

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
            SET tipo_concepto=:tipo_c, nombre=:nombre, tipo_operacion=: tipo_o, plan_inventario=:plan, concepto_financiero=:concepto,
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

    public function obtnerConceptoFinanciero($id_unico, $panno)
    {
        try {
            $id  = 0;
            $res = $this->con->Listar("SELECT concepto_rubro FROM gp_configuracion_concepto 
            WHERE concepto = $id_unico AND parametrizacionanno = $panno");
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
            $res = $this->con->Listar("SELECT
            con.id_unico,
            con.nombre,
            tar.valor,
            tar.id_unico,
            tar.porcentaje_iva,
            tar.porcentaje_impoconsumo,
            tpt.nombre,
            unf.id_unico,
            unf.nombre,
            elu.valor_conversion,
            cpt.id_unico,
            cpt.porcentajeI
          FROM
            gp_concepto con
            LEFT JOIN gp_concepto_tarifa cpt ON con.id_unico = cpt.concepto
            LEFT JOIN gp_tarifa tar ON cpt.tarifa = tar.id_unico
            LEFT JOIN gp_tipo_tarifa tpt ON tar.tipo_tarifa = tpt.id_unico
            LEFT JOIN gf_elemento_unidad elu ON cpt.elemento_unidad = elu.id_unico
            LEFT JOIN gf_unidad_factor unf ON elu.unidad_empaque = unf.id_unico
            WHERE con.plan_inventario = $plan_inventario
            AND tar.id_unico IS NOT NULL");
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

    public function obtnerConceptoPlanI($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    con.plan_inventario
            FROM gp_concepto con
            WHERE con.id_unico = $id");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptosPlanId()
    {
        try {
            $plan = $this->plan_inventario;
            $res = $this->con->Listar("SELECT DISTINCT
            con.id_unico,
            con.nombre
          FROM
            gp_concepto con
            LEFT JOIN gp_concepto_tarifa cpt ON con.id_unico = cpt.concepto
            LEFT JOIN gp_tarifa tar ON cpt.tarifa = tar.id_unico
            WHERE     con.plan_inventario = $plan");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
