<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action     = $_REQUEST['action'];

//Captura de datos e instrucción SQL para su modificación en la tabla gf_centro_cosoto.

switch ($action) {
  case 1:
    $id = $_REQUEST['id'];



    //Si no existe el registro como predecesor se elimina.


    $sql_cons = "DELETE FROM gf_tipo_vigencia WHERE Id_Unico = :id_unico";
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

    

    


    $tipov  = $_REQUEST['tipov'];

    $sql_cons  = "INSERT INTO gf_tipo_vigencia(Nombre) VALUES (:tipov)";
    $sql_dato = array(
      array(":tipov", $tipov),

    );


    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
      echo ($obj_resp);
    }

    break;
  case 3:


    $tipov = $_REQUEST['tipov'];
    $id  = $_REQUEST['id'];
    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_tipo_vigencia SET Nombre=:tipov WHERE Id_Unico = :id_unico";

    $sql_dato = array(
      array(":tipov", $tipov),
      array(":id_unico", $id),
    );

    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
      echo ($id);
    }

    break;

  default:
    # code...
    break;
}
//obtiene la informacion para la modificacion
