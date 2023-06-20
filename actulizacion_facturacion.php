<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();

#*****************************************************************************************************
$html ='';

$row = $con->Listar("SELECT id_unico,  codigo_interno, numero_factura, id_unico, fecha_vencimiento from act_subir where id_factura is null order by id_unico limit 8000");
for ($i=0; $i <count($row); $i++) { 
	$ba = $con->Listar("SELECT id_unico, id_unico  from gp_factura where numero_factura = ".$row[$i][2]." and tipofactura = 2 ");
	//ECHO "SELECT id_unico, id_unico  from gp_factura where numero_factura = ".$row[$i][2]." and periodo = 15";
	if(empty($ba[0][0])){
		$html .= $row[$i][2].',<br/>';
	} else {
		$sql = "UPDATE  act_subir set id_factura ='".$ba[0][1]."' WHERE id_unico = ".$row[$i][3];
	    $resultadoP = $mysqli->query($sql);
	    ECHO $sql.'<BR/>';
	}
}
echo $html;

/*

$row = $con->Listar("SELECT id_unico,  codigo_interno, numero_factura, id_unico, fecha_vencimiento, fecha_factura from act_subir order by id_unico ");
for ($i=0; $i <count($row); $i++) { 
	$ba = $con->Listar("SELECT id_unico, id_unico  from gp_factura where numero_factura = ".$row[$i][2]." and tipo_factura = 2");
	//ECHO "SELECT id_unico, id_unico  from gp_factura where numero_factura = ".$row[$i][2]." and periodo = 15";
	if(empty($ba[0][0])){
		echo $row[$i][2].',<br/>';
	} else {
		//$sql = "UPDATE  gp_factura set fecha_vencimiento ='".$row[$i][4]."', fecha_factura ='".$row[$i][5]."' WHERE id_unico = ".$ba[0][1];
		$sql = "UPDATE  gp_factura set fecha_vencimiento ='".$row[$i][4]."' WHERE id_unico = ".$ba[0][1];
	    $resultadoP = $mysqli->query($sql);
	    ECHO $sql.';<BR/>';
	}
}
ECHO 'FIN'; 
#*/