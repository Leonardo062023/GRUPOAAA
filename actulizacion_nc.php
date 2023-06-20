<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];

#* DATOS DOCUMENTO
$row = $con->Listar("SELECT codigo_interno, codigo_ruta, sector, direccion, usuario, uso, estrato, identificador, 
    total_acdto_alc  - (ifnull(ROUND(acueducto_cargo_fijo), 0)+
        ifnull(ROUND(acueducto_consumo_basico), 0)+
        ifnull(ROUND(acueducto_consumo_complementario), 0)+
        ifnull(ROUND(acueducto_consumo_suntuario), 0)+
        ifnull(ROUND(acueducto_subsido_cargo_fijo)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_basico)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_complementario)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_suntuario)*-1, 0)+
        ifnull(ROUND(acueducto_mora), 0)+
        ifnull(ROUND(alcantarillado_cargo_fijo), 0)+
        ifnull(ROUND(alcantarillado_consumo_basico), 0)+
        ifnull(ROUND(alcantarillado_consumo_complementario), 0)+
        ifnull(ROUND(alcantarillado_consumo_suntuario), 0)+
        ifnull(ROUND(alcantarillado_subsido_cargo_fijo)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_basico)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_complementario)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_suntuario)*-1, 0)+
        ifnull(ROUND(deuda_anterior), 0)+
        ifnull(ROUND(financiacion), 0)+
        ifnull(ROUND(abonos), 0)+
        ifnull(ROUND(ajuste_peso), 0)
       ) as dif from gp_facturacion_servicios_historico where periodo = '03/2021' 
       HAVING dif =-4836.00 
       ORDER BY sector, codigo_ruta ");
 
for ($i=0; $i <count($row) ; $i++) { 
    #** valores 
    $rowv = $con->Listar("SELECT DISTINCT acueducto_cargo_fijo, acueducto_subsido_cargo_fijo*-1 FROM gp_facturacion_servicios_historico 
    where uso = '".$row[$i][5]."' and estrato ='".$row[$i][6]."' and periodo = '03/2021'  
    AND acueducto_cargo_fijo != 0 and acueducto_subsido_cargo_fijo IS NOT NULL");
    $cargof = $rowv[0][0];

    #**CF
    $sql = "INSERT INTO act_otros_c
            (id_c,servicio,
            valor,
            codigo,
            fecha,
            id_uvms)
            VALUES(164, 'Cargo F', $cargof, ".$row[$i][0].",NULL,".$row[$i][7]." )";
    $resultadoP = $mysqli->query($sql);

    if( $rowv[0][1]>0){
    	$cont = $rowv[0][1];
    	#**CF
	    $sql = "INSERT INTO act_otros_c
	            (id_c,servicio,
	            valor,
	            codigo,
	            fecha,
	            id_uvms)
	            VALUES(166, 'Cont F', $cont, ".$row[$i][0].",NULL,".$row[$i][7]." )";
	    $resultadoP = $mysqli->query($sql);
    } else {
    	$sub  = $rowv[0][1];
    	#**CF
	    $sql = "INSERT INTO act_otros_c
	            (id_c,servicio,
	            valor,
	            codigo,
	            fecha,
	            id_uvms)
	            VALUES(165, 'subs F', $sub, ".$row[$i][0].",NULL,".$row[$i][7]." )";
	    $resultadoP = $mysqli->query($sql);
    }
    
}