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

      $sql_cons = "DELETE FROM gf_dependencia 
	  WHERE id_unico = :id_unico";
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
    $nombre  = $_REQUEST['nombre'];
    $sigla            = $_REQUEST['sigla'];
    $movimiento       = $_REQUEST['movimiento'];
    $activa           = $_REQUEST['activa'];
    $predecesor       = $_REQUEST['predecesor'];
    $centroCosto      = $_REQUEST['centroC'];
    $tipoDependencia  = $_REQUEST['tipo'];
    $compania = $_SESSION['compania'];
   
    //registro en la base de datos 

    $sql_cons  = "INSERT INTO gf_dependencia (nombre, sigla, movimiento, activa, predecesor, centrocosto, tipodependencia, compania) 
    VALUES (:nombre, :sigla, :movimiento, :activa, :predecesor, :centroCosto, :tipoDependencia, :compania)";
    $sql_dato = array(
      array(":nombre", $nombre),
      array(":sigla", $sigla),
      array(":movimiento", $movimiento),
      array(":activa", $activa),
      array(":predecesor", $predecesor),
      array(":centroCosto", $centroCosto),
      array(":tipoDependencia", $tipoDependencia),
      array(":compania", $compania),
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

    
    $id = $_REQUEST['id'];
  	$nombre  = $_REQUEST['nombre'];
    $sigla            = $_REQUEST['sigla'];
    $movimiento       = $_REQUEST['movimiento'];
    $activa           = $_REQUEST['activa'];
    $predecesor       = $_REQUEST['predecesor'];
    $centroCosto      = $_REQUEST['centroC'];
    $tipoDependencia  = $_REQUEST['tipo'];
    $factura          = $_REQUEST['optFactura'];
    $compania = $_SESSION['compania'];
    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_dependencia 
    SET nombre=:nombre, sigla=:sigla,movimiento=:movimiento, activa=:activa, predecesor=:predecesor, centrocosto=:centroCosto, tipodependencia=:tipoDependencia 
    WHERE id_unico = :id_unico
    AND compania =:compania";

    $sql_dato = array(
      array(":nombre", $nombre),
      array(":id_unico", $id),
      array(":sigla", $sigla),
      array(":movimiento", $movimiento),
      array(":activa", $activa),
      array(":predecesor", $predecesor),
      array(":centroCosto", $centroCosto),
      array(":tipoDependencia", $tipoDependencia),
      array(":factura", $factura),
      array(":compania", $compania),
    );

    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
      //echo ($id);
    }

    break;

  default:
    # code...
    break;
}
//obtiene la informacion para la modificacion
