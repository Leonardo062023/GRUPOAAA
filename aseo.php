<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];


	$rowj = $con->Listar("SELECT DISTINCT cuenta_contrato, mora, 
		tipo_productor, cft_cvna_vbs, 
		trbl_trlu_trra,	historicos_123, 
		historicos_456, valor_vigencia_actual, 
		valor_1, valor_5,valor_6,  
		unidad_habitacional, unidad_no_habitacional, 
		subsidio, contribucion, 
		frecuencia_barrido, frecuencia_recoleccion, 
		total_facturado,  valor_2, 
		trna_tafna_tra_tafa, id_unico 
		from gp_aseo a WHERE a.periodo = 14 
		and id_unico >=87028 
		order by id_unico ASC LIMIT 10000");
	$item = 0;
	$html = 'Codigo No Encontrados<br/>';
	for ($ij=0; $ij < count($rowj) ; $ij++) { 
		$cuenta_c = $rowj[$ij][0];

		
		$ud = $con->Listar("SELECT DISTINCT uv.id_unico, uv.codigo_interno , concat_ws(' ', es.codigo, es.nombre) , fs.aseo from  gp_aseo a 
		left join gp_unidad_vivienda uv ON a.cuenta_contrato like CONCAT('%',uv.codigo_interno,'%')
		left join gp_estrato es ON uv.estrato = es.id_unico 
		left join gp_facturacion_servicios_enero fs ON uv.codigo_interno = fs.codigo_interno
		where  a.cuenta_contrato like CONCAT('%',uv.codigo_interno,'%') and  a.cuenta_contrato = '".$cuenta_c."'");


		$codigo_interno = $ud[0][1];


		 $sql = "update gp_facturacion_servicios_febrero
		set 
		mora 					='".$rowj[$ij][1]."', 
		tipo_cliente			='".$rowj[$ij][2]."', 
		cft 					='".$rowj[$ij][3]."',
		trbl 					='".$rowj[$ij][4]."',
		historico_123 			='".$rowj[$ij][5]."',
		historico_456 			='".$rowj[$ij][6]."',
		aseo 					='".$rowj[$ij][7]."',
		subsidio 				='".$rowj[$ij][8]."',
		deuda 					='".$rowj[$ij][9]."',
		ajuste_d 				='".$rowj[$ij][10]."',
		ajuste_c 				='".$rowj[$ij][11]."',
		uni_residenciales 		='".$rowj[$ij][12]."',
		un_comerciales 			='".$rowj[$ij][13]."',
		porcentaje_subsidio		='".$rowj[$ij][14]."',
		porcentaje_contribucion	='".$rowj[$ij][15]."',
		frec_barrido 			='".$rowj[$ij][16]."',
		frec_recoleccion 		='".$rowj[$ij][17]."',
		total_aseo				='".$rowj[$ij][18]."',
		contribucion_aseo		='".$rowj[$ij][19]."',
		trna_tafna_tra_tafa		='".$rowj[$ij][20]."'
		
		where  codigo_interno = '".$codigo_interno."'";
		$resultadoP = $mysqli->query($sql);

		if(empty($codigo_interno)){
			$html .='';
		}
		echo $rowj[$ij][21].' Cuenta_CONTRATO: :' .$cuenta_c.'- R:'.$resultadoP.'<br/>';
	



	}

	echo $html;