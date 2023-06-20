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
       $sql_cons ="DELETE FROM gf_estado_anno WHERE id_unico = :id_unico";
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
       $pais   = $_REQUEST['pais'];
       $rss   = $_REQUEST['rss'];
       $codigo   = $_REQUEST['cod_carg'];
       $sql_cons ="INSERT INTO gf_estado_anno (nombre) VALUES (:nombre)";
       $sql_dato = array(
               array(":nombre",$nombre),
               array(":pais",$pais),
               array(":rss",$rss),
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
       $pais   = $_REQUEST['pais'];
       $rss   = $_REQUEST['rss'];
       $id       = $_REQUEST['id'];
       $sql_cons ="UPDATE gf_estado_anno SET nombre = :nombre WHERE id_unico = :id_unico";
       $sql_dato = array(
            array(":nombre",$nombre),
            array(":pais",$pais),
            array(":rss",$rss),
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

  