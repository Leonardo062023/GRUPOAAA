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
    if(count($queryPred)== 0  )
    {
    
     $sql_cons ="DELETE FROM gf_concepto WHERE Id_Unico = :id_unico";
     $sql_dato = array(
         array(":id_unico",$id)
     );
     $resp = $con->InAcEl($sql_cons,$sql_dato);
     if(empty($resp)){
      echo 1;
  } else {
      echo 2;
    // echo($obj_resp);
  }
    }


    

    break;
  case 2:
    $anno       = $_SESSION['anno'];
    $nombre     = $_POST['nombre'];
    $tipo       = $_POST['sltTipoConcepto'];
    $amortizable= $_POST['rdamrt'];
    if(!empty($_REQUEST['sltTipoServicio'])){
        $servicio = $_REQUEST['sltTipoServicio'];
    } else {
        $servicio = 0 ;
    }
  




    $sql_cons  = "INSERT INTO gf_concepto (nombre,clase_concepto, parametrizacionanno, amortizable, tipo_servicio) 
    VALUES (:nombre,:tipo, :anno, :amortizable, :servicio)";
    $sql_dato = array(
      array(":nombre", $nombre),
      array(":tipo", $tipo),
      array(":anno", $anno),
      array(":amortizable", $amortizable),
      array(":servicio", $servicio),
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


    $nombre     = $_REQUEST['nombre'];
    $id         = $_REQUEST['id'];
    $tipoC      = $_REQUEST['sltTipoConcepto'];
    $amortizable= $_REQUEST['rdamrt'];
    if(!empty($_REQUEST['sltTipoServicio'])){
        $servicio = $_REQUEST['sltTipoServicio'];
    } else {
        $servicio = 0 ;
    }
    //modifica los campos involucrados

    $sql_cons = "UPDATE gf_concepto SET nombre=:nombre,
    clase_concepto=:tipoC, amortizable = :amortizable,  
    tipo_servicio = :servicio
    WHERE id_Unico = :id_unico";

    $sql_dato = array(
      array(":nombre", $nombre),
      array(":tipoC", $tipoC),
      array(":amortizable", $amortizable),
      array(":servicio", $servicio ),
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


?>
