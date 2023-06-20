<?php

require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
        case 1:
                //eliminar en la base de datos
                $id = $_REQUEST['id'];
                //Si no existe el registro como predecesor se elimina.
                if (count($queryPred) == 0) {
                        $sql_cons = "DELETE FROM gf_direccion WHERE id_unico = :id_unico";
                        $sql_dato = array(
                                array(":id_unico", $id)
                        );
                        $resp = $con->InAcEl($sql_cons, $sql_dato);
                        if (empty($resp)) {
                                echo 1;
                        } else {
                                echo 2;
                        }
                }
                break;
        case 2:
                //Insertar en la base de datos

                //Codigo nuevo
                $direccion = $_REQUEST['direccion'];
                $tipodireccion = $_REQUEST['tipodireccion'];
                $ciudad = $_REQUEST['ciudad'];
                $tercero  = $_REQUEST['tercero'];

                $sql_cons  = "INSERT INTO gf_direccion (direccion, tipo_direccion, ciudad_direccion, tercero) 
                VALUES (:direccion, :tipo_direccion, :ciudad_direccion, :tercero)";
                $sql_dato = array(
                        array(":direccion", $direccion),
                        array(":tipo_direccion", $tipodireccion),
                        array(":ciudad_direccion", $ciudad),
                        array(":tercero", $tercero),
                );

                $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
                if (empty($obj_resp)) {
                        echo 1;
                } else {
                        echo 2;
                }

                break;
        case 3:
                //Actualizar
                $id = $_REQUEST['idm'];
                $direccion = $_REQUEST['direccionM'];
                $tipo = $_REQUEST['tipod'];
                $ciu = $_REQUEST['ciudadm'];
                $terc = $_REQUEST['tercerom'];

                $sql_cons = "UPDATE gf_direccion 
                SET direccion=:direccion, tipo_direccion=:tipo, ciudad_direccion=:ciudad, tercero=:tercero 
                WHERE id_unico = :id_unico";
                $sql_dato = array(
                        array(":direccion", $direccion),
                        array(":tipo", $tipo),
                        array(":ciudad", $ciu),
                        array(":tercero", $terc),
                        array(":id_unico", $id),
                );
                $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
                if (empty($obj_resp)) {
                        echo 1;
                } else {
                        echo 2;
                }
                break;

        default:
                # code...
                break;
}

?>