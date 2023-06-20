<?php

require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
session_start();
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    #** Cargar Estrato Por Uso **#
    case 1:
        $uso = $_REQUEST['uso'];
        if(!empty($uso)){
            $row = $con->Listar("SELECT id_unico, nombre FROM gp_estrato WHERE uso =$uso");
            if(count($row)>0){
                for ($i = 0; $i < count($row); $i++) {
                    echo '<option value="'.$row[$i][0].'">'. ucwords(mb_strtolower($row[$i][1])).'</option>';
                }
            } else {
                echo '<option value=" "> No Hay Estratos </option>';
            }
        } else {
            echo '<option value=""> - </option>';
        }
    break;
}
