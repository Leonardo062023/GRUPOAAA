<?php
// require_once('../Conexion/conexion.php');
// session_start();

// //obtiene los datos que se van a modificar
// $nombre  = '"' . $mysqli->real_escape_string('' . $_POST['nombre'] . '') . '"';
// $id  = '"' . $mysqli->real_escape_string('' . $_POST['id'] . '') . '"';

// //modificar ne la base de datos
// $insertSQL = "UPDATE gp_tipo_concepto SET nombre=$nombre WHERE id_unico = $id";
// $resultado = $mysqli->query($insertSQL);

?>
<?php
session_start();
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    // $id = $_GET["id"];
    // $sql = "DELETE FROM gp_tipo_concepto WHERE id_unico = $id";
    // $resultado = $mysqli->query($sql);
    $id = $_REQUEST['id'];
    $sql_cons = "DELETE FROM gp_tipo_concepto WHERE id_unico = :id_unico";
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
    $nombre = $_REQUEST['nombre'];
    // $sql = "INSERT INTO gp_tipo_concepto(nombre) VALUES ($nombre)";
    $sql_cons  = "INSERT INTO gp_tipo_concepto (nombre) VALUES (:nombre)";
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
    //obtiene los datos que se van a modificar
    $nombre  = $_REQUEST['nombre'];
    $id  = $_REQUEST['id'];

    //modificar ne la base de datos
    // $insertSQL = "UPDATE gp_tipo_concepto SET nombre=$nombre WHERE id_unico = $id";
    $sql_cons = "UPDATE gp_tipo_concepto 
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