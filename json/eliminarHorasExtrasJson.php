<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();
$id = $_GET["id"];
$elm = eliminarHorasE($id);

$sql = "DELETE FROM gn_horas_extras WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo $resultado;
?>





