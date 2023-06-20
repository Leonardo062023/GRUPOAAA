<?php
session_start();
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    // $query = "DELETE FROM gp_tipo_operacion WHERE Id_Unico = $id";
    $id = $_REQUEST['id'];
    $sql_cons = "DELETE FROM gp_tipo_operacion WHERE id_unico = :id_unico";
    $sql_dato = array(
      array(":id_unico", $id)
    );
    $resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($resp)) {
      echo 1;
    } else {
      echo 2;
    }
    break;
  case 2:
    $nombre  = $_REQUEST['nombre'];
    // $insert = "INSERT INTO gp_tipo_operacion (Nombre) VALUES($nombre)";
    $sql_cons  = "INSERT INTO gp_tipo_operacion (nombre) VALUES (:nombre)";
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
    // $update = "UPDATE gp_tipo_operacion SET Nombre=$nombre
    // WHERE Id_Unico = $id";
    $sql_cons = "UPDATE gp_tipo_operacion 
      SET nombre=:nombre
      WHERE id_unico=:id_unico";
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