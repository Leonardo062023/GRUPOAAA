<?php
require_once('./Conexion/ConexionPDO.php');
/**
 * Modelo de facturación
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
    public $vendedor;
    public $parametrizacionanno;
    public $descuento;

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

    public function getNumeroFactura()
    {
        return $this->numero_factura;
    }

    public function setNumeroFactura($numero_factura)
    {
        $this->numero_factura = $numero_factura;
    }

    public function getTipofactura()
    {
        return $this->tipofactura;
    }

    public function setTipofactura($tipofactura)
    {
        $this->tipofactura = $tipofactura;
    }

    public function getTercero()
    {
        return $this->tercero;
    }

    public function setTercero($tercero)
    {
        $this->tercero = $tercero;
    }

    public function getFechaFactura()
    {
        return $this->fecha_factura;
    }

    public function setFechaFactura($fecha_factura)
    {
        $this->fecha_factura = $fecha_factura;
    }

    public function getFechaVencimiento()
    {
        return $this->fecha_vencimiento;
    }

    public function setFechaVencimiento($fecha_vencimiento)
    {
        $this->fecha_vencimiento = $fecha_vencimiento;
    }

    public function getCentrocosto()
    {
        return $this->centrocosto;
    }

    public function setCentrocosto($centrocosto)
    {
        $this->centrocosto = $centrocosto;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getEstadoFactura()
    {
        return $this->estado_factura;
    }

    public function setEstadoFactura($estado_factura)
    {
        $this->estado_factura = $estado_factura;
    }

    public function getResponsable()
    {
        return $this->responsable;
    }

    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;
    }

    public function getVendedor()
    {
        return $this->vendedor;
    }

    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
    }

    public function getParametrizacionanno()
    {
        return $this->parametrizacionanno;
    }

    public function setParametrizacionanno($parametrizacionanno)
    {
        $this->parametrizacionanno = $parametrizacionanno;
    }

    public function getDescuento()
    {
        return $this->descuento;
    }

    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    }

    public function registrar(factura $data)
    {
        try {
            $sql_cons  = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, centrocosto,
                                      descripcion, estado_factura, responsable, vendedor, parametrizacionanno) 
            VALUES (:numero_factura, :tipofactura, :tercero, :fecha_factura, :fecha_vencimiento, :centrocosto, :descripcion,
            :estado_factura, :responsable, :vendedor, :parametrizacionanno)";
            $sql_dato = array(
                array(":numero_factura", $data->numero_factura),
                array(":tipofactura", $data->tipofactura),
                array(":tercero", $data->tercero),
                array(":fecha_factura", $data->fecha_factura),
                array(":fecha_vencimiento", $data->fecha_vencimiento),
                array(":centrocosto", $data->centrocosto),
                array(":descripcion", $data->descripcion),
                array(":estado_factura", $data->estado_factura),
                array(":responsable", $data->responsable),
                array(":vendedor", $data->vendedor),
                array(":parametrizacionanno", $data->parametrizacionanno),
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

    public function modificar($id_unico, $fecha, $fecha_vencimiento, $descripcion, $tercero, $vendedor)
    {
        try {
            // $sql = "UPDATE gp_factura
            //         SET    fecha_factura     = '$fecha',
            //                fecha_vencimiento = '$fecha_vencimiento',
            //                descripcion       = '$descripcion',
            //                tercero           = $tercero, 
            //                vendedor          = $vendedor 
            //         WHERE  id_unico          = $id_unico";
            // return $this->mysqli->query($sql);
            $sql_cons = "UPDATE gp_factura 
            SET fecha_factura = :fecha, fecha_vencimiento=:fecha_vencimiento, descripcion=: descripcion, tercero=:tercero, vendedor=:vendedor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":fecha", $fecha),
                array(":fecha_vencimiento", $fecha_vencimiento),
                array(":descripcion", $descripcion),
                array(":tercero", $tercero),
                array(":vendedor", $vendedor),
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

    public function obtnerFactura($id_unico)
    {
        try {
            $id_unico = base64_decode($id_unico);
            $row = $this->con->Listar("SELECT gpf.id_unico, gpf.tipofactura, gpf.numero_factura, gpf.tercero, gpf.centrocosto,
            TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'),
            TO_CHAR(gpf.fecha_vencimiento, 'DD/MM/YYYY'),
            gpf.descripcion, gpf.estado_factura, gpf.tercero, gpf.descuento, gef.nombre, gtp.resolucion,
            gtp.nombre,
            (
              CASE
                WHEN TRIM(gtr.nombre_comercial) IS NULL THEN
                  CASE
                    WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                    ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
                  END
                ELSE
                  CASE
                    WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial || ' ' || gtr.nombre_comercial
                    ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos || ' ' || gtr.nombre_comercial
                  END
              END
            ),
            gtr.numeroidentificacion,
            gdr.direccion,
            gci.nombre,
            (
              CASE
                WHEN TRIM(gvn.nombreuno || ' ' || gvn.nombredos || ' ' || gvn.apellidouno || ' ' || gvn.apellidodos) = '' THEN gvn.razonsocial
                ELSE gvn.nombreuno || ' ' || gvn.nombredos || ' ' || gvn.apellidouno || ' ' || gvn.apellidodos
              END
            ),
            gpf.vendedor
            FROM gp_factura gpf
            LEFT JOIN gp_estado_factura gef ON gpf.estado_factura = gef.id_unico
            LEFT JOIN gp_tipo_factura gtp ON gpf.tipofactura = gtp.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            LEFT JOIN gf_direccion gdr ON gdr.tercero = gtr.id_unico
            LEFT JOIN gf_ciudad gci ON gdr.ciudad_direccion = gci.id_unico
            LEFT JOIN gf_tercero gvn ON gpf.vendedor = gvn.id_unico
            WHERE gpf.id_unico = $id_unico");
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimaFacturaTN($tipo, $numero)
    {
        try {
            $id  = 0;
            $res = $this->con->Listar("SELECT MAX(id_unico) FROM gp_factura WHERE tipofactura = $tipo AND numero_factura = $numero");
            if (count($res) > 0) {
                $id  = $res[0][0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoComprobanteCnt($tipo)
    {
        try {
            $id  = 0;
            $res = $this->con->Listar("SELECT tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $tipo");
            if (count($res) > 0) {
                $id  = $res[0][0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFecha($tipo)
    {
        try {
            $row = $this->con->Listar("SELECT MAX(fecha_factura) FROM gp_factura WHERE tipofactura = $tipo");
            return $row[0][0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerEstado($id_unico)
    {
        try {
            if (!empty($id_unico)) {
                $nom = "";
                $res = $this->con->Listar("SELECT nombre FROM gp_estado_factura WHERE id_unico = $id_unico");
                if (count($res) > 0) {
                    $nom = $res[0][0];
                }
                return $nom;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoCompania($id_unico)
    {
        try {
            $id_ = 0;
            $res = $this->con->Listar("SELECT tipo_compania FROM gf_tercero WHERE id_unico = $id_unico");
            if (count($res) > 0) {
                $id_ = $res[0][0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerClaseFactura($id_unico)
    {
        try {
            $id_ = 0;
            $res = $this->con->Listar("SELECT clase_factura FROM gp_tipo_factura WHERE id_unico = $id_unico");
            if (count($res) > 0) {
                $id_ = $res[0][0];
            }
            return TRIM($id_);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerTipoMovimiento($id)
    {
        try {
            $id_ = 0;
            $res = $this->con->Listar("SELECT tipo_movimiento FROM gp_tipo_factura WHERE id_unico = $id");
            if (count($res) > 0) {
                $id_ = $res[0][0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerDependenciasResponsable($tercero)
    {
        try {
            $res = $this->con->Listar("SELECT dep.id_unico, dep.sigla || ' ' || dep.nombre
            FROM gf_dependencia_responsable dpr
            LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
            LEFT JOIN gf_dependencia dep ON dpr.dependencia = dep.id_unico
            WHERE dpr.responsable = $tercero
            AND  dep.xFactura    = 0
            OR dep.tipodependencia != 1");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependencias()
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, CONCAT_WS(' ',sigla, nombre) FROM gf_dependencia ORDER BY nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataTercero($id)
    {
        try {
            $res = $this->con->Listar("SELECT ter.id_unico,
            CASE
                WHEN (ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
                ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END
            FROM gf_tercero ter
            LEFT JOIN gf_tipo_identificacion tpi ON ter.tipoidentificacion = tpi.id_unico
            WHERE ter.id_unico = $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarNumeroMaximo($tipo, $param)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT MAX(numero) FROM gf_movimiento WHERE  tipomovimiento = $tipo AND parametrizacionanno = $param");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarNumero($tipo, $param)
    {
        $xxx = $this->buscarNumeroMaximo($tipo, $param);
        if (empty($xxx)) {
            $anno = $this->obtenerAnnoParam($param);
            $num  = $anno . '000001';
        } else {
            $num  = $xxx + 1;
        }
        return $num;
    }

    public function obtenerAnnoParam($param)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarComprobantesFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT cnt.id_unico as cnt, ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura        = tpg.id_unico
                    AND         tpc.id_unico          = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante   = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante  = tcp.id_unico
                    AND         pg.numero_factura     = ptal.numero
                    AND         pg.numero_factura     = cnt.numero
                    AND         pg.id_unico           =  $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarMovFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT detallemovimiento FROM gp_detalle_factura WHERE factura = $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenercomprobanteCntFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT      dtc.comprobante
                    FROM        gp_detalle_factura dtf
                    LEFT JOIN   gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante
                    WHERE       dtf.factura = $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalles($factura)
    {
        try {
            $factura = base64_decode($factura);
            $res = $this->con->Listar("SELECT    gdf.id_unico, gdf.concepto_tarifa, gdf.valor, gdf.cantidad, gdf.iva, gdf.impoconsumo, gdf.ajuste_peso, gdf.valor_total_ajustado, UPPER(gct.nombre)
            FROM      gp_detalle_factura  gdf
            LEFT JOIN gp_concepto  gct ON gdf.concepto_tarifa = gct.id_unico
            WHERE gdf.factura = $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovimientoFactura($clase)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT MIN(id_unico) FROM gp_tipo_factura WHERE clase_factura = $clase");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarFact($num, $tipo, $tercero, $fecha, $estado, $responsable, $vendedor, $param, $centro)
    {
        try {
            // $str = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento,  estado_factura, responsable, vendedor, parametrizacionanno, centrocosto)
            //         VALUES ($num, $tipo, $tercero, '$fecha', '$fecha', $estado, $responsable, $vendedor, $param, $centro)";
            // return $this->mysqli->query($str);
            $sql_cons  = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, estado_factura, responsable, vendedor, parametrizacionanno, centrocosto) 
            VALUES (:numero_factura, :tipofactura, :tercero, :fecha_factura, :fecha_vencimiento, :estado_factura, :responsable, :vendedor, :parametrizacionanno, :centrocosto)";
            $sql_dato = array(
                array(":numero_factura", $num),
                array(":tipofactura", $tipo),
                array(":tercero", $tercero),
                array(":fecha_factura", $fecha),
                array(":fecha_vencimiento", $fecha),
                array(":estado_factura", $estado),
                array(":responsable", $responsable),
                array(":vendedor", $vendedor),
                array(":parametrizacionanno", $param),
                array(":centrocosto", $centro),
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

    public function validarNumeroFactura($tipo, $param)
    {
        try {
            $xxx = $this->obtenerMaximoFactura($tipo, $param);
            if (empty($xxx)) {
                $anno = $this->obtenerAnnoParam($param);
                $num  = $anno . '000001';
            } else {
                $num  = $xxx + 1;
            }
            return $num;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMaximoFactura($tipo, $param)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT MAX(numero_factura) FROM gp_factura WHERE tipofactura = $tipo AND parametrizacionanno = $param");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimoIdTipo($tipo)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT MAX(id_unico) FROM gp_factura WHERE tipofactura = $tipo");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCantidadDetalles($factura)
    {
        try {
            $xxx = 0;
            $res =  $this->con->Listar("SELECT COUNT(*) FROM gp_detalle_factura WHERE factura = $factura");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT    dtf.id_unico, dtf.concepto_tarifa, pln.codi, pln.nombre, dtf.cantidad, dtf.iva, dtf.impoconsumo,
            dtf.valor, dtf.valor_total_ajustado, dtf.ajuste_peso, dtf.detallemovimiento, dtf.factura, dtm.unidad_origen, pln.id_unico,
            dtm.cantidad_origen, dtm.valor, guf.nombre
            FROM      gp_detalle_factura   dtf
            LEFT JOIN gp_concepto con ON dtf.concepto_tarifa   = con.id_unico
            LEFT JOIN gf_plan_inventario   pln ON con.plan_inventario   = pln.id_unico
            LEFT JOIN gf_detalle_movimiento dtm ON dtf.detallemovimiento = dtm.id_unico
            LEFT JOIN gf_unidad_factor  guf ON dtm.unidad_origen     = guf.id_unico
            WHERE factura = $factura
            ORDER BY  dtf.id_unico DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorProducto($concepto)
    {
        try {
            $dat = array();
            $res = $this->con->Listar("SELECT gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo
            FROM      gp_concepto_tarifa gct
            LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
            WHERE gct.concepto = $concepto");
            if (count($res) > 0) {
                $dat['valor'] = $res[0][0];
                $dat['iva']   = $res[0][1];
                $dat['impo']  = $res[0][2];
            }
            return $dat;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtener($id)
    {
        try {
            $data = array();
            $res  = $this->con->Listar("SELECT tpf.nombre, fat.numero_factura, tpf.resolucion, fat.estado_factura,
            CASE
                WHEN ter.nombre_comercial = ' ' THEN
                    CASE
                        WHEN (ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = ' ' THEN ter.razonsocial
                        ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
                    END
                ELSE
                    CASE
                        WHEN (ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = ' ' THEN ter.razonsocial || ' ' || ter.nombre_comercial
                        ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos || ' ' || ter.nombre_comercial
                    END
            END,
            ter.numeroidentificacion, dir.direccion, tel.valor, TO_CHAR(fat.fecha_factura, 'DD/MM/YYYY')
            FROM gp_factura fat
            LEFT JOIN gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
            LEFT JOIN gf_tercero ter ON fat.tercero = ter.id_unico
            LEFT JOIN gf_direccion dir ON dir.tercero = ter.id_unico
            LEFT JOIN gf_telefono tel ON tel.tercero = ter.id_unico
            WHERE fat.id_unico = $id");
            if (count($res) > 0) {
                $data['tipo']     = $res[0][0];
                $data['num']      = $res[0][1];
                $data['res']      = $res[0][2];
                $data['estd']     = $res[0][3];
                $data['cliente']  = $res[0][4];
                $data['doc']      = $res[0][5];
                $data['dir']      = $res[0][6];
                $data['tel']      = $res[0][7];
                $data['fecha']    = $res[0][8];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasFechaa($fechaI, $fechaF)
    {
        try {
            $res = $this->con->Listar("SELECT fat.id_unico, fat.numero_factura, TO_CHAR(fat.fecha_factura, 'DD/MM/YYYY')
            FROM gp_factura fat
            WHERE  fat.fecha_factura BETWEEN TO_DATE($fechaI, 'YYYY-MM-DD') AND TO_DATE($fechaF, 'YYYY-MM-DD')");

            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorFactura($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT valor_total_ajustado FROM gp_detalle_factura WHERE factura = $id");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function cambiarFechaFactura($id, $fecha)
    {
        try {
            // $str = "UPDATE gp_factura SET fecha_factura = '$fecha' WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_factura 
            SET fecha_factura=:fecha
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":fecha_factura", $fecha),
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

    public function obtenerMovAlmacen($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gdm.movimiento
                    FROM      gp_detalle_factura   gpd
                    LEFT JOIN gf_detalle_movimiento gdm ON gpd.detallemovimiento = gdm.id_unico
                    WHERE     gpd.factura = $factura
                    AND       gdm.id_unico IS NOT NULL");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRelacionFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT
                cnt.id_unico AS cnt,
                ptal.id_unico AS ptal
            FROM
                gp_factura pg
            LEFT JOIN gp_tipo_factura tpg ON
                pg.tipofactura = tpg.id_unico
            LEFT JOIN gf_tipo_comprobante tpc ON
                tpc.id_unico = tpg.tipo_comprobante
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.tipocomprobante = tpc.id_unico AND pg.numero_factura = cnt.numero
            LEFT JOIN gf_tipo_comprobante_pptal tcp ON
                tpc.comprobante_pptal = tcp.id_unico
            LEFT JOIN gf_comprobante_pptal ptal ON
                ptal.tipocomprobante = tcp.id_unico AND pg.numero_factura = ptal.numero
            WHERE 
                pg.id_unico  = $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarRelacionCnt($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    dtc.comprobante FROM gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante
                    WHERE     dtf.factura = $factura
                    AND       dtc.comprobante IS NOT NULL");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTarifasElementosD($elemento)
    {
        try {
            $res = $this->con->Listar("SELECT gct.id_unico, gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, gun.id_unico, gun.nombre,
            glu.valor_conversion, gct.porcentajeI, gtr.id_unico
            FROM      gp_concepto_tarifa gct
            LEFT JOIN gp_tarifa          gtr ON gct.tarifa          = gtr.id_unico
            LEFT JOIN gf_elemento_unidad glu ON gct.elemento_unidad = glu.id_unico
            LEFT JOIN gf_unidad_factor   gun ON glu.unidad_empaque  = gun.id_unico
            LEFT JOIN gp_concepto        gcn ON gct.concepto        = gcn.id_unic
            WHERE gcn.plan_inventario  = $elemento");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerInfoConceptoTarifa($id)
    {
        try {
            $data = array();
            $res = $this->con->Listar("SELECT    gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, gun.id_unico, gun.nombre, glu.valor_conversion, gcn.id_unico
            FROM      gp_concepto_tarifa gct
            LEFT JOIN gp_tarifa          gtr ON gct.tarifa          = gtr.id_unico
            LEFT JOIN gf_elemento_unidad glu ON gct.elemento_unidad = glu.id_unico
            LEFT JOIN gf_unidad_factor   gun ON glu.unidad_empaque  = gun.id_unico
            LEFT JOIN gp_concepto        gcn ON gct.concepto        = gcn.id_unico
            WHERE     gct.id_unico = $id");
            if (count($res) > 0) {
                $data['valor']    = $res[0][0];
                $data['iva']      = $res[0][1];
                $data['impo']     = $res[0][2];
                $data['factor']   = $res[0][5];
                $data['concepto'] = $res[0][6];
                $data['unidad']   = $res[0][3];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleMov($id)
    {
        try {
            $xxx = array();
            $res = $this->con->Listar("SELECT dtf.detallemovimiento, dtm.cantidad, dtm.valor
                    FROM gp_detalle_factura AS dtf
                    LEFT JOIN gf_detalle_movimiento AS dtm ON dtf.detallemovimiento = dtm.id_unico
                    WHERE dtf.id_unico = $id");
            if (count($res) > 0) {
                $xxx['id']       = $res[0][0];
                $xxx['cantidad'] = $res[0][1];
                $xxx['valor']    = $res[0][2];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDetalleFac($cantidad, $iva, $impo, $valor, $total, $id)
    {
        try {
            // $str = "UPDATE gp_detalle_factura SET cantidad = $cantidad, iva = $iva, impoconsumo = $impo, valor = $valor, valor_total_ajustado = $total WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_detalle_factura 
            SET cantidad=:cantidad, , iva=:iva, impoconsumo=: impoconsumo, valor=:valor, valor_total_ajustado=:valor_total_ajustado
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":cantidad", $cantidad),
                array(":iva", $iva),
                array(":impoconsumo", $impo),
                array(":valor", $valor),
                array(":valor_total_ajustado", $total),
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

    public function obtenerFacturaDetalle($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT factura FROM gp_detalle_factura WHERE id_unico = $id");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadMinimaPlan($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT unidad FROM gf_plan_inventario WHERE id_unico = $id");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadesConcepto($concepto)
    {
        try {
            $res = $this->con->Listar("SELECT gun.id_unico, gun.nombre
            FROM gp_concepto_tarifa  gtr
            LEFT JOIN gf_elemento_unidad gel ON gtr.elemento_unidad = gel.id_unico
            LEFT JOIN gf_unidad_factor   gun ON gel.unidad_empaque  = gun.id_unico
            WHERE gtr.concepto = $concepto");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasFecha($fecha, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT fat.id_unico, fat.numero_factura
                    FROM      gp_factura fat
                    LEFT JOIN gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
                    WHERE     fat.fecha_factura = $fecha
                    AND       tpf.clase_factura = $clase
                    ORDER BY  fat.fecha_factura ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTercerosDiff($id, $compania)
    {
        try {
            $res = $this->con->Listar("SELECT ter.id_unico,
            UPPER(
                CASE
                    WHEN (ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
                    ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
                END
            ),
            tip.sigla || ' ' || ter.numeroidentificacion
            FROM gf_tercero ter
            LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
            WHERE     ter.id_unico != $id
            AND       ter.compania  = $compania
            ORDER BY  ter.numeroidentificacion");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarTercero($id, $tercero)
    {
        try {
            // $str = "UPDATE gp_factura SET tercero = $tercero WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_factura 
            SET tercero=:tercero
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":tercero", $tercero),
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

    public function obtenerUnidadFactor($unidad, $concepto)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    geu.valor_conversion
                    FROM      gp_concepto_tarifa gct
                    LEFT JOIN gf_elemento_unidad geu ON gct.elemento_unidad = geu.id_unico
                    WHERE     gct.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function cambiarEstadoFactura($factura, $estado)
    {
        try {
            // $str = "UPDATE gp_factura SET estado_factura = $estado WHERE id_unico = $factura";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_factura 
            SET estado_factura=:estado
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":estado", $estado),
                array(":id_unico", $factura),
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

    public function CambiarRemision($factura, $numero, $tipo)
    {
        try {
            // $str = "UPDATE gp_factura SET numero_factura = $numero, tipofactura = $tipo WHERE id_unico = $factura";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_factura 
            SET numero_factura=:numero, tipofactura=:tipo
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":numero", $numero),
                array(":tipo", $tipo),
                array(":id_unico", $factura),
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

    public function obtenerClaseFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT gtp.clase_factura
            FROM gp_factura gft
            LEFT JOIN gp_tipo_factura gtp ON gft.tipofactura = gtp.id_unico
            WHERE gft.id_unico = $factura");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoRecaudo($factura)
    {
        $xxx = 0;
        $res = $this->con->Listar("SELECT tipo_recaudo FROM gp_tipo_factura WHERE id_unico = $factura");
        if ($res > 0) {
            $xxx = $res[0][0];
        }
        return $xxx;
    }

    public function obtenerRecaudoFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gdp.pago
                    FROM      gp_detalle_pago gdp
                    LEFT JOIN gp_detalle_factura gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRelacioncontableCnt($pago)
    {
        try {
            $data = array();
            $res  = $this->con->Listar("SELECT   gdc.comprobante, gdt.comprobantepptal
                    FROM      gp_detalle_pago gdp
                    LEFT JOIN gf_detalle_comprobante  gdc ON gdp.detallecomprobante      = gdc.id_unico
                    LEFT JOIN gf_detalle_comprobante_pptal gdt ON gdc.detallecomprobantepptal = gdt.id_unico
                    WHERE     gdp.pago = $pago");
            if (count($res) > 0) {
                $data['cnt'] = $res[0][0];
                $data['pto'] = $res[0][1];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposComprobantes($tipo)
    {
        try {
            $data = array();
            $res  = $this->con->Listar("SELECT    gtp.id_unico, gtc.id_unico, gtc.comprobante_pptal
                     FROM      gp_tipo_factura gtf
                     LEFT JOIN gp_tipo_pago gtp ON gtf.tipo_recaudo     = gtp.id_unico
                     LEFT JOIN gf_tipo_comprobante gtc ON gtp.tipo_comprobante = gtc.id_unico
                     WHERE     gtf.id_unico = $tipo");
            if (count($res) > 0) {
                $data['tipo_pago'] = $res[0][0];
                $data['tipo_cnt']  = $res[0][1];
                $data['tipo_pto']  = $res[0][2];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarIndicador($tipo)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT xDescuento FROM gp_tipo_factura WHERE id_unico = $tipo");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadConceptoTarifa($concepto, $unidad)
    {
        try {
            $res = $this->con->Listar("SELECT    gtf.valor, gtr.id_unico
                    FROM      gp_concepto_tarifa gtr
                    LEFT JOIN gf_elemento_unidad geu ON gtr.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          gtf ON gtr.tarifa          = gtf.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad ORDER BY  gtf.valor DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function InsertarData(
        $numero_factura,
        $tipofactura,
        $tercero,
        $fecha_factura,
        $fecha_vencimiento,
        $centrocosto,
        $descripcion,
        $estado_factura,
        $responsable,
        $vendedor,
        $parametrizacionanno,
        $descuento
    ) {
        try {
            // $str = "INSERT INTO gp_factura(
            //             numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, centrocosto, descripcion,
            //             estado_factura, responsable, vendedor, parametrizacionanno, descuento
            //           ) VALUES(
            //             $numero_factura, $tipofactura, $tercero, '$fecha_factura', '$fecha_vencimiento', $centrocosto,
            //             '$descripcion', $estado_factura, $responsable, $vendedor, $parametrizacionanno, $descuento
            //           )";

            $sql_cons  = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, centrocosto, descripcion,
            estado_factura, responsable, vendedor, parametrizacionanno, descuento) 
            VALUES (:numero_factura, :tipofactura, :tercero, :fecha_factura, :fecha_vencimiento, :centrocosto, :descripcion,
            :estado_factura, :responsable, :vendedor, :parametrizacionanno, :descuento)";
            $sql_dato = array(
                array(":numero_factura", $numero_factura),
                array(":tipofactura", $tipofactura),
                array(":tercero", $tercero),
                array(":fecha_factura", $fecha_factura),
                array(":fecha_vencimiento", $fecha_vencimiento),
                array(":centrocosto", $centrocosto),
                array(":estado_factura", $estado_factura),
                array(":responsable", $responsable),
                array(":vendedor", $vendedor),
                array(":parametrizacionanno", $parametrizacionanno),
                array(":descuento", $descuento),
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

    public function obtenerIdElementoUnidad($concepto, $unidad)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gel.id_unico
                    FROM      gp_concepto_tarifa gtr
                    LEFT JOIN gf_elemento_unidad gel ON gtr.elemento_unidad = gel.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       gel.unidad_empaque = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarElementoUnidad($id, $factor, $unidad)
    {
        try {
            // $str = "UPDATE gf_elemento_unidad SET unidad_empaque = $unidad, valor_conversion = $factor WHERE id_unico = $id";
            $sql_cons = "UPDATE gf_elemento_unidad 
            SET unidad_empaque=:unidad, valor_conversion=:factor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":unidad", $unidad),
                array(":factor", $factor),
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

    public function actualizarVendedor($id, $tercero)
    {
        try {
            // $str = "UPDATE gp_factura SET vendedor = $tercero WHERE id_unico = $id";
            $sql_cons = "UPDATE gp_factura 
            SET vendedor=:tercero
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":tercero", $tercero),
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

    public function obtenerUnidadElemento($elemento)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT unidad FROM gf_plan_inventario where  id_unico = $elemento");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposClase($clase, $orden)
    {
        try {
            $res = $this->con->Listar("SELECT gtp.id_unico, gtp.nombre || ' ' || gtp.prefijo
            FROM gp_tipo_factura gtp
            WHERE  gtp.clase_factura IN ($clase)
            ORDER BY gtp.id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoConceptos($orden, $param)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gp_concepto WHERE compania = $param ORDER BY id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoTerceros($orden, $compania)
    {
        try {
            $res = $this->con->Listar("SELECT ter.id_unico,
            CASE
                WHEN (ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos) = '' THEN ter.razonsocial
                ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END AS NOMBRE,
            tip.nombre || ' ' || ter.numeroidentificacion || ' ' || ter.digitoverficacion
            FROM gf_tercero ter
            LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
            WHERE     ter.compania = $compania
            ORDER BY id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listdaoFacturas($fechaI, $fechaF, $tipoI, $tipoF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT gpf.id_unico, TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
            CASE
                WHEN (gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
            END
            FROM gp_factura gpf
            LEFT JOIN gp_tipo_factura gtf ON gpf.tipofactura = gtf.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            WHERE  gpf.fecha_factura BETWEEN TO_DATE($fechaI, 'DD-MM-YYYY') AND TO_DATE($fechaF, 'DD-MM-YYYY')
            AND gtf.id_unico   BETWEEN $tipoI AND $tipoF
            AND gtf.clase_factura IN ($clase)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listdaoFacturasDetalle($fechaI, $fechaF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT gpf.id_unico, TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
            CASE
                WHEN (gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
            END
            FROM gp_factura gpf
            LEFT JOIN gp_tipo_factura gtf ON gpf.tipofactura = gtf.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            WHERE gpf.fecha_factura BETWEEN TO_DATE($fechaI, 'DD-MM-YYYY') AND TO_DATE($fechaF, 'DD-MM-YYYY')
            AND gtf.clase_factura IN ($clase)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoConceptosFactura($conceptoI, $conceptoF)
    {
        try {
            $res = $this->con->Listar("SELECT gdf.concepto_tarifa, UPPER(gct.nombre)
            FROM gp_detalle_factura gdf
            LEFT JOIN gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
            WHERE gdf.concepto_tarifa BETWEEN $conceptoI AND $conceptoF
            GROUP BY gdf.concepto_tarifa, UPPER(gct.nombre)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listdaoFacturasConcepto($fechaI, $fechaF, $concepto, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT gpf.id_unico, TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
            CASE
              WHEN gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos IS NULL THEN gtr.razonsocial
              ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
            END
            FROM gp_detalle_factura gdf
            LEFT JOIN gp_factura gpf ON gdf.factura = gpf.id_unico
            LEFT JOIN gp_tipo_factura gtf ON gpf.tipofactura = gtf.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            WHERE gpf.fecha_factura BETWEEN TO_DATE('$fechaI', 'DD/MM/YYYY') AND TO_DATE('$fechaF', 'DD/MM/YYYY')
                AND gtf.clase_factura IN ($clase)
                AND gdf.concepto_tarifa = $concepto
            GROUP BY gpf.id_unico, gpf.fecha_factura, gtf.prefijo, gpf.numero_factura, gpf.descripcion, 
            gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos, gtr.razonsocial");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesConcepto($factura, $concepto)
    {
        try {
            $factura = base64_decode($factura);
            $res = $this->con->Listar("SELECT    gdf.id_unico, gdf.concepto_tarifa, gdf.valor, gdf.cantidad, gdf.iva, gdf.impoconsumo, gdf.ajuste_peso, gdf.valor_total_ajustado, UPPER(gct.nombre)
                    FROM      gp_detalle_factura gdf
                    LEFT JOIN gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE     gdf.factura    = $factura
                    AND       gdf.concepto_tarifa = $concepto");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoTercerosFactura($terI, $terF)
    {
        $res = $this->con->Listar("SELECT gtr.id_unico,
        CASE
          WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
          ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
        END || ' ' || UPPER(gti.nombre) || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion
        FROM gp_factura gft
        LEFT JOIN gf_tercero gtr ON gft.tercero = gtr.id_unico
        LEFT JOIN gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
        WHERE     gtr.id_unico BETWEEN $terI AND $terF
        GROUP BY gtr.id_unico, gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos, gtr.razonsocial, gti.nombre, 
        gtr.numeroidentificacion, gtr.digitoverficacion");
        return $res;
    }

    public function listdaoFacturasTercero($fechaI, $fechaF, $tercero, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT gpf.id_unico, TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
            CASE
              WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
              ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
            END
            FROM gp_factura gpf
            LEFT JOIN gp_tipo_factura gtf ON gpf.tipofactura = gtf.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            WHERE gpf.fecha_factura BETWEEN TO_DATE('$fechaI', 'DD/MM/YYYY') AND TO_DATE('$fechaF', 'DD/MM/YYYY')
            AND gtf.clase_factura   IN ($clase)
            AND gpf.tercero= $tercero");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorRecaudoFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    (gdp.valor + gdp.iva + gdp.impoconsumo + gdp.ajuste_peso)
                    FROM      gp_detalle_pago gdp
                    LEFT JOIN gp_detalle_factura gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ListadoFacturasClaseOrden($clase, $orden, $param)
    {
        try {
            $res = $this->con->Listar("SELECT fat.id_unico,
            tpf.prefijo || ' ' || fat.numero_factura,
            CASE
              WHEN (   ter.nombreuno = ''
                    OR ter.nombredos IS NULL
                    OR ter.apellidouno IS NULL
                    OR ter.apellidodos IS NULL) THEN ter.razonsocial
              ELSE ter.nombreuno || ' ' || ter.nombredos || ' ' || ter.apellidouno || ' ' || ter.apellidodos
            END AS 'NOMBRE',
            ti.nombre || ' ' || ter.numeroidentificacion || ' ' || ter.digitoverficacion AS 'TipoD',
            TO_CHAR(fat.fecha_factura, 'DD/MM/YYYY')
            FROM gp_factura fat
            LEFT JOIN gp_tipo_factura tpf ON tpf.id_unico = fat.tipofactura
            LEFT JOIN gf_tercero ter ON ter.id_unico = fat.tercero
            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
            WHERE tpf.clase_factura IN ($clase)
            AND fat.parametrizacionanno = $param
            ORDER BY fat.id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoFacturasClase($fechaI, $fechaF, $fatI, $fatF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT gft.id_unico,
            gtf.prefijo || ' ' || gft.numero_factura,
            TO_CHAR(gft.fecha_factura, 'DD/MM/YYYY'),
            CASE
              WHEN (   TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = ''
                    OR gtr.nombreuno IS NULL
                    OR gtr.nombredos IS NULL
                    OR gtr.apellidouno IS NULL
                    OR gtr.apellidodos IS NULL) THEN gtr.razonsocial
              ELSE gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos
            END || ' ' || gti.nombre || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion
            FROM gp_factura gft
            LEFT JOIN gp_tipo_factura gtf ON gft.tipofactura = gtf.id_unico
            LEFT JOIN gf_tercero gtr ON gft.tercero = gtr.id_unico
            LEFT JOIN gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
            WHERE gft.id_unico BETWEEN $fatI AND $fatF
            AND gft.fecha_factura BETWEEN TO_DATE('$fechaI', 'DD/MM/YYYY') AND TO_DATE('$fechaF', 'DD/MM/YYYY')
            AND gtf.clase_factura IN ($clase)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorTotalFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT (valor + iva + impoconsumo + ajuste_peso) * cantidad FROM gp_detalle_factura WHERE factura = $factura");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRecaudosFactura($factura)
    {
        try {
            $res = $this->con->Listar("SELECT TO_CHAR(gpg.fecha_pago, 'DD/MM/YYYY'),
            gtp.nombre,
            gpg.numero_pago,
            gdp.valor,
            gdp.iva,
            gdp.impoconsumo,
            gdp.ajuste_peso
            FROM gp_detalle_pago gdp
            LEFT JOIN gp_pago gpg ON gdp.pago = gpg.id_unico
            LEFT JOIN gp_tipo_pago gtp ON gpg.tipo_pago = gtp.id_unico
            LEFT JOIN gp_detalle_factura gft ON gdp.detalle_factura = gft.id_unico
            WHERE gft.factura = $factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoProductosOrden($orden)
    {
        try {
            $res = $this->con->Listar("SELECT gpl.id_unico, gpl.codi || ' ' || UPPER(gpl.nombre)
            FROM gp_concepto gct
            LEFT JOIN gf_plan_inventario gpl ON gct.plan_inventario = gpl.id_unico
            ORDER BY  gpl.id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoFacturasClase($fechaI, $fechaF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT
            gft.id_unico,
            UPPER(gct.nombre),
            SUM(gpd.valor * gpd.cantidad),
            SUM(gpd.cantidad),
            UPPER(gun.nombre),
            SUM(gdm.cantidad),
            SUM(gdm.valor * gdm.cantidad),
            UPPER(gum.nombre)
            FROM
            gp_detalle_factura gpd
            LEFT JOIN gp_factura gft ON gpd.factura = gft.id_unico
            LEFT JOIN gp_tipo_factura gtf ON gft.tipofactura = gtf.id_unico
            LEFT JOIN gp_concepto gct ON gpd.concepto_tarifa = gct.id_unico
            LEFT JOIN gf_detalle_movimiento gdm ON gpd.detallemovimiento = gdm.id_unico
            LEFT JOIN gf_unidad_factor gun ON gdm.unidad_origen = gun.id_unico
            LEFT JOIN gf_plan_inventario gpl ON gct.plan_inventario = gpl.id_unico
            LEFT JOIN gf_unidad_factor gum ON gpl.unidad = gum.id_unico
            WHERE
            gft.fecha_factura BETWEEN TO_DATE($fechaI, 'YYYY/MM/DD') AND TO_DATE($fechaF, 'YYYY/MM/DD')
            AND gtf.clase_factura IN ($clase)
            GROUP BY
            gft.id_unico,
            UPPER(gct.nombre),
            UPPER(gun.nombre),
            UPPER(gum.nombre)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoProductosFechaPlanI($fechaI, $fechaF, $prodI, $podF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT
            TO_CHAR(gft.fecha_factura, 'DD/MM/YYYY'),
            UPPER(gct.nombre),
            SUM(gdf.valor * gdf.cantidad),
            SUM(gdf.cantidad),
            UPPER(gun.nombre),
            UPPER(gum.nombre),
            SUM(gdm.cantidad),
            SUM(gdm.valor * gdm.cantidad),
            gun.id_unico,
            gum.id_unico,
            gdm.planmovimiento,
            gft.id_unico
            FROM
                gp_detalle_factura gdf
                LEFT JOIN gf_detalle_movimiento gdm ON gdf.detallemovimiento = gdm.id_unico
                LEFT JOIN gp_factura gft ON gdf.factura = gft.id_unico
                LEFT JOIN gf_plan_inventario gpl ON gdm.planmovimiento = gpl.id_unico
                LEFT JOIN gp_tipo_factura gtf ON gft.tipofactura = gtf.id_unico
                LEFT JOIN gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
                LEFT JOIN gf_unidad_factor gun ON gdm.unidad_origen = gun.id_unico
                LEFT JOIN gf_unidad_factor gum ON gpl.unidad = gum.id_unico
            WHERE
                gft.fecha_factura BETWEEN TO_DATE($fechaI, 'DD/MM/YYYY') AND TO_DATE($fechaF, 'DD/MM/YYYY')
                AND (gpl.id_unico BETWEEN $prodI AND $podF)
                AND gtf.clase_factura IN ($clase)
            GROUP BY
                gft.fecha_factura,
                UPPER(gct.nombre),
                gun.nombre,
                gum.nombre,
                gun.id_unico,
                gum.id_unico,
                gdm.planmovimiento,
                gft.id_unico
            ORDER BY
                gft.fecha_factura,
                UPPER(gct.nombre) ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoProductosCostoFechaTipoVendedorClase($fechaI, $fechaF, $tipoI, $tipoF, $vendI, $vendF, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT TO_CHAR(gft.fecha_factura, 'DD/MM/YYYY'), UPPER(gtf.prefijo), gft.numero_factura,
            UPPER(gct.nombre), UPPER(gun.nombre), gdf.cantidad, (gdf.valor * gdf.cantidad),
            UPPER(gum.nombre), gdm.cantidad, gdm.valor, (gdm.valor * gdm.cantidad), gdm.planmovimiento,
            gun.id_unico, gdm.id_unico, gum.id_unico
            FROM gp_detalle_factura gdf
            LEFT JOIN gp_factura gft ON gdf.factura = gft.id_unico
            LEFT JOIN gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
            LEFT JOIN gf_detalle_movimiento gdm ON gdf.detallemovimiento = gdm.id_unico
            LEFT JOIN gp_tipo_factura gtf ON gft.tipofactura = gtf.id_unico
            LEFT JOIN gf_unidad_factor gun ON gdm.unidad_origen = gun.id_unico
            LEFT JOIN gf_plan_inventario gpl ON gdm.planmovimiento = gpl.id_unico
            LEFT JOIN gf_unidad_factor gum ON gpl.unidad = gum.id_unico
            WHERE gft.fecha_factura BETWEEN TO_DATE($fechaI, 'DD/MM/YYYY') AND TO_DATE($fechaF, 'DD/MM/YYYY')
            AND gft.tipofactura BETWEEN $tipoI AND $tipoF
            AND gft.vendedor BETWEEN $vendI AND $vendF
            AND gtf.clase_factura IN ($clase)
            ORDER BY gft.fecha_factura, gft.numero_factura");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoCentrocosto($param, $compania)
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, UPPER(nombre) FROM gf_centro_costo WHERE parametrizacionanno = $param AND compania = $compania ORDER BY nombre DESC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoConceptos($param)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT cnp.id_unico, pln.codi || ' ' || UPPER(cnp.nombre), unf.nombre
            FROM gp_concepto_tarifa cont
            LEFT JOIN gp_concepto cnp ON cont.concepto = cnp.id_unico
            LEFT JOIN gf_plan_inventario pln ON cnp.plan_inventario = pln.id_unico
            LEFT JOIN gf_unidad_factor unf ON pln.unidad = unf.id_unico
            WHERE     cnp.id_unico IS NOT NULL AND cnp.parametrizacionanno = $param");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtnerDataFactura($id_unico)
    {
        try {
            $id_unico = base64_decode($id_unico);
            $res = $this->con->Listar("SELECT gpf.id_unico, gpf.tipofactura, gpf.numero_factura, gpf.tercero, gpf.centrocosto,
            TO_CHAR(gpf.fecha_factura, 'DD/MM/YYYY'),
            TO_CHAR(gpf.fecha_vencimiento, 'DD/MM/YYYY'),
            gpf.descripcion, gpf.estado_factura, gpf.tercero, gpf.descuento, gef.nombre, gtp.resolucion,
            gtp.nombre,
            (
            CASE
            WHEN (gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
            ELSE (gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
            END
            ),
            gtr.numeroidentificacion,
            gdr.direccion,
            gci.nombre,
            (
            CASE
            WHEN (gvn.nombreuno || ' ' || gvn.nombredos || ' ' || gvn.apellidouno || ' ' || gvn.apellidodos) = '' THEN gvn.razonsocial
            ELSE (gvn.nombreuno || ' ' || gvn.nombredos || ' ' || gvn.apellidouno || ' ' || gvn.apellidodos)
            END
            ),
            gpf.vendedor,
            (gtp.prefijo || ' ' || gtp.nombre),
            gcc.nombre, gpf.fecha_factura
            FROM gp_factura gpf
            LEFT JOIN gp_estado_factura gef ON gpf.estado_factura = gef.id_unico
            LEFT JOIN gp_tipo_factura gtp ON gpf.tipofactura = gtp.id_unico
            LEFT JOIN gf_tercero gtr ON gpf.tercero = gtr.id_unico
            LEFT JOIN gf_direccion gdr ON gdr.tercero = gtr.id_unico
            LEFT JOIN gf_ciudad gci ON gdr.ciudad_direccion = gci.id_unico
            LEFT JOIN gf_tercero gvn ON gpf.vendedor = gvn.id_unico
            LEFT JOIN gf_centro_costo gcc ON gpf.centrocosto = gcc.id_unico
            WHERE gpf.id_unico = $id_unico");
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listadoBancos($compania)
    {
        try {
            $res = $this->con->Listar("SELECT ctb.id_unico, ctb.numerocuenta || ' ' || UPPER(ctb.descripcion)
            FROM gf_cuenta_bancaria ctb
            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
            WHERE ctbt.tercero = $compania
            ORDER BY  ctb.numerocuenta");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadConceptoTarifaPrimero($concepto, $unidad)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gtf.valor
                    FROM      gp_concepto_tarifa gtr
                    LEFT JOIN gf_elemento_unidad geu ON gtr.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          gtf ON gtr.tarifa          = gtf.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataConceptoTarifa($id_unico)
    {
        try {
            $res = $this->con->Listar("SELECT tarifa, elemento_unidad, concepto FROM gp_concepto_tarifa WHERE  id_unico = $id_unico");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarConceptoTarifa($id_unico)
    {
        try {
            // $str = "DELETE FROM gp_concepto_tarifa WHERE id_unico = $id_unico";
            // return $this->mysqli->query($str);
            $sql_cons = "DELETE FROM gp_concepto_tarifa WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico),
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

    public function eliminarTarifa($id_unico)
    {
        try {
            // $str = "DELETE FROM gp_tarifa WHERE id_unico = $id_unico";
            // return $this->mysqli->query($str);
            $sql_cons = "DELETE FROM gp_tarifa WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico),
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

    public function eliminarElementoUnidad($id_unico)
    {
        try {
            // $str = "DELETE FROM gf_elemento_unidad WHERE id_unico = $id_unico";
            // return $this->mysqli->query($str);
            $sql_cons = "DELETE FROM gf_elemento_unidad WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico),
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

    public function eliminarConcepto($id_unico)
    {
        try {
            // $str = "DELETE FROM gp_concepto WHERE id_unico = $id_unico";
            // return $this->mysqli->query($str);
            $sql_cons = "DELETE FROM gp_concepto WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico),
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

    public function GuardarFacturaPrecio($detalle, $concepto, $unidad, $precio_ant, $precio_act, $estado, $fecha, $usuario)
    {
        try {
            // $str = "INSERT INTO gf_precio_producto(detalle_mov, concepto_tarifa, unidad, precio_ant, precio_act, estado, fecha, usuario)
            //                               VALUES ($detalle, $concepto, $unidad, $precio_ant, $precio_act, $estado, '$fecha', $usuario);";
            // return $this->mysqli->query($str);
            $sql_cons  = "INSERT INTO gf_precio_producto (detalle_mov, concepto_tarifa, unidad, precio_ant, precio_act, estado, fecha, usuario) 
            VALUES (:detalle, :concepto, :unidad, :precio_ant, :precio_act, :estado, :fecha, :usuario)";
            $sql_dato = array(
                array(":detalle", $detalle),
                array(":concepto", $concepto),
                array(":unidad", $unidad),
                array(":precio_ant", $precio_ant),
                array(":precio_act", $precio_act),
                array(":estado", $estado),
                array(":fecha", $fecha),
                array(":usuario", $usuario),
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

    public function actualizarValorTarifa($id, $valor)
    {
        try {
            // $str = "UPDATE gp_tarifa SET valor = $valor WHERE id_unico = $id";
            // return $res = $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_tarifa 
            SET valor=:valor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":valor", $valor),
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

    public function obtenerListadoPrecios($estado)
    {
        try {
            $res = $this->con->Listar("SELECT gpr.id_unico, TO_CHAR(gpr.fecha, 'DD/MM/YYYY'), gpl.codi, UPPER(gpl.nombre),
            UPPER(guf.nombre), gpr.precio_act, gpr.precio_ant, gdm.valor, gep.id_unico, UPPER(gep.nombre),
            gpl.id_unico, guf.id_unico, gtr.id_unico, gdm.planmovimiento
            FROM gf_precio_producto gpr
            LEFT JOIN gf_detalle_movimiento gdm ON gpr.detalle_mov = gdm.id_unico
            LEFT JOIN gp_concepto_tarifa gct ON gpr.concepto_tarifa = gct.id_unico
            LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
            LEFT JOIN gf_unidad_factor guf ON gpr.unidad = guf.id_unico
            LEFT JOIN gf_estado_precio gep ON gpr.estado = gep.id_unico
            LEFT JOIN gf_plan_inventario gpl ON gdm.planmovimiento = gpl.id_unico
            WHERE     gpr.estado IN ($estado)
            ORDER BY  gpr.id_unico DESC, gpl.id_unico, guf.nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoPreciosFecha($estado, $fechaI, $fechaF)
    {
        try {
            $res = $this->con->Listar("SELECT gpr.id_unico, TO_CHAR(gpr.fecha, 'DD/MM/YYYY'), gpl.codi, UPPER(gpl.nombre),
            UPPER(guf.nombre), gpr.precio_act, gpr.precio_ant, gdm.valor, gep.id_unico, UPPER(gep.nombre),
            gpl.id_unico, guf.id_unico, gtr.id_unico, gdm.planmovimiento
            FROM gf_precio_producto gpr
            LEFT JOIN gf_detalle_movimiento gdm ON gpr.detalle_mov = gdm.id_unico
            LEFT JOIN gp_concepto_tarifa gct ON gpr.concepto_tarifa = gct.id_unico
            LEFT JOIN gp_tarifa gtr ON gct.tarifa = gtr.id_unico
            LEFT JOIN gf_unidad_factor guf ON gpr.unidad = guf.id_unico
            LEFT JOIN gf_estado_precio gep ON gpr.estado = gep.id_unico
            LEFT JOIN gf_plan_inventario gpl ON gdm.planmovimiento = gpl.id_unico
            WHERE     gpr.estado IN ($estado)
            AND gpr.fecha BETWEEN TO_DATE($fechaI 'YYYY-MM-DD') AND TO_DATE($fechaF, 'YYYY-MM-DD')
            ORDER BY  gpr.id_unico DESC, gpl.id_unico, guf.nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorAnterior($unidad, $id, $plan)
    {
        try {
            $res = $this->con->Listar("SELECT gdm.valor
            FROM gf_precio_producto gpr
            LEFT JOIN gf_detalle_movimiento gdm ON gpr.detalle_mov = gdm.id_unico
            WHERE gpr.unidad = $unidad
            AND gpr.id_unico < $id
            AND gdm.planmovimiento =  $plan
            ORDER BY gpr.id_unico DESC
            FETCH FIRST 1 ROW ONLY");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function CambiarEstadoPrecio($id, $estado)
    {
        try {
            // $str = "UPDATE gf_precio_producto SET estado = $estado WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gf_precio_producto 
            SET estado=:estado
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":estado", $estado),
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

    public function modificarPrecioEstado($id, $precio, $estado)
    {
        try {
            // $str = "UPDATE gf_precio_producto SET precio_act = $precio, estado = $estado WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gf_precio_producto 
            SET precio_act=:precio, estado =: estado
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":precio_act", $precio),
                array(":estado", $estado),
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

    public function modificarTarifa($id, $valor)
    {
        try {
            // $str = "UPDATE gp_tarifa SET valor = $valor WHERE id_unico = $id";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_tarifa 
            SET valor=:valor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":valor", $valor),
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

    public function obtenerValorConversionUnidadConcepto($concepto, $unidad)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gun.valor_conversion
                    FROM      gp_concepto_tarifa gct
                    LEFT JOIN gf_elemento_unidad gun ON gct.elemento_unidad = gun.id_unico
                    WHERE     gct.concepto       = $concepto
                    AND       gun.unidad_empaque = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallePlanInventario($fat, $plan)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gdm.id_unico
                    FROM      gp_detalle_factura    gdf
                    LEFT JOIN gf_detalle_movimiento gdm ON gdf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gp_concepto           gct ON gdf.concepto_tarifa   = gct.id_unico
                    WHERE     gdf.factura         = $fat
                    AND       gct.plan_inventario = $plan");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarAsociadosPlanInventario($id)
    {
        try {
            $res = $this->con->Listar("SELECT gdm.valor
                    FROM   gf_detalle_movimiento gdm
                    WHERE  gdm.detalleasociado = $id");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorAsociadosSalida($padre)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT valor FROM gf_detalle_movimiento WHERE detalleasociado = $padre");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorConversionUnidadElemento($elemento, $unidad)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gun.valor_conversion
                    FROM      gp_concepto_tarifa gct
                    LEFT JOIN gf_elemento_unidad gun ON gct.elemento_unidad = gun.id_unico
                    LEFT JOIN gp_concepto        gcn ON gct.concepto = gcn.id_unico
                    WHERE     gcn.plan_inventario = $elemento
                    AND       gun.unidad_empaque  = $unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function facturasFechaTipo($fechaI, $fechaF, $tipo)
    {
        try {
            $res = $this->con->Listar("SELECT gpf.id_unico, gpf.numero_factura, gpf.fecha_factura, gpf.fecha_vencimiento, gpf.descripcion,
            gpf.centrocosto, gpf.tercero
            FROM gp_factura gpf
            WHERE (gpf.fecha_factura BETWEEN TO_DATE($fechaI, 'YYYY/MM/DD') AND TO_DATE($fechaF, 'YYYY/MM/DD'))
            AND    (gpf.tipofactura = $tipo )");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDetalleCnt($id, $detalle)
    {
        try {
            // $str = "UPDATE gp_detalle_factura SET detallecomprobante = $detalle WHERE  id_unico = $id";
            // return $this->mysqli->query($str);

            $sql_cons = "UPDATE gp_detalle_factura 
            SET detallecomprobante=:detalle
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":detalle", $detalle),
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

    public function obtenerValorDetalleCnt($comprobante, $cuenta)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT valor FROM gf_detalle_comprobante WHERE comprobante =$comprobante AND cuenta = $cuenta");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDataComprobante($valor, $comprobante, $cuenta)
    {
        try {
            // $str = "UPDATE gf_detalle_comprobante
            //         SET    valor       = valor + ($valor)
            //         WHERE  comprobante = $comprobante
            //         AND    cuenta      = $cuenta";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gf_detalle_comprobante 
            SET valor=:valor
            WHERE comprobante = :comprobante
            AND cuenta =: cuenta";
            $sql_dato = array(
                array(":valor", $valor),
                array(":comprobante", $comprobante),
                array(":cuenta", $cuenta),
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

    /**
     * obtenerTerceroPerfilCompania
     *
     * Metodo para obtener los terceros por medio de perfiles
     *
     * @param int|integer $compania Id de compañia logueada
     * @param string|mixed $orden Ordenamiento para la consulta (ASC, DESC)
     * @return mixed|string
     */
    public function obtenerTerceroOrdenCompania($compania, $orden)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT gtr.id_unico,
            CASE
            WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) IS NULL THEN
            NVL(gtr.razonsocial || ' ' || gti.sigla || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion, '')
            ELSE
            UPPER(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos || ' ' || gti.sigla || ' ' || gtr.numeroidentificacion)
            END
            FROM gf_tercero gtr
            LEFT JOIN gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
            WHERE     (gtr.compania = $compania)
            ORDER BY  gtr.id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarTercerosCuentas($terI, $terF)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT
            gtr.id_unico,
            CASE
                WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE UPPER(TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos))
            END,
            gti.sigla || ' ' || gtr.numeroidentificacion
            FROM
            gp_factura gft
            LEFT JOIN
            gf_tercero gtr ON gft.tercero = gtr.id_unico
            LEFT JOIN
            gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
            WHERE     (gtr.id_unico BETWEEN $terI AND $terF)
            GROUP BY  GROUP BY
            gtr.id_unico,
            CASE
                WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE UPPER(TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos))
            END,
            gti.sigla || ' ' || gtr.numeroidentificacion");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasCliente($cliente, $tipI, $tipF)
    {
        try {
            $res = $this->con->Listar("SELECT
            gft.id_unico,
            TO_CHAR(gft.fecha_factura, 'DD/MM/YYYY'),
            gtf.prefijo || ' ' || gft.numero_factura
            FROM
                gp_factura gft
            LEFT JOIN
                gp_tipo_factura gtf ON gft.tipofactura = gtf.id_unico
            WHERE     (gft.tercero     = $cliente)
            AND       (gtf.id_unico BETWEEN $tipI AND $tipF)
            ORDER BY  gft.fecha_factura ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function VerificarRecaudoFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT DISTINCT gdp.pago
            FROM      gp_detalle_pago    gdp
            LEFT JOIN gp_detalle_factura gdf ON gdp.detalle_factura = gdf.id_unico
            WHERE     gdf.factura = $factura");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarAbonosPago($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT  gdp.valor + gdp.iva + gdp.impoconsumo
                    FROM      gp_detalle_pago gdp
                    LEFT JOIN gp_detalle_factura gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasXFechaTipoSinMovimiento($fechaI, $fechaF, $tipo)
    {
        try {
            $res = $this->con->Listar("SELECT
            gdf.id_unico,
            gpl.id_unico,
            gdf.unidad_origen,
            gpl.unidad,
            gdf.cantidad,
            gft.id_unico
        FROM
            gp_detalle_factura gdf
        LEFT JOIN
            gp_factura gft ON gdf.factura = gft.id_unico
        LEFT JOIN
            gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
        LEFT JOIN
            gf_plan_inventario gpl ON gct.plan_inventario = gpl.id_unico
        WHERE
            gft.fecha_factura BETWEEN TO_DATE('$fechaI', 'YYYY/MM/DD') AND TO_DATE('$fechaF', 'YYYY/MM/DD')
        AND (gft.tipofactura = $tipo)
        AND  (gdf.detallemovimiento IS NULL)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarIdMovimientoFactura($factura)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT gdm.movimiento
                    FROM      gp_detalle_factura    gdf
                    LEFT JOIN gf_detalle_movimiento gdm on gdf.detallemovimiento = gdm.id_unico
                    WHERE     factura = $factura
                    AND       gdf.detallemovimiento IS NOT NULL");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTercero($num)
    {
        try {
            $res = $this->con->Listar("SELECT
            gtr.id_unico,
            CASE
                WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
            END
        FROM
            gf_tercero gtr
        WHERE
            (gtr.numeroidentificacion LIKE '%$num%')
            OR (gtr.razonsocial LIKE '%$num%')
            OR (gtr.nombreuno LIKE '%$num%')
            OR (gtr.apellidouno LIKE '%$num%')
        FETCH FIRST 10 ROWS ONLY");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasTercero($tercero, $clase)
    {
        try {
            $res = $this->con->Listar("SELECT    fat.id_unico, fat.numero_factura
                    FROM      gp_factura      fat
                    LEFT JOIN gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
                    WHERE     fat.tercero       = $tercero
                    AND       tpf.clase_factura =  $clase
                    ORDER BY  fat.fecha_factura ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerVendedores($orden)
    {
        try {
            $res = $this->con->Listar("SELECT DISTINCT
            gtr.id_unico,
            CASE
                WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
            END,
            CASE
                WHEN gtr.razonsocial != ' ' THEN TRIM(gti.sigla || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion)
                ELSE TRIM(gti.sigla || ' ' || gtr.numeroidentificacion)
            END
            FROM
                gp_factura gpf
            LEFT JOIN
                gf_tercero gtr ON gpf.vendedor = gtr.id_unico
            LEFT JOIN
                gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
            WHERE
                gpf.vendedor IS NOT NULL
            GROUP BY
                gtr.id_unico,
                CASE
                    WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                    ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
                END,
                CASE
                    WHEN gtr.razonsocial != ' ' THEN TRIM(gti.sigla || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion)
                    ELSE TRIM(gti.sigla || ' ' || gtr.numeroidentificacion)
                END,
                gti.sigla,
                gtr.numeroidentificacion,
                gtr.digitoverficacion
            ORDER BY
                gtr.id_unico $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoTercerosFactura($orden)
    {
        try {
            $res = $this->con->Listar("SELECT
            gtr.id_unico,
            CASE
                WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
            END,
            CASE
                WHEN gtr.razonsocial != ' ' THEN TRIM(gti.sigla || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion)
                ELSE TRIM(gti.sigla || ' ' || gtr.numeroidentificacion)
            END
            FROM
                gp_factura gft
            LEFT JOIN
                gf_tercero gtr ON gft.tercero = gtr.id_unico
            LEFT JOIN
                gf_tipo_identificacion gti ON gtr.tipoidentificacion = gti.id_unico
            GROUP BY
                gtr.id_unico,
                CASE
                    WHEN TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos) = '' THEN gtr.razonsocial
                    ELSE TRIM(gtr.nombreuno || ' ' || gtr.nombredos || ' ' || gtr.apellidouno || ' ' || gtr.apellidodos)
                END,
                CASE
                    WHEN gtr.razonsocial != ' ' THEN TRIM(gti.sigla || ' ' || gtr.numeroidentificacion || ' ' || gtr.digitoverficacion)
                    ELSE TRIM(gti.sigla || ' ' || gtr.numeroidentificacion)
                END
            ORDER BY
            gtr.id_unico  $orden");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturaAso($fat)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT    gda.factura
                    FROM      gp_detalle_factura gdf
                    LEFT JOIN gp_detalle_factura gda ON gdf.detalleafectado = gda.id_unico
                    WHERE gdf.factura = $fat");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarAfectacionesDetalle($id)
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT gpf.cantidad FROM gp_detalle_factura gpf WHERE  gpf.detalleafectado = $id");
            if (count($res) > 0) {
                foreach ($res as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoFacturasSinCoste($clase, $fecha)
    {
        try {
            $res = $this->con->Listar("SELECT    DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y') AS fecha, UPPER(gpi.nombre) AS nom, gtf.prefijo,
                              gft.numero_factura, gtm.sigla, gmv.numero, UPPER(guf.nombre) as unm, UPPER(guo.nombre) AS uns,
                              (gpf.valor + gpf.iva + gpf.impoconsumo) * gpf.cantidad AS valor
                    FROM      gp_detalle_factura gpf
                    LEFT JOIN gp_factura         gft ON gpf.factura           = gft.id_unico
                    LEFT JOIN gp_tipo_factura    gtf ON gft.tipofactura       = gtf.id_unico
                    LEFT JOIN gf_detalle_movimiento gdm ON gpf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gf_movimiento      gmv ON gdm.movimiento        = gmv.id_unico
                    LEFT JOIN gp_concepto        gct ON gpf.concepto_tarifa   = gct.id_unico
                    LEFT JOIN gf_plan_inventario gpi ON gct.plan_inventario   = gpi.id_unico
                    LEFT JOIN gf_unidad_factor   guf ON gpi.unidad            = guf.id_unico
                    LEFT JOIN gf_unidad_factor   guo ON gdm.unidad_origen     = guo.id_unico
                    LEFT JOIN gf_tipo_movimiento gtm ON gmv.tipomovimiento    = gtm.id_unico
                    WHERE     gtf.clase_factura IN ($clase)
                    AND       gft.fecha_factura <= '$fecha'
                    AND       gdm.valor         = 0
                    ORDER BY  gft.fecha_factura ASC, gtf.id_unico");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ListadoProductosConEntrada()
    {
        try {
            $res = $this->con->Listar("SELECT
            gpi.id_unico,
            gpi.codi || ' ' || UPPER(gpi.nombre) AS gpnom
            FROM
                gf_detalle_movimiento gdm
            LEFT JOIN
                gp_concepto gct ON gct.plan_inventario = gdm.id_unico
            LEFT JOIN
                gf_movimiento gmv ON gdm.movimiento = gmv.id_unico
            LEFT JOIN
                gf_tipo_movimiento gtm ON gmv.tipomovimiento = gtm.id_unico
            LEFT JOIN
                gf_plan_inventario gpi ON gdm.planmovimiento = gpi.id_unico
                WHERE
                gtm.clase = 2
            GROUP BY
                gpi.id_unico,
                gpi.codi,
                UPPER(gpi.nombre)");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function BuscarSalidasProductoEntreFechas($producto, $fechaI, $fechaF)
    {
        try {
            $res = $this->con->Listar("SELECT     gdf.id_unico
                    FROM       gp_detalle_factura gdf
                    LEFT JOIN  gp_factura  gft ON gdf.factura         = gft.id_unico
                    LEFT JOIN  gp_concepto gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE gct.plan_inventario = $producto
                    AND gft.fecha_factura BETWEEN TO_DATE($fechaI, 'YYYY-MM-DD') AND TO_DATE($fechaF, 'YYYY-MM-DD')");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimoCosteElemento($id)
    {
        try {
            $xxx = array();
            $res = $this->con->Listar("SELECT    gdm.valor, DATE_FORMAT(gmv.fecha, '%d/%m/%Y') as fecha
                    FROM      gf_detalle_movimiento AS gdm
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
                    WHERE     gtm.clase          = 2
                    AND       gdm.planmovimiento = $id
                    ORDER BY  gdm.id_unico DESC");
            if (count($res) > 0) {
                $xxx['valor'] = $res[0][0];
                $xxx['fecha'] = $res[0][1];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFechaVenta($id)
    {
        try {
            $xxx = "";
            $res = $this->con->Listar("SELECT
            gdm.valor,
            TO_CHAR(gmv.fecha, 'DD/MM/YYYY') as fecha
            FROM
                gf_detalle_movimiento gdm
            LEFT JOIN
                gf_movimiento gmv ON gdm.movimiento = gmv.id_unico
            LEFT JOIN
                gf_tipo_movimiento gtm ON gmv.tipomovimiento = gtm.id_unico
            WHERE     gct.plan_inventario = $id
            ORDER BY  gft.id_unico");

            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
