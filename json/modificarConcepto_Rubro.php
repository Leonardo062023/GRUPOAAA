<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action     = $_REQUEST['action'];

//Captura de datos e instrucción SQL para su modificación en la tabla gf_centro_cosoto.

switch ($action) {
  case 1:

    //elimina en la base de datos



    $id = $_REQUEST['id'];


    //Si no existe el registro como predecesor se elimina.
    if (count($queryPred) == 0) {

      $sql_cons = "DELETE FROM gf_concepto_rubro WHERE Id_Unico = :id_unico";
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
    }




    break;
  case 2:

    $rubro  = $_REQUEST['rubro'];
    $concepto  = $_REQUEST['concepto'];
    //registro en la base de datos 

   





    $sql_cons  = "INSERT INTO gf_concepto_rubro (Rubro,Concepto) VALUES(:rubro,:concepto)";
    $sql_dato = array(
      array(":rubro", $rubro),
      array(":concepto", $concepto),
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
    $rubro  = $_REQUEST['rubro'];
    $concepto  = $_REQUEST['concepto'];
    $id  = $_REQUEST['id'];

    //modifica en la base de datos 


    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_concepto_rubro SET Rubro =:rubro,Concepto=:concepto WHERE Id_Unico =:id_unico";

    $sql_dato = array(
      array(":rubro", $rubro),
      array(":concepto", $concepto),
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
