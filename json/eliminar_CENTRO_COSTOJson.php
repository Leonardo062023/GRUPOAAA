<?php 
	require_once('../Conexion/conexion.php');
  require_once('../Conexion/ConexionPDO.php');

  $con = new ConexionPDO();
  

    session_start();
   
   //Captura de ID y eliminación del registro en la tabla gf_centro_costo.
   $id = $_GET['id'];

   //Hacer la consulta en la tabla gf_centro_costo para descartar que el registro a eliminar exista en un predecesor.
   $queryPred =$con->Listar("SELECT Id_Unico FROM gf_centro_costo WHERE Predecesor = $id") ;
  
   
   //Si no existe el registro como predecesor se elimina.
   if(count($queryPred)== 0  )
   {
   
    $sql_cons ="DELETE FROM  gf_centro_costo
    WHERE id_unico=:id_unico";
    $sql_dato = array(
        array(":id_unico",$id)
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
   
   }
   else //Si existe el registro como predecesor, no se realiza la eliminación y el resultado se hará false para que el usuario reciba el mensaje.
    $resp = false;

  echo json_encode($resp);
?>