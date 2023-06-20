<?php
        require_once '../Conexion/conexion.php';
        require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
        setlocale(LC_ALL,"es_ES");
        date_default_timezone_set("America/Bogota");
        @session_start();
        @$tercero1 = $_GET['ids'];
        $calendario = CAL_GREGORIAN;
        $idAnno=$_SESSION['anno'];
        $annio1 = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE id_unico = $idAnno";
        $rsannio = $mysqli->query($annio1);
        $filaAnnio = mysqli_fetch_row($rsannio);
        $anno=$filaAnnio[1];
        @$mes = $_GET['sltmesi'];
        if ($mes==2) {
           $mes='02';
        }else{
          $mes=$mes;
        }
        if ($mes==3) {
           $mes='03';
        }else{
          $mes=$mes;
        }
        if ($mes==4) {
           $mes='04';
        }else{
          $mes=$mes;
        }
        if ($mes==5) {
           $mes='05';
        }else{
          $mes=$mes;
        }
        $sqlMes="SELECT id_unico FROM gf_mes
        WHERE numero=$mes
        AND parametrizacionanno=$idAnno";
        $resultMes = $mysqli->query($sqlMes);
        $rowMes = $resultMes->fetch_assoc();
        $idMes = $rowMes['id_unico'];
        $diaF = cal_days_in_month($calendario, $mes , $anno); 
        $fechaInicial= "'$anno-$mes-01'";
        $fechaFinal= "'$anno-$mes-$diaF'";
        $fechaInicial1= "$anno-$mes-01";
        $fechaFinal1= "$anno-$mes-$diaF";
        $paramA     = $_SESSION['anno'];
        $compania   = $_SESSION['compania'];
        $usuarioEnv = $_SESSION['usuario'];
        $fechaActual=date('Y-m-d h:i:s');
        
        //token
        /* $sqlTkn = "SELECT token_fe FROM `gf_tercero` where id_unico='" . $compania . "'";
        $resultTkn = $mysqli->query($sqlTkn);
        $rowTkn = $resultTkn->fetch_assoc();
        $token = $rowTkn["token_fe"];
        if ($token == null) {
            Token($compania, $mysqli);
        } */

        //Parametros Consecutivo Y prefijo
        //echo   $empleado;
        $parm = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Prefijo Nomina Electronica'";
            $parm = $mysqli->query($parm);
            $parm = $parm->fetch_assoc();
            $prefijoPrm = $parm['valor'];
            
        $parmC = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Consecutivo Nomina Electronica'";
            $parmC = $mysqli->query($parmC);
            $parmC = $parmC->fetch_assoc();
            $consecutivoPrm = $parmC['valor'];    
            
            $sqlEmpleadoC="SELECT id_unico FROM gf_tercero
            WHERE id_unico IN ($tercero1)";
            $empleadoC = $mysqli->query($sqlEmpleadoC);

while ($rowEmpleado=mysqli_fetch_row($empleadoC)) {

      $tercero=$rowEmpleado[0];

        $sqlConsecutivoF="SELECT prefijo,consecutivo
        FROM gn_nomina_electronica  
        WHERE tercero=$tercero
        AND mes=$idMes";

        $resultConsecutivoF=$mysqli->query($sqlConsecutivoF);   
        $rowConsecutivoF = $resultConsecutivoF->fetch_assoc();
        $prefijoFinal = $rowConsecutivoF['prefijo'];

        if ($prefijoFinal!=null) {
           
            $sqlAlert="SELECT prefijo,consecutivo,cune
           FROM gn_nomina_electronica  
           WHERE tercero=$tercero
           AND mes=$idMes";
            $resultAlert=$mysqli->query($sqlAlert);   
            $rowAlert = $resultAlert->fetch_assoc();
            $prefijoAlert = $rowAlert['prefijo'];
            $consecutivoAlert = $rowAlert['consecutivo'];
            $cuneAlert= $rowAlert['cune'];
               $empYa=true;
        }else{

            //Buscar Consecutivo
            $sqlConsecutivo="SELECT prefijo,consecutivo,tercero 
                            FROM gn_nomina_electronica  
                            WHERE id_unico = (SELECT MAX(id_unico) FROM gn_nomina_electronica)";
            $resultConsecutivo = $mysqli->query($sqlConsecutivo);
            $rowValidaCon=mysqli_fetch_row($resultConsecutivo);
            //Validar si existen registros
            if($rowValidaCon==false){
                $sqlInicio="INSERT INTO gn_nomina_electronica (prefijo,consecutivo,mes,anno,tercero)
                VALUES ('$prefijoPrm','$consecutivoPrm',$idMes,$idAnno,$tercero)";
                $resultInicio = $mysqli->query($sqlInicio);
                
            }else{
                $sqlConsecutivo="SELECT prefijo,consecutivo,tercero 
                FROM gn_nomina_electronica  
                WHERE id_unico = (SELECT MAX(id_unico) FROM gn_nomina_electronica)";
                $resultConsecutivo = $mysqli->query($sqlConsecutivo);
                $rowConse = $resultConsecutivo->fetch_assoc();
                $prefijoAnt=$rowConse['prefijo'];
                $consecutivoAnt=$rowConse['consecutivo'];
                $terceroCon=$rowConse['tercero'];
                $consecutivoAct=$consecutivoAnt+1;   
            
                $sqlinsert="INSERT INTO gn_nomina_electronica (prefijo,consecutivo,mes,anno,tercero)
                VALUES ('$prefijoAnt','$consecutivoAct',$idMes,$idAnno,$tercero)";
                $resultInsert = $mysqli->query($sqlinsert);  
        }


        
        //---Consulta para datos empleado----//
        $sqlEm=" SELECT e.id_unico,t.numeroidentificacion, t.nombreuno AS primernombre, t.nombredos AS otronombre, t.apellidouno AS  primerapellido,
        t.apellidodos AS segundoapellido,ti.codigo_fe as tipoidentificacion,e.tipo_riesgo AS riesgopension, e.salInt AS salariointegral,
        c.codigo_dian as ciudad,d.direccion,MAX(vr.fechaacto) as fechaingreso,(SELECT MAX(fechaacto) FROM gn_vinculacion_retiro where estado=2 AND empleado=e.id_unico LIMIT 1) AS fecharetiro,tc.equivalente_NE as tipocuenta, cb.numerocuenta, cat.salarioactual,te.equivalente_NE as tipotrabajador,te.equivalenteSubtipoTrabajador_NE as subtipotrabajador,tcn.equivalente_NE as tipocontrato,tpne.equivalente_NE as periodoNomina,mp.equivalente_NE as mediopago,t1.equivalente_NE as entidadbancaria
        FROM gn_novedad n
                LEFT JOIN gn_empleado e  ON n.empleado=e.id_unico
                LEFT JOIN gf_tercero t ON e.tercero=t.id_unico
                LEFT JOIN gn_periodo p ON n.periodo=p.id_unico
                LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion= ti.id_unico
                LEFT JOIN gf_ciudad c ON t.ciudadresidencia=c.id_unico
                LEFT JOIN gf_direccion d ON d.tercero=t.id_unico
                LEFT JOIN gn_vinculacion_retiro vr  ON vr.empleado=e.id_unico
                LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero=t.id_unico
                LEFT JOIN gf_cuenta_bancaria cb ON cb.id_unico=cbt.cuentabancaria
                LEFT JOIN gf_tipo_cuenta tc ON tc.id_unico = cb.tipocuenta
                LEFT JOIN gn_tercero_categoria tca   ON tca.empleado = e.id_unico
                LEFT JOIN gn_categoria cat          ON cat.id_unico =tca.categoria
                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                LEFT JOIN gn_tipo_contrato_nomina_E tcn ON tcn.id_unico=e.equivalente_NE
                LEFT JOIN gn_tipo_proceso_nomina tpn ON tpn.id_unico=p.tipoprocesonomina
                LEFT JOIN gn_tipo_periodo_nomina_MQ  tpne ON tpne.id_unico=tpn.tipo_periodo_nomina
                LEFT JOIN gn_medio_pago mp ON e.mediopago = mp.id_unico
                LEFT JOIN gf_tercero t1 ON t1.id_unico=cb.banco
                WHERE  p.tipoprocesonomina=1
                AND t.id_unico=$tercero
                AND vr.estado=1
                GROUP BY t.id_unico";   

        $empSql=$mysqli->query($sqlEm);
        $rowE=mysqli_fetch_row($empSql);
        //Consulta para Auxilio transporte//
        $sqlAuxT="SELECT SUM(n.valor) AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'auxilioTransporte'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
                        
        $auxT=$mysqli->query($sqlAuxT);
        $rowAT=mysqli_fetch_row($auxT);
        $auxTransporte1=$rowAT[0];
        $auxTransporte = (int)$auxTransporte1;

        if($auxTransporte==0){
            $auxTransporte=null;
        }else{
            $auxTransporte=$auxTransporte;
        }


        //Consulta para sueldo Trabajado//
        $sqlSuel="SELECT SUM(n.valor) AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre='sueldoTrabajado'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";

                        
        $suel=$mysqli->query($sqlSuel);
        $rowS=mysqli_fetch_row($suel);
        $sueldo1=$rowS[0];
        $sueldo = (int)$sueldo1;

        //Consulta para dias Trabajados//
        $sqlDias="SELECT SUM(n.valor) AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre='diasTrabajados'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";

                        
        $dias=$mysqli->query($sqlDias);
        $rowD=mysqli_fetch_row($dias);
        $diasTrabajados1=$rowD[0];
        $diasTrabajados = (int)$diasTrabajados1;


        #------------------------------------------------------------------------#
        //Consulta para horas extras diurnas ordinarias
        $sqlHEDO="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_EXTRA_DIURNA'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";
        $horasD=$mysqli->query($sqlHEDO);
        $valorDF=0;
        $horasExtrasDiurnasJson=[];
            while($rowHED=mysqli_fetch_row($horasD)){
                $tipoHED=$rowHED[0];
                $valorHED1=$rowHED[1];
                $horasExtrasDiurnas1=$rowHED[2];
                $valorHED = (int)$valorHED1;
                $horasExtrasDiurnas = (int)$horasExtrasDiurnas1;
                if($horasExtrasDiurnas!=0){
            
                    if($tipoHED!=null){
                
                        if($valorHED!=0){
                array_push($horasExtrasDiurnasJson,[
                            "tipo"       => $tipoHED,
                            "pago"       => $valorHED,
                            "cantidad"   => $horasExtrasDiurnas
                ]);
                    
                        }
                    }
                
                }else{ 
                    
            
                }
                $valorDF+=$valorHED;
            }




        #------------------------------------------------------------------------#
        //Consulta para horas extras nocturnas 
        $sqlHEN="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_EXTRA_NOCTURNA'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";
        $horasN=$mysqli->query($sqlHEN);

        $valorHENTo=0;
        $horasExtrasNocturnasJson=[];
            while($rowHEN=mysqli_fetch_row($horasN)){
                $tipoHEN=$rowHEN[0];
                $valorHEN1=$rowHEN[1];
                $valorHEN=(int)$valorHEN1;
                $horasExtrasNocturnas1=$rowHEN[2];
                $horasExtrasNocturnas=(int)$horasExtrasNocturnas1;
                if($horasExtrasNocturnas!=null){
            
                    if($tipoHEN!=null){
                
                        if($valorHEN!=null){
                array_push($horasExtrasNocturnasJson,[
                            "tipo"       => $tipoHEN,
                            "pago"       => $valorHEN,
                            "cantidad"   => $horasExtrasNocturnas
                ]); 
                        }
                    }
                
                }else{
                
                }
                $valorHENTo+=$valorHEN;
            }






        #------------------------------------------------------------------------#
        //Consulta para horas recargo nocturno
        $sqlHERN="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,n1.valor as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        LEFT JOIN gn_periodo p1 ON p1.id_unico=n1.periodo
                        WHERE c.equivalente_NE='H_RECARGO_NOCTURNA'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        AND p1.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p1.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p1.parametrizacionanno=$paramA";
        $horasR=$mysqli->query($sqlHERN);

        $valorHERTo=0;
        $horasExtrasReNocturnasJson=[];
        while($rowHER=mysqli_fetch_row($horasR)){
            $tipoHER=$rowHER[0];
            $valorHER1=$rowHER[1];
            $valorHER=(int)$valorHER1;
            $horasExtrasReNocturnas1=$rowHER[2];
            $horasExtrasReNocturnas=(int)$horasExtrasReNocturnas1;
            if($horasExtrasReNocturnas!=null){

                if($tipoHER!=null){
            
                    if($valorHER!=null){
                array_push($horasExtrasReNocturnasJson,[
                        "tipo"       => $tipoHER,
                        "pago"       => $valorHER,
                        "cantidad"   => $horasExtrasReNocturnas
                ]);
                
                    }
                }
            
            }else{
            }
            $valorHERTo+=$valorHER;
        }





        #------------------------------------------------------------------------#
        //Consulta para hora extra diurna dominical y festivos 
        $sqlHEDD="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_EXTRA_DIURNA_DOM_FEST'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";



        $horasDD=$mysqli->query($sqlHEDD);
        $valorHEDDFTo=0;
        $horasExtrasDiurnaDomFesJson=[];
        while($rowHEDD=mysqli_fetch_row($horasDD)){
            $tipoHEDDF=$rowHEDD[0];
            $valorHEDDF1=$rowHEDD[1];
            $valorHEDDF=(int)$valorHEDDF1;
            $horasExtrasDiurnaDomFes1=$rowHEDD[2];
            $horasExtrasDiurnaDomFes=(int)$horasExtrasDiurnaDomFes1;
            if($horasExtrasDiurnaDomFes!=null){

                if($tipoHEDDF!=null){
            
                    if($valorHEDDF!=null){
                        array_push($horasExtrasDiurnaDomFesJson, [
                            "tipo"       => $tipoHEDDF,
                            "pago"       => $valorHEDDF,
                            "cantidad"   => $horasExtrasDiurnaDomFes
                        ]);
                    }
                }
            
            }else{
            
            }
            $valorHEDDFTo+=$valorHEDDF;
        }



        #------------------------------------------------------------------------#
        //Consulta para Hora Recargo Diurno Dominical y Festivos    
        $sqlHERDF="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_REC_DIURNO_DOM_FEST'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";

        $horasERDF=$mysqli->query($sqlHERDF);
        $valorHEDDTo=0;
        $horasExtrasRecargoDiurnoDomFesJson=[];
        while ($rowERDF=mysqli_fetch_row($horasERDF)) {
            $tipoHEDD=$rowHEDD[0];
            $valorHEDD1=$rowHEDD[1];
            $valorHEDD=(int)$valorHEDD1;
            $horasExtrasRecargoDiurnoDomFes1=$rowERDF[2];
            $horasExtrasRecargoDiurnoDomFes=(int)$horasExtrasRecargoDiurnoDomFes1;
            if($horasExtrasRecargoDiurnoDomFes!=null){

                if($tipoHEDD!=null){
            
                    if($valorHEDD!=null){
                    array_push($horasExtrasRecargoDiurnoDomFesJson,[
                        "tipo"       => $tipoHEDD,
                        "pago"       => $valorHEDD,
                        "cantidad"   => $horasExtrasRecargoDiurnoDomFes
                    ]);
                    }
                }
            
            }else{
            
            }
            $valorHEDDTo+=$valorHEDD;
        }





        #------------------------------------------------------------------------#
        //Consulta para Hora Extra Nocturna Dominical y Festivos    
        $sqlHENDF="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_EXT_NOCT_DOM_FEST'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";
        $horasHENDF=$mysqli->query($sqlHENDF);

        $valorHENDFTo=0;
        $horasExtrasNocturnaDomFesJson=[];
        while ($rowHENDF=mysqli_fetch_row($horasHENDF)) {
            $tipoHENDF=$rowHENDF[0];
            $valorHENDF1=$rowHENDF[1];
            $valorHENDF=(int)$valorHENDF1;
            $horasExtrasNocturnaDomFes1=$rowHENDF[2];
             if ($horasExtrasNocturnaDomFes1=='0.50') {
               $horasExtrasNocturnaDomFes1='1';
           }else{
              $horasExtrasNocturnaDomFes1=$rowHENDF[2];
           }
            $horasExtrasNocturnaDomFes=(int)$horasExtrasNocturnaDomFes1;
            if($horasExtrasNocturnaDomFes!=null){

                if($tipoHENDF!=null){
            
                    if($valorHENDF!=null){
                array_push($horasExtrasNocturnaDomFesJson,[
                        "tipo"       => $tipoHENDF,
                        "pago"       => $valorHENDF,
                        "cantidad"   => $horasExtrasNocturnaDomFes
                    ]);
            
                    }
                }
            
            }else{
            
            }
            $valorHENDFTo+=$valorHENDF;
        }




        #------------------------------------------------------------------------#
        //Consulta para Hora Recargo Nocturno Dominical y Festivos
        $sqlHRNDF="SELECT c.equivalente_NE as tipo,SUM(n.valor) AS valor,SUM(n1.valor) as cantidad
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto= c1.id_unico
                        AND n1.fecha=n.fecha
                        WHERE c.equivalente_NE='H_REC_NOCT_DOM_FEST'
                        AND n.empleado=$rowE[0]
                        AND n1.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA
                        GROUP BY c.id_unico";
        $horasHRNDF=$mysqli->query($sqlHRNDF);
        $valorHRNDFTo=0;
        $horasExtrasRecargoNocturnoDomFesJson=[];
        while ($rowHRNDF=mysqli_fetch_row($horasHRNDF)) {
            $tipoHRNDF=$rowHRNDF[0];
            $valorHRNDF1=$rowHRNDF[1];
            $valorHRNDF=(int)$valorHRNDF1;
            $horasExtrasRecargoNocturnoDomFes1=$rowHRNDF[2];
            $horasExtrasRecargoNocturnoDomFes=(int)$horasExtrasRecargoNocturnoDomFes1;
            if($horasExtrasRecargoNocturnoDomFes!=null){
                if($tipoHRNDF!=null){
                    if($valorHRNDF!=null){
                    array_push($horasExtrasRecargoNocturnoDomFesJson,[
                        "tipo"       => $tipoHRNDF,
                        "pago"       => $valorHRNDF,
                        "cantidad"   => $horasExtrasRecargoNocturnoDomFes
                    ]);
                    }
                }
            
            }else{
            
            
            }
            $valorHRNDFTo+=$valorHRNDF;
        }



        //Consulta para vacaciones 
        $sqlVac="SELECT v.fechainiciodisfrute, v.fechafindisfrute, v.dias_hab,tn.equivalente_NE
                        FROM gn_vacaciones v
                        LEFT JOIN gn_periodo p ON p.id_unico=v.periodo
                        LEFT JOIN gn_novedad n ON n.id_unico=v.empleado
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=v.tiponovedad
                        WHERE tn.equivalente_NE='VAC' 
                        AND v.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
        $vacacionesD=$mysqli->query($sqlVac);
        $rowVac=mysqli_fetch_row($vacacionesD);
        $fechaInicio=$rowVac[0];
        $fechaFin=$rowVac[1];
        $dias1=$rowVac[2];
        $dias=(int)$dias1;
        $tipoVac=$rowVac[3];

        //valor vacaciones
        $sqlValVac="SELECT n.valor AS valor,c.equivalente_NE as tipo
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        WHERE c.equivalente_NE='VAC'
                        AND p.tipoprocesonomina=7
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
        $valorVac=$mysqli->query($sqlValVac);
        $rowValVac=mysqli_fetch_row($valorVac);
        $valorVacaciones1=$rowValVac[0];
        $valorVacaciones=(int)$valorVacaciones1;
        $vacacionesJson=[];

        if($fechaInicio!=null && $fechaFin!=null){
            if($dias!=null){
                if($tipoVac!=null){
                if($valorVacaciones!=null){
                    array_push($vacacionesJson,[
                        "tipo"           => $tipoVac,
                        "fechaInicio"    => $fechaInicio,
                        "fechaFin"       => $fechaFin,
                        "cantidadDias"   => $dias,
                        "pago"           => $valorVacaciones
                    ]);
                }  
                }
            }
        }else{

        }

        //Consulta para Prima
        $sqlPri="SELECT   SUM(n1.valor)  as dias, SUM(n.valor) AS valor        
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        LEFT JOIN gn_concepto c1 ON  c1.id_unico=c.conceptorel
                        LEFT JOIN gn_novedad n1 ON n1.concepto=c1.conceptorel
                        WHERE tn.nombre = 'pagoPrima'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
        $prima=$mysqli->query($sqlPri);
        $rowPrima=mysqli_fetch_row($prima);
        $diasPrima1=$rowPrima[0];
        $diasPrima=(int)$diasPrima1;
        $pagoPrima1=$rowPrima[1];
        $pagoPrima=(int)$pagoPrima1;
        $pagoNSPrima=0;
          
        if($pagoPrima!=null){
            
             $primasJson=[
                "cantidadDias"    => $diasPrima,
                "pago"            => $pagoPrima,
                "pagoNS"          => $pagoNSPrima
            ];
            
        }else{

        }
                
                



        //Consulta para Cesantias
        //Falta porcentaje de cesantias
        /* $sqlPorCes="SELECT n.valor AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'porcentajeCesantias'
                        AND n.empleado=$rowE[0]
                        AND p.parametrizacionanno=$paramA";
        $porcenC=$mysqli->query($sqlPorCes);
        $rowPorcenC=mysqli_fetch_row($porcenC);
        $porcentajeCesantias=$rowPorcenC[0]; 

        $sqlCesP="SELECT n.valor AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'pagoCesantias'
                        AND n.empleado=$rowE[0]
                        AND p.parametrizacionanno=$paramA";
        $pagoCesP=$mysqli->query($sqlCesP);
        $rowPagoCesP=mysqli_fetch_row($pagoCesP);
        $pagoCesantias=$rowPagoCesP[0];


        $sqlCesIn="SELECT n.valor AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'pagoInteresesCesantias'
                        AND n.empleado=$rowE[0]
                        AND p.parametrizacionanno=$paramA";
        $pagoCes=$mysqli->query($sqlCesIn);
        $rowPagoCes=mysqli_fetch_row($pagoCes);
        $pagoIntereses=$rowPagoCes[0];
        */

        //Consulta para Incapacidades 
        //Laboral
        $sqlIncaLa="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                        FROM gn_incapacidad i
                        LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                        WHERE tn.equivalente_NE='LABORAL'
                        AND i.empleado=$rowE[0]
                        AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";
        $incapacidadesL=$mysqli->query($sqlIncaLa);
        $rowIncapacidadesLaboral=mysqli_fetch_row($incapacidadesL);
        $valorIncaLaboralTo=0;
        $sqlValIncaLa="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='LABORAL'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";
            $valorIncaLa=$mysqli->query($sqlValIncaLa);
            $rowValIncaLa=mysqli_fetch_row($valorIncaLa);
            $valorIncaLaboral1=$rowValIncaLa[0];
            $valorIncaLaboral=(int)$valorIncaLaboral1;

        $incapacidadesLaboralJson=[];
        while ($rowIncapacidadesLaboral=mysqli_fetch_row($incapacidadesL)) {
            $tipoIncaLaboral=$rowIncapacidadesLaboral[3];
            $fechaInicialIncaLaboral=$rowIncapacidadesLaboral[0];
            $fechaFinIncaLaboral=$rowIncapacidadesLaboral[1];
            $diasIncaLaboral1=$rowIncapacidadesLaboral[2];
            $diasIncaLaboral=(int)$diasIncaLaboral1;
            if($valorIncaLaboral==null){
                $valorIncaLaboral=0;
            }else{
                $valorIncaLaboral=$valorIncaLaboral;
            }

            if($fechaInicialIncaLaboral!=null && $fechaFinIncaLaboral!=null){

                if($diasIncaLaboral!=null){
                    if($tipoIncaLaboral!=null){
                        array_push($incapacidadesLaboralJson,[
                            "tipo"           => $tipoIncaLaboral,
                            "fechaInicio"    => $fechaInicialIncaLaboral,
                            "fechaFin"       => $fechaFinIncaLaboral,
                            "cantidadDias"   => $diasIncaLaboral,
                            "pago"           => $valorIncaLaboral
                        ]);
                    }
                }
            }else{
            
            }
            $valorIncaLaboralTo+=$valorIncaLaboral;
        }



        //Profesional
        $sqlIncaPro="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                        FROM gn_incapacidad i
                        LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                        WHERE tn.equivalente_NE='PROFESIONAL' 
                        AND i.empleado=$rowE[0]
                        AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";
        $incapacidadesP=$mysqli->query($sqlIncaPro);
        $rowIncapacidadesProfesional=mysqli_fetch_row($incapacidadesP);
        $valorIncaProfesionalTo=0;
         $sqlValIncaPr="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='PROFESIONAL'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";
            $valorIncaPr=$mysqli->query($sqlValIncaPr);
            $rowValIncaPr=mysqli_fetch_row($valorIncaPr);
            $valorIncaProfesional1=$rowValIncaPr[0];
            $valorIncaProfesional=(int)$valorIncaProfesional1;

        $incapacidadesProfesionalJson=[];
        while ($rowIncapacidadesProfesional=mysqli_fetch_row($incapacidadesP)) {
            $tipoIncaProfesional=$rowIncapacidadesProfesional[3];
            $fechaInicialIncaProfesional=$rowIncapacidadesProfesional[0];
            $fechaFinIncaProfesional=$rowIncapacidadesProfesional[1];
            $diasIncaProfesional1=$rowIncapacidadesProfesional[2];
            $diasIncaProfesional=(int)$diasIncaProfesional1;
            if($valorIncaProfesional==null){
                $valorIncaProfesional=0;
            }else{
                $valorIncaProfesional=$valorIncaProfesional;
            }
            if($fechaInicialIncaProfesional!=null && $fechaFinIncaProfesional!=null){

                if($diasIncaProfesional!=null){
                    if($tipoIncaProfesional!=null){
                        array_push($incapacidadesProfesionalJson,[
                            "tipo"           => $tipoIncaProfesional,
                            "fechaInicio"    => $fechaInicialIncaProfesional,
                            "fechaFin"       => $fechaFinIncaProfesional,
                            "cantidadDias"   => $diasIncaProfesional,
                            "pago"           => $valorIncaProfesional
                        ]);
                    }
                }
            }else{
            
            }
            $valorIncaProfesionalTo+=$valorIncaProfesional;
        }


        //COMUN
        $sqlIncaCo="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                        FROM gn_incapacidad i
                        LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                        WHERE tn.equivalente_NE='COMUN' 
                        AND i.empleado=$rowE[0]
                        AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";
        $incapacidadesC=$mysqli->query($sqlIncaCo);
        $valorIncaComunTo=0;

        $sqlValIncaComun="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='COMUN'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";
            $valorIncaCo=$mysqli->query($sqlValIncaComun);
            $rowValIncaCo=mysqli_fetch_row($valorIncaCo);
            $valorIncaComun1=$rowValIncaCo[0];
            $valorIncaComun=(int)$valorIncaComun1;

        $incapacidadesComunJson=[];
        while ($rowIncapacidadesComun=mysqli_fetch_row($incapacidadesC)) {
            $tipoIncaComun=$rowIncapacidadesComun[3];
            $fechaInicialIncaComun=$rowIncapacidadesComun[0];
            $fechaFinIncaComun=$rowIncapacidadesComun[1];
            $diasIncaComun1=$rowIncapacidadesComun[2];
            $diasIncaComun = (int)$diasIncaComun1;
            
            if($valorIncaComun==null){
                $valorIncaComun=0;
            }else{
                $valorIncaComun=$valorIncaComun;
            }
            if($fechaInicialIncaComun!=null && $fechaFinIncaComun!=null){

                if($diasIncaComun!=null){
                    if($tipoIncaComun!=null){
                        array_push($incapacidadesComunJson,[
                            "tipo"           => $tipoIncaComun,
                            "fechaInicio"    => $fechaInicialIncaComun,
                            "fechaFin"       => $fechaFinIncaComun,
                            "cantidadDias"   => $diasIncaComun,
                            "pago"           => $valorIncaComun
                        ]);
                    }
                }
            }else{
            
            }
            $valorIncaComunTo+=$valorIncaComun;
        }



    //Consulta para Licencias 
    //Licencia de maternidad o paternidad   
    $sqlLiMP="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                     FROM gn_incapacidad i
                     LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                     WHERE tn.equivalente_NE='LICENCIA_MP' 
                     AND i.empleado=$rowE[0]
                     AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                     AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";

    $licenciaMP=$mysqli->query($sqlLiMP);
    $valorLicenciaMPTo=0;
    $sqlValLiMP="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='LICENCIA_MP'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";
            $valorLiMP=$mysqli->query($sqlValLiMP);
            $rowValLiMP=mysqli_fetch_row($valorLiMP);
            $valorLicenciaMP1=$rowValLiMP[0];
            $valorLicenciaMP=(int)$valorLicenciaMP1;

    $licenciaMPJson=[];
    while ($rowLicenciaMP=mysqli_fetch_row($licenciaMP)) {
        $tipoLicenciaMP=$rowLicenciaMP[3];
        $fechaInicialLicenciaMP=$rowLicenciaMP[0];
        $fechaFinLicenciaMP=$rowLicenciaMP[1];
        $diasLicenciaMP1=$rowLicenciaMP[2];
        $diasLicenciaMP=(int)$diasLicenciaMP1;

        if ($valorLicenciaMP>0) {
            $valorLicenciaMP= $valorLicenciaMP;
        }else{
            $valorLicenciaMP=0;
        }

        if($fechaInicialLicenciaMP!=null && $fechaFinLicenciaMP!=null){
            if($diasLicenciaMP!=null){
                if($tipoLicenciaMP!=null){
                    array_push($licenciaMPJson,[
                        "fechaInicio"    => $fechaInicialLicenciaMP,
                        "fechaFin"       => $fechaFinLicenciaMP,
                        "cantidadDias"   => $diasLicenciaMP,
                        "pago"           => $valorLicenciaMP,
                        "tipo"           => $tipoLicenciaMP
                    ]);
                }
            }
        }else{
        
        }
        $valorLicenciaMPTo+=$valorLicenciaMP;
    }


    //Licencia remunerada   

    $sqlLiR="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                     FROM gn_incapacidad i
                     LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                     WHERE tn.equivalente_NE='LICENCIA_R' 
                     AND i.empleado=$rowE[0]
                     AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                     AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";
    $licenciaR=$mysqli->query($sqlLiR);
    $valorLicenciaRTo=0;
        $sqlValLiR="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='LICENCIA_R'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";

            $valorLiR=$mysqli->query($sqlValLiR);
            $rowValLiR=mysqli_fetch_row($valorLiR);
            $valorLicenciaR1=$rowValLiR[0];
            $valorLicenciaR=(int)$valorLicenciaR1;

    $licenciaReJson=[];
    while ($rowLicenciaR=mysqli_fetch_row($licenciaR)) {
        $tipoLicenciaR=$rowLicenciaR[3];
        $fechaInicialLicenciaR=$rowLicenciaR[0];
        $fechaFinLicenciaR=$rowLicenciaR[1];
        $diasLicenciaR1=$rowLicenciaR[2];
        $diasLicenciaR=(int)$diasLicenciaR1;
        if ($valorLicenciaR>0) {
            $valorLicenciaR= $valorLicenciaR;
        }else{
            $valorLicenciaR=0;
        }

        if($fechaInicialLicenciaR!=null && $fechaFinLicenciaR!=null){

            if($diasLicenciaR!=null){
                if($tipoLicenciaR!=null){
                    array_push($licenciaReJson,[
                        "fechaInicio"    => $fechaInicialLicenciaR,
                        "fechaFin"       => $fechaFinLicenciaR,
                        "cantidadDias"   => $diasLicenciaR,
                        "pago"           => $valorLicenciaR,
                        "tipo"           => $tipoLicenciaR
                    ]);
                }
            }
        }else{
        
        }
        $valorLicenciaRTo+=$valorLicenciaR;
    }



    //Licencia no remunerada

    $sqlLiNR="SELECT i.fechainicio,i.fechafinal, i.numerodias,tn.equivalente_NE,n.valor
                     FROM gn_incapacidad i
                     LEFT JOIN gn_novedad n ON n.id_unico=i.empleado
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_tipo_novedad tn ON tn.id_unico=i.tiponovedad
                     WHERE tn.equivalente_NE='LICENCIA_NR' 
                     AND i.empleado=$rowE[0]
                     AND i.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                     AND i.fechafinal BETWEEN $fechaInicial AND $fechaFinal";
    $licenciaNR=$mysqli->query($sqlLiNR);
    $valorLicenciaNRTo=0;

        $sqlValLiNR="SELECT SUM(n.valor) AS valor,c.equivalente_NE as tipo
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                            LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                            WHERE c.equivalente_NE='LICENCIA_NR'
                            AND n.empleado=$rowE[0]
                            AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                            AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                            AND p.parametrizacionanno=$paramA";

            $valorLiNR=$mysqli->query($sqlValLiNR);
            $rowValLiNR=mysqli_fetch_row($valorLiNR);
            $valorLicenciaNR1=$rowValLiNR[0];
            $valorLicenciaNR=(int)$valorLicenciaNR1;

    $licenciaNReJson=[];
    while ($rowLicenciaNR=mysqli_fetch_row($licenciaNR)) {
        $tipoLicenciaNR=$rowLicenciaNR[3];  
        $fechaInicialLicenciaNR=$rowLicenciaNR[0];
        $fechaFinLicenciaNR=$rowLicenciaNR[1];
        $diasLicenciaNR1=$rowLicenciaNR[2];
        $diasLicenciaNR=(int)$diasLicenciaNR1;
        if ($valorLicenciaNR>0) {
            $valorLicenciaNR= $valorLicenciaNR;
        }else{
            $valorLicenciaNR=0;
        }
        if($fechaInicialLicenciaNR!=null && $fechaFinLicenciaNR!=null){

            if($diasLicenciaNR!=null){
                if($tipoLicenciaNR!=null){
                    array_push($licenciaNReJson, [
                        "fechaInicio"    => $fechaInicialLicenciaNR,
                        "fechaFin"       => $fechaFinLicenciaNR,
                        "cantidadDias"   => $diasLicenciaNR,
                        "pago"           => $valorLicenciaNR,
                        "tipo"           => $tipoLicenciaNR
                    ]);
                }
            }
        }else{
        }
        $valorLicenciaNRTo+=$valorLicenciaNR;
    }


        //Otros Conceptos
        //Otros Devengados
    //Viático manutención y alojamiento
    $sqlViaticoManu="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='VIATICO_MANU_ALOJ_S'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $viaticoManu=$mysqli->query($sqlViaticoManu);
    $valorViaticoManuTo=0;
    $viaticosManuJson=[];
    while ($rowViaticoManu=mysqli_fetch_row($viaticoManu)) {
        $valorViaticoManu1=$rowViaticoManu[0];
        $tipoViaticoManu=$rowViaticoManu[1];
        $valorViaticoManu=(int)$valorViaticoManu1;
        if( $valorViaticoManu!=null){
            array_push( $viaticosManuJson,[
                "tipo"        => $tipoViaticoManu,
                "valor"       => $valorViaticoManu
            ]);
        }else{
        }
        $valorViaticoManuTo+=$valorViaticoManu;
    }

    //Viático manutención y alojamiento no salarial
    $sqlViaticoManuNS="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='VIATICO_MANU_ALOJ_NS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $viaticoManuNS=$mysqli->query($sqlViaticoManuNS);
    $valorViaticoManuNSTo=0;
    $viaticosManuNSJson=[];
    while ($rowViaticoManuNS=mysqli_fetch_row($viaticoManuNS)) {
        $valorViaticoManuNS1=$rowViaticoManuNS[0];
        $tipoViaticoManuNS=$rowViaticoManuNS[1];
        $valorViaticoManuNS=(int)$valorViaticoManuNS1;
        if( $valorViaticoManuNS!=null){
            array_push($viaticosManuNSJson,[
                "tipo"        => $tipoViaticoManuNS,
                "valor"       => $valorViaticoManuNS
            ]);
        }else{
        }
        $valorViaticoManuNSTo+=$valorViaticoManuNS;
    }


    //Bonificación salarial
    $sqlBoniS="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='BONIFICACION_S'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $bonificacionS=$mysqli->query($sqlBoniS);
    $valorBoniSTo=0;
    $bonificacionSJson=[];
    while ($rowBoniS=mysqli_fetch_row($bonificacionS)) {
        $valorBoniS1=$rowBoniS[0];
        $tipoBoniS=$rowBoniS[1];
        $valorBoniS=(int)$valorBoniS1;
        if( $valorBoniS!=null){
            array_push($bonificacionSJson,[
                "tipo"        => $tipoBoniS,
                "valor"       => $valorBoniS
            ]);
        }else{
        }
        $valorBoniSTo+=$valorBoniS;
    }


    //Bonificación no salarial
    $sqlBoniNS="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='BONIFICACION_NS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $bonificacionNS=$mysqli->query($sqlBoniNS);
    $valorBoniNSTo=0;
    $bonificacionNSJson=[];
    while ($rowBoniNS=mysqli_fetch_row($bonificacionNS)) {
        $valorBoniNS1=$rowBoniNS[0];
        $tipoBoniNS=$rowBoniNS[1];
        $valorBoniNS=(int)$valorBoniNS1;
        if( $valorBoniNS!=null){
            array_push($bonificacionNSJson, [
                "tipo"        => $tipoBoniNS,
                "valor"       => $valorBoniNS
            ]);
            
        }else{
        }
        $valorBoniNSTo+=$valorBoniNS;
    }

    //Auxilios no salariales
    $sqlAuxNS="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='AUXILIO_NS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $auxiliosNS=$mysqli->query($sqlAuxNS);
    $valorAuxiliosNSTo=0;
    $auxiliosNSJson=[];
    while ($rowAuxiliosNS=mysqli_fetch_row($auxiliosNS)) {
        $valorAuxiliosNS1=$rowAuxiliosNS[0];
        $tipoAuxiliosNS=$rowAuxiliosNS[1];
        $valorAuxiliosNS=(int)$valorAuxiliosNS1;
        if( $valorAuxiliosNS!=null){
            array_push($auxiliosNSJson,[
                "tipo"        => $tipoAuxiliosNS,
                "valor"       => $valorAuxiliosNS
            ]);
        }else{
        }
        $valorAuxiliosNSTo+=$valorAuxiliosNS;
    }

    //Compensaciones ordinarias
    $sqlCompensacionO="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='COMPENSACION_O'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $compensacionO=$mysqli->query($sqlCompensacionO);
    $valorCompensacionOTo=0;
    $compensacionOJson=[];
    while ($rowCompensacionO=mysqli_fetch_row($compensacionO)) {
        $valorCompensacionO1=$rowCompensacionO[0];
        $tipoCompensacionO=$rowCompensacionO[1];
        $valorCompensacionO=(int)$valorCompensacionO1;
        if( $valorCompensacionO!=null){
            array_push($compensacionOJson,[
                "tipo"        => $tipoCompensacionO,
                "valor"       => $valorCompensacionO
            ]);
        }else{
        }
        $valorCompensacionOTo+=$valorCompensacionO;
    }

    //Compensaciones extraordinarias
    $sqlCompensacionEX="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='COMPENSACION_E'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $compensacionEX=$mysqli->query($sqlCompensacionEX);
    $valorCompensacionEXTo=0;
    $compensacionEXJson=[];
    while ($rowCompensacionEX=mysqli_fetch_row($compensacionEX)) {
        $valorCompensacionEX1=$rowCompensacionEX[0];
        $tipoCompensacionEX=$rowCompensacionEX[1];
        $valorCompensacionEX=(int)$valorCompensacionEX1;
        if( $valorCompensacionEX!=null){
            array_push($compensacionEXJson, [
                "tipo"        => $tipoCompensacionEX,
                "valor"       => $valorCompensacionEX
            ]);
        }else{
        }
        $valorCompensacionEXTo+=$valorCompensacionEX;
    }


    //Valor que el trabajador recibe como concepto salarial
    $sqlConceptoSAL="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_S'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $conceptoSAL=$mysqli->query($sqlConceptoSAL);
    $valorConceptoSALTo=0;
    $conceptoSALJson=[];
    while ($rowConceptoSAL=mysqli_fetch_row($conceptoSAL)) {
        $valorConceptoSAL1=$rowConceptoSAL[0];
        $tipoConceptoSAL=$rowConceptoSAL[1];
        $valorConceptoSAL=(int)$valorConceptoSAL1;
        if( $valorConceptoSAL!=null){
            array_push($conceptoSALJson, [
                "tipo"        => $tipoConceptoSAL,
                "valor"       => $valorConceptoSAL
            ]);
        }else{
        }
        $valorConceptoSALTo+=$valorConceptoSAL;
    }

    //Valor que el trabajador recibe como concepto no salarial  

    $sqlConceptoNSAL="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_NS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $conceptoNSAL=$mysqli->query($sqlConceptoNSAL);
    $valorConceptoNSALTo=0;
    $conceptoNSALJson=[];
    while ($rowConceptoNSAL=mysqli_fetch_row($conceptoNSAL)) {
        $valorConceptoNSAL1=$rowConceptoNSAL[0];
        $tipoConceptoNSAL=$rowConceptoNSAL[1];
        $valorConceptoNSAL=(int)$valorConceptoNSAL1;
        if( $valorConceptoNSAL!=null){
            array_push($conceptoNSALJson,[
                "tipo"        => $tipoConceptoNSAL,
                "valor"       => $valorConceptoNSAL
            ]);
        }else{
        }
        $valorConceptoNSALTo+=$valorConceptoNSAL;
    }


    //Alimentación salarial

    $sqlAliSAL="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_ALIMENTACION_S'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $alimentacionSAL=$mysqli->query($sqlAliSAL);
    $valorAlimentacionSALTo=0;
    $alimentacionSALJson=[];
    while ($rowAlimentacionSAL=mysqli_fetch_row($alimentacionSAL)) {
        $valorAlimentacionSAL1=$rowAlimentacionSAL[0];
        $tipoAlimentacionSAL=$rowAlimentacionSAL[1];
        $valorAlimentacionSAL=(int) $valorAlimentacionSAL1;
        if( $valorAlimentacionSAL!=null){
            array_push($alimentacionSALJson,[
                "tipo"        => $tipoAlimentacionSAL,
                "valor"       => $valorAlimentacionSAL
            ]);
        }else{
        }
        $valorAlimentacionSALTo+=$valorAlimentacionSAL;
    }


    //Alimentación no salarial

    $sqlAliNSAL="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_ALIMENTACION_NS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $alimentacionNSAL=$mysqli->query($sqlAliNSAL);
    $valorAlimentacionNSALTo=0;
    $alimentacionNSALJson=[];
    while ($rowAlimentacionNSAL=mysqli_fetch_row($alimentacionNSAL)) {
        $valorAlimentacionNSAL1=$rowAlimentacionNSAL[0];
        $tipoAlimentacionNSAL=$rowAlimentacionNSAL[1];
        $valorAlimentacionNSAL=(int)$valorAlimentacionNSAL1;
        if( $valorAlimentacionNSAL!=null){
            array_push($alimentacionNSALJson,[
                "tipo"        => $tipoAlimentacionNSAL,
                "valor"       => $valorAlimentacionNSAL
            ]);
        }else{
        }
        $valorAlimentacionNSALTo+=$valorAlimentacionNSAL;
    }


    //Pago tercero

    $sqlPagoTer="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_TERCERO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $pagoTer=$mysqli->query($sqlPagoTer);
    $valorPagoTerTo=0;
    $pagoTerceroJson=[];
    while ($rowPagoTer=mysqli_fetch_row($pagoTer)) {
        $valorPagoTer1=$rowPagoTer[0];
        $tipoPagoTer=$rowPagoTer[1];
        $valorPagoTer=(int)$valorPagoTer1;
        if( $valorPagoTer!=null){
            array_push($pagoTerceroJson, [
                "tipo"        => $tipoPagoTer,
                "valor"       => $valorPagoTer
            ]);
        }else{
        }
        $valorPagoTerTo+=$valorPagoTer;
    }


    //DOTACION

    $sqlDotacion="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='DOTACION'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $dotacion=$mysqli->query($sqlDotacion);
    $valorDotacionTo=0;
    $dotacionJson=[];
    while ($rowDotacion=mysqli_fetch_row($dotacion)) {
        $valorDotacion1=$rowDotacion[0];
        $tipoDotacion=$rowDotacion[1];
        $valorDotacion=(int)$valorDotacion1;
        if( $valorDotacion!=null){
            array_push($dotacionJson,[
                "tipo"        => $tipoDotacion,
                "valor"       => $valorDotacion
            ]);
        }else{
        }
        $valorDotacionTo+=$valorDotacion;
    }

    //Teletrabajo

    $sqlTeletrabajo="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='TELETRABAJO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $teletrabajo=$mysqli->query($sqlTeletrabajo);
    $valorTeletrabajoTo=0;
    $teletrabajoJson=[];
    while ($rowTeletrabajo=mysqli_fetch_row($teletrabajo)) {
        $valorTeletrabajo1=$rowTeletrabajo[0];
        $tipoTeletrabajo=$rowTeletrabajo[1];
        $valorTeletrabajo=(int)$valorTeletrabajo1;
        if( $valorTeletrabajo!=null){
            array_push($teletrabajoJson, [
                "tipo"        => $tipoTeletrabajo,
                "valor"       => $valorTeletrabajo
            ]);
        }else{
        }
        $valorTeletrabajoTo+=$valorTeletrabajo;
    }


    //ReintegroDevengos

    $sqlReintegroDevengos="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='REINTEGRO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $reintegroDevengos=$mysqli->query($sqlReintegroDevengos);
    $valorReintegroDevengosTo=0;
    $reintegroDevengosJson=[];
    while ($rowReintegroDevengos=mysqli_fetch_row($reintegroDevengos)) {
        $valorReintegroDevengos1=$rowReintegroDevengos[0];
        $tipoReintegroDevengos=$rowReintegroDevengos[1];
        $valorReintegroDevengos=(int)$valorReintegroDevengos1;
        if( $valorReintegroDevengos!=null){
            array_push($reintegroDevengosJson, [
                "tipo"        => $tipoReintegroDevengos,
                "valor"       => $valorReintegroDevengos
            ]);
        }else{
        }
        $valorReintegroDevengosTo+=$valorReintegroDevengos;
    }


        //Consultas de deducciones 
        //Pago Salud
        $sqlSalud="SELECT SUM(n.valor) AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'pagoSalud'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
        $saludP=$mysqli->query($sqlSalud);
        $rowPagoSalud=mysqli_fetch_row($saludP);
        $valorSalud1=$rowPagoSalud[0];
        $valorSalud=(int)$valorSalud1;
        $valorSaludTo+=$valorSalud;
        //Pago Pension
        $sqlPension="SELECT SUM(n.valor) AS valor
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                        WHERE tn.nombre = 'pagoPension'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
        $pensionP=$mysqli->query($sqlPension);
        $rowPagoPension=mysqli_fetch_row($pensionP);
        $valorPension1=$rowPagoPension[0];
        $valorPension=(int)$valorPension1;
        $valorPensionTo+=$valorPension;
 //Consulta para Deducciones Porcentuales
        //Fondo de solidaridad pensional
        $sqlDeducSP="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        WHERE c.equivalente_NE='DEDUCCION_SP'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
    $deduccionSP=$mysqli->query($sqlDeducSP);
    $valorDeduccionSPTo=0;
    $deduccionSPJson=[];
while ( $rowDeduccionSP=mysqli_fetch_row($deduccionSP)) {

    
        $valorDeduccionSP1=$rowDeduccionSP[0];
        $tipoDeduccionSP=$rowDeduccionSP[1]; 
        $valorDeduccionSP=(int)$valorDeduccionSP1;
        $porcentajeDeduccionSP=1;
        if ($valorDeduccionSP!=null) {
            array_push($deduccionSPJson,[
                "tipo"              => $tipoDeduccionSP,
                "porcentaje"        => $porcentajeDeduccionSP,
                "valorDeduccion"    => $valorDeduccionSP
            ]);
        }else{
        }
        $valorDeduccionSPTo+=$valorDeduccionSP;

}


       

        //Fondo de subsistencia
        $sqlDeducSub="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        WHERE c.equivalente_NE='DEDUCCION_SUB'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
  $deduccionSub=$mysqli->query($sqlDeducSub);
  $valorDeduccionSubTo=0;
  $deduccionSubJson=[];
while ( $rowDeduccionSub=mysqli_fetch_row($deduccionSub)) {
  
        $valorDeduccionSub1=$rowDeduccionSub[0];
        $tipoDeduccionSub=$rowDeduccionSub[1]; 
        $valorDeduccionSub=(int) $valorDeduccionSub1;
        $porcentajeDeduccionSub=1;
        if ($valorDeduccionSub!=null) {
            array_push($deduccionSubJson,[
                "tipo"              => $tipoDeduccionSub,
                "porcentaje"        => $porcentajeDeduccionSub,
                "valorDeduccion"    => $valorDeduccionSub
            ]);
        }else{
        }

         $valorDeduccionSubTo+=$valorDeduccionSub;
}

      

        //Sindicato
         $sqlDeducSin="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                        LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                        WHERE c.equivalente_NE='SINDICATO'
                        AND n.empleado=$rowE[0]
                        AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                        AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                        AND p.parametrizacionanno=$paramA";
         $deduccionSin=$mysqli->query($sqlDeducSin);
         $valorDeduccionSinTo=0;
         $deduccionSinJson=[];
        while( $rowDeduccionSin=mysqli_fetch_row($deduccionSin)){
        $valorDeduccionSin1=$rowDeduccionSin[0];
        $tipoDeduccionSin=$rowDeduccionSin[1]; 
        $valorDeduccionSin=(int) $valorDeduccionSin1;
        $porcentajeDeduccionSin=1;
        if ($valorDeduccionSin!=null) {
            array_push($deduccionSinJson,[
                "tipo"              => $tipoDeduccionSin,
                "porcentaje"        => $porcentajeDeduccionSin,
                "valorDeduccion"    => $valorDeduccionSin
            ]);
        }else{
        }

         $valorDeduccionSinTo+=$valorDeduccionSin;
        }




    //Consulta Deducciones Valor

    //Sanción pública
    $sqlSancionP="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='SANCION_PUBLIC'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $sancionP=$mysqli->query($sqlSancionP);
    $valorSancionPTo=0;
    $sancionPublicaJson=[];
    while ($rowSancionP=mysqli_fetch_row($sancionP)) {
        $valorSancionP1=$rowSancionP[0];
        $tipoSancionP=$rowDeduccionSin[1];
        $valorSancionP=(int)$valorSancionP1;
        if( $valorSancionP!=null){
            array_push( $sancionPublicaJson,[
                "tipo"        => $tipoSancionP,
                "valor"       => $valorSancionP
            ]);
        }else{
        }
        $valorSancionPTo+=$valorSancionP;
    }



    //Sanción privada
    $sqlSancionPri="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='SANCION_PUBLIC'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $sancionPri=$mysqli->query($sqlSancionPri);
    $valorSancionPriTo=0;
    $sancionPrivadaJson=[];
    while ($rowSancionPri=mysqli_fetch_row($sancionPri)) {
        $valorSancionPri1=$rowSancionPri[0];
        $tipoSancionPri=$rowSancionPri[1];
        $valorSancionPri=(int)$valorSancionPri1;
        if( $valorSancionPri!=null){
            array_push($sancionPrivadaJson, [
                "tipo"        => $tipoSancionPri,
                "valor"       => $valorSancionPri
            ]);
        }else{
        }
        $valorSancionPriTo+=$valorSancionPri;
    }


    //Pago tercero
    $sqlPagoTerDe="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PAGO_TERCERO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $pagoTerDe=$mysqli->query($sqlPagoTerDe);
    $valorPagoTerDeTo=0;
    $pagotercerDedeuccionesJson=[];
    while ($rowPagoTerDe=mysqli_fetch_row($pagoTerDe)) {
        $valorPagoTerDe1=$rowPagoTerDe[0];
        $tipoPagoTerDe=$rowPagoTerDe[1];
        $valorPagoTerDe=(int)$valorPagoTerDe1;
        if( $valorPagoTerDe!=null){
            array_push($pagotercerDedeuccionesJson,[
                "tipo"        => $tipoPagoTerDe,
                "valor"       => $valorPagoTerDe
            ]);
        }else{
        }
        $valorPagoTerDeTo+=$valorPagoTerDe;
    }



    //Anticipo
    $sqlAnticipo="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='ANTICIPO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $anticipo=$mysqli->query($sqlAnticipo);
    $valorAnticipoTo=0;
    $anticipoJson=[];
    while ($rowAnticipo=mysqli_fetch_row($anticipo)) {
        $valorAnticipo1=$rowAnticipo[0];
        $tipoAnticipo=$rowAnticipo[1];
        $valorAnticipo=(int)$valorAnticipo1;
        if( $valorAnticipo!=null){
            array_push($anticipoJson,[
                "tipo"        => $tipoAnticipo,
                "valor"       => $valorAnticipo
            ]);
        }else{
        }
        $valorAnticipoTo+=$valorAnticipo;
    }


    //Otra deducción
    $sqlOtraDe="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='OTRA_DEDUCCION'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $otraDed=$mysqli->query($sqlOtraDe);


    $valorOtraDedTo=0;
    $otraDeduccionJson=[];
        while ($rowOtraDed=mysqli_fetch_row($otraDed)){
            $valorOtraDed1=$rowOtraDed[0];
            $tipoOtraDed=$rowOtraDed[1];
            $valorOtraDed=(int)$valorOtraDed1;
            if( $valorOtraDed!=null){
                array_push($otraDeduccionJson,[
                    "tipo"        => $tipoOtraDed,
                    "valor"       => $valorOtraDed
                ]);
            }else{
            }
            $valorOtraDedTo+=$valorOtraDed;
        }


       
    //Pensión voluntaria
    $sqlPensionV="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PENSION_VOLUNTARIA'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $pensionV=$mysqli->query($sqlPensionV);

    $valorPensionVTo=0;
    $pensionVoluntariaJson=[];
    while ($rowPensionV=mysqli_fetch_row($pensionV)){
        $valorPensionV1=$rowPensionV[0];
        $tipoPensionV=$rowPensionV[1];
        $valorPensionV=(int)$valorPensionV1;
        if( $valorPensionV!=null){
            array_push($pensionVoluntariaJson, [
                "tipo"        => $tipoPensionV,
                "valor"       => $valorPensionV
            ]);
        }else{
        }
        $valorPensionVTo+=$valorPensionV;
    }

    //Retención en la fuente
    $sqlReteF="SELECT SUM(n.valor) AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='RETENCION_FUENTE'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $retenF=$mysqli->query($sqlReteF);
    $valorRetenFTo=0;
    $retencionFuenteJson=[];
    while ($rowRetenF=mysqli_fetch_row($retenF)){
        $valorRetenF1=$rowRetenF[0];
        $tipoRetenF=$rowRetenF[1];
        $valorRetenF=(int)$valorRetenF1;
        if( $valorRetenF!=null){
            array_push($retencionFuenteJson, [
                "tipo"        => $tipoRetenF,
                "valor"       => $valorRetenF
            ]);
        }else{
        }
        $valorRetenFTo+=$valorRetenF;
    }



    //Ahorro fomento a la construcción  
    $sqlFomentoC="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='AFC'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $fomentoC=$mysqli->query($sqlFomentoC);
    $valorFomentoCTo=0;
    $fomentoConsJson=[];
    while ($rowFomentoC=mysqli_fetch_row($fomentoC)){
        $valorFomentoC1=$rowFomentoC[0];
        $tipoFomentoC=$rowFomentoC[1];
        $valorFomentoC=(int)$valorFomentoC1;
        if( $valorFomentoC!=null){
            array_push($fomentoConsJson, [
                "tipo"        => $tipoFomentoC,
                "valor"       => $valorFomentoC
            ]);
        }else{
        }
        $valorFomentoCTo+=$valorFomentoC;
    }





    //Cooperativa
    $sqlCoope="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='COOPERATIVA'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $cooperativa=$mysqli->query($sqlCoope);
    $valorCooperativaTo=0;
    $tipoCooperativaJson=[];
    while ($rowCooperativa=mysqli_fetch_row($cooperativa)){
        $valorCooperativa1=$rowCooperativa[0];
        $tipoCooperativa=$rowCooperativa[1];
        $valorCooperativa=(int)$valorCooperativa1;
        if( $valorCooperativa!=null){
            array_push($tipoCooperativaJson, [
                "tipo"        => $tipoCooperativa,
                "valor"       => $valorCooperativa
            ]);
        }else{
        }
        $valorCooperativaTo+=$valorCooperativa;
    }



    //Embargo fiscal
    $sqlEmbargoF="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='EMBARGO_FISCAL'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $embargoF=$mysqli->query($sqlEmbargoF);
    $valorEmbargoFTo=0;
    $tipoEmbargoJson=[];
    while ($rowEmbargoF=mysqli_fetch_row($embargoF)){
        $valorEmbargoF1=$rowEmbargoF[0];
        $tipoEmbargoF=$rowEmbargoF[1];
        $valorEmbargoF=(int)$valorEmbargoF1;
        if( $valorEmbargoF!=null){
            array_push($tipoEmbargoJson,[
                "tipo"        => $tipoEmbargoF,
                "valor"       => $valorEmbargoF
            ]);
        }else{
        }
        $valorEmbargoFTo+=$valorEmbargoF;
    }




    //Planes complementarios
    $sqlPlanesC="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='PLAN_COMPLEMENTARIOS'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";
    $planesC=$mysqli->query($sqlPlanesC);
    $valorPlanesCTo=0;
    $planesComplementariosJson=[];
    while ($rowPlanesC=mysqli_fetch_row($planesC)){
        $valorPlanesC1=$rowPlanesC[0];
        $tipoPlanesC=$rowPlanesC[1];
        $valorPlanesC=(int) $valorPlanesC1;
        if( $valorPlanesC!=null){
            array_push($planesComplementariosJson, [
                "tipo"        => $tipoPlanesC,
                "valor"       => $valorPlanesC
            ]);
        }else{
        }
        $valorPlanesCTo+=$valorPlanesC;
    }


    //Educación
    $sqlEducacion="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='EDUCACION'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $educacion=$mysqli->query($sqlEducacion);
    $valorEducacionTo=0;
    $educacionJson=[];
    while ($rowEducacion=mysqli_fetch_row($educacion)){
        $valorEducacion1=$rowEducacion[0];
        $tipoEducacion=$rowEducacion[1];
        $valorEducacion=(int)$valorEducacion1;
        if( $valorEducacion!=null){
            array_push($educacionJson, [
                "tipo"        => $tipoEducacion,
                "valor"       => $valorEducacion
            ]);
        }else{
        }
        $valorEducacionTo+=$valorEducacion;
    }


    //Reintegro
    $sqlReintegro="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='REINTEGRO'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $reintegro=$mysqli->query($sqlReintegro);
    $valorReintegroTo=0;
    $reintegroJson=[];
    while ($rowReintegro=mysqli_fetch_row($reintegro)){
        $valorReintegro1=$rowReintegro[0];
        $tipoReintegro=$rowReintegro[1];
        $valorReintegro=(int)$valorReintegro1;
        if( $valorReintegro!=null){
            array_push($reintegroJson,[
                "tipo"        => $tipoReintegro,
                "valor"       => $valorReintegro
            ]);
        }else{
        }
        $valorReintegroTo+=$valorReintegro;
    }




    //Deuda
    $sqlDeuda="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                     FROM gn_novedad n 
                     LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                     LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                     WHERE c.equivalente_NE='DEUDA'
                     AND n.empleado=$rowE[0]
                     AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $deuda=$mysqli->query($sqlDeuda);
    $rowDeuda=mysqli_fetch_row($deuda);
    $valorDeuda=$rowDeuda[0];
    $tipoDeuda=$rowDeuda[1];
    $valorDeudaTo=0;
    $deudaJson=[];
    while ($rowDeuda=mysqli_fetch_row($deuda)){
        $valorDeuda1=$rowDeuda[0];
        $tipoDeuda=$rowDeuda[1];
        $valorDeuda=(int)$valorDeuda1;
        if( $valorDeuda!=null){
            array_push( $deudaJson,[
                "tipo"        => $tipoDeuda,
                "valor"       => $valorDeuda
            ]);
        }else{
        }
        $valorDeudaTo+=$valorDeuda;
    }



        //Consecutivo
        $sqlConsecutivoF="SELECT prefijo,consecutivo
                        FROM gn_nomina_electronica  
                        WHERE tercero=$tercero
                        AND mes=$idMes" ;

        $resultConsecutivoF=$mysqli->query($sqlConsecutivoF);   
        $rowConsecutivoF = $resultConsecutivoF->fetch_assoc();
        $prefijoFinal = $rowConsecutivoF['prefijo'];
        $consecutivoFinal = $rowConsecutivoF['consecutivo'];
        //Notas
        $notas='';

        //Total Deducciones
        $deduccionTotal=$valorSaludTo+$valorPensionTo+$valorSancionPTo+$valorSancionPriTo+$valorPagoTerDeTo
        +$valorAnticipoTo+$valorOtraDedTo+$valorRetenFTo+$valorFomentoCTo+$valorCooperativaTo+$valorEmbargoFTo
        +$valorPlanesCTo+$valorEducacionTo+$valorReintegroTo+$valorDeudaTo+$valorDeduccionSPTo+$valorDeduccionSubTo+$valorDeduccionSinTo;
        //Total Devengados
        $devengadoTotal=$auxTransporte+$sueldo+$valorDF+$valorHENTo+$valorHERTo+$valorHEDDFTo+$valorHEDDTo+$valorHENDFTo
        +$valorHRNDFTo+$valorVacaciones+$pagoPrima+$valorIncaLaboralTo+$valorIncaProfesionalTo+$valorIncaComunTo+$valorLicenciaMPTo
        +$valorLicenciaRTo+$valorLicenciaNRTo+$valorViaticoManuTo+$valorViaticoManuNSTo+$valorBoniSTo+$valorBoniNSTo+$valorAuxiliosNSTo
        +$valorCompensacionOTo+$valorCompensacionEXTo+$valorConceptoSALTo+$valorConceptoNSALTo+$valorAlimentacionSALTo+$valorAlimentacionNSALTo
        +$valorPagoTerTo+$valorDotacionTo+$valorTeletrabajoTo+$valorReintegroDevengosTo;
        //Comprobante Total
        $comprobanteTotal=$devengadoTotal-$deduccionTotal;

       if ($rowE[11]>$rowE[12]) {
          $fechaRe=null;
         
       }else{
        $fechaRe=$rowE[12];
       }
        $riesgoPension=$rowE[7];
        if($riesgoPension==1){
            $riesgoPension=false; 
        }else{
            $riesgoPension=true; 
        }

        $salarioInt=$rowE[8];
        if($salarioInt==1){
        $salarioIntegral=true; 
        }else{
        $salarioIntegral=false; 
        }
        $salarioAct1=$rowE[15];
        $salarioAct=(int)$salarioAct1;


        $nomina = [
            "fechaLiquidacionInicio"           => $fechaInicial1,
            "fechaLiquidacionFin"              => $fechaFinal1,
            "notas"                            => $notas,
            "prefijo"                          => $prefijoFinal,
            "consecutivo"                      => $consecutivoFinal,
            "fechasPago"                       => $fechaFinal1,
            "fechaGeneracion"                  => $fechaActual,
            "comprobanteTotal"                 => $comprobanteTotal,
            "deduccionTotal"                   => $deduccionTotal,
            "devengadoTotal"                   => $devengadoTotal,
            "empleado" => [
                "numeroIdentificacion"      => $rowE[1],
                "primerNombre"              => $rowE[2],
                "otrosNombres"              => $rowE[3],
                "primerApellido"            => $rowE[4],
                "segundoApellido"           => $rowE[5],
                "tipoIdentificacion"        => $rowE[6],
                "altoRiesgoPension"         => $riesgoPension,
                "salarioIntegral"           => $salarioIntegral,
                "ciudad"                    => $rowE[9],
                "direccion"                 => $rowE[10],
                "fechaIngreso"              => $rowE[11],
                "fechaRetiro"               =>  $fechaRe,
                "tipoCuenta"                => $rowE[13],
                "numeroCuenta"              => $rowE[14],
                "salario"                   => $salarioAct,
                "tipoTrabajador"            => $rowE[16],
                "subtipoTrabajador"         => $rowE[17],
                "tipoContrato"              => $rowE[18],
                "periodoNomina"             => $rowE[19],
                "metodoPago"                => $rowE[20],
                "entidadBancaria"           => $rowE[21]
            ],
            "devengados" => [
                
                "auxilioTransporte"      => $auxTransporte,
                "sueldoTrabajado"        => $sueldo,
                "diasTrabajados"         => $diasTrabajados
            ]     
        ]; 






        function vacio($vacio){
        return !($vacio=="");

        }

        $datosCodificados1 = json_encode($nomina);

        $jsonGrande=rtrim($datosCodificados1, "}");

        $arrayTempo=[];

        foreach ($horasExtrasDiurnasJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasNocturnasJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasReNocturnasJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasDiurnaDomFesJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasRecargoDiurnoDomFesJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasNocturnaDomFesJson as  $value) {
            array_push($arrayTempo,$value);
        }
        foreach ($horasExtrasRecargoNocturnoDomFesJson as  $value) {
            array_push($arrayTempo,$value);
        }
        if (count($arrayTempo)>0) {
            $arrayTempo1=json_encode($arrayTempo);
        $varHorasDiurnas= ',"horasExtrasRecargos":'.$arrayTempo1.'}}';

        }else{
            $varHorasDiurnas='}}';
        }
        $jsonFi=$jsonGrande.$varHorasDiurnas;

        $jsonFi1=rtrim($jsonFi, "}");


            if (count($vacacionesJson)>0) {
            $vacacionesJson=json_encode($vacacionesJson);
            $varVacaciones= ',"vacaciones":'.$vacacionesJson.'}}';
            }else{
            $varVacaciones='}}';
            }
        $jsonFiV1=$jsonFi1.$varVacaciones;

        $jsonFi2=rtrim($jsonFiV1, "}");


        if (count($primasJson)>0) {
            $primasJson=json_encode($primasJson);
        $varPrima= ',"primas":'.$primasJson.'}}';
        }else{
            $varPrima='}}'; 
        }

        $jsonFiP2=$jsonFi2.$varPrima;

        $jsonFi3=rtrim($jsonFiP2, "}");


        $arrayTempoInca=[];

        foreach ($incapacidadesComunJson as  $value) {
            array_push($arrayTempoInca,$value);
        }
        foreach ($incapacidadesLaboralJson as  $value) {
            array_push($arrayTempoInca,$value);
        }
        foreach ($incapacidadesProfesionalJson as  $value) {
            array_push($arrayTempoInca,$value);
        }

        if (count($arrayTempoInca)>0) {
            if (count($primasJson)>0) {
            $arrayTempoInca1=json_encode($arrayTempoInca);
            $varIncapacidades= '},"incapacidades":'.$arrayTempoInca1.'}}';
            }else{
            $arrayTempoInca1=json_encode($arrayTempoInca);
            $varIncapacidades= ',"incapacidades":'.$arrayTempoInca1.'}}';
            }
            
        }else{
            $varIncapacidades='}}';
        }

        $jsonFiIn3=$jsonFi3.$varIncapacidades;

        $jsonFi4=rtrim($jsonFiIn3, "}");


        $arrayTempoLicen=[];

        foreach ($licenciaMPJson as  $value) {
            array_push($arrayTempoLicen,$value);
        }
        foreach ($licenciaNReJson as  $value) {
            array_push($arrayTempoLicen,$value);
        }
        foreach ($licenciaReJson as  $value) {
            array_push($arrayTempoLicen,$value);
        }

        if (count($arrayTempoLicen)>0) {
            if (count($primasJson)>0) {
                $arrayTempoLicen1=json_encode($arrayTempoLicen);
                $varLicencias= '},"licencias":'.$arrayTempoLicen1.'}}';
            }else{
                $arrayTempoLicen1=json_encode($arrayTempoLicen);
                $varLicencias= ',"licencias":'.$arrayTempoLicen1.'}}';
            }

        }else{
            $varLicencias='}}';
        }
        $jsonFiLi4=$jsonFi4.$varLicencias;

        $jsonFi5=rtrim($jsonFiLi4, "}");

        $arrayTempoOtrosDe=[];
        foreach ($viaticosManuJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($viaticosManuNSJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($bonificacionSJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($bonificacionNSJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($auxiliosNSJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($compensacionOJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        
        foreach ($compensacionEXJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($conceptoSALJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($conceptoNSALJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($alimentacionSALJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($alimentacionNSALJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($pagoTerceroJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($dotacionJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($teletrabajoJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($reintegroDevengosJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }
        foreach ($reintegroDevengosJson as  $value) {
            array_push($arrayTempoOtrosDe,$value);
        }

        
        if (count($arrayTempoOtrosDe)>0) {
            if (count($primasJson)>0) {
                if(count($arrayTempoInca)>0){
                     $arrayTempoOtrosDe1=json_encode($arrayTempoOtrosDe);
                $varOtrosDe= ',"otrosDevengados":'.$arrayTempoOtrosDe1.'}}';
                }else{
                $arrayTempoOtrosDe1=json_encode($arrayTempoOtrosDe);
                $varOtrosDe= '},"otrosDevengados":'.$arrayTempoOtrosDe1.'}}';
                }
            }else{
                $arrayTempoOtrosDe1=json_encode($arrayTempoOtrosDe);
                $varOtrosDe= ',"otrosDevengados":'.$arrayTempoOtrosDe1.'}}';
            }
        }else{
           $varOtrosDe='}}';
        }

        $jsonFiLi5=$jsonFi5.$varOtrosDe;
        
        $jsonFi6=rtrim($jsonFiLi5,"}");


        $deduccionJ = [
            "pagoSalud"    => $valorSalud,
            "pagoPension"  => $valorPension
        ];

    if (count($arrayTempoInca)>0 || count($arrayTempoLicen)>0 || count($arrayTempoOtrosDe)>0 || count($arrayTempo)>0 ) {
         if (count($arrayTempoOtrosDe)>0) {

         $jsonFiDe6=$jsonFi6.'},"deducciones":'.json_encode($deduccionJ).'}';
        }elseif(count($primasJson)>0){
         $jsonFiDe6=$jsonFi6.'}},"deducciones":'.json_encode($deduccionJ).'}';
        }else{
         $jsonFiDe6=$jsonFi6.'},"deducciones":'.json_encode($deduccionJ).'}';
        }

    }elseif(count($primasJson)>0){
         $jsonFiDe6=$jsonFi6.'}},"deducciones":'.json_encode($deduccionJ).'}';
    }else{
         $jsonFiDe6=$jsonFi6.'},"deducciones":'.json_encode($deduccionJ).'}';
    }     

    
        $jsonFi7=rtrim($jsonFiDe6, "}");

        $arrayTempoDeduPor=[];
        foreach ($deduccionSPJson as  $value) {
            array_push($arrayTempoDeduPor,$value);
        }
        foreach ($deduccionSubJson as  $value) {
            array_push($arrayTempoDeduPor,$value);
        }
        foreach ($deduccionSinJson as  $value) {
            array_push($arrayTempoDeduPor,$value);
        }


        if (count($arrayTempoDeduPor)>0) {
            $arrayTempoDeduPor1=json_encode($arrayTempoDeduPor);
            $varDeducPor= ',"deduccionesPorcentuales":'.$arrayTempoDeduPor1.'}}';
            }else{
           $varDeducPor='}}';
            }   


        $jsonFiDeV7=$jsonFi7.$varDeducPor;


        $jsonFi8=rtrim($jsonFiDeV7,"}");



        $arrayTempoDeduV=[];
        foreach ($sancionPublicaJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($sancionPrivadaJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($pagotercerDedeuccionesJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($anticipoJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($otraDeduccionJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($pensionVoluntariaJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }

        foreach ($retencionFuenteJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($fomentoConsJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($tipoCooperativaJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($tipoEmbargoJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($planesComplementariosJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($educacionJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($reintegroJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        foreach ($deudaJson as  $value) {
            array_push($arrayTempoDeduV,$value);
        }
        
        if (count($arrayTempoDeduV)>0) {
            $arrayTempoDeduV1=json_encode($arrayTempoDeduV);
            $varDeducVal= ',"deduccionesValor":'.$arrayTempoDeduV1.'}}';
            }else{
           $varDeducVal='}}';
            }

            $jsonFinalT=$jsonFi8.$varDeducVal;

         $jsonFinalT; 
      echo  $datosCodificados=$jsonFinalT;



        $token = Token($compania, $mysqli);
        $Respuesta = nomina($datosCodificados, $token, $mysqli, $tercero,$idMes,$fechaActual,$usuarioEnv);
         $Mensaje = $Respuesta;
         
            
        if ($Mensaje == '') {
            $token = Token($compania, $mysqli);

            $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
            $resultTkn = $mysqli->query($sqlEdiTknn);

            $Respuestaa = nomina($datosCodificados, $token, $mysqli, $tercero,$idMes,$fechaActual,$usuarioEnv);
            $Mensaje = $Respuestaa;
        }

        if ($Mensaje == "Error en credenciales de Usuario Su sesión ha expirado, por favor vuelva a iniciar sesión US05") {
            $token = Token($compania, $mysqli);

            $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
            $resultTkn = $mysqli->query($sqlEdiTknn);

            $Respuestaa = nomina($datosCodificados, $token, $mysqli, $tercero,$idMes,$fechaActual,$usuarioEnv);
            $Mensaje = $Respuestaa;
        }

        if($Mensaje == "Error en credenciales de Usuario No se ha enviado correctamente el token de acceso US03") {
            $token = Token($compania, $mysqli);

            $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
            $resultTkn = $mysqli->query($sqlEdiTknn);

            $Respuestaa = nomina($datosCodificados, $token, $mysqli, $tercero,$idMes,$fechaActual,$usuarioEnv);
            $Mensaje = $Respuestaa;
        }

        }

    }

         //Funciones 

         function Token($compania, $mysqli) {

            $qury      = "SELECT usuario_fe,contrasena_fe FROM `gf_tercero` where id_unico='".$compania."' ";
            $resl      = $mysqli->query($qury);
            $rowwu       = $resl->fetch_assoc(); 
            $usu  = $rowwu['usuario_fe'];
            $contra  = $rowwu['contrasena_fe'];
            
                //LOGUIN - SACAR TOKEN
                //parametros :
            
            
                $usuario = $usu;
                $contrasenia = $contra;
            
                //API url:
                $url = 'https://csi.clarisa.co:8443/seguridad/rest/api/v1/login/';
            
                //JSON
                $data = array(
                    'usuario' => $usuario,
                    'contrasenia' => $contrasenia
                );
            
                //configuraciones del json
                $options = array(
                    'http' => array(
                        'header' => "Content-Type: application/json",
                        'method' => 'POST',
                        'content' => json_encode($data)
                    )
                );
            
                //engine:
                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                if ($result === FALSE) { 
                }
            
                $resultData = json_decode($result, TRUE);
                $re = $resultData["data"];
                $token = $re["token"];
            
                $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
                $resultTkn = $mysqli->query($sqlEdiTknn);
                if ($resultTkn == false) {
                    echo "ERROR";
                } else {
                    
                } 
            
                return $token;
            }
    
        
        
                function nomina($datosCodificados, $token, $mysqli, $tercero,$idMes,$fechaActual,$usuarioEnv) {
        
                    $curl = curl_init();
                    
                    curl_setopt_array($curl, [
                        CURLOPT_PORT => "8443",
                        CURLOPT_URL => "https://csi.clarisa.co:8443/nomina/rest/api/v1/nominaIndividual",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $datosCodificados,
                        CURLOPT_COOKIE => "JSESSIONID=T-BnFgT-56AicuIZjUdJwv5k6ZwZBOi_UAu0olV7.ip-172-31-9-34",
                        CURLOPT_HTTPHEADER => [
                            "Content-Type: application/json",
                            "Authorization: '".$token."'"
                        ],
                    ]);
                
                     $response = curl_exec($curl);
                     $err = curl_error($curl);
                
                    curl_close($curl);
                
                    if ($err) {
                    
                         $Mensaje = $err;
                    } else {
                        $solu = json_decode($response, true);
                
                        if(is_null($solu)){
                            $Mensaje = '';
                        }else{
                
                        $soluc2 = $solu['textResponse'];
                
                        if ($soluc2 == "Nómina individual creada exitosamente") {
                          $soluc3 = $solu['data'];
                          $solu4 = $soluc3['cune'];
                          if  ($solu4!=null) {
                            $fecha = date("Y-m-d");
                            $hora = date("H:i:s");
                            $valJson=json_decode($datosCodificados);
                            $sqledit = "UPDATE gn_nomina_electronica set   fecha_envio ='$fechaActual',  usuario_envio ='$usuarioEnv',cune = '$solu4',  json ='$datosCodificados' WHERE tercero = '$tercero' AND mes='$idMes'"; 
                            if ($mysqli->query($sqledit) == true) {
                               $sqlId="SELECT MAX(id_unico) FROM gn_nomina_electronica";
                               $ultmId = $mysqli->query($sqlId);
                               $rowId=mysqli_fetch_row($ultmId);
                               $id_nomina=$rowId[0];
                               $agr = agregarNominaE($id_nomina);
                            }
                            if ($mysqli->query($sqledit) != true) {
                                echo "Error";
                            }  
        
                            $Mensaje = $soluc2 . " " . $solu4;
                        }else{
                            $sqlEli="DELETE
                            FROM gn_nomina_electronica  
                            WHERE tercero=$tercero
                            AND mes=$idMes";
                            $resultEli=$mysqli->query($sqlEli);   
                            if ($resultEli != true) {
                                echo "Error Eliminar";
                            }

                          $Mensaje="La Dian retorna mensaje de creada exitosamente, pero no retorna codigo CUNE,este problema se asocia directamente al sistema de ellos."
                        }
                        } else {

                            $sqlEli="DELETE
                            FROM gn_nomina_electronica  
                            WHERE tercero=$tercero
                            AND mes=$idMes";
                            $resultEli=$mysqli->query($sqlEli);   
                            if ($resultEli != true) {
                                echo "Error Eliminar";
                            }
                             $error1 = $solu['errores'];
                            $error2 = $error1['errores'];
                            $error3 = $error2[0];
                            $Error5 = $error3['codError'];
                            $Error4 = $error3['errorMessage'];
                            $Mensaje = $soluc2 . " " . $Error4 . " " . $Error5;
                        }
                    }
                }
                    return $Mensaje;
                }
                
       

        ?>

        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="../css/bootstrap.min.css">
                <link rel="stylesheet" href="../css/style.css">
                <script src="../js/md5.pack.js"></script>
                <script src="../js/jquery.min.js"></script>
                <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
                <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
            </head>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p> <?=$Mensaje; ?> </p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div> 
            </div>
        </div>

        <div class="modal fade" id="myModalEm" role="dialog" align="center" >
            <div class="modal-dialog" style="width:980px">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 12px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-left: -200px;width:700px;margin-rigth: -200px">
                        <h1 style="font-size: 18;">Este empleado ya ha sido enviado este mes, con el prefijo<?php echo ' '.$prefijoAlert.',consecutivo '.$consecutivoAlert.' y codigo cune '.$cuneAlert; ?> </h1>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div> 
            </div>
        </div>

        <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
        <script src="../js/bootstrap.min.js"></script>

        <?php if ($empYa==true) { ?>
            <script type="text/javascript">
        $("#myModalEm").modal('show');
                $("#ver2").click(function () {
                    $("#myModalEm").modal('hide');
                     window.history.go(-1);
                });
            </script>
            <?php 
            }else{
                ?>
                <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function () {
                    $("#myModal1").modal('hide');
                     window.history.go(-1);
                });
            </script>

        <?php 
            }  
            ?>
            

