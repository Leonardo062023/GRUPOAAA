<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();
$id = $_GET["id"];
$elm = eliminarIncapacidad($id);
$sql = "DELETE FROM gn_incapacidad WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>