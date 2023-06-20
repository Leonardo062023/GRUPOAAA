<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];

//Captura de datos e instrucción SQL para su modificación en la tabla gf_cuenta_bancaria.

switch ($action) {
  case 1:
    //eliminar en la base de datos
    $id = $_REQUEST['id'];
    //Si no existe el registro como predecesor se elimina.
    if (count($queryPred) == 0) {
      $sql_cons = "DELETE FROM gf_cuenta_bancaria WHERE id_unico = :id_unico";
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
    /*Variables*/ 
    $banco = $_REQUEST['banco'];
    $numeroCuenta = $_REQUEST['numC'];
    $descripcion = $_REQUEST['descrip'];
    $tipoCuenta = $_REQUEST['tipoC'];
    $formato = $_REQUEST['sltFormato'];
    $recurso = $_REQUEST['sltRecurso'];
    $destinacion = $_REQUEST['sltDestinacion'];
    
    //registro en la base de datos 
    $sql_cons  = "INSERT INTO gf_cuenta_bancaria (banco, numerocuenta, descripcion, tipocuenta, formato, recursofinanciero, destinacion,parametrizacionanno) 
    VALUES (:banco, :numeroCuenta, :descripcion, :tipoCuenta, :formato, :recueroFinanciero, :destinacion, :parametrizacion)";
    $sql_dato = array(
      array(":banco", $banco),
      array(":numeroCuenta", $numeroCuenta),
      array(":descripcion", $descripcion),
      array(":tipoCuenta", $tipoCuenta),
      array(":formato", $formato),
      array(":recueroFinanciero", $recurso),
      array(":destinacion", $destinacion),
      array(":parametrizacion", $anno),
      
    );

    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
    }
    break;

  case 3:
    /*Variables*/
    $banco = $_REQUEST['banco'];
    $numeroCuenta = $_REQUEST['numC'];
    $descripcion = $_REQUEST['descrip'];
    $tipoCuenta = $_REQUEST['tipoC'];
    $id = $_REQUEST['id'];

    if($tipoCuenta=='""'){
      $tipoCuenta='NULL';
    }

    if(!empty($_REQUEST['sltFormato'])){
      $formato = $_REQUEST['sltFormato'];
    }else{
      $formato = 'NULL';
    }

    if(!empty($_REQUEST['sltRecurso'])){
      $recurso = $_REQUEST['sltRecurso'];
    }else{
      $recurso = 'NULL';
    }

    if(!empty($_REQUEST['sltDestinacion'])){
      $destinacion = $_REQUEST['sltDestinacion'];
    }else{
      $destinacion = 'NULL';
    }

    $numA=$con->Listar("SELECT banco, numerocuenta FROM gf_cuenta_bancaria WHERE id_unico = $id");
    
    $num=$con->Listar("SELECT * FROM gf_cuenta_bancaria WHERE numerocuenta = $numeroCuenta AND banco = $banco");
    $num = count($num);

    $var1 = $numA[0][1];
    $var2 =$numA[0][0];
    if($numA[0][0]==$banco && $numA[0][1] ==$numeroCuenta ){
       /*Consulta nueva*/
        $sql_cons = "UPDATE gf_cuenta_bancaria 
            SET numerocuenta=:numeroCuenta, descripcion=:descripcion, banco=:banco, tipocuenta=:tipo, formato=:formato, recursofinanciero=:recurso, destinacion=:destinacion 
            WHERE id_unico = :id_unico";
        $sql_dato = array(
          array(":numeroCuenta", $numeroCuenta),
          array(":descripcion", $descripcion),
          array(":banco", $banco),
          array(":tipo", $tipoCuenta),
          array(":formato", $formato),
          array(":recurso", $recurso),
          array(":destinacion", $destinacion),
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
        if($num == 0) { 
          /*Consulta nueva*/
          $sql_cons = "UPDATE gf_cuenta_bancaria 
            SET numerocuenta=:numeroCuenta, descripcion=:descripcion, banco=:banco, tipocuenta=:tipo, formato=:formato, recursofinanciero=:recurso, destinacion=:destinacion 
            WHERE id_unico = :id_unico";
          $sql_dato = array(
            array(":numeroCuenta", $numeroCuenta),
            array(":descripcion", $descripcion),
            array(":banco", $banco),
            array(":tipo", $tipoCuenta),
            array(":formato", $formato),
            array(":recurso", $recurso),
            array(":destinacion", $destinacion),
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
        if($num>0){
          $resultado=3;
        } else {
          $resultado = false;
        }  
      }
    }    
  default:
    break;
}

?>

