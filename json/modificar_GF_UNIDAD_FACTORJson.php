<?php
  require '../Conexion/ConexionPDO.php';
  require '../Conexion/conexion.php';
  @session_start();
  $con        = new ConexionPDO();
  $compania   = $_SESSION['compania'];
  $usuario    = $_SESSION['usuario'];
  $anno       = $_SESSION['anno'];
  $action     = $_REQUEST['action'];
  //Captura de datos e instrucción SQL para su modificación en la tabla gf_actividad_mantenimiento.
 switch ($action) {
   #   *** Eliminar clase pregunta    ***  #
   case 1:
       $id      = $_REQUEST['id'];
       $sql_cons ="DELETE FROM gf_unidad_factor WHERE id_unico = :id_unico";
       $sql_dato = array(
               array(":id_unico",$id),
       );
       $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
       if(empty($obj_resp)){
           echo 1;
       } else {
           echo 2;
         echo($obj_resp);
       }
   break;
   #   *** Guardar clase pregunta    ***  #
   case 2:
       $nombre   = $_REQUEST['nombre'];
       $valor   = $_REQUEST['valor'];
       $codigo_fe   = $_REQUEST['codigo'];
       $sql_cons ="INSERT INTO gf_unidad_factor (nombre,valor,codigo_fe) VALUES (:nombre,:valor,:codigo_fe)";
       $sql_dato = array(
        array(":nombre",$nombre),
        array(":valor",$valor),
        array(":codigo_fe",$codigo_fe),
       
      );
       $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
       if(empty($obj_resp)){
           echo 1;
       } else {
           echo 2;
         echo($obj_resp);
       }
   break;
   #   *** Modificar clase pregunta    ***  #
   case 3:
       $nombre   = $_REQUEST['nombre'];
       $valor   = $_REQUEST['valor'];
       $codigo_fe   = $_REQUEST['codigo'];
       $id       = $_REQUEST['id'];
       $sql_cons ="UPDATE gf_unidad_factor SET nombre = :nombre, valor = :valor, codigo_fe = :codigo_fe WHERE id_unico = :id_unico";
       $sql_dato = array(
        array(":nombre",$nombre),
        array(":valor",$valor),
        array(":codigo_fe",$codigo_fe),
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