<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
  case 1:
    //eliminar en la base de datos
    $id = $_REQUEST['id'];
    $tercero = $_SESSION['id_tercero'];
    //Si no existe el registro como predecesor se elimina.
    if (count($queryPred) == 0) {
      $sql_cons = "DELETE FROM gf_condicion_tercero WHERE perfilcondicion = :perfilcondicion AND tercero = :tercero";
      $sql_dato = array(
        array(":perfilcondicion", $id),
        array(":tercero", $tercero)
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
    //Captura de datos e instrucción SQL para su inserción en la tabla gf_perfil_tercero.
    $perfil  = $_REQUEST['perfil'];
    $tercero = $_REQUEST['tercero'];

    $row = $con->Listar("SELECT td.nombre, pf.obligatorio FROM  gf_perfil_condicion pf  LEFT JOIN gf_condicion c ON pf.condicion= c.id_unico LEFT JOIN gf_tipo_dato td ON c.tipodato = td.id_unico WHERE pf.id_unico='$perfil'");

    $tipod = utf8_encode($row[0][0]);
    $obl = $row[0][1];

    switch (true) {
      case ($tipod == 'Booleano') && ($obl == '0'):
        $valor  = $_REQUEST['valorbO'];
        break;
      case ($tipod == 'Booleano') && ($obl == '1'):
        $valor  = $_REQUEST['valorbN'];
        break;
      default:
        $valor = $_REQUEST['valord'];
        break;
    }
    if ($valor == '' || $valor == " " || $valor == NULL) {
      $valor = 'NULL';
    }

    $num = $con->Listar("SELECT perfilcondicion FROM gf_condicion_tercero WHERE perfilcondicion = $perfil AND tercero = $tercero");
    $num = count($num);

    if ($num == 0)  {
      /*$insertSQL = "INSERT INTO gf_condicion_tercero (perfilcondicion, tercero, valor) VALUES($perfil, $tercero, $valor)";
      $resultado = $mysqli->query($insertSQL);*/

      $sql_cons  = "INSERT INTO gf_condicion_tercero (perfilcondicion, tercero, valor) 
                VALUES (:perfil, :tercero ,:valor)";
      $sql_dato = array(
        array(":perfil", $perfil),
        array(":tercero", $tercero),
        array(":valor", $valor),
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
    //Modificar
    $tercero = $_REQUEST['p1'];
    $perfil = $_REQUEST['p2'];
    if ($_REQUEST['p3'] == " " || $_REQUEST['p3'] == NULL || $_REQUEST['p3'] == 'NULL' || $_REQUEST['p3'] == '') {
      $valor = NULL;
    } else {
      $valor = $_REQUEST['p3'];
    }
    $perfilA = $_REQUEST['p4'];

    $num = $con->Listar("SELECT perfilcondicion FROM gf_condicion_tercero WHERE perfilcondicion = $perfil AND tercero = $tercero");
    $num = count($num);

    if ($perfil == $perfilA) {
      $sql_cons = "UPDATE gf_condicion_tercero 
      			SET valor=:valor
      			WHERE tercero =:tercero
            AND perfilcondicion =:perfilcondicion";
      $sql_dato = array(
        array(":valor", $valor),
        array(":tercero", $tercero),
        array(":perfilcondicion", $perfilA),
      );

      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
      if (empty($obj_resp)) {
        echo 1;
      } else {
        echo 2;
      }
    } else {
      if ($num == 0) {
        $sql_cons = "UPDATE gf_condicion_tercero 
      			SET perfilcondicion=:perfilA, valor=:valor
      			WHERE tercero =:tercero
            AND perfilcondicion =:perfilA";
        $sql_dato = array(
          array(":tipo", $tipoActi),
          array(":valor", $valor),
          array(":tercero", $tercero),
          array(":perfilA", $perfilA),
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
  case 4:
    $perfil = $_REQUEST['id'];
    $row = $con->Listar("SELECT td.nombre, pf.obligatorio 
    FROM  gf_perfil_condicion pf  
    LEFT JOIN gf_condicion c ON pf.condicion= c.id_unico 
    LEFT JOIN gf_tipo_dato td ON c.tipodato = td.id_unico WHERE pf.id_unico='$perfil'");
    $tipod = $row[0][0];
    $oblig = $row[0][1];
    $datos =  utf8_encode($tipod) . " - " . $oblig;
    echo $datos;
    break;
  default:
    # code...
    break;
}
?>