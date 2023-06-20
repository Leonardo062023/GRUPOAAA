<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];


#ACTUALIZAR DEUDA NEGATIVA y POSITIVA POR DECIMALES 
$row = $con->Listar("SELECT uv.id_unico, uv.codigo_interno, uv.deuda_c, uvms.id_unico  FROM gp_unidad_vivienda uv 
LEFT JOIN gp_unidad_vivienda_servicio uvs ON uv.id_unico = uvs.unidad_vivienda
left join gp_unidad_vivienda_medidor_servicio uvms ON uvs.id_unico = uvms.unidad_vivienda_servicio
where uvms.id_unico is not null
--  and uv.deuda_c < 0 and uv.deuda_c >-100
 and uv.deuda_c > 0 and uv.deuda_c <100
order by uv.id_unico ");



for ($i=0; $i < count($row) ; $i++) { 
	$uvms = $row[$i][3];

	$df = $con->Listar("SELECT f.numero_factura, df.id_unico, df.valor, (SELECT SUM(dp.valor) from gp_detalle_pago dp WHERE dp.detalle_factura = df.id_unico) as vp , 
		(SELECT GROUP_CONCAT(DISTINCT dp.id_unico) from gp_detalle_pago dp WHERE dp.detalle_factura = df.id_unico) as dp, 
		(SELECT GROUP_CONCAT(DISTINCT dp.pago) from gp_detalle_pago dp WHERE dp.detalle_factura = df.id_unico LIMIT 1) as pg, 
		f.id_unico , f.tercero 
		from gp_detalle_factura df
		left join gp_factura f ON f.id_unico = df.factura 
		where f.unidad_vivienda_servicio = $uvms 
		HAVING df.valor!= (SELECT IF(SUM(dp.valor) is null, 0, SUM(dp.valor)) 
                +IF(SUM(dp.iva) is null, 0, SUM(dp.iva)) 
                +IF(SUM(dp.impoconsumo) is null, 0, SUM(dp.impoconsumo)) 
                +IF(SUM(dp.ajuste_peso) is null, 0, SUM(dp.ajuste_peso))   from gp_detalle_pago dp WHERE dp.detalle_factura = df.id_unico and df.factura = f.id_unico) ");
	for ($d=0; $d <count($df) ; $d++) { 
		$id_p = '';
		$vd = $df[$d][2];
		if(empty($df[$d][4])){
			if($id_p ==''){
				$pg = $con->Listar("SELECT MAX(dp.pago) from gp_detalle_factura df 
					LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura 
					where df.factura = ".$df[$d][6]);
				
				if(empty($pg[0][0])){
					$pg = $con->Listar("SELECT MIN(id_unico) from gp_pago where responsable = ".$df[$d][7]);
					$id_p = $pg[0][0];
				} else {
					$id_p = $pg[0][0];
				}
			} else {
				$id_p = $id_p;
			}
			$sql_cons ="INSERT INTO `gp_detalle_pago`
            ( `detalle_factura`, `valor`,
            `iva`,`impoconsumo`,
            `ajuste_peso`,`saldo_credito`,
            `pago`)
            VALUES (:detalle_factura, :valor,
            :iva, :impoconsumo,
            :ajuste_peso,:saldo_credito,
            :pago)";
            $sql_dato = array(
                array(":detalle_factura",$df[$d][1]),
                array(":valor",$df[$d][2]),
                array(":iva",0),
                array(":impoconsumo",0),
                array(":ajuste_peso",0),
                array(":saldo_credito",0),
                array(":pago",$id_p),

            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            var_dump($resp);
		} else {
			$dp = $con->Listar("SELECT id_unico, valor,pago from gp_detalle_pago where id_unico in (".$df[$d][4].")");
			//echo "SELECT id_unico, valor from gp_detalle_pago where id_unico in (".$df[$d][4].")";
			$vl = $vd;
			for ($p=0; $p <count($dp) ; $p++) { 
				
				if($vl !=0){
					$vrdp = $dp[$p][0];
					
					if($vrdp>$vd){
						$sql		= "UPDATE gp_detalle_pago SET valor = $vd where id_unico = ".$dp[$p][0];
						$resultadoP = $mysqli->query($sql);	
						$vl = $vl - $vd;
					} else {
						$vl = $vl - $dp[$p][1];
					}
				} else {
					$id_p 		= $dp[$p][2];
					$sql		= "DELETE FROM gp_detalle_pago where id_unico = ".$dp[$p][0];
					$resultadoP = $mysqli->query($sql);
				}
			}
		}
	}
	echo 'ID:'.$row[$i][0].'<br/>';
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