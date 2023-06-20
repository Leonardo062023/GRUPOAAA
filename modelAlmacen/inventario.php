<?php
require_once('./Conexion/ConexionPDO.php');

class inventario
{

    private $con;

    public $id_unico;

    public function getIdUnico()
    {
        return $this->id_unico;
    }

    public function setIdUnico($id_unico)
    {
        $this->id_unico = $id_unico;
    }

    public function __construct()
    {
        $this->con = new ConexionPDO();
    }

    public function obtnerPredecesor()
    {
        $compania = $_SESSION['compania'];
        try {
            $row = $this->con->Listar("SELECT gfpi.id_unico, gfpi.codi || ' ' || gfpi.nombre AS plan
            FROM gf_plan_inventario gfpi
            LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico
            WHERE gfpi.tienemovimiento = 1 AND gfpi.compania = $compania 
            ORDER BY gfpi.codi ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventario()
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario ORDER BY nombre ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactor()
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivo()
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterFicha()
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, descripcion FROM gf_ficha ORDER BY descripcion ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerPadre()
    {
        $compania = $_SESSION['compania'];
        try {
            $row = $this->con->Listar("SELECT id_unico, codi || ' ' || nombre
            FROM gf_plan_inventario WHERE compania = $compania ORDER BY id_unico ASC");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterPadreId($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, codi || ' ' || nombre AS nombre_completo, codi, predecesor FROM gf_plan_inventario WHERE id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadHijos($padre)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT count(id_unico) FROM gf_plan_inventario WHERE predecesor = $padre");

            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoCodigoHijo($padre)
    {
        try {
            $row = $this->con->Listar("SELECT (id_unico) FROM gf_plan_inventario WHERE predecesor = $padre order by codi desc FETCH FIRST 1 ROW ONLY");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventarioId($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactorId($id_unico, $plan)
    {
        try {
            $row = $this->con->Listar("SELECT gct.id_unico, gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, guf.id_unico, guf.nombre
            FROM gp_concepto_tarifa gct
            LEFT JOIN gf_elemento_unidad gun ON gct.elemento_unidad = gun.id_unico
            LEFT JOIN gf_unidad_factor guf ON gun.unidad_empaque = guf.id_unico
            LEFT JOIN gp_concepto gcn ON gct.concepto = gcn.id_unico
            LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
            WHERE guf.id_unico = $id_unico
            AND gcn.plan_inventario = $plan");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivoId($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventarioDiferentes($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico != $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactorDiferentes($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico != $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivoDiferentes($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico != $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDatosPlan($id_unico)
    {
        try {
            $row = $this->con->Listar("SELECT id_unico, nombre, codi, tienemovimiento, compania, tipoinventario, unidad, predecesor, tipoactivo, ficha
                        FROM   gf_plan_inventario
                        WHERE  id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtner_hijos($codigo)
    {
        $compania = $_SESSION['compania'];
        try {
            $row = $this->con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE codi LIKE '$codigo%' AND compania = $compania");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function actualizar_tipo_inv($id_unico, $tipo)
    {
        try {
            $sql_cons = "UPDATE gf_plan_inventario 
            SET tipoactivo=:tipo
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":tipo", $tipo),
                array(":id_unico", $id_unico),
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

    public function obtenerCodigo($id)
    {
        $xxx = 0;
        $res = $this->con->Listar("SELECT codi FROM gf_plan_inventario WHERE id_unico = $id");
        if (count($res) > 0) {
            $xxx = $res[0][0];
        }
        return $xxx;
    }

    public function buscarElementos($codBarras)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT gpl.id_unico, gpl.codi, UPPER(gct.nombre), gpl.unidad
                FROM gp_concepto gct
                LEFT JOIN gf_plan_inventario gpl ON gct.plan_inventario = gpl.id_unico
                WHERE (
                      gpl.codigo_barras LIKE '%$codBarras%'
                      OR gpl.nombre LIKE '%$codBarras%'
                      OR gpl.codi LIKE '%$codBarras%'
                      )
                AND xFactura = 1
                AND ROWNUM <= 20");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerIdConcepto($id_unico)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT id_unico FROM gp_concepto WHERE plan_inventario = $id_unico");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerIdElemento($codi)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE codigo_barras = '$codi'");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorProducto($plan, $unidad)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT gtr.valor
                FROM gp_concepto_tarifa gct
                LEFT JOIN gp_concepto gcn ON gct.concepto = gcn.id_unico
                LEFT JOIN gf_elemento_unidad geu ON gct.elemento_unidad = geu.id_unico
                LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
                WHERE gcn.plan_inventario = $plan
                AND geu.unidad_empaque = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptoFactura($plan)
    {
        try {
            $xxx = 0;
            $plan = base64_decode($plan);
            $res = $this->con->Listar("SELECT id_unico FROM gp_concepto WHERE plan_inventario= $plan");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtener()
    {
        try {
            $id = base64_decode($this->id_unico);
            $res = $this->con->Listar("SELECT nombre, id_unico FROM gf_plan_inventario WHERE id_unico = $id");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerUnidadF()
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadId($id)
    {
        try {
            $xxx = "";
            $res = $this->con->Listar("SELECT nombre FROM gf_unidad_factor WHERE id_unico = $id");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleAnteriorEntrada($plan, $unidad)
    {
        try {
            $res = $this->con->Listar("SELECT gdm.id_unico, gpp.precio_act, gdm.unidad_origen
                FROM gf_precio_producto gpp
                LEFT JOIN gf_detalle_movimiento gdm ON gpp.detalle_mov = gdm.id_unico
                WHERE gdm.planmovimiento = $plan
                AND gpp.unidad = $unidad
                ORDER BY gpp.id_unico DESC
                OFFSET 1 ROWS FETCH NEXT 1 ROW ONLY");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerElementosDiferenteId($id)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, codi || ' ' || UPPER(nombre) AS concatenado
                FROM gf_plan_inventario
                WHERE id_unico != $id
                ORDER BY codi ASC;");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoElementos()
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, codi || ' ' || UPPER(nombre) AS concatenado
                FROM gf_plan_inventario
                ORDER BY codi ASC;");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataAsociados($padre, $tarifa)
    {
        try {
            $res = $this->con->Listar("SELECT gpa.id_unico, gpl.codi || ' ' || UPPER(gpl.nombre) AS concatenado, gpa.cantidad
                FROM gf_plan_inventario_asociado gpa
                LEFT JOIN gf_plans_inventario gpl ON gpa.plan_hijo = gpl.id_unico
                WHERE gpa.plan_padre = $padre
                AND gpa.tarifa = $tarifa");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarRelacionData($padre, $hijo, $tarifa, $cantidad)
    {
        try {
            $sql_cons  = "INSERT INTO gf_plan_inventario_asociado (plan_padre, plan_hijo, tarifa, cantidad) 
            VALUES (:plan_padre, :plan_hijo, :tarifa, :cantidad)";
            $sql_dato = array(
                array(":plan_padre", $padre),
                array(":plan_hijo", $hijo),
                array(":tarifa", $tarifa),
                array(":cantidad", $cantidad),
            );
            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarRelacion($id)
    {
        try {
            $sql_cons = "DELETE FROM gf_plan_inventario_asociado WHERE id_unico = :id_unico";
            
            $sql_dato = array(
                array(":id_unico", $id)
            );

            $resp = $this->con->InAcEl($sql_cons, $sql_dato);

            if (empty($resp)) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>

<?php
?>