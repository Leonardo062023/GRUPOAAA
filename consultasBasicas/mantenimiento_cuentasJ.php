<?php
require_once ('../Conexion/conexion.php');
ini_set('max_execution_time', 0);
session_start();
$anno = $_SESSION['anno'];
#BUSCAN TODAS LAS CUENTAS
$cuentas = "SELECT id_unico, codi_cuenta FROM gf_cuenta  WHERE parametrizacionanno = $anno ORDER BY codi_cuenta ASC";
$cuentas = $mysqli->query($cuentas);
#VARIABLE CONTEO DE ACTUALIZACION
$n=0;
#SI HAY CUENTAS ENCONTRADASS
if(mysqli_num_rows($cuentas)>0){
    while ($row = mysqli_fetch_array($cuentas)) {
        #ASIGNAR EL CODIGO A UNA VARIABLE
        $codigo = str_replace(' ', '', $row[1]);
        #CONTAR LA CANTIDAD DE DIGITOS DEL CODIGO
        $cant = strlen($codigo);
        #SI LA CANTIDAD ES UNO NO SE HACE NADA
        if ($cant == 1) {

        }else{
        #SI NO 
        #SE DEFINE UNAS VARIABLES CONTEO
        $ctn = 0;
        $cant2=$cant;
        #CICLO PARA BUSCAR PREDECESOR
        for($i = 0;$i <=$cant;$i++){
            #SI EL CODIGO TIENE DOS DIGITOS
            $men = substr($codigo,0,-1);
            
            #CON EL CODIGO HALLADO BUSCAMOS UN CODIGO IGUAL EL CUAL SERIA EL PREDECESOR
             $sql = "SELECT DISTINCTROW
                           PADRE.id_unico,                        
                           PADRE.codi_cuenta,
                           PADRE.nombre 
                    FROM
                           gf_cuenta PADRE
                    WHERE
                        PADRE.parametrizacionanno = $anno 
                        AND PADRE.codi_cuenta = '$men'";

                    $ctn = $ctn + 1;
                        
                $codigo = $men;
                $cant2=$cant2-1;
            $query = $mysqli->query($sql);
            if (mysqli_num_rows($query)>0) {
                
                #SI HAY ALGUNA COINCIDENCIA 
                #SE PASA A REALIZAR LA ACTUALIZACION
                $pred = mysqli_fetch_row($query);
                $predecesor = $pred[0];
                $update = "UPDATE gf_cuenta SET predecesor ='$predecesor' WHERE id_unico = '$row[0]'";
                $result= $mysqli->query($update);
                if($result==true){
                #SE CUENTA LA ACTUALIZACION
                $n=$n+1;
                }
                #VARIABLE ROMPE EL CICLO
                $cant=0;
                $ctn=0;
            } else {
                
            }
                                   
        }          
       }
    }
    $mensaje= $n." Cuentas Actualizadas";
} else {
    $mensaje = 'No se encontraron cuentas';
}
echo json_encode($mensaje);