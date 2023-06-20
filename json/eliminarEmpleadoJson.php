<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();
$id = $_GET["id"];
$validaCaTer="SELECT * FROM gn_novedad WHERE empleado=$id";
$resultadoCaTer = $mysqli->query($validaCaTer);
if (mysqli_num_rows($resultadoCaTer)>0) {
}else{
    $elm=eliminarEmpleado($id);
}
$sql = "DELETE FROM gn_empleado WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>