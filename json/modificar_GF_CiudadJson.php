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
       $sql_cons ="DELETE FROM gf_ciudad WHERE id_unico = :id_unico";
       $sql_dato = array(
               array(":id_unico",$id),
       );
       $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
       if(empty($obj_resp)){
           echo 1;
       } else {
           echo 2;
         // echo($obj_resp);
       }
   break;
   #   *** Guardar clase pregunta    ***  #
   case 2:
       $nombre   = $_REQUEST['nombre'];
       $departamento   = $_REQUEST['departamento'];
       $rss   = $_REQUEST['rss'];
       $sql_cons ="INSERT INTO gf_ciudad (nombre,departamento,rss) VALUES (:nombre,:departamento,:rss)";
       $sql_dato = array(
               array(":nombre",$nombre),
               array(":departamento",$departamento),
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
       $departamento   = $_REQUEST['departamento'];
       $rss   = $_REQUEST['rss'];
       $id       = $_REQUEST['id'];
       $sql_cons ="UPDATE gf_ciudad SET nombre = :nombre, departamento = :departamento, rss = :rss WHERE id_unico = :id_unico";
       $sql_dato = array(
            array(":nombre",$nombre),
            array(":departamento",$departamento),
            array(":rss",$rss),
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
   case 4:
   break;
 }
 