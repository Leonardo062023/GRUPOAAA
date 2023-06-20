<?php
session_start();
require_once '../Conexion/conexion.php';
$anno = $_SESSION['anno'];
$cuenta = $_POST['cuenta'];
echo '<option value="">Centro Costo</option>';
$sql = "SELECT id_unico,nombre FROM gf_centro_costo WHERE parametrizacionanno = $anno ";
$result = $mysqli->query($sql);
while($row = mysqli_fetch_row($result)){
    echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
}
$filas = $mysqli->num_rows($result);
if($filas==0){
    echo '<option value="">Centro Costo</option>';
    $sql = "SELECT id_unico,nombre FROM gf_centro_costo WHERE parametrizacionanno = $anno";
    $res = $mysqli->query($sql);
    while($fila = mysqli_fetch_row($res)){
        echo '<option value="'.$fila[0].'">'.ucwords(mb_strtolower($fila[1])).'</option>';
    }
}
?>