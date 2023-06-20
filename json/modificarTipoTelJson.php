<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    //Eliminar
    $id = $_REQUEST['id'];
    $sql_cons = "DELETE FROM gf_tipo_telefono WHERE id_unico = :id_unico";
    $sql_dato = array(
      array(":id_unico", $id)
    );
    $resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($resp)) {
      echo 1;
    } else {
      echo 2;
      // echo($obj_resp);
    }
    break;
  case 2:
    $nombre  = $_REQUEST['nombre'];
    //registro en la base de datos 
    $sql_cons  = "INSERT INTO gf_tipo_telefono (nombre) 
                VALUES (:nombre)";
    $sql_dato = array(
      array(":nombre", $nombre),
    );

    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
    }

    break;
  case 3:
    $nombre  = $_REQUEST['nombre'];
    $id  = $_REQUEST['id'];

    $sql_cons = "UPDATE gf_tipo_telefono 
          SET nombre=:nombre
          WHERE id_unico = :id_unico";
    $sql_dato = array(
      array(":nombre", $nombre),
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
