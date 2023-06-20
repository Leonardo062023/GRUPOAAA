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
       $sql_cons ="DELETE FROM gf_tipo_documento WHERE id_unico = :id_unico";
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
    $es_obligatorio   = $_REQUEST['obligatorio'];
    $consecutivo_unico   = $_REQUEST['consecutivo'];
    $formato   = $_REQUEST['formato'];
    $dependencia   = $_REQUEST['dependencia'];
    $clase_informe   = $_REQUEST['clase'];
    $vigencia   = $_REQUEST['vigencia'];

    $sql_cons ="INSERT INTO gf_tipo_documento (nombre,es_obligatorio,consecutivo_unico,formato,clase_informe,dependencia,compania,vigencia) 
    VALUES (:nombre,:es_obligatorio,:consecutivo_unico,:formato,:clase_informe,:dependencia,:compania,:vigencia)";
       $sql_dato = array(
        array(":nombre",$nombre),
        array(":es_obligatorio",$es_obligatorio),
        array(":consecutivo_unico",$consecutivo_unico),
        array(":formato",$formato),
        array(":clase_informe",$clase_informe),
        array(":dependencia",$dependencia),
        array(":compania",$compania),
        array(":vigencia",$vigencia),
       
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
       $es_obligatorio   = $_REQUEST['obligatorio'];
       $consecutivo_unico   = $_REQUEST['consecutivo'];
       $formato   = $_REQUEST['formato'];
       $dependencia   = $_REQUEST['dependencia'];
       $clase_informe   = $_REQUEST['clase'];
       $vigencia   = $_REQUEST['vigencia'];
       $id       = $_REQUEST['id'];
       $sql_cons ="UPDATE gf_tipo_documento SET nombre = :nombre, es_obligatorio = :es_obligatorio, consecutivo_unico = :consecutivo_unico, 
       formato = :formato, clase_informe = :clase_informe, dependencia = :dependencia, vigencia = :vigencia  WHERE id_unico = :id_unico";
       $sql_dato = array(
        array(":nombre",$nombre),
        array(":es_obligatorio",$es_obligatorio),
        array(":consecutivo_unico",$consecutivo_unico),
        array(":formato",$formato),
        array(":clase_informe",$clase_informe),
        array(":dependencia",$dependencia),
        array(":vigencia",$vigencia),
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