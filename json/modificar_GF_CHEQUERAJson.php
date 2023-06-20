<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
//Captura de datos e instrucción SQL para su modificación en la tabla gf_centro_cosoto.

switch ($action) {
  case 1:
    //eliminar en la base de datos
    $id = $_REQUEST['id'];
    //Si no existe el registro como predecesor se elimina.
    if (count($queryPred) == 0) {
      $sql_cons = "DELETE FROM gf_chequera WHERE id_unico = :id_unico";
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
    $numero = $_REQUEST['numero'];
    $numI = $_REQUEST['numeroI'];
    $numF = $_REQUEST['numeroF'];
    $estado = $_REQUEST['estado'];
    $cuentaB = $_REQUEST['cuenta'];
    $compania = $_SESSION['compania'];

    //registro en la base de datos 
    if ($numI >= $numF) {
      $error = '10';
    } else {
      $sql_cons  = "INSERT INTO GF_CHEQUERA (NUMEROCHEQUERA, NUMEROINICIAL, NUMEROFINAL, ESTADOCHEQUERA, PARAMETRIZACIONANNO, COMPANIA, CUENTABANCARIA) 
    VALUES (:numeroChequera, :numeroInicial, :numeroFinal, :estado, :parametrizacionAnno, :compania, :cuentaBancaria)";
      $sql_dato = array(
        array(":numeroChequera", $numero),
        array(":numeroInicial", $numI),
        array(":numeroFinal", $numF),
        array(":estado", $estado),
        array(":parametrizacionAnno", $anno),
        array(":compania", $compania),
        array(":cuentaBancaria", $cuentaB),
      );
    }
    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
    }
    break;
  case 3:
    $id = $_REQUEST['id'];
    $numero = $_REQUEST['numero'];
    $numI = $_REQUEST['numeroI'];
    $numF = $_REQUEST['numeroF'];
    $estado = $_REQUEST['estado'];
    $cuentaB = $_REQUEST['cuenta'];
    $compania = $_SESSION['compania'];

    $error = '';

    //modifica los campos involucrados
    if ($numI >= $numF) {
      $error = '10';
    } else {
      $numA = $con->Listar("SELECT numerochequera, cuentabancaria FROM gf_chequera WHERE id_unico = '$id'");

      $num = $con->Listar("SELECT * FROM gf_chequera WHERE numerochequera = $numero AND cuentabancaria = $cuentaB");
      $num = count($num);
      if ($numA[0][0] == $numero && $numA[0][1] == $cuentaB) {
        //Consulta nueva
        $sql_cons = "UPDATE GF_CHEQUERA 
          SET numerochequera=:numeroChequera, numeroinicial=:numeroI, numerofinal=:numeroF, estadochequera=:estado, cuentabancaria=:cuentaBancaria 
          WHERE id_unico = :id_unico";

        $sql_dato = array(
          array(":numeroChequera", $numero),
          array(":numeroI", $numI),
          array(":numeroF", $numF),
          array(":estado", $estado),
          array(":cuentaBancaria", $cuentaB),
          array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
          echo 1;
        } else {
          echo 2;
          //echo ($id);
        }
        break;
      } else {
        if ($num == 0) {
          //Consulta nueva
          $sql_cons = "UPDATE GF_CHEQUERA 
          SET numerochequera=:numeroChequera, numeroinicial=:numeroI, numerofinal=:numeroF, estadochequera=:estado, cuentabancaria=:cuentaBancaria 
          WHERE id_unico = :id_unico";

          $sql_dato = array(
            array(":numeroChequera", $numero),
            array(":numeroI", $numI),
            array(":numeroF", $numF),
            array(":estado", $estado),
            array(":cuentaBancaria", $cuentaB),
            array(":id_unico", $id),
          );

          $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
          if (empty($obj_resp)) {
            echo 1;
            //$resultado = 1;
          } else {
            //$resultado = 2;
            echo 2;
            //echo ($id);
          }
          break;
        } else {
          if ($num > 0) {
            $resultado = 3;
          } else {
            $resultado = false;
          }
        }
      }
    }
  default:
    break;
}
