<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();
$id = $_GET["id"];
$opc = $_GET["opc"];
if($opc == 2) {
	$empl = $_GET["emp"];
	$per = $_GET["peri"];
	$elm=eliminarNovedades($empl,$per,$opc,$id);
	$sql = "DELETE FROM gn_novedad WHERE empleado = $empl and periodo = $per";	
}else {
	$elm=eliminarNovedades($empl,$per,$opc,$id);
	$sql = "DELETE FROM gn_novedad WHERE id_unico = $id";	
}

$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>