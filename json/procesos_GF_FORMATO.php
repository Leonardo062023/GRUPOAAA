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
       $sql_cons ="DELETE FROM gf_formato WHERE id_unico = :id_unico";
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
	$nombre   = $_REQUEST['txtNombre'];
	$version   = $_REQUEST['txtVersion'];
  $codigo_calidad   = $_REQUEST['txtCodigo'];
  $fechaVersion   = $_REQUEST['txtFechaVersion'];
	$descripcion   = $_REQUEST['txtDescripcion'];
	$esCheque   = $_REQUEST['optCheque'];
	$plantilla   = $_REQUEST['plantilla'];
	$sql_cons ="INSERT INTO gf_formato (nombre,version,codigo_calidad,fechaVersion,descripcion,esCheque,compania,plantilla) VALUES (:nombre,:version,:codigo_calidad,:fechaVersion,:descripcion,:esCheque,:compania,:plantilla)";
       $sql_dato = array(
		array(":nombre",$nombre),
		array(":version",$version),
    array(":codigo_calidad",$codigo_calidad),
		array(":fechaVersion",$fechaVersion),
		array(":descripcion",$descripcion),
		array(":esCheque",$esCheque),
		array(":plantilla",$plantilla),
		array(":compania",$compania),
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
    $nombre   = $_REQUEST['txtNombre'];
    $version   = $_REQUEST['txtVersion'];
    $codigo_calidad   = $_REQUEST['txtCodigo'];
    $fechaVersion   = $_REQUEST['txtFechaVersion'];
    $descripcion   = $_REQUEST['txtDescripcion'];
    $esCheque   = $_REQUEST['optCheque'];
    $plantilla   = $_REQUEST['plantilla'];
       $id       = $_REQUEST['txtId'];
       $sql_cons ="UPDATE gf_formato SET nombre = :nombre, version = :version, codigo_calidad = :codigo_calidad,fechaVersion = :fechaVersion, descripcion = :descripcion, esCheque = :esCheque, plantilla =:plantilla WHERE id_unico = :id_unico";

       $sql_dato = array(
            array(":nombre",$nombre),
            array(":version",$version),
            array(":codigo_calidad",$codigo_calidad),
            array(":fechaVersion",$fechaVersion),
            array(":descripcion",$descripcion),
            array(":esCheque",$esCheque),
            array(":plantilla",$plantilla),
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
