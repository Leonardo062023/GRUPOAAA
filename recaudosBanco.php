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

#/*# RECAUDOS BANCO 
	
	$rowj = $con->Listar("SELECT fecha, banco, codigo, valor, id_unico, year(fecha) , fecha
		FROM dbo_recaudos_febrero   
		where  id_unico >= 107100 and id_pago is null 
		ORDER BY id_unico asc  "); 
	$item = 0;
	$codigos_no_encontrados = '';	 
	$item = 0;
	for ($ij=0; $ij < count($rowj) ; $ij++) { 
		$codigo_v = $rowj[$ij][2];
		$fecha_p  = $rowj[$ij][6];
		$banco_r  = $rowj[$ij][1];
		$anno	  = $rowj[$ij][5];
	    IF($anno =='2020'){
	    	$panno = 5;
	    } else {
	    	$panno = 6;
	    }

		$buv = $con->Listar("SELECT uv.id_unico, uvms.id_unico, uv.tercero, uv.codigo_ruta, uv.codigo_interno, 0, uv.id_unico 
		FROM gp_unidad_vivienda uv 
		LEFT JOIN gp_unidad_vivienda_servicio uvs ON uv.id_unico = uvs.unidad_vivienda 
		LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvs.id_unico = uvms.unidad_vivienda_servicio 
		Where uv.codigo_interno =".$codigo_v." AND uvms.id_unico IS NOT NULL");

		
		if(count($buv)>0){				
				 $id_unidad_viviendas  = $buv[0][1];
				 $id_tercero_uv  	  = $buv[0][2];
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
			    and f.periodo != 15 
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
				echo 'Saldo'.$total_r ;
				var_dump($total_r > 0);
				if($total_r > 0 ){
				
					#* GUARDAR PAGO 
					echo 1;
					echo "SELECT * FROM gp_pago WHERE tipo_pago = 2 AND parametrizacionanno = $panno";
					/*$fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = 2 AND parametrizacionanno = $panno");

					echo 2;
			        if(count($fac)>0){*/
			            $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = 2 AND parametrizacionanno = $panno");
			            $numeroPago = $sql[0][0] + 1;
			        /*} else {
			            $numeroPago = $anno. '000001';
			        }*/

			        
			        $estado = 1;
			        echo $sql = "INSERT INTO gp_pago
			                (numero_pago,
			                tipo_pago,
			                responsable,
			                fecha_pago,
			                banco,
			                estado, parametrizacionanno, usuario)
			                VALUES('$numeroPago',
			                2,$id_tercero_uv,
			                '$fecha_p',$banco_r,$estado, $panno, 1)";

			        $resultadoP = $mysqli->query($sql);
			        if($resultadoP==true){
			            #********* Buscar el Registro Pago Realizado **************#
			            $idPago 	= $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=2");
			            $pago 		= $idPago[0][0];
						$valor 		= $rowj[$ij][3];

						$rowfr = $con->Listar("SELECT f.id_unico 
					    FROM  gp_factura f 
					    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
					    WHERE  uvms.id_unico = $id_unidad_viviendas 
					     and  f.periodo != 15 
					    GROUP BY f.id_unico ORDER BY  f.fecha_factura DESC ");
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
				
				
				echo 'item = '.$rowj[$ij][4].' IDVMS: '.$id_unidad_viviendas.' UV:'.$codigo_v.' Saldo factura='.$saldoT.'<br/>';	
			   
		}else {
			$codigos_no_encontrados .= $codigo_v.',';	
		}
		echo 'I'.$ij.'<br/>';
	}
	

	echo 'Códigos no encontrados: '.$codigos_no_encontrados;
#*/
/*


		$rowj = $con->Listar("SELECT fecha, banco, codigo, valor, id_unico, year(fecha) , fecha, id_factura 
		FROM dbo_recaudos_febrero   
		where  id_uvms is null and id_factura is not null  
		 and id_pago is null                                    
		ORDER BY id_unico asc ");  
	$item = 0;
	$codigos_no_encontrados = '';	 
	$item = 0;
	for ($ij=0; $ij < count($rowj) ; $ij++) { 
		$codigo_v = $rowj[$ij][2];
		$fecha_p  = $rowj[$ij][6];
		$banco_r  = $rowj[$ij][1];
		$anno	  = $rowj[$ij][5];
	    IF($anno =='2020'){
	    	$panno = 5;
	    } else {
	    	$panno = 6;
	    }
	    $id_factura = $rowj[$ij][7];
		#Deuda Factura Actual
		$rowdf = $con->Listar("SELECT f.id_unico, 
	    DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
	    SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico),  f.tercero , tf.tipo_recaudo 
	    FROM gp_detalle_factura df 
	    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
	    LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
	    WHERE  f.id_unico = $id_factura
	    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
		$dm = 0;
		$saldoT = 0;
		$id_tercero_uv  = $rowdf[0][5];
		$tr 			= $rowdf[0][6];
		for ($f = 0; $f < count($rowdf); $f++) {
		    $vr =0;
		    if(!empty($rowdf[$f][4])){
		        $valor = $rowdf[$f][3];
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
		        $saldof = $valor -$vr;
		    } else {
		        $saldof = 0;
		    }
		    $saldoT +=  $saldof;
		}	



		$total_r 			  = $saldoT;
		echo 'TOTAL'.$total_r.'<br/>';
		if($total_r > 0 ){
		
			#* GUARDAR PAGO 
			$fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tr AND parametrizacionanno = $panno");
	        if(count($fac)>0){
	            $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tr AND parametrizacionanno = $panno");
	            $numeroPago = $sql[0][0] + 1;
	        } else {
	            $numeroPago = $anno. '000001';
	        }

	        
	        $estado = 1;
	         $sql = "INSERT INTO gp_pago
	                (numero_pago,
	                tipo_pago,
	                responsable,
	                fecha_pago,
	                banco,
	                estado, parametrizacionanno, usuario)
	                VALUES('$numeroPago',
	                $tr,$id_tercero_uv,
	                '$fecha_p',$banco_r,$estado, $panno, 1)";

	        $resultadoP = $mysqli->query($sql);
	        if($resultadoP==true){
	            #********* Buscar el Registro Pago Realizado **************#
	            $idPago 	= $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tr");
	            $pago 		= $idPago[0][0];
				$valor 		= $rowj[$ij][3];

				$rowfr = $con->Listar("SELECT f.id_unico 
			    FROM  gp_factura f 
			    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
			    WHERE   f.id_unico = $id_factura 
			    GROUP BY f.id_unico ORDER BY  f.fecha_factura DESC ");
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
		
		
		echo 'item = '.$rowj[$ij][4].' IDVMS: '.$id_unidad_viviendas.' UV:'.$codigo_v.' Saldo factura='.$saldoT.'<br/>';	

		echo 'I';
	}
	

	echo 'Códigos no encontrados: '.$codigos_no_encontrados;
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