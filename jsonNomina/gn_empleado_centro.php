<?php require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO(); 
$action     = $_REQUEST['action'];

switch ($action){
    #* Guardar
    case 1:
        $rta    = 0;
        $sql_cons ="INSERT INTO `gn_empleado_centro_costo` 
                ( `empleado`, `centro_costo`, 
                `valor`,`porcentaje`) 
        VALUES (:empleado, :centro_costo, 
                :valor,:porcentaje)";
        $sql_dato = array(
                array(":empleado",$_REQUEST['empleado']),
                array(":centro_costo",$_REQUEST['centro_costo']),
                array(":valor",$_REQUEST['valor']),
                array(":porcentaje",$_REQUEST['porcentaje']),
                
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 

        if(empty($resp)){
            $rta = 1;
        }
        echo $rta;
    break;
    #Eliminar
    case 2:
        $rta    = 0;
        $sql_cons ="DELETE FROM `gn_empleado_centro_costo` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$_REQUEST['id']),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta    = 1;
        }
        echo $rta;
    break;
}

?>