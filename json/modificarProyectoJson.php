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
    if (count($queryPred) == 0) {

      $sql_cons = "DELETE FROM gf_proyecto WHERE Id_Unico =:id_unico";
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
    $compania   = $_SESSION['compania'];
    $nombre     = $_REQUEST['nombre'];
    $codigo     = $_REQUEST['codigo'];
    $codigo_bpin= $_REQUEST['codigobpin'];
    
 



    //Consultar si existe el año en parametrizacion año.
    

      $sql_cons  = "insert into gf_proyecto(nombre,compania, codigo, codigo_bpin) 
      values(:nombre,:compania,:codigo,:codigo_bpin)";
      $sql_dato = array(
        array(":compania", $compania),
        array(":nombre", $nombre),
        array(":codigo", $codigo),
        array(":codigo_bpin", $codigo_bpin),
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
    $nombre     = $_POST['nombre'];
    $id         = $_POST['id'];
    $codigo     = $_POST['codigo'];
    $codigo_bpin= $_POST['codigobpin'];
    

    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_proyecto SET Nombre=:nombre, codigo =:codigo, codigo_bpin = :codigo_bpin WHERE Id_Unico =:id_unico";

    $sql_dato = array(
      array(":nombre", $nombre),
      array(":codigo", $codigo),
      array(":codigo_bpin", $codigo_bpin),
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
