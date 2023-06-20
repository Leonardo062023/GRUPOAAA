<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0);
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];

	$rowp = $con->Listar("SELECT  dp.id_unico, dp.valor as valor , dp.pago as pago, dp.id_unico as dp, df.id_unico as df, p.fecha_pago as fecha , f.unidad_vivienda_servicio as uvms 
		FROM gp_factura f 
		LEFT JOIN gp_detalle_factura df oN f.id_unico = df.factura 
		LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura 
		LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
		WHERE  df.concepto_tarifa = 84 and f.periodo in (12, 13) order by dp.id_unico  ");

	
	for ($p=0; $p < count($rowp) ; $p++) { 
		$valor 		= $rowp[$p]['valor'];
		$pago  		= $rowp[$p]['pago'];
		$dpago 		= $rowp[$p]['dp'];
		$dfactura 	= $rowp[$p]['df'];
		//var_dump($rowj);
		$fecha_p    = $rowp[$p]['fecha'];
		$id_unidad_viviendas  = $rowp[$p]['uvms'];
		
		#ELIMINAR DETALLE PAGO 
		$sql = "DELETE FROM gp_detalle_pago WHERE id_unico = $dpago";
        $resultadoP = $mysqli->query($sql);

        #ELIMINAR DETALLE FACTURA 

        $sql = "DELETE FROM gp_detalle_factura  WHERE id_unico = $dfactura";
        $resultadoP = $mysqli->query($sql);


		#Deuda Factura Actual
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
	    AND f.fecha_factura <='".$fecha_p."'
	    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
	    echo "SELECT f.id_unico, 
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
	    AND f.fecha_factura <='".$fecha_p."'
	    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura";
		$dm = 0;
		$saldoT = 0;
		for ($f = 0; $f < count($rowdf); $f++) {
		    $vr =0;
		    if(!empty($rowdf[$f][4])){
		        $valorf = $rowdf[$f][3];
		        $ids   = $rowdf[$f][4];
		        $rowr  = $con->Listar("SELECT 
		            SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
		            FROM gp_detalle_pago dp 
		            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
		            WHERE dp.detalle_factura In ($ids) ");
		        if(empty($rowr[0][0])){
		        	$vr     = 0;
		        } else { 
		        	$vr     = $rowr[0][0];
		    	}
		        $saldof = $valorf -$vr;
		    } else {
		        $saldof = 0;
		    }
		    $saldoT +=  $saldof;
		}	



		$total_r = $saldoT;
		if($total_r > 0 ){
			$rowfr = $con->Listar("SELECT f.id_unico 
		    FROM  gp_factura f 
		    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
		    WHERE  uvms.id_unico = $id_unidad_viviendas 
		    GROUP BY f.id_unico ORDER BY  f.fecha_factura");
			for ($fr=0; $fr <count($rowfr) ; $fr++) { 
				if($valor>0){
					$factura = $rowfr[$fr][0];
					$saldo_factura = saldoFactura($factura);		    
			        $row = $con->Listar("SELECT id_unico, iva, impoconsumo,
			            ajuste_peso, valor_total_ajustado, cantidad 
			            FROM gp_detalle_factura
			            WHERE factura = $factura AND valor_total_ajustado !=0 
			            ORDER BY valor_total_ajustado ASC");
				    $saldo_final = $saldo_factura;
				    $dr     = 0;    
				    for ($i = 0; $i < count($row); $i++) {
				        $total_recaudo =0;    
				        if($valor!=0){
				            $reg            = 0;
				            $id_detalle     = $row[$i][0];
				            $valor_ajustad1 = $row[$i][4];
				            $cantidad       = $row[$i][5];
				            $valor_ajustado = $row[$i][4];
				            if($tipo_compania==1){
				                $iva            = ($row[$i][1]);
				                $impoconsumo    = ($row[$i][2]);
				            } else {
				                $iva            = ($row[$i][1] * $cantidad);
				                $impoconsumo    = ($row[$i][2] * $cantidad);
				            }            
				            $ajuste         = $row[$i][3];
				            $valor_recaud   = $valor_ajustado - ($iva + $impoconsumo + $ajuste);
				            $saldo_credito  = 0;
				            #Buscar Afectaciones
				            $dtp = afectadoDetalleF($id_detalle);
				            $valor_rc =0;
				            
				            if(!empty($dtp)|| $dtp!=NULL){
				                #Buscar Valores Recaudo
				                $vlr = $con->Listar("SELECT SUM(valor), SUM(iva), SUM(impoconsumo), SUM(ajuste_peso)
				                    FROM gp_detalle_pago WHERE id_unico IN ($dtp)");
				                $valor_r = $vlr[0][0];
				                $iva_r   = $vlr[0][1];
				                $impo_r  = $vlr[0][2];
				                $ajuste_r= $vlr[0][3];
				                # *** Valor Recaudado ***#
				                $valor_rc = $valor_r + $iva_r + $impo_r + $ajuste_r;

				                $diferencia = $valor_ajustad1 - $valor_rc;
				                if($diferencia > 0){
				                    $iva            -= $iva_r;
				                    $impoconsumo    -= $impo_r;
				                    $ajuste         -= $ajuste_r;
				                    $valor_ajustado -= $valor_r + $iva + $impoconsumo + $ajuste;
				                    $valor_recaud   -= $valor_r;
				                    if($saldo_final >= 0){
				                        $reg =1;
				                        $saldo_credito = $valor_ajustad1- $valor_ajustado;
				                        $saldo_final    -= $valor_ajustado;
				                    }
				                }
				            } else {
				                $saldo_final -=$valor_ajustad1;
				                if(round($saldo_final)>=0 || round($saldo_final)=='-0'){
				                   $reg =1;
				                   $saldo_credito = $valor_ajustado -($valor_recaud+$iva+$impoconsumo+$ajuste);
				                } else {
				                    $reg =1;
				                    $saldo_credito =0;
				                }
				            }
				            #*** Insertamos Detalle Pago
				            if($reg ==1){
				            #************ Validamos Total Recaudo Por El Valor **********#
				            $total_recaudo += $valor_recaud+$iva+$impoconsumo+$ajuste;
				            $crs =0;
				            if($valor < $total_recaudo){
				                # **** Validamos Iva Primero ***** #
				                if($iva <= $valor){
				                    $valor -=$iva;
				                    if($valor > 0){
				                        #*** Validamos Impoconsumo **#
				                        if($impoconsumo <= $valor){
				                            $valor -=$impoconsumo;
				                            if($valor>0){
				                                if($ajuste <= $valor){
				                                    $valor -=$ajuste;
				                                    if($valor > 0){
				                                        if($valor_recaud <= $valor){
				                                            $valor -=$valor_recaud;
				                                        } else {
				                                            $valor_recaud = $valor;
				                                            $valor -=$valor_recaud;
				                                        }
				                                    } else {
				                                        $valor_recaud = 0;
				                                    }
				                                } else {
				                                    $ajuste       = $valor;
				                                    $valor -=$ajuste;
				                                    $valor_recaud = 0;
				                                }
				                            } else {
				                                $ajuste       = 0;
				                                $valor_recaud = 0;
				                            }
				                        } else {
				                            $impoconsumo  = $valor;
				                            $valor -=$impoconsumo;
				                            $ajuste       = 0;
				                            $valor_recaud = 0;
				                        }
				                    } else {
				                        $impoconsumo  = 0;
				                        $ajuste       = 0;
				                        $valor_recaud = 0;
				                    }
				                } else {
				                    $iva          = $valor;
				                    $valor -=$iva;
				                    $impoconsumo  = 0;
				                    $ajuste       = 0;
				                    $valor_recaud = 0;
				                }
				            } else {
				                $valor -=$total_recaudo;
				            }
				            $saldo_credito = $valor_ajustad1-$valor_rc-($valor_recaud + $iva + $impoconsumo + $ajuste);
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
				                array(":detalle_factura",$id_detalle),
				                array(":valor",$valor_recaud),
				                array(":iva",$iva),
				                array(":impoconsumo",$impoconsumo),
				                array(":ajuste_peso",$ajuste),
				                array(":saldo_credito",$saldo_credito),
				                array(":pago",$pago),

				            );
				            $resp = $con->InAcEl($sql_cons,$sql_dato);
				            var_dump($resp);
				            if(empty($resp)){
				                $dr +=1;
				            }
				        }
				        }
				    }
				}
			}
		}
		
		
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