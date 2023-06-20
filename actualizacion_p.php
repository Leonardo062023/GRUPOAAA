<?php
#

require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
session_start();
$con 	= new ConexionPDO();
$id 	= $_GET['id'];
$anno 	= $_SESSION['anno'];
$panno  = $_SESSION['anno'];

$rowj = $con->Listar("SELECT a.id_uvms, f.id_unico , a.FechaPago, f.tercero , a.Suscriptor , a.id_unico 
    FROM facturaacueductorecaudo a 
    LEFT JOIN gp_factura f ON a.id_factura  = f.id_unico 
    LEFT JOIN gp_detalle_factura df on f.id_unico = df.factura 
    WHERE  a.NumeroFactura not in (68084,
68088,
68092,
68093,
68097,
68099,
68101,
68102,
68106,
68108,
68109,
68111,
68118,
68120,
68121,
68123,
68124,
68126,
68140,
68141,
68142,
68145,
68149,
68153,
68155,
68156,
68160,
68161,
68166,
68167,
68169,
68174,
68181,
68185,
68186,
68188,
68193,
68194,
68198,
68203,
68204,
68205,
68213,
68222,
68223,
68225,
68229,
68230,
68231,
68235,
68236,
68238,
68239,
68246,
68249,
68251,
68252,
68255,
68258,
68262,
68263,
68264,
68266,
68268,
68271,
68273,
68275,
68277,
68278,
68286,
68287,
68288,
68289,
68291,
68294,
68296,
68305,
68306,
68307,
68308,
68309,
68317,
68318,
68319,
68322,
68326,
68327,
68329,
68330,
68332,
68333,
68334,
68339,
68342,
68348,
68350,
68351,
68355,
68356,
68357,
68360,
68365,
68366,
68369,
68375,
68378,
68379,
68385,
68386,
68389,
68390,
68391,
68392,
68393,
68394,
68395,
68396,
68397,
68398,
68399,
68400,
68402,
68403,
68406,
68410,
68412,
68414,
68416,
68417,
68424,
68426,
68427,
68428,
68430,
68432,
68434,
68437,
68438,
68439,
68440,
68441,
68446,
68447,
68450,
68452,
68456,
68457,
68464,
68465,
68467,
68468,
68472,
68474,
68476,
68479,
68482,
68483,
68484,
68487,
68491,
68493,
68494,
68496,
68499,
68500,
68505,
68506,
68508,
68509,
68519,
68520,
68521,
68524,
68525,
68526,
68528,
68530,
68531,
68533,
68534,
68537,
68538,
68541,
68543,
68544,
68545,
68548,
68549,
68551,
68552,
68558,
68559,
68566,
68569,
68572,
68573,
68574,
68575,
68576,
68577,
68580,
68584,
68585,
68586,
68587,
68589,
68590,
68592,
68595,
68598,
68599,
68600,
68604,
68605,
68606,
68607,
68610,
68611,
68614,
68620,
68621,
68622,
68623,
68624,
68627,
68629,
68632,
68633,
68635,
68636,
68637,
68638,
68640,
68641,
68642,
68643,
68644) ");
for ($ij=0; $ij < count($rowj) ; $ij++) { 
	$id_unidad_viviendas  =$rowj[$ij][0];
    #* GUARDAR PAGO 
	$fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = 2 AND parametrizacionanno = 6");
    if(count($fac)>0){
        $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = 2 AND parametrizacionanno = 6");
        $numeroPago = $sql[0][0] + 1;
    } else {
        $numeroPago = $anno. '000001';
    }

    $id_tercero_uv=$rowj[$ij][3];
    $fechaPago = $rowj[$ij][2];
    $estado = 1;
    $sql = "INSERT INTO gp_pago
            (numero_pago,
            tipo_pago,
            responsable,
            fecha_pago,
            banco,
            estado, parametrizacionanno, usuario)
            VALUES('$numeroPago',
            2,$id_tercero_uv,
            '$fechaPago',NULL,$estado,6, 1)";
    //echo  $sql;
    $resultadoP = $mysqli->query($sql);
    if($resultadoP==true) {
        #********* Buscar el Registro Pago Realizado **************#
        $idPago 	= $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=2");
        $pago 		= $idPago[0][0];

		$factura = $rowj[$ij][1];
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
	        if($saldo_final!=0){
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

	echo 'UV ,'.$rowj[$ij][4].'id: '.$rowj[$ij][5].',<br/>';
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