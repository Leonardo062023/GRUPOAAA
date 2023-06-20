<?php

/**
 * registrar_GF_ELEMENTO_FICHAJson.php
 *
 * Archivo para registro,eliminado y modificado de elemento ficha
 *
 * @author Alexander Numpaque
 * @package Dependencia
 * @version $Id: registrar_GF_ELEMENTO_FICHAJson.php 001 2017-05-26 Alexander Numpaque$
 * */

/**
 * gf_elemento_ficha
 *
 * Clase para guardado, modificado y eliminado
 *
 * @author Alexander Numpaque
 * @package Elemento Ficha
 * @version $Id: registrar_GF_ELEMENTO_FICHAJson.php 001 2017-05-26 Alexander Numpaque$
 * */
require('../Conexion/conexionPDO.php');
$con = new ConexionPDO();

class gf_elemento_ficha
{

    private $id_unico;
    private $nombre;
    private $tipoDato;

    function __construct($nombre, $tipoDato, $id_unico = NULL)
    {
        $this->id_unico = $id_unico;
        $this->nombre = $nombre;
        $this->tipoDato = $tipoDato;
    }

    /**
     * @return null
     */
    public function getIdUnico()
    {
        return $this->id_unico;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getTipoDato()
    {
        return $this->tipoDato;
    }

    /**
     * @param mixed $tipoDato
     */
    public function setTipoDato($tipoDato)
    {
        $this->tipoDato = $tipoDato;
    }

    /**
     * save_data
     * 
     * Función para guardar valores en la base de datos
     * 
     * @author Alexander Numpaque
     * @package Elemento Ficha
     * @param String $nombre Nombre de elemento ficha
     * @param int $tipoDato Id de tipo Dato
     * @return bool $inserted Retorna verdadero cuando se inserta en la base de datos
     */

    public static function save_data($nombre, $tipoDato)
    {
        global $con;
        $inserted = false;

        $sql_cons  = "INSERT INTO gf_elemento_ficha (nombre, tipodato) 
        VALUES (:nombre, :tipodato)";
        $sql_dato = array(
            array(":nombre", $nombre),
            array(":tipodato", $tipoDato),
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
     * save_data
     *
     * Función para editar valores en la base de datos
     *
     * @author Alexander Numpaque
     * @package Elemento Ficha
     * @param String $nombre Nombre de elemento ficha
     * @param int $tipoDato Id de tipo Dato
     * @param int $id_unico Id de registro selecionado para modificar
     * @return bool $edited Retorna verdadero cuando se modfica en la base de datos
     */

    public static function modify_data($nombre, $tipoDato, $id_unico)
    {
        global $con;
        $edited = false;
        $sql_cons = "UPDATE gf_elemento_ficha 
          SET nombre=:nombre, tipodato=:tipo
          WHERE id_unico = :id_unico";

        $sql_dato = array(
            array(":nombre", $nombre),
            array(":tipo", $tipoDato),
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
     * Función para eliminar los registros de la base de datos
     * 
     * @param int $id_unico Id del registro a eliminar
     * @return bool $deleted Retornara verdadero cuando el registro sea eliminado de la base de datos
     */

    public static function delete_data($id_unico)
    {
        global $con;
        $deleted = false;
        $sql_cons = "DELETE FROM gf_elemento_ficha WHERE id_unico = :id_unico";
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
