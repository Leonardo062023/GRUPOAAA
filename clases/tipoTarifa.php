<?php

/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 29/05/2018
 * Time: 12:22
 */
require_once('./Conexion/ConexionPDO.php');
class tipoTarifa
{
    private $con;
    public $id_unico;
    public $nombre;

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

    public function registrar(tipoTarifa $data)
    {
        try {
            $sql_cons  = "INSERT INTO gp_tipo_tarifa (nombre) 
            VALUES (:nombre)";
            $sql_dato = array(
                array(":nombre", $data->nombre),
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

    public function modificar(tipoTarifa $data)
    {
        try {
            $sql_cons = "UPDATE gp_tipo_tarifa 
            SET nombre=:nombre
            WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":nombre", $data->nombre),
                array(":id_unico", $data->id_unico),
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

    public function eliminar($id)
    {
        try {
            $sql_cons = "DELETE FROM gp_tipo_tarifa WHERE id_unico = :id_unico";

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

    public function obtener($id)
    {
        try {
            $id_unico = base64_decode($id);
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gp_tipo_tarifa WHERE id_unico = $id_unico");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTodos()
    {
        try {
            $res = $this->con->Listar("SELECT id_unico, nombre FROM gp_tipo_tarifa ORDER BY nombre ASC");
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
