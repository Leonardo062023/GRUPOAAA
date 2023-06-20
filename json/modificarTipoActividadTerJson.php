
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
      $sql_cons = "DELETE FROM gf_tipo_actividad_tercero WHERE tipoactividad = :id_unico";
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
    //Captura de datos e instrucción SQL para su inserción en la tabla 
    $tipoA  = $_REQUEST['tipoA'];
    $tercero = $_REQUEST['tercero'];


    $num = $con->Listar("SELECT tipoactividad, tercero FROM gf_tipo_actividad_tercero WHERE  tipoactividad= $tipoA AND tercero=$tercero");
    $num = count($num);

    if ($num == 0) //Si no existe el año, se realizará la inserción de datos en la tabla.
    {
      $sql_cons  = "INSERT INTO gf_tipo_actividad_tercero (tipoactividad, tercero) 
                VALUES (:tipo, :tercero)";
      $sql_dato = array(
        array(":tipo", $tipoA),
        array(":tercero", $tercero),
      );

      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
    } else //Si no se encuentra, retronará false en el resultado.
    {
      $resultado = false;
    }
    break;
  case 3:
    $id = $_REQUEST['id'];
    $tipoActi = $_REQUEST['tipoActmodal'];
    if ($tipoActi == NULL) {
      echo 2;
    } else {
      $sql_cons = "UPDATE gf_tipo_actividad_tercero 
      SET tipoactividad=:tipo
      WHERE tercero = :tercero";
      $sql_dato = array(
        array(":tipo", $tipoActi),
        array(":tercero", $id),
      );
      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
    }


    /*$updateSQL = "UPDATE gf_tipo_actividad_tercero SET tipoactividad = '$tipoActi' WHERE tercero = '$id'";
    $resultado = $mysqli->query($updateSQL);*/
    break;
  default:
    # code...
    break;
}
?>