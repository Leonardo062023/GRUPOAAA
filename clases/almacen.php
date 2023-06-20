<?php
require_once('./Conexion/ConexionPDO.php');
class almacen
{

    public $id_unico;

    private $con;

    public function __construct()
    {
        $this->con = new ConexionPDO();
    }

    public function obtenerAsociado($clase)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, nombre, UPPER(sigla)
                    FROM            gf_tipo_movimiento
                    WHERE            clase = $clase");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposAsociado($id, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT  DISTINCT id_unico, nombre, UPPER(sigla)
                    FROM             gf_tipo_movimiento
                    WHERE            id_unico != $id
                    AND              clase     = $clase");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerInfoAso($idaso)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT mv.id_unico, mv.numero, TO_CHAR(mv.fecha,'DD/MM/YYYY'), tmv.nombre, tmv.sigla
            FROM gf_movimiento mv
            LEFT JOIN gf_detalle_movimiento dtm ON dtm.movimiento = mv.id_unico
            LEFT JOIN gf_tipo_movimiento tmv ON mv.tipomovimiento = tmv.id_unico
            WHERE mv.id_unico = $idaso");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataXDetalle($id)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE movimiento = $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoMovimiento($clase, $compania)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT tm.id_unico, UPPER(tm.nombre || ' ' || UPPER(tm.sigla))
            FROM gf_tipo_movimiento tm
            LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
            WHERE tm.clase = $clase
            AND tm.compania = $compania");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposDiferentes($tipo, $compania, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT tm.id_unico, UPPER(tm.nombre || ' ' || UPPER(tm.sigla))
            FROM gf_tipo_movimiento tm
            LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
            WHERE tm.clase = $clase
            AND tm.id_unico != $tipo
            AND compania = $compania");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCentroCosto($compania, $param)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre) AS nombre
            FROM  gf_centro_costo
            WHERE compania = $compania
            AND  parametrizacionanno = $param
            ORDER BY nombre DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCentroCostoDiff($compania, $param, $id)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre) AS nombre
            FROM  gf_centro_costo
            WHERE = $compania
            AND parametrizacionanno = $param
            AND id_unico != $id
            ORDER BY nombre DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerProyectos()
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre) AS nombre FROM gf_proyecto ORDER BY nombre DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerProyectosDiff($id)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre) AS nombre FROM gf_proyecto WHERE id_unico != $id ORDER BY nombre DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependencia($compania, $tipo)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre || ' ' || sigla)
                    FROM   gf_dependencia
                    WHERE  compania        = $compania
                    AND    tipodependencia = $tipo");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependenciaDiff($compania, $tipo, $id)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT id_unico, UPPER(nombre || ' ' || sigla)
                    FROM   gf_dependencia
                    WHERE  compania        = $compania
                    AND    tipodependencia = $tipo
                    AND    id_unico       != $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerResponsablesDiff($id, $compania, $dependencia)
    {
        try {
            $res = $this->con->Listar(
                "SELECT DISTINCT ter.id_unico,
            UPPER(
            CASE
            WHEN TRIM(ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
            ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END
            ) || ' ' || ti.nombre || ' ' || ter.numeroidentificacion
            FROM gf_dependencia_responsable dpr
            LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
            LEFT JOIN gf_dependencia_responsable dtr ON dtr.responsable = ter.id_unico
            WHERE ter.id_unico != $id
            AND ter.compania = $compania
            AND dtr.dependencia = $dependencia"
            );
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTerceros($compania)
    {
        try {
            try {
                $res = $this->con->Listar("SELECT ter.id_unico,
                UPPER(
                CASE
                WHEN TRIM(ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
                ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
                END || ' ' || ti.nombre || ' ' || ter.numeroidentificacion
                )
                FROM gf_tercero ter
                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                WHERE (prt.perfil BETWEEN 5 AND 6)
                AND       ter.compania = $compania");
                return $res;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoDocSoporte()
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a ORDER BY nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoDocSoporteDiff($id)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a WHERE id_unico != $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovsBusqueda($compania, $param)
    {
        try {
            $res = $this->con->Listar("SELECT mov.id_unico,
            tpm.sigla || ' ' || mov.numero || ' ' || TO_CHAR(mov.fecha, 'DD/MM/YYYY'),
            CASE
            WHEN TRIM(ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
            ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END
            FROM gf_movimiento mov
            LEFT JOIN gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
            LEFT JOIN gf_tercero ter ON ter.id_unico = mov.tercero2
            WHERE tpm.clase IN (2)
            AND mov.compania = $compania
            AND mov.parametrizacionanno = $param");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataMov($id)
    {
        try {
            $id_unico = base64_decode($id);
            $res = $this->con->Listar("SELECT mv.id_unico, mv.tipomovimiento, UPPER(tpm.nombre || ' ' || tpm.sigla), mv.numero,
            TO_CHAR(mv.fecha, 'DD/MM/YYYY'), mv.centrocosto, UPPER(cnc.nombre), mv.proyecto,
            UPPER(pry.nombre), mv.dependencia, UPPER(dpc.nombre || ' ' || dpc.sigla),
            mv.tercero,
            (
            CASE
            WHEN TRIM(trc.nombreuno || ' ' || trc.nombredos || ' ' || trc.apellidouno || ' ' || trc.apellidodos) = '' THEN trc.razonsocial
            ELSE trc.nombreuno || ' ' || trc.nombredos || ' ' || trc.apellidouno || ' ' || trc.apellidodos
            END
            ),
            mv.descripcion,
            mv.tercero2,
            (
            CASE
            WHEN TRIM(ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
            ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END
            ),
            mv.porcivaglobal, mv.tipo_doc_sop, UPPER(tpd.nombre), mv.numero_doc_sop, mv.descuento
            FROM gf_movimiento mv
            LEFT JOIN gf_tipo_movimiento tpm ON mv.tipomovimiento = tpm.id_unico
            LEFT JOIN gf_centro_costo cnc ON mv.centrocosto = cnc.id_unico
            LEFT JOIN gf_proyecto pry ON mv.proyecto = pry.id_unico
            LEFT JOIN gf_dependencia dpc ON mv.dependencia = dpc.id_unico
            LEFT JOIN gf_tercero trc ON mv.tercero = trc.id_unico
            LEFT JOIN gf_tercero ter ON mv.tercero2 = ter.id_unico
            LEFT JOIN gf_tipo_documento_soporte_a tpd ON mv.tipo_doc_sop = tpd.id_unico
            WHERE     mv.id_unico = $id_unico");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerParametroBasico($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT valor FROM gs_parametros_basicos WHERE id_unico = $id");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerPlanInventario($compania)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, UPPER(codi || ' ' || nombre)
                    FROM     gf_plan_inventario
                    WHERE    tienemovimiento = 2
                    AND      compania        = $compania
                    AND      codi           != ' '
                    ORDER BY codi ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * GuardarMov
     *
     * Proceso para guardar movimiento de almacen
     *
     * @param int $tipo
     * @param string $numero
     * @param date   $fecha
     * @param int    $centrocosto
     * @param int    $proyecto
     * @param int    $dep
     * @param int    $responsable
     * @param int    $tercero
     * @param double $iva
     * @param int    $tipo_doc
     * @param string $num_doc
     * @param double $descuento
     * @return bool|mysqli_result|string
     */
    public function GuardarMov(
        $tipo,
        $numero,
        $fecha,
        $centrocosto,
        $proyecto,
        $dep,
        $responsable,
        $tercero,
        $iva,
        $tipo_doc,
        $num_doc,
        $descuento,
        $des,
        $compania,
        $param
    ) {
        try {
            $sql_cons  = "INSERT INTO gf_movimiento (tipomovimiento, numero, fecha, centrocosto, proyecto, dependencia, tercero,
            tercero2, porcivaglobal, tipo_doc_sop, numero_doc_sop, descuento, descripcion,compania, parametrizacionanno, estado) 
            VALUES (:tipo, :numero, :fecha, :centrocosto, :proyecto, :dep, :responsable, :tercero, :iva, :tipo_doc, :num_doc,
            :descuento, :descripcion, :compania, :parametrizacion, :estado )";
            $sql_dato = array(
                array(":tipo", $tipo),
                array(":numero", $numero),
                array(":fecha", $fecha),
                array(":centrocosto", $centrocosto),
                array(":proyecto", $proyecto),
                array(":dep", $dep),
                array(":responsable", $responsable),
                array(":tercero", $tercero),
                array(":iva", $iva),
                array(":tipo_doc", $tipo_doc),
                array(":num_doc", $num_doc),
                array(":descuento", $descuento),
                array(":descripcion", $des),
                array(":compania", $compania),
                array(":parametrizacion", $param),
                array(":estado", 2),
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

    public function getData($id_unico)
    {
        try {
            $res = $this->con->Listar("SELECT dtm.id_unico
            FROM gf_detalle_movimiento dtm
            LEFT JOIN gf_plan_inventario pln ON dtm.planmovimiento = pln.id_unico
            WHERE     dtm.movimiento      = $id_unico
            AND       pln.tipoinventario != 5");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function get_values_detail($id_unico)
    {
        try {
            $values = array();
            $res    = $this->con->Listar("SELECT id_unico, planmovimiento, cantidad, valor, iva, movimiento FROM gf_detalle_movimiento WHERE id_unico = $id_unico");
            if (count($res) > 0) {
                $values[0] = $res[0][0]; #Id del Detalle
                $values[1] = $res[0][1]; #Id de plan inventario
                $values[2] = $res[0][2]; #Cantidad de elementos
                $values[3] = $res[0][3]; #Valor del detalle
                $values[4] = $res[0][4]; #Valor iva del detalle
                $values[5] = $res[0][5]; #Id del movimiento
            }
            return $values;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtnerDataAsociado($aso)
    {
        try {
            $res = $this->con->Listar("SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $aso");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function save_detail_mov($planI, $cantidad, $valor, $valorIva, $movimiento, $afectado = NULL)
    {
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i:s');
            $sql_cons  = "INSERT INTO gf_detalle_movimiento (planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, hora) 
            VALUES (:planI, :cantidad, :valor, :iva, :movimiento, :detalleasociado, :hora)";
            $sql_dato = array(
                array(":planI", $planI),
                array(":cantidad", $cantidad),
                array(":valor", $valor),
                array(":iva", $valorIva),
                array(":movimiento", $movimiento),
                array(":detalleasociado", $afectado),
                array(":hora", $hora),
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

    public function obtenerUnidadesElemento($elemento)
    {
        try {
            $res = $this->con->Listar("SELECT gct.id_unico, gpl.codi, UPPER(gpl.nombre), UPPER(gun.nombre), gct.porcentajeI
            FROM gp_concepto_tarifa gct
            LEFT JOIN gp_concepto gcn ON gct.concepto = gcn.id_unico
            LEFT JOIN gf_elemento_unidad gel ON gct.elemento_unidad = gel.id_unico
            LEFT JOIN gf_unidad_factor gun ON gel.unidad_empaque = gun.id_unico
            LEFT JOIN gf_plan_inventario gpl ON gcn.plan_inventario = gpl.id_unico
            WHERE gcn.plan_inventario IN ($elemento)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ActualizarPorcentajeIncremento($id, $x)
    {
        try {
            $sql_cons = "UPDATE gp_concepto_tarifa 
            SET porcentajeI=:porcentajeI
            WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":porcentajeI", $x),
                array(":id_unico", $id),
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

    public function obtenerTarifasConceptoElemento($id)
    {
        try {
            $res = $this->con->Listar("SELECT gtr.id_unico, gtr.valor, gct.porcentajeI, gel.valor_conversion, gct.id_unico
            FROM gp_concepto_tarifa gct
            LEFT JOIN gp_concepto gcn ON gct.concepto = gcn.id_unico
            LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
            LEFT JOIN gf_elemento_unidad gel ON gct.elemento_unidad = gel.id_unico
            WHERE gcn.plan_inventario = $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRelacionHijosUnidad($padre, $unidad)
    {
        try {
            $res = $this->con->Listar("SELECT gpa.plan_hijo, gpa.cantidad
            FROM gf_plan_inventario_asociado gpa
            LEFT JOIN gp_concepto_tarifa gct ON gpa.tarifa = gct.id_unico
            LEFT JOIN gf_elemento_unidad gel ON gct.elemento_unidad = gel.id_unico
            WHERE gpa.plan_padre     = $padre
            AND gel.unidad_empaque = $unidad");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
