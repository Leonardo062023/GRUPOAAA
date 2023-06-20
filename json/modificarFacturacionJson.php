<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modificaciones
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Fecha 		: 25/04/2017
// Modificado 	: Alexander Numpaque
// Descripción 	: Se cambio validación y metodo de envio de los id para modificar
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
session_start();
require_once '../Conexion/conexion.php';
$id=$_POST['id'];
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$descripcion = '"'.$_POST['descripcion'].'"';
$fechaVT = ''.$mysqli->real_escape_string(''.$_POST['fechaVencimiento'].'').'';
$valorV = explode("/",$fechaVT);
$fechaVencimiento =  '"'.$valorV[2].'-'.$valorV[1].'-'.$valorV[0].'"';
$descripcion = '"'.$_POST['descripcion'].'"';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Validamos que la descripción no este vacia
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST['descripcion'])) {
	$descripcion = '"'.$_POST['descripcion'].'"';
}else{
	$descripcion = 'NULL';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modificamos los valores de la cabeza
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "UPDATE gp_factura SET fecha_factura = $fecha, fecha_vencimiento = $fechaVencimiento, descripcion = $descripcion WHERE id_unico = $id";
$result = $mysqli->query($sql);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Validamos que la variable id_cnt no esta vacia y actualizamos los valores del comprobante
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST['id_cnt'])) {
	$id_cnt = $_POST['id_cnt'];
	$sqlC = "UPDATE gf_comprobante_cnt SET fecha = $fecha, descripcion = $descripcion WHERE id_unico = $id_cnt";
	$resultC = $mysqli->query($sqlC);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Validamos que la variable id_pptal no esta vacia y actualizamos los valores del comprobante
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST['id_pptal'])) {
	$id_pptal = $_POST['id_pptal'];
	$sqlP = "UPDATE gf_comprobante_pptal SET fecha = $fecha, fechavencimiento = $fechaVencimiento, descripcion = $descripcion WHERE id_unico = $id_pptal";
	$resultP = $mysqli->query($sqlP);
}
echo json_encode($result);
?>
