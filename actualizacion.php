<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];


/*
$row = $con->Listar("SELECT a.id_uv, a.id_uvms, a.codigo_interno, a.total_deuda, a.deuda_actual, a.total_deuda- a.deuda_actual,
    SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico) , GROUP_CONCAT(f.id_unico) 
    FROM a_v a 
    LEFT JOIN gp_factura f ON a.id_uvms = f.unidad_vivienda_servicio
    LEFT JOIN gp_detalle_factura df on f.id_unico = df.factura 
    WHERE  
     (f.periodo != 14 or f.periodo is null)
    AND f.fecha_factura <'2021-02-21' 
    AND  a.total_deuda- a.deuda_actual >0 
    and a.refacturado is null and a.deuda_actual =0 
    GROUP BY  f.unidad_vivienda_servicio order by a.id_uv " );

for ($i=0; $i < count($row) ; $i++) { 
	$valor = $row[$i][5];
	$vr = $valor;

	while ($vr > 0) {
		$vrl = $con->Listar("SELECT id_unico, valor FROM gp_detalle_pago WHERE  detalle_factura In (".$row[$i][7].") and valor >0 order by id_unico desc");
		if(empty($vrl[0][1])){
			$vr = 0;
		}  else { 
			ECHO 'Valor Detalle:'.$vrl[0][1].'<br/>';
			if($vrl[0][1]> $vr){
				 $vrestar =  $vrl[0][1]-$vr ;
				echo $sql = "UPDATE gp_detalle_pago SET valor = $vrestar WHERE id_unico = ".$vrl[0][0];
	    		$resultadoP = $mysqli->query($sql);
	    		$vr =  0;
			} else {
				echo $sql = "DELETE FROM  gp_detalle_pago WHERE id_unico = ".$vrl[0][0];
	    		$resultadoP = $mysqli->query($sql);
	    		$vr -=  $vrl[0][1];
			}
			
			ECHO ' * '.$vrestar;
			ECHO ' * '.$vr;
		}
	}

	//$vr = $con->Listar("SELECT * FROM gp_detalle_pago WHERE detalle_factura In (".$row[$i][7].")");
	
}
*/

/*
#********* Buscar el Registro Pago Realizado **************#
$rowj = $con->Listar("SELECT a.id_pago,  a.deuda_actual, a.id_uvms, f.tercero ,a.deuda_actual- a.total_deuda, a.id_uv 
    FROM a_v a 
    LEFT JOIN gp_factura f ON a.id_uvms = f.unidad_vivienda_servicio
    LEFT JOIN gp_detalle_factura df on f.id_unico = df.factura 
    WHERE  (f.periodo != 14 )
    and a.refacturado is null 
    and a.id_uv not in (1883,2615,2733,2960,2999,3478)
    GROUP BY  f.unidad_vivienda_servicio
    ORDER BY a.id_uv ");
for ($ij=0; $ij < count($rowj) ; $ij++) { 
	$id_unidad_viviendas  = $rowj[$ij][2];
    #* GUARDAR PAGO 
	$fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = 3 AND parametrizacionanno = 5");
    if(count($fac)>0){
        $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = 3 AND parametrizacionanno = 5");
        $numeroPago = $sql[0][0] + 1;
    } else {
        $numeroPago = $anno. '000001';
    }

    $id_tercero_uv=$rowj[$ij][3];
    $estado = 1;
    $sql = "INSERT INTO gp_pago
            (numero_pago,
            tipo_pago,
            responsable,
            fecha_pago,
            banco,
            estado, parametrizacionanno, usuario)
            VALUES('$numeroPago',
            3,$id_tercero_uv,
            '2020-12-31',NULL,$estado,5, 1)";
    //echo  $sql;
    $resultadoP = $mysqli->query($sql);
    if($resultadoP==true) {
        #********* Buscar el Registro Pago Realizado **************#
        $idPago 	= $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=3");
        $pago 		= $idPago[0][0];

		$valor 		= $rowj[$ij][4];
		$id_unidad_viviendas =  $rowj[$ij][2];
		$rowfr = $con->Listar("SELECT f.id_unico, f.id_unico  
	    FROM  gp_factura f 
	    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
	    WHERE  uvms.id_unico = $id_unidad_viviendas 
	    and f.periodo != 14  and f.fecha_factura <='2021-12-31'
	    GROUP BY f.id_unico ORDER BY  f.fecha_factura");
	    //echo $valor.'<br/>';
		for ($fr=0; $fr <count($rowfr) ; $fr++) { 
			if($valor != 0){
				$factura = $rowfr[$fr][1];
				$saldo_factura = saldoFactura($factura);		    
				//echo 'SF'.$saldo_factura;
		        $row = $con->Listar("SELECT id_unico, 0, 0,
		            0, valor_total_ajustado, cantidad , id_unico
		            FROM gp_detalle_factura
		            WHERE factura = $factura AND valor_total_ajustado !=0 
		            ORDER BY valor_total_ajustado ASC");
			    $saldo_final = $saldo_factura;
			    $dr     = 0;    
			    for ($i = 0; $i < count($row); $i++) {
			        $total_recaudo =0;    
			        if($valor!=0){
			            $reg            = 0;
			            $id_detalle     = $row[$i][6];
			            $valor_ajustad1 = $row[$i][4];
			            $cantidad       = $row[$i][5];
			            $valor_ajustado = $row[$i][4];
			            $iva            = 0;
			            $impoconsumo    = 0;
			            $ajuste         = 0;
			            $valor_recaud   = $valor_ajustado - ($iva + $impoconsumo + $ajuste);
			            $saldo_credito  = 0;
			            #Buscar Afectaciones
			            $dtp = afectadoDetalleF($id_detalle);
			            $valor_rc =0;
			            
			            if(!empty($dtp)|| $dtp!=NULL){
			                #Buscar Valores Recaudo
			                $vlr = $con->Listar("SELECT SUM(valor), 0, 0, 0
			                    FROM gp_detalle_pago WHERE id_unico IN ($dtp)");
			                $valor_r = $vlr[0][0];
			                $iva_r   = 0;
			                $impo_r  = 0;
			                $ajuste_r= 0;
			                # *** Valor Recaudado ***#
			                $valor_rc = $valor_r + $iva_r + $impo_r + $ajuste_r;

			                $diferencia = $valor_ajustad1 - $valor_rc;
			                if($diferencia != 0){
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
			            //echo ' Valor:_ '.$valor.' TR- '.$total_recaudo;
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
			            if(empty($resp)){
			                $dr +=1;
			            }
			        }
			        }
			    }
			}
		}
	}

	echo 'UV ,'.$rowj[$ij][5].',<br/>';
}

#*/
/*

#*****************************************************************************************************
$rowj = $con->Listar("SELECT id_uvms FROM `a_v` where  refacturado is null and falta_recaudo is null and total_deuda <deuda_actual and total_deuda = 0  ORDER BY `id_uvms`  ASC ");
for ($ij=0; $ij < count($rowj) ; $ij++) {
	$id_uvms = $rowj[$ij][0];
	#* BUSCAR PAGOS 
	$rowp = $con->Listar("SELECT DISTINCT p.id_unico, p.numero_pago, p.fecha_pago,p.tipo_pago , SUM(dp.valor) 
		FROM gp_factura f 
		LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
		LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura 
		LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
		where f.unidad_vivienda_servicio IN ($id_uvms) and f.periodo != 14 
		AND p.id_unico is not null 
		GROUP by dp.pago
		ORDER BY p.fecha_pago DESC");
	for ($p=0; $p < count($rowp); $p++) { 
		$id_p 	= $rowp[$p][0];
		if($rowp[$p][3]==3){
			$valor = 10000000000;
			$fecha = '2020-12-31';
		} else {
			$valor 	= $rowp[$p][4];
			$fecha  = $rowp[$p][2];
		}
		echo 'VALOR:'.$valor.'<br/>';
		#* ELIMINAR DATOS DEL PAGO 
		$sql = "DELETE FROM  gp_detalle_pago WHERE pago = ".$id_p;
	    $resultadoP = $mysqli->query($sql);

	    #idpago
	    $pago 		= $id_p;
		$id_unidad_viviendas =  $id_uvms;
		$rowfr = $con->Listar("SELECT f.id_unico 
	    FROM  gp_factura f 
	    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
	    WHERE  uvms.id_unico = $id_unidad_viviendas 
	    and f.periodo != 14  AND f.fecha_factura <='".$fecha."'
	    GROUP BY f.id_unico ORDER BY  f.fecha_factura DESC ");
	    //echo $valor.'<br/>';
		for ($fr=0; $fr <count($rowfr) ; $fr++) { 
			if($valor != 0){
				$factura = $rowfr[$fr][0];
				$saldo_factura = saldoFactura($factura);		    
				//echo 'Saldo Factura'.$saldo_factura.'<br/>';
		        $row = $con->Listar("SELECT id_unico, 0, 0,
		            0, valor_total_ajustado, cantidad, id_unico 
		            FROM gp_detalle_factura
		            WHERE factura = $factura AND valor_total_ajustado !=0 
		            ORDER BY valor_total_ajustado ASC");
			    $saldo_final = $saldo_factura;
			    $dr     = 0;    
			    for ($i = 0; $i < count($row); $i++) {
			        $total_recaudo =0;    
			        if($valor!=0){
			            $reg            = 0;
			            $id_detalle     = $row[$i][6];
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

			                $diferencia = $valor_ajustad1 - $valor_rc.'<br/>';
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
			            //var_dump($resp);
			            if(empty($resp)){
			                $dr +=1;
			            }
			        }
			        }
			    }
			}
		}
	}
	echo $id_uvms.'<br/>';
}
echo 'ok';

*/

/*
#******************************************************************************************************
#ACTUALIZAR PAGOS CARGADOS DECIMALES

$row = $con->Listar("SELECT DISTINCT r.id_unico,r.id_uvms, r.id_pago, r.valor, r.valor_p, r.valor - r.valor_p, 
	(SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 )
FROM dbo_recaudos_completa r 
LEFT JOIN gp_pago p ON r.id_pago = p.id_unico 
WHERE id_pago is not null and r.valor - r.valor_p<>0 
HAVING (r.valor-r.valor_p)>-100 and (r.valor-r.valor_p)<100

order by id_unico "); 

for ($i=0; $i <count($row) ; $i++) { 
	$id_pago = $row[$i][2];
	$valor_i = $row[$i][5];
	$id_fact = $row[$i][6];
	$rowa = $con->Listar("SELECT id_unico, valor FROM gp_detalle_factura where factura = $id_fact and concepto_tarifa = 93");
	//echo "SELECT * FROM gp_detalle_factura where factura = $id_fact and concepto_tarifa = 93";
	//var_dump(empty($rowa[0][0]));
	if(empty($rowa[0][0])){
		#inset 
		$sql = "INSERT INTO `gp_detalle_factura`( `factura`, `concepto_tarifa`, `valor`, `cantidad`, `iva`, `impoconsumo`, `ajuste_peso`, `valor_total_ajustado`) 
		VALUES ($id_fact, 93, $valor_i, 1,0,0,0,$valor_i)";
    	$resultadoP = $mysqli->query($sql);

    	$idf = $con->Listar("SELECT id_unico from gp_detalle_factura df WHERE factura = $id_fact and concepto_tarifa = 93");
    	$idf = $idf[0][0];
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
            array(":detalle_factura",$idf),
            array(":valor",$valor_i),
            array(":iva",0),
            array(":impoconsumo",0),
            array(":ajuste_peso",0),
            array(":saldo_credito",0),
            array(":pago",$id_pago),

        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        var_dump($resp);

	} else {
		$vd = $rowa[0][1]+$valor_i;
		echo $sql = "UPDATE gp_detalle_factura SET valor = $vd, valor_total_ajustado = $vd where id_unico = ".$rowa[0][0];
    	$resultadoP = $mysqli->query($sql);

    	$dr = $con->Listar("SELECT * FROM gp_detalle_pago where pago = $id_pago and detalle_factura =".$rowa[0][0]);
    	if(empty($dr[0][0])){
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
		            array(":detalle_factura",$rowa[0][0]),
		            array(":valor",$valor_i),
		            array(":iva",0),
		            array(":impoconsumo",0),
		            array(":ajuste_peso",0),
		            array(":saldo_credito",0),
		            array(":pago",$id_pago),

		        );
		        $resp = $con->InAcEl($sql_cons,$sql_dato);
		        var_dump($resp);
		} else {
	    	echo $sql = "UPDATE gp_detalle_pago SET valor = $vd  where id_unico = ".$dr[0][0];
	    	$resultadoP = $mysqli->query($sql);
	    }

	}
	echo 'ID: '.$row[$i][0].'<br/>'; 
}
¨*/

#*****************************************************************************************************
#ACTUALIZAR DEUDA 

$row = $con->Listar("SELECT uv.id_unico, uvms.id_unico, uv.tercero, uv.codigo_ruta, uv.codigo_interno, 0, uv.id_unico 
FROM gp_unidad_vivienda uv 
LEFT JOIN gp_unidad_vivienda_servicio uvs ON uv.id_unico = uvs.unidad_vivienda 
LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvs.id_unico = uvms.unidad_vivienda_servicio 
Where  uvms.id_unico IS NOT NULL
 and uv.deuda_c is null 
ORDER BY uv.id_unico");
for ($i=0; $i < count($row) ; $i++) { 
	
	$id_unidad_viviendas  = $row[$i][1];
	$id_tercero_uv  	  = $row[$i][2];
	$id_uv 				  = $row[$i][6];	
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
    AND f.periodo is not null 
    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
	$dm = 0;
	$saldoT = 0;
	for ($f = 0; $f < count($rowdf); $f++) {
	    $vr =0;
	    if(!empty($rowdf[$f][4])){
	        $valor = $rowdf[$f][3];
	        $ids   = $rowdf[$f][4];
	        $rowr  = $con->Listar("SELECT 
	            IF(SUM(dp.valor) is null, 0, SUM(dp.valor)) 
                +IF(SUM(dp.iva) is null, 0, SUM(dp.iva)) 
                +IF(SUM(dp.impoconsumo) is null, 0, SUM(dp.impoconsumo)) 
                +IF(SUM(dp.ajuste_peso) is null, 0, SUM(dp.ajuste_peso)) 
	            FROM gp_detalle_pago dp 
	            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
	            WHERE dp.detalle_factura In ($ids) ");
	        if(empty($rowr[0][0])){
	        	$vr     = 0;
	        } else { 
	        	$vr     = $rowr[0][0];
	    	}
	        $saldof = $valor -$vr;
	    } else {
	        $saldof = 0;
	    }
	    $saldoT +=  $saldof;
	}	

	$total_r 			  = $saldoT;
	$sql = "UPDATE gp_unidad_vivienda SET deuda_c = $total_r where id_unico = $id_uv";
    $resultadoP = $mysqli->query($sql);

    $sql = "INSERT INTO `act_d`(id, digito, deuda) 
		VALUES ($id_unidad_viviendas, ".$row[$i][4].", $total_r)";
    $resultadoP = $mysqli->query($sql);

    if($resultadoP==true){
		echo 'IDC: '.$id_uv.'Deuda: '.$total_r.'<br/>';
	} else {
		echo 'IDNC: '.$id_uv.'<br/>';
	}
}

echo 'OK2';



/*
#ACTUALIZAR AJUSTE AL PESO RECAUDOS CARGADOS
$row = $con->Listar("SELECT DISTINCT r.id_unico,r.id_uvms, r.id_pago as pago, r.valor, r.valor_p, r.valor - r.valor_p, 
  (SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 ) as factura, 
  (select sum(df.valor_total_ajustado) from gp_detalle_factura df where df.concepto_tarifa != 93 and  df.factura =(SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 )) as vd, 
  r.valor - (select sum(df.valor_total_ajustado) from gp_detalle_factura df where df.concepto_tarifa != 93 and  df.factura =(SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 )) as ajuste 
FROM dbo_recaudos_completa r 
LEFT JOIN gp_pago p ON r.id_pago = p.id_unico 
WHERE 
id_pago is not null and r.valor - r.valor_p<>0 
and  (r.valor-r.valor_p)<100 and (r.valor-r.valor_p)>0
HAVING r.valor - (select sum(df.valor_total_ajustado) from gp_detalle_factura df where df.concepto_tarifa != 93 and  df.factura =(SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 )) <100 and 
r.valor - (select sum(df.valor_total_ajustado) from gp_detalle_factura df where df.concepto_tarifa != 93 and  df.factura =(SELECT df.factura FROM gp_detalle_pago dp LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico WHERE dp.pago = r.id_pago ORDER BY dp.id_unico LIMIT 1 )) >-100
order by id_unico");

for ($i=0; $i <count($row) ; $i++) { 
	$valor_a = $row[$i]['ajuste'];
	$id_fact = $row[$i]['factura'];
	$id_pago = $row[$i]['pago'];
	

	$sql = "UPDATE gp_detalle_factura df left join gp_detalle_pago dp ON dp.detalle_factura = df.id_unico set df.valor = $valor_a, df.valor_total_ajustado = $valor_a, dp.valor = $valor_a where df.concepto_tarifa = 93 and df.factura = $id_fact";
    $resultadoP = $mysqli->query($sql);


    
    echo $sql.'<br/>';
    #/*
        echo $sql = "INSERT INTO `gp_detalle_factura`( `factura`, `concepto_tarifa`, `valor`, `cantidad`, `iva`, `impoconsumo`, `ajuste_peso`, `valor_total_ajustado`) 
		VALUES ($id_fact, 93, $valor_a, 1,0,0,0,$valor_a)";
    	$resultadoP = $mysqli->query($sql);

    	$idf = $con->Listar("SELECT id_unico from gp_detalle_factura df WHERE factura = $id_fact and concepto_tarifa = 93");
    	$idf = $idf[0][0];
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
            array(":detalle_factura",$idf),
            array(":valor",$valor_a),
            array(":iva",0),
            array(":impoconsumo",0),
            array(":ajuste_peso",0),
            array(":saldo_credito",0),
            array(":pago",$id_pago),

        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        var_dump($resp);
	
        
}
$sql = "UPDATE dbo_recaudos_completa r set r.valor_p = (SELECT SUM(dp.valor) from gp_detalle_pago dp where dp.pago = r.id_pago)";
    $resultadoP = $mysqli->query($sql);
    echo 'FIN';

*/
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