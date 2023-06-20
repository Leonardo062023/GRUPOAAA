<?php

/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 26/04/2018
 * Time: 9:57
 */
require_once('./Conexion/ConexionPDO.php');

class concepto_tarifa
{

    public $id_unico;
    public $nombre;
    public $concepto;
    public $tarifa;
    public $elemento_unidad;
    public $porcentajeI;
    public $parametrizacion;
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getConcepto()
    {
        return $this->concepto;
    }

    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;
    }

    public function getTarifa()
    {
        return $this->tarifa;
    }

    public function setTarifa($tarifa)
    {
        $this->tarifa = $tarifa;
    }

    public function getElementoUnidad()
    {
        return $this->elemento_unidad;
    }

    public function setElementoUnidad($elemento_unidad)
    {
        $this->elemento_unidad = $elemento_unidad;
    }

    public function getPorcentajeI()
    {
        return $this->porcentajeI;
    }

    public function setPorcentajeI($porcentajeI)
    {
        $this->porcentajeI = $porcentajeI;
    }

        /**
     * Get the value of parametrizacion
     */ 
    public function getParametrizacion()
    {
        return $this->parametrizacion;
    }

    /**
     * Set the value of parametrizacion
     *
     * @return  self
     */ 
    public function setParametrizacion($parametrizacion)
    {
        $this->parametrizacion = $parametrizacion;

        return $this;
    }

    public function guardar()
    {
        try {
            // $str = "INSERT INTO gp_concepto_tarifa(nombre, concepto, tarifa, elemento_unidad, porcentajeI) 
            //         VALUES ('NULL', $this->concepto, $this->tarifa, $this->elemento_unidad, $this->porcentajeI)";
            // return $this->mysqli->query($str);
            $sql_cons  = "INSERT INTO gp_concepto_tarifa (nombre, concepto, tarifa, elemento_unidad, porcentajeI, parametrizacionanno) 
            VALUES (:nombre, :concepto, :tarifa, :elemento_unidad, :porcentajeI, :parametrizacion)";
            $sql_dato = array(
                array(":nombre", NULL),
                array(":concepto", $this->concepto),
                array(":tarifa", $this->tarifa),
                array(":elemento_unidad", $this->elemento_unidad),
                array(":porcentajeI", $this->porcentajeI),
                array(":parametrizacion", $this->parametrizacion),
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

    public function eliminar()
    {
        try {
            // $str = "DELETE FROM gp_concepto_tarifa WHERE tarifa = $this->tarifa AND concepto = $this->concepto";
            // return $this->mysqli->query($str);
            $sql_cons = "DELETE FROM gp_concepto_tarifa WHERE tarifa = :id_unico AND concepto = :concepto";

            $sql_dato = array(
                array(":id_unico", $$this->tarifa),
                array(":id_unico", $$this->concepto)
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

    public function registrarUnidadE($unidad, $valor_conversion)
    {
        try {
            // $str = "INSERT INTO gf_elemento_unidad(unidad_empaque, valor_conversion) VALUES($unidad, $valor_conversion)";
            // return $this->mysqli->query($str);
            $sql_cons  = "INSERT INTO gf_elemento_unidad (unidad_empaque, valor_conversion) 
            VALUES (:unidad, :valor)";
            $sql_dato = array(
                array(":unidad", $unidad),
                array(":valor", $valor_conversion),
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

    public function obtenerUltimoElementoUnidad()
    {
        try {
            $xxx = 0;
            $res = $this->con->Listar("SELECT MAX(id_unico) FROM gf_elemento_unidad");
            if (count($res) > 0) {
                $xxx = $res[0][0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarPorcentaje($concepto, $tarifa, $elemento, $porcentaje)
    {
        try {
            // $str = "UPDATE gp_concepto_tarifa 
            //         SET    porcentajeI     = $porcentaje 
            //         WHERE  concepto        = $concepto 
            //         AND    tarifa          = $tarifa 
            //         AND    elemento_unidad = $elemento";
            // return $this->mysqli->query($str);
            $sql_cons = "UPDATE gp_concepto_tarifa 
            SET porcentajeI=:porcentaje
            WHERE concepto =:concepto
            AND tarifa =:tarifa
            AND elemento_unidad =:elemento";
            $sql_dato = array(
                array(":porcentaje", $porcentaje),
                array(":concepto", $concepto),
                array(":tarifa", $tarifa),
                array(":elemento", $elemento),
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


}
