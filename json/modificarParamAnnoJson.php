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

      $sql_cons = "DELETE FROM gf_parametrizacion_anno WHERE Id_Unico = :id_unico";
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
    $anno         = $_REQUEST['valor'];
    $salariom     = $_REQUEST['salariom'];
    $minimod      = $_REQUEST['minimod'];
    $uvt          = $_REQUEST['uvt'];
    $cajam        = $_REQUEST['cajam'];
    $estadoA      = $_REQUEST['estadoA'];
    $compania     = $_REQUEST['compania'];
    $min_c        = $_REQUEST['min_c'];
    $menorc       = $_REQUEST['menorc'];
    $menorcm      = $_REQUEST['menorcm'];
    $mayorc       = $_REQUEST['mayorc'];

    //Consultar si existe el año en parametrizacion año.
    

      $sql_cons  = "INSERT INTO gf_parametrizacion_anno (Anno, SalarioMinimo, MinDepreciacion, 
      UVT, CajaMenor, EstadoAnno, Compania, minimacuantia, menorcuantia, menorcuantia_m, mayorcuantia) 
      VALUES (:anno, :salariom, :minimod,:uvt, :cajam, :estadoA, :compania, :min_c, :menorc, :menorcm, :mayorc) ";
      $sql_dato = array(
        array(":anno", $anno),
        array(":salariom", $salariom),
        array(":minimod", $minimod),
        array(":uvt", $uvt),
        array(":cajam", $cajam),
        array(":estadoA", $estadoA),
        array(":compania", $compania),
        array(":min_c", $min_c),
        array(":menorc", $menorc),
        array(":menorcm", $menorcm),
        array(":mayorc", $mayorc),
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

    $anno  = $_REQUEST['valor'];
    $salariom  = $_REQUEST['salariom'];
    $minimod  = $_REQUEST['minimod'];
    $uvt  = $_REQUEST['uvt'];
    $cajam  = $_REQUEST['cajam'];
    $estadoA = $_REQUEST['estadoA'];
    $id  = $_REQUEST['id'];
    $min_c = $_REQUEST['min_c'];
    $menorc = $_REQUEST['menorc'];
    $menorcm = $_REQUEST['menorcm'];
    $mayorc = $_REQUEST['mayorc'];



    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_parametrizacion_anno SET Anno=:anno, SalarioMinimo=:salariom, 
    MinDepreciacion=:minimod, UVT=:uvt, CajaMenor=:cajam, EstadoAnno=:estadoA  ,
    minimacuantia= :min_c, menorcuantia= :menorc, 
    menorcuantia_m= :menorcm, mayorcuantia = :mayorc 
    WHERE Id_Unico =:id_unico";

    $sql_dato = array(
      array(":anno", $anno),
      array(":salariom", $salariom),
      array(":minimod", $minimod),
      array(":uvt", $uvt),
      array(":cajam", $cajam),
      array(":estadoA", $estadoA),
      array(":id_unico", $id),
      array(":min_c", $min_c),
      array(":menorc", $menorc),
      array(":menorcm", $menorcm),
      array(":mayorc", $mayorc),
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
