<?php
session_start();
######################################################################################################
# Modificaciones
# 14/02/2015 | 04:04 p.m. | Descripción se cambio la validación y la impresión de la consulta
$compania = $_SESSION['compania'];
require_once '../Conexion/conexion.php';
$cuenta = $_POST['cuenta'];
echo '<option value="">Proyecto</option>';

$sqlC = "SELECT DISTINCT id_unico,nombre
FROM gf_proyecto WHERE compania = $compania";
$resultC = $mysqli->query($sqlC);
while($rowC = mysqli_fetch_row($resultC)){
    echo '<option value="'.$rowC[0].'">'.ucwords(strtolower($rowC[1])).'</option>';
}

?>