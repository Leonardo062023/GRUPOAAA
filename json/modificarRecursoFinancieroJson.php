<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

$compania = $_SESSION['compania'];
$param = $_SESSION['anno'];

switch ($action) {
  case 1:
    //eliminar en la base de datos
    $id = $_REQUEST['id'];
    //Si no existe el registro como predecesor se elimina.
    if (count($queryPred) == 0) {
      $sql_cons = "DELETE FROM gf_recurso_financiero WHERE id_unico = :id_unico";
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
    $nombre = $_REQUEST['nombre'];
    $codigo = $_REQUEST['codigo'];
    $tipoR = $_REQUEST['tipoR'];

    //Consultar si existe el código en recurso financiero.
    $num = $con->Listar("SELECT codi FROM gf_recurso_financiero WHERE codi = $codigo");
    $num = count($num);

    if ($num == 0) //Si no existe el código, se realizará la inserción de datos en la tabla gf_recurso_financiero.
    {
      //si el campo Tipo recurso financiero esta vacio permita guardar le resto de datos
      if ($tipoR == '""') {
        // Insert
        $sql_cons  = "INSERT INTO gf_recurso_financiero (Nombre, Codi, ParametrizacionAnno, compania) 
        VALUES (:nombre, :codigo, :parametrizacion, :compania)";
        $sql_dato = array(
          array(":nombre", $nombre),
          array(":codigo", $codigo),
          array(":parametrizacion", $param),
          array(":compania", $compania),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
          echo 1;
        } else {
          echo 2;
        }
      } else {
        $sql_cons  = "INSERT INTO gf_recurso_financiero (Nombre, Codi, TipoRecursoFinanciero, ParametrizacionAnno, compania) 
        VALUES (:nombre, :codigo, :tipo, :parametrizacion, :compania)";
        $sql_dato = array(
          array(":nombre", $nombre),
          array(":codigo", $codigo),
          array(":tipo", $tipoR),
          array(":parametrizacion", $param),
          array(":compania", $compania),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
          echo 1;
        } else {
          echo 2;
        }
      }
    } else //Si no se encuentra, retronará false en el resultado.
    {
      $resultado = false;
    }
    break;
  case 3:
    $nombre = $_REQUEST['nombre'];
    $codigo = $_REQUEST['codigo'];
    $tipoR = $_REQUEST['tipoR'];

    $id = $_REQUEST['id'];

    //Consultar si existe el código en recurso financiero.
    $num = $con->Listar("SELECT codi FROM gf_recurso_financiero WHERE codi = $codigo");
    $num = count($num);

    //si el campo Tipo recurso financiero esta vacio permita guardar le resto de datos
    if ($tipoR == '""') {
      //Update antiguo 
      /*$updateSQL = "UPDATE gf_recurso_financiero SET nombre=$nombre, codi=$codigo,tipoRecursoFinanciero=NULL WHERE id_unico = $id";
      $resultado = $mysqli->query($updateSQL);*/

      //Update nuevo
      $sql_cons = "UPDATE gf_recurso_financiero 
          SET nombre=:nombre, codi=:codigo, tipoRecursoFinanciero=:tipo
          WHERE id_unico = :id_unico";

      $sql_dato = array(
        array(":nombre", $nombre),
        array(":codigo", $codigo),
        array(":tipo", NULL),
        array(":id_unico", $id),
      );

      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);

      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
      break;
    } else {
      //Update nuevo
      $sql_cons = "UPDATE gf_recurso_financiero 
        SET nombre=:nombre, codi=:codigo, tipoRecursoFinanciero=:tipo
        WHERE id_unico = :id_unico";

      $sql_dato = array(
        array(":nombre", $nombre),
        array(":codigo", $codigo),
        array(":tipo", $tipoR),
        array(":id_unico", $id),
      );

      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);

      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
      break;
    }
  default:
    break;
}

?>
