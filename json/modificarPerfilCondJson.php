<?php
/*require_once('../Conexion/conexion.php');
session_start();

$id = $_GET['p1'];
$perfil = $_GET['p2'];
$condicion = $_GET['p3'];
$obligatorio = $_GET['p4'];

$queryU = "SELECT * FROM gf_perfil_condicion WHERE perfil = $perfil AND condicion = $condicion AND obligatorio=$obligatorio";
$car = $mysqli->query($queryU);
$num = mysqli_num_rows($car);

$queryB = "SELECT perfil, condicion, obligatorio FROM gf_perfil_condicion WHERE id_unico = $id";
$queryBus = $mysqli->query($queryB);
$busqu = mysqli_fetch_row($queryBus);
$perfilA = $busqu[0];
$condicionA = $busqu[1];
$obligatorioA = $busqu[2];
if ($perfil == $perfilA && $condicion == $condicionA && $obligatorio == $obligatorioA) {
  $updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
  $resultado = $mysqli->query($updateSQL);

  echo json_encode($resultado);
} else {

  if ($num == 0) {

    $updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
    $resultado = $mysqli->query($updateSQL);

    echo json_encode($resultado);
  } else {
    $resultado = '3';
    echo json_encode($resultado);
  }
}*/
?>

<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    $id = $_REQUEST['id'];

    $sql_cons = "DELETE FROM gf_perfil_condicion WHERE id_unico =:id_unico";
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
    $oblig  = $_REQUEST['obligatorio'];
    $perfil  = $_REQUEST['perfil'];
    $condicion  = $_REQUEST['condicion'];

    $num = $con->Listar("SELECT * FROM gf_perfil_condicion WHERE perfil=$perfil AND condicion=$condicion ");
    $num = count($num);
    if ($num == 0) {
      $sql_cons  =
        "INSERT INTO gf_perfil_condicion (Obligatorio, Perfil, Condicion) 
      VALUES (:obligatorio, :perfil, :condicion)";
      $sql_dato = array(
        array(":obligatorio", $oblig),
        array(":perfil", $perfil),
        array(":condicion", $condicion),
      );

      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
    } else {
      echo 3;
    }
    break;
  case 3:
    $id = $_REQUEST['p1'];
    $perfil = $_REQUEST['p2'];
    $condicion = $_REQUEST['p3'];
    $obligatorio = $_REQUEST['p4'];

    $num = $con->Listar("SELECT * FROM gf_perfil_condicion WHERE perfil = $perfil AND condicion = $condicion AND obligatorio=$obligatorio");
    $num = count($num);

    $busqu = $con->Listar("SELECT perfil, condicion, obligatorio FROM gf_perfil_condicion WHERE id_unico = $id");

    $perfilA = $busqu[0];
    $condicionA = $busqu[1];
    $obligatorioA = $busqu[2];

    if ($perfil == $perfilA && $condicion == $condicionA && $obligatorio == $obligatorioA) {
      /*$updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
      $resultado = $mysqli->query($updateSQL);*/
      $sql_cons = "UPDATE gf_perfil_condicion 
          SET perfil=:perfil, condicion=:condicion, obligatorio=:obligatorio
          WHERE id_unico = :id_unico";
      $sql_dato = array(
        array(":perfil", $perfil),
        array(":condicion", $condicion),
        array(":obligatorio", $obligatorio),
        array(":id_unico", $id),
      );
      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
    } else {

      if ($num == 0) {
        /*$updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
        $resultado = $mysqli->query($updateSQL);*/
        $sql_cons = "UPDATE gf_perfil_condicion 
          SET perfil=:perfil, condicion=:condicion, obligatorio=:obligatorio
          WHERE id_unico = :id_unico";
        $sql_dato = array(
          array(":perfil", $perfil),
          array(":condicion", $condicion),
          array(":obligatorio", $obligatorio),
          array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
          echo 1;
        } else {
          echo 2;
        }
      } else {
        echo 2;
      }
    }
    break;
  default:
    # code...
    break;
}
?>
