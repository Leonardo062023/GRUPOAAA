<?php
/*require_once('../Conexion/conexion.php');
session_start();

$nombre  = '"' . $mysqli->real_escape_string('' . $_POST['nombre'] . '') . '"';
//registro en la base de datos 
$insertSQL = "INSERT INTO gf_tipo_telefono (nombre) VALUES($nombre)";
$resultado = $mysqli->query($insertSQL);*/

?>
<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    # code...
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
  default:
    # code...
    break;
}
?>
