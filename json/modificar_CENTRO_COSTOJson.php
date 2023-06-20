<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#14/12/2017 | Parametrizacion y compañia- Redireccionamiento
########################################################################################
  require_once('../Conexion/conexionPDO.php');
  session_start();
  $con = new ConexionPDO();
  $action     = $_REQUEST['action'];

  //Captura de datos e instrucción SQL para su modificación en la tabla gf_centro_cosoto.
  switch ($action) {
    #   *** Eliminar centro costo    ***  #
    case 1:
      
      $id = $_REQUEST['id'];

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
       if(empty($resp)){
        echo 1;
    } else {
        echo 2;
      // echo($obj_resp);
    }
      }
    break;
    #   *** Guardar centro costo   ***  #
    case 2:
      $nombre = $_REQUEST['nombre'];
      $movimiento = $_REQUEST['movimiento'];
      $sigla = $_REQUEST['sigla'];
      $tipoCentCost = $_REQUEST['tipoCentCost'];
      $predecesor = $_REQUEST['predecesor'];
      $claseServ = $_REQUEST['claseServ'];
      $param = $_SESSION['anno'];
      $compania = $_SESSION['compania'];
      
      $cantidad = NULL;
      if($movimiento == 1){
         $cantidad = $_REQUEST['cantidad'];
      }  
      if($predecesor == '""')//Consulta si predecesor es vacío.
      {
        $predecesor ='NULL';
      }
      $sql_cons ="INSERT INTO gf_centro_costo (Nombre, Movimiento, Sigla, TipoCentroCosto, Predecesor,
      ClaseServicio, ParametrizacionAnno, Compania, cantidad_distribucion)  
      VALUES(:nombre, :movimiento,:sigla,:tipocentrocosto, :predecesor, 
      :claseservicio, :parametro, :compania, :cantidad_distribucion)";
      $sql_dato = array(
      array(":nombre",$nombre),
      array(":movimiento",$movimiento),
      array(":sigla",$sigla), 
      array(":tipocentrocosto",$tipoCentCost),
      array(":predecesor",$predecesor),
      array(":claseservicio",$claseServ),
      array(":cantidad_distribucion",$cantidad),
      array(":parametro",$param),
      array(":compania",$compania),
      );
      $resp = $con->InAcEl($sql_cons,$sql_dato);
      
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
          echo($obj_resp);
        }
    break;
    #   *** Modificar clase centro costo   ***  #
    case 3:
      $id  = $_REQUEST['id'];
      $nombre = $_REQUEST['nombre'];
      $movimiento = $_REQUEST['movimiento'];
      $sigla = $_REQUEST['sigla'];
      $tipoCentCost = $_REQUEST['tipoCentCost'];
      $predecesor = $_REQUEST['predecesor'];
      $claseServ = $_REQUEST['claseServ'];
      
      $cantidad = NULL;
      if($movimiento == 1){
         $cantidad = $_REQUEST['cantidad'];
      }  
      if($predecesor == '""')//Consulta si predecesor es vacío.
      {
        $predecesor ='NULL';
      }
      $sql_cons ="UPDATE gf_centro_costo
      SET Nombre = :Nombre, Movimiento = :Movimiento, Sigla = :Sigla, 
         TipoCentroCosto = :TipoCentroCosto, Predecesor = :Predecesor, 
          ClaseServicio = :ClaseServicio, cantidad_distribucion = :cantidad_distribucion
      WHERE id_Unico = :id_unico";
      $sql_dato = array(
      array(":Nombre",$nombre),
      array(":Movimiento", $movimiento),
      array(":TipoCentroCosto", $tipoCentCost),
      array(":Predecesor",$predecesor),
      array("ClaseServicio",$claseServ),
      array("Sigla",$sigla),
      array(":cantidad_distribucion",$cantidad),
      array(":id_unico",$id),
      );
      $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
          echo($id);
        }
    break;
    case 4:
    break;
  }

  
?>
