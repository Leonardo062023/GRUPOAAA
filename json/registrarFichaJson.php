<?php

/**
 * registrarFichaJson.php
 * 
 * Archivo para registro,eliminado y modificado de dependencia
 * 
 * @author Alexander Numpaque
 * @package Ficha
 * @version $Id: registrarFichaJson.php 001 2017-05-26 Alexander Numpaque$
 * */

/**
 * gf_ficha
 * 
 * Clase para guardado, modificado y eliminado
 * 
 * @author Alexander Numpaque
 * @package Ficha
 * @version $Id: registrarFichaJson.php 001 2017-05-26 Alexander Numpaque$
 * */
require('../Conexion/conexionPDO.php');
$con = new ConexionPDO();
class gf_ficha
{

    private $id_unico;
    private $descripcion;

    /**
     * @return mixed
     */
    public function getIdUnico()
    {
        return $this->id_unico;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    function __construct($descripcion, $id_unico = NULL)
    {
        $this->id_unico = $id_unico;
        $this->descripcion = $descripcion;
    }

    /**
     * save_data
     * 
     * Función para registrar en la base de datos
     * @author Alexander Numpaque
     * @package Ficha
     * @param String $descripcion Nombre de la ficha
     * @return bool Si el valor es registrado retornara verdadero
     */

    public static function save_data($descripcion)
    {
        global $con;
        $inserted = false;
        $sql_cons  = "INSERT INTO gf_ficha (descripcion) 
        VALUES (:descripcion)";
        $sql_dato = array(
            array(":descripcion", $descripcion),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            $inserted = true;
        } else {
            $inserted = false;
        }
        return $inserted;
    }

    /**
     * modify_data
     * 
     * Función para modificar los valores en la base de datos
     * 
     * @author Alexander Numpaque
     * @package Ficha
     * @param String $descripcion Nombre de la ficha
     * @param int $id_unico Id del archivo para modificar
     * @return bool Si el valor es registrado retornara verdadero
     */
    public static function modify_data($descripcion, $id_unico)
    {
        global $con;
        $edited = false;

        $sql_cons = "UPDATE gf_ficha 
          SET descripcion=:descripcion
          WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":descripcion", $descripcion),
            array(":id_unico", $id_unico),
        );

        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            $edited = true;
        } else {
            $edited = false;
        }
        return $edited;
    }

    /**
     * delete_data
     * 
     * Función para eliminiar los registro de la base de datos
     * 
     * @package Ficha
     * @author Alexander Numpaque
     * @param int id_unico id del registro a eliminar de la base de datos
     * @return bool $deleted Si el registro es eliminado retornara verdadero
     */
    public static function delete_data($id_unico)
    {
        global $con;
        $deleted = false;
        $sql_cons = "DELETE FROM gf_ficha WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":id_unico", $id_unico)
        );
        $resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($resp)) {
            $deleted = true;
        } else {
            $deleted = false;
        }
        return $deleted;
    }
}
