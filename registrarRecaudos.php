<?php
require 'Conexion/ConexionPDO.php';                                                     
require 'Conexion/conexion.php';                                                     
require './jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
#Registrar Pago
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$pg =0;

$facturas = $con->Listar("SELECT id_unico FROM gp_factura ORDER BY numero_factura ASC ");
for ($i = 0; $i < count($facturas); $i++) { 
        $factura = $facturas[$i][0];
        $tipoPago = 1;
        $banco   = 105;
        $rta =0;
        
        #Buscar Datos Factura
        $df = $con->Listar("SELECT f.id_unico, 
                        f.numero_factura, tp.nombre, 
                        f.tercero, f.descripcion, f.fecha_factura, f.centrocosto  
                    FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura 
                    WHERE f.id_unico = $factura");
        $fecha      = $df[0][5];
        $responsable= $df[0][3];
        $centrocosto      = $df[0][6];
        #Descripción del comprobante
        $descripcion= '"Comprobante de recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
        #Calcular Numero Pago
        $fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
        if(count($fac)>0){
            $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
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
                estado, parametrizacionanno) 
                VALUES('$numeroPago',
                $tipoPago,$responsable,
                '$fecha',$banco,$estado, $panno)";
        $resultadoP = $mysqli->query($sql);
        
        if($resultadoP==true){
            #********* Buscar el Registro Pago Realizado **************#
            $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago");
            $pago = $idPago[0][0];
            #************ Registrar Comprobante CNT***************#
            $tipoComprobanteCnt=$con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
            if(!empty($tipoComprobanteCnt[0][0])){
                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                $tipocnt =$tipoComprobanteCnt[0][0];
                $numeroCnt=$con->Listar("select max(numero) from gf_comprobante_cnt "
                        . "where tipocomprobante=$tipocnt AND parametrizacionanno = $panno ");
                if(!empty($numeroCnt[0][0])){
                    $numeroC=$numeroCnt[0][0]+1;
                }else{
                    $numeroC=$anno.'00001';
                }
                
                #Insertamos el comprobante
                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                        . "values('$numeroC','$fecha',$descripcion,$tipocnt,$panno,$responsable,'1',$compania)";
                $resultInsertC=$mysqli->query($sqlInsertC);
                #Consultamos el ultimo comprobante ingresado
                $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                $id_cnt = $idCnt[0][0];
                
                #*********** Comprobante Pptal ***********#
                #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
                $tipoComPtal=$con->Listar("select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipocnt");
                #Validamos que el tipo de comprobante no venga vacio
                if(!empty($tipoComPtal[0][0])){
                    $tipopptal = $tipoComPtal[0][0];
                    #Consultamos el ultmo número registrado de acuerdo al tipo de comprobante pptal
                    $numeroP=$con->Listar("select max(numero) from gf_comprobante_pptal where tipocomprobante=$tipopptal AND parametrizacionanno = $panno");
                    #Validamos si el valor consultado viene vacio que inicialize el conteo, de lo contrarop que sume uno al valor obtenido
                    if(!empty($numeroP[0][0])){
                        $numeroPp=$numeroP[0][0]+1;
                    }else{
                        $numeroPp=$anno.'00001';
                    }
                    #Insertamos los datos en comprobante pptal
                    $insertPptal="insert into "
                            . "gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                            . "values('$numeroPp','$fecha','$fecha',$descripcion,$panno,$tipopptal,$responsable,'1',$responsable)";
                    $resultInsertPptal=$mysqli->query($insertPptal);
                    #Consultamos el ultimo comprobante pptal insertado
                    $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                    $id_pptal = $idPPAL[0][0];
                }   
                #************ Registrar Comprobante Causación***************#
                $tipoComprobanteC=$con->Listar("select tipo_comp_hom from gf_tipo_comprobante where id_unico=".$tipoComprobanteCnt[0][0]);
                if(!empty($tipoComprobanteC[0][0])){
                    #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                    $tipocau =$tipoComprobanteC[0][0];
                    $numeroCa=$con->Listar("select max(numero) from gf_comprobante_cnt "
                            . "where tipocomprobante=$tipocau AND parametrizacionanno = $panno ");
                    if(!empty($numeroCa[0][0])){
                        $numeroCausacion=$numeroCa[0][0]+1;
                    }else{
                        $numeroCausacion=$anno.'00001';
                    }
                    #Descripción del comprobante
                    $descripcion= '"Comprobante de causación recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                    #Insertamos el comprobante
                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                            . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
                    $resultInsertC=$mysqli->query($sqlInsertC);
                    #Consultamos el ultimo comprobante ingresado
                    $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                    $id_causacion = $idCau[0][0];
                
                }
            } 
            #************* Registrar Detalles Pago *********************#
            $sqlValor = "SELECT     id_unico, 
                                    dtf.valor,
                                    dtf.iva,
                                    dtf.impoconsumo,
                                    dtf.ajuste_peso, 
                                    dtf.concepto_tarifa, 
                                    dtf.cantidad 
                        FROM        gp_detalle_factura dtf 
                        WHERE       dtf.factura = $factura ORDER BY id_unico ASC ";
            $resultValor = $mysqli->query($sqlValor); 
            
            $sqlBanco = $con->Listar("SELECT cb.cuenta, c.naturaleza 
                    FROM gf_cuenta_bancaria cb 
                    LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                    WHERE cb.id_unico = $banco");
            $cuentaB = $sqlBanco[0][0];
            $Ncuenta = $sqlBanco[0][1];
            $vpt = 0;
            while($rowValor = mysqli_fetch_row($resultValor)){
                $valor  = $rowValor[1] * $rowValor[6];
                $iva    = (double) $rowValor[2];
                $impo   = (double) $rowValor[3];
                $ajuste = (double) $rowValor[4];
                
                $sqlc=$con->Listar("SELECT 
                    cp.id_unico, 
                    c.id_unico , 
                    cr.id_unico,
                    rf.id_unico, 
                    crc.cuenta_debito, 
                    cd.naturaleza, 
                    crc.cuenta_credito, 
                    cc.naturaleza, 
                    crc.cuenta_iva, 
                    civ.naturaleza, 
                    crc.cuenta_impoconsumo, 
                    ci.naturaleza
                FROM gp_concepto cp 
                LEFT JOIN gf_concepto c ON cp.concepto_financiero = c.id_unico
                LEFT JOIN gf_concepto_rubro cr ON cr.concepto = c.id_unico 
                LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro 
                LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico 
                LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva 
                LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo 
                WHERE cp.id_unico =$rowValor[5]");
                if(count($sqlc)>0){
                    $conceptorubro  = $sqlc[0][2];
                    $rubrofuente    = $sqlc[0][3];
                    #********** Detalle Pptal*****************#
                    $insertP = "INSERT INTO gf_detalle_comprobante_pptal 
                            (valor, comprobantepptal, conceptorubro, 
                            tercero, proyecto, rubrofuente) 
                            VALUES(($valor+$ajuste), $id_pptal, $conceptorubro, 
                            $responsable, 2147483647, $rubrofuente)";
                    $resultP = $mysqli->query($insertP);
                    $id_dp = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal");
                    $id_dp = $id_dp[0][0];
                    ##********** Detalle Cnt*****************#
                    #cuenta credito 
                    $cc =$sqlc[0][6]; 
                    #cuenta debito 
                    $cd =$sqlc[0][4];
                    $naturalezad = $sqlc[0][5];
                    #Verificar Naturaleza
                    $naturalezac = $sqlc[0][7];
                    $vpt += $valor+$ajuste;
                    if($naturalezac==1){
                        $valorc = ($valor+$ajuste)*-1;
                        
                    } else {
                        $valorc = ($valor+$ajuste);
                    }
                    #Insertar Detalle Cnt
                    $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto, centrocosto, 
                            detallecomprobantepptal) 
                            VALUES('$fecha', $valorc, 
                            $id_cnt, $naturalezac, $cc,
                            $responsable,  2147483647, $centrocosto, $id_dp)";
                    $resultado = $mysqli->query($insertD);
                    
                    $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                    $id_dc = $id_dc[0][0];
                    
                    #Insertar Detalle Causacion 
                    ##Debito 
                    if($naturalezad==1){
                        $valord = ($valor+$ajuste);
                    } else {
                        $valord = ($valor+$ajuste)*-1;
                    }
                    if($cd == $cc){
                        
                    } else {
                    $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto, centrocosto, 
                            detalleafectado) 
                            VALUES('$fecha', $valord, 
                            $id_causacion, $naturalezad, $cd,
                            $responsable,  2147483647, $centrocosto, $id_dc)";
                    $resultado = $mysqli->query($insertD);
                    #** Credito 
                    
                    $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto, centrocosto, 
                            detalleafectado) 
                            VALUES('$fecha', $valorc, 
                            $id_causacion, $naturalezac, $cc,
                            $responsable,  2147483647, $centrocosto, $id_dc)";
                    $resultado = $mysqli->query($insertD);
                    }
                    
                    #********** Detalle Pago*****************#
                    $sql = "INSERT INTO gp_detalle_pago (detalle_factura, 
                    valor, iva, impoconsumo, ajuste_peso, pago, saldo_credito, detallecomprobante)  
                    VALUES ($rowValor[0], $valor, $iva, $impo, $ajuste, $pago, 0, $id_dc)";
                    $resultado = $mysqli->query($sql);
                    #Registrar Cuenta Iva
                    if($iva !="" || $iva !=0){
                        #Verificar Naturaleza
                        $civa           = $sqlc[0][8];
                        $naturalezaci   = $sqlc[0][9];
                        $vpt += $iva;
                        if($naturalezaci==1){
                            $valorci = $iva*-1;
                        } else {
                            $valorci = $iva;
                        }
                        $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto, centrocosto) 
                            VALUES('$fecha', $valorci, 
                            $id_cnt, $naturalezaci, $civa,
                            $responsable,  2147483647, $centrocosto)";
                        $resultado = $mysqli->query($insertD);
                        
                    }
                    #Registrar Cuenta Impoconsumo
                    if($impo !="" || $impo !=0){
                        #Verificar Naturaleza
                        $cimpo           = $sqlc[0][10];
                        $naturalezacim   = $sqlc[0][11];
                        $vpt += $impo;
                        if($naturalezacim==1){
                            $valorcim = $impo*-1;
                        } else {
                            $valorcim = $impo;
                        }
                        $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto, centrocosto) 
                            VALUES('$fecha', $valorcim, 
                            $id_cnt, $naturalezacim, $cimpo,
                            $responsable,  2147483647, $centrocosto)";
                        $resultado = $mysqli->query($insertD);
                    }
                    
                } else {
                    #********** Detalle Pago*****************#
                   $sql = "INSERT INTO gp_detalle_pago (detalle_factura, 
                    valor, iva, impoconsumo, ajuste_peso, pago)  
                    VALUES ($rowValor[0], $valor, $iva, $impo, $ajuste, $pago)";
                    $resultado = $mysqli->query($sql);
                }
                
                
            }
            #Registrar Cuenta de Banco 
            if($Ncuenta ==1){
                $vpt =$vpt;
            } else {
                $vpt = $vpt*-1;
            }
            $insertD = "INSERT INTO gf_detalle_comprobante 
                (fecha, valor, 
                comprobante, naturaleza, cuenta, 
                tercero, proyecto, centrocosto) 
                VALUES('$fecha', $vpt, 
                $id_cnt, $Ncuenta, $cuentaB,
                $responsable,  2147483647, $centrocosto)";
            $resultado = $mysqli->query($insertD);    
                
            } else {
                $rta =1;
            }
            $pg +=1;
}
echo $pg;