<?php 
    require_once('../Conexion/conexion.php');
    session_start();
    $id = $_GET['id'];
    $query = "DELETE FROM gp_concepto_servicio WHERE id_unico = $id";
    $resultado = $mysqli->query($query);

    echo json_encode($resultado);
?>