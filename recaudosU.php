<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];

	$rowj = $con->Listar("SELECT DISTINCT uvms.id_unico, uv.tercero, uv.deuda , uv.id_unico 
		FROM gp_unidad_vivienda_medidor_servicio uvms 
	LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
	LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
	where uv.deuda_dic !=0 

	-- where deuda_dic!= 0 and deuda_dic>0 and deuda_dic = deuda");
	$item = 0;
	for ($ij=0; $ij < count($rowj) ; $ij++) { 
		$id_unidad_viviendas  = $rowj[$ij][0];
		$id_tercero_uv  	  = $rowj[$ij][1];
		$rowdf = $con->Listar("SELECT f.id_unico, 
		    DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
		    SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
		    uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
		    f.periodo , p.fecha_cierre 
		    FROM gp_detalle_factura df 
		    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
		    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
		    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
		    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
		    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
		    LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
		    LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
		    WHERE  uvms.id_unico = $id_unidad_viviendas 
		    AND f.fecha_factura <='2020-12-01' AND f.tipofactura IN(2,6)
		    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
		$dm = 0;
		$saldoT = 0;
		for ($f = 0; $f < count($rowdf); $f++) {
		    $vr =0;
		    if(!empty($rowdf[$f][4])){
		        $valor = $rowdf[$f][3];
		        $ids   = $rowdf[$f][4];
		        $rowr  = $con->Listar("SELECT 
		            SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
		            FROM gp_detalle_pago dp 
		            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
		            WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='2020-12-01'");
		        $vr     = $rowr[0][0];
		         $saldof = $valor -$rowr[0][0];
		    } else {
		        $saldof = 0;
		    }
		    $saldoT +=  $saldof;
		}

		if(empty($rowj[$ij][2])){
			$saldouv = 0;
		} else {
			$saldouv = $rowj[$ij][2];
		}
		$total_r = $saldoT - $saldouv;
		echo 'item = '.$item.'IDV: '.$id_unidad_viviendas.' SALDO FINAL='.$rowj[$ij][2].' Saldo factura='.$saldoT.' Saldo a Recaudar : '.$total_r.'<br/>';	
		$sql = "UPDATE   gp_unidad_vivienda 
	                set deuda_dic = $total_r WHERE id_unico = ".$rowj[$ij][3];
	        $resultadoP = $mysqli->query($sql);
		$item += 1;
		

	}






function afectadoDetalleF($id_detalle_f){
    global $con;
    $afec    = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_detalle_pago WHERE detalle_factura = $id_detalle_f");
    $afec    = $afec[0][0];
    return $afec;
}

function saldoFactura($factura){
    global $con;
    $sld    = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
    $sld    = $sld[0][0];
    $saldo  = 0;
    if(!empty($sld)){
        # Buscar Recaudos
        $rc = $con->Listar("SELECT SUM(dp.valor + dp.iva + dp.impoconsumo + dp.ajuste_peso)
            FROM gp_detalle_pago dp
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
            WHERE df.factura =$factura");
        $rc = $rc[0][0];
        if(!empty($rc)){
            $saldo = $sld-$rc;
        } else {
            $saldo = $sld;
        }
    }
    
    return $saldo;
}