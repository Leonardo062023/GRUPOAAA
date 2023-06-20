<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();

#*****************************************************************************************************

$row = $con->Listar("SELECT id_unico,  codigo_interno from gp_facturacion_servicios_inicial where id_unico not in (SELECT id_unico from gp_facturacion_servicios) ORDER BY id_unico ");


for ($i=0; $i <count($row); $i++) { 
	$ba = $con->Listar("SELECT atraso from act_atraso where codigo = ".$row[$i][1]);
	if(!empty($ba[0][0])){
		echo $at = $ba[0][0] + 1;
		
		$sql = "UPDATE gp_facturacion_servicios_inicial set atraso = ".$at." WHERE id_unico = ".$row[$i][0];
		$resultadoP = $mysqli->query($sql);
		echo $sql.'<br/>';
		
	}
}

