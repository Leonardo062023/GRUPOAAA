<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require '../jsonNomina/funcionesNomina.php';
@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];

$empleado   = $_REQUEST['sltEmpleado'];  
$periodo    = $_REQUEST['sltPeriodo'];  

$rowp        = $con->Listar("SELECT p.fechainicio, p.fechafin, p.dias_nomina, pa.anno, month(p.fechainicio), year(p.fechainicio)-1  FROM  gn_periodo p 
    LEFT JOIN gf_parametrizacion_anno pa oN p.parametrizacionanno = pa.id_unico 
 WHERE p.id_unico = $periodo");
$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        
$diasPeriodo = $rowp[0][2];        
$annoa       = intval($rowp[0][5]);
#Validar Retiro entre fechas 
$rowref = $con->Listar("SELECT * FROM gn_vinculacion_retiro WHERE empleado = $empleado  AND fechaacto BETWEEN '$fechaInicio' and '$fechaFin'");
if(count($rowref)>0) {
    
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico)) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), et.tipo, e.cesantias
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
        LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
        WHERE e.id_unico = $empleado 
        ORDER BY e.id_unico"); 

    $tipoc      = $rowe[0][7];

    #* Parametros 
    $rowP = $con->Listar("SELECT id_unico, vigencia, salmin, tope_aux_transporte, auxt, talimentacion, primaA, tipo_liquidaciond, dias_primav, dias_prima_servicio, dias_prima_navidad 
        FROM gn_parametros_liquidacion WHERE vigencia = $panno AND tipo_empleado=".$rowe[0][6]);

    $pid        = $rowP[0][0]; 
    $pvi        = $rowP[0][1]; 
    $psm        = $rowP[0][2]; 
    $ptat       = $rowP[0][3]; 
    $pat        = $rowP[0][4];
    $ptaa       = $rowP[0][5];
    $paa        = $rowP[0][6];
    $diasps     = $rowP[0][9];
    $diaspnv    = $rowP[0][10];
    


    #* Eliminar COnceptos 
    $ld = "DELETE  n.* FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.periodo = $periodo AND n.empleado = $empleado AND n.aplicabilidad in(1,2,3) ";
    $resultado1 = $mysqli->query($ld);


    #Ingreso 
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");

    $rowsal = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 2 
        ORDER BY fechaacto  DESC LIMIT 1");
    $fechaIngreso = $rowing[0][0];
    if(empty($rowsal[0][0])){
        $fechaSalida  = $fechaFin;
    } else {
        $fechaSalida  = $rowsal[0][0];    
    }


    $fing = new DateTime($fechaIngreso);
    $fsal = new DateTime($fechaSalida);


    #* Dias Trabajados En El Periodo 
    $diasTP = diasTPeriodo($empleado, $periodo);

    #DIAS TRABAJADOS
    $diasTotales = diasTrabajados($fechaIngreso, $fechaSalida);
    $id_conceptodt = id_concepto('007');
    guardarNovedad($diasTotales , $empleado, $periodo, $id_conceptodt);

    #Auxilio Transporte
    guardarActual($empleado,'953', $periodo );
    #* Auxilio Alimentación
    guardarActual($empleado,'1005', $periodo );


    #TIPO DE LIQUIDACIÍON

    #Prima Navidad
    $id_cpn     =  id_concepto('158');
    $valorpn    = doceavaAcumulado($empleado,$id_cpn,$periodo, $fechaIngreso);
    $id_cpnf    =  id_concepto('1011');
    if($valorpn!=0){
        guardarNovedad($valorpn, $empleado, $periodo, $id_cpnf);    
    }

    #Prima Vacaciones
    $id_cpv     =  id_concepto('175');

    if($rowP[0][7]=='Oficiales'){ 
        $valorpv    = ultimoValor($empleado,$id_cpv,$periodo, $fechaIngreso);
    } else {
        $valorpv    = doceavaAcumulado($empleado,$id_cpv,$periodo, $fechaIngreso);
    }
    
    $id_cpvf    =  id_concepto('804');
    if($valorpv!=0){
        guardarNovedad($valorpv, $empleado, $periodo, $id_cpvf);    
    }


    #Prima Semestral
    $id_cps     =  id_concepto('160');
    $valorps    = doceavaAcumulado($empleado,$id_cps,$periodo, $fechaIngreso);
    $id_cpsf    =  id_concepto('1002');
    if($valorps!=0){
        guardarNovedad($valorps, $empleado, $periodo, $id_cpsf);    
    }

    #Prima Bonificacin por servicios prestados
    $id_cbs     =  id_concepto('161');
    $valorbs    = doceavaAcumulado($empleado,$id_cbs,$periodo, $fechaIngreso);
    $id_cbsf    =  id_concepto('956');
    if($valorbs!=0){
        guardarNovedad($valorbs, $empleado, $periodo, $id_cbsf);    
    }


    #Prima Antiguedad
    $id_cpa     =  id_concepto('150');
    $valorpa    =  ultimoValor($empleado,$id_cpa,$periodo, $fechaIngreso);
    $id_cpaf    =  id_concepto('806');
    if($valorpa!=0){
        guardarNovedad($valorpa, $empleado, $periodo, $id_cpaf);    
    }

    #Horas Extras
    $valorhe    = doceavaHE($empleado,$periodo, $fechaIngreso);
    $id_chef    =  id_concepto('1008');
    if($valorhe!=0){
        guardarNovedad($valorhe, $empleado, $periodo, $id_chef);    
    }
            


    $fechar = new DateTime($fechaSalida);
    #******CESANTIAS
    #Base Cesantías
    $salario = sueldo($empleado);
    $id_cns  =  id_concepto('001');
    guardarNovedad($salario, $empleado, $periodo, $id_cns);  

    $auxt    = valorConceptoPeriodo($empleado,$periodo, '953');
    $palim   = valorConceptoPeriodo($empleado,$periodo, '1005');

    if($tipoc==2){
        #Ultimo periodo cesantias
        $diasc  = diasPendientes(0, $empleado, $fechaIngreso, $fechar, $fechaSalida);
        #dias d
        $diasd = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            WHERE p.fechainicio BETWEEN '$fechaIngreso' AND '$fechaSalida'
            AND n.empleado = $empleado AND c.codigo = '361' AND p.id_unico != $periodo");
        if(empty($diasd[0][0])){
            $diasc = $diasc;
        } else {
            $diasc -= $diasd[0][0];
        }
        $id_dcn =  id_concepto('1030');
        guardarNovedad($diasc, $empleado, $periodo, $id_dcn);    
        
        #Salario + Prima de Navidad +Prima de vacaciones+ Prima Semestral + Bonificación Por servicios prestados + + Prima Antiguedad + Auxilio Transporte + Auxilio Alimentación 
        $valorbc = $salario+ $valorpn + $valorpv +$valorps +$valorbs +$valorpa +$valorhe + $auxt +$palim;
        $id_bcn  =  id_concepto('850');
        guardarNovedad($valorbc, $empleado, $periodo, $id_bcn);    
        #Cesantias Retroactivas 
        $vcenr      = ROUND(($valorbc * $diasc)/360);
        $id_ccn     = id_concepto('189');
        guardarNovedad($vcenr, $empleado, $periodo, $id_ccn);
        #188
        #Buscar cesantias 
        $cran = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            WHERE p.fechainicio BETWEEN '$fechaIngreso' AND '$fechaSalida'
            AND n.empleado = $empleado AND c.codigo = '189' AND p.id_unico != $periodo");
        if(empty($cran[0][0])){
            $vl188 = $vcenr;
        } else {
            $vl188 = $cran[0][0];
        }
        #Retiros
        $crret = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            WHERE p.fechainicio BETWEEN '$fechaIngreso' AND '$fechaSalida'
            AND n.empleado = $empleado AND c.codigo = '169' AND p.id_unico != $periodo");
        if(!empty($crret[0][0])){
            $vl188 -= $crret[0][0];
            $c170 = $vcenr - $crret[0][0];
            $id_c14  = id_concepto('C14');
            guardarNovedad($crret[0][0], $empleado, $periodo, $id_c14);
            
        } else {
            $c170 = $vcenr;
        }
        if($c170!=0){
            $id_ccn  = id_concepto('170');
            guardarNovedad($c170, $empleado, $periodo, $id_ccn);
        }
    }else {
        #Ultimo periodo cesantias
        $diasc  = diasPendientes(11, $empleado, $fechaIngreso, $fechar, $fechaSalida);
        $id_dcn =  id_concepto('1030');
        guardarNovedad($diasc, $empleado, $periodo, $id_dcn);    

        #Salario + Prima de Navidad +Prima de vacaciones+ Prima Semestral + Bonificación Por servicios prestados + + Prima Antiguedad + Auxilio Transporte + Auxilio Alimentación 
        $valorbc = $salario+ $valorpn + $valorpv +$valorps +$valorbs +$valorpa +$valorhe + $auxt +$palim;
        $id_bcn  =  id_concepto('850');
        guardarNovedad($valorbc, $empleado, $periodo, $id_bcn);    
        #Valor Cesantias
        $valorcn = ROUND(($valorbc * $diasc)/360);
        $id_ccn  = id_concepto('170');
        guardarNovedad($valorcn, $empleado, $periodo, $id_ccn);
        //Cesantias Ley50
        #INTERESES A LAS CESANTIAS
        $vlintc   = ROUND(($valorcn *12/100*$diasc/360),0);
        $id_cin  = id_concepto('171');
        guardarNovedad($vlintc, $empleado, $periodo, $id_cin);
    }

        

    
    #*****VACACIONES******
    #DIAS PENDIENTES VACACIONES
    $diasrv = diasPendientes(7, $empleado, $fechaIngreso, $fechar, $fechaSalida);
    $id_cfv  = id_concepto('L003');
    guardarNovedad($diasrv, $empleado, $periodo, $id_cfv);


    #Dias a Liquidar
    $diasl   = ROUND(($diasrv *21/360), 2);
    $id_cdv  = id_concepto('094');
    guardarNovedad($diasl, $empleado, $periodo, $id_cdv);

    #Indemnizacion
    if($rowP[0][7]=='Oficiales'){ 
        #Salario +Auxilio Transporte(953) + Auxilio Alimentación(1005) 
        $v_iv = ROUND(($salario + $auxt + $palim +$valorps)*$diasl/30, 0);
    } else {
        $v_iv = ROUND((($salario+ $valorps +$valorbs + $valorpa + $auxt +$palim)* $diasl/30), 0);
    }
    $id_civ  = id_concepto('176');
    guardarNovedad($v_iv, $empleado, $periodo, $id_civ);


    #**********PRIMA DE VACACIONES 
    $dbpv    = ROUND(($diasrv*$rowP[0][8]/360), 2);
    $id_cdv  = id_concepto('040');
    guardarNovedad($dbpv, $empleado, $periodo, $id_cdv);
    
    if($rowP[0][7]=='Oficiales'){ 
        #Salario +Auxilio Transporte(953) + Prima servicios(1002)+Auxilio Alimentación(1005) + Prima Navidad (1011) + Prima Antiguedad(806) 
        $valorpv2 = ROUND(($salario + $auxt + $valorps+ $palim + $valorpn + $valorpa )*$dbpv/30, 0);
    } else {
        //Salario + Prima de Servicios + Bonificacion Por Servicios P + Prima Antiguedad + Aux T + Aux A) 
        $valorpv2 = ROUND(($salario +  $valorps +$valorbs +$valorpa + $auxt +$palim)*$dbpv/30, 0); 
    }
    
    $id_cpv  = id_concepto('175');
    guardarNovedad($valorpv2, $empleado, $periodo, $id_cpv);


    #PRIMA DE SERVICIOS
    #Validar que no haya sido pagada en el mes 

    $rowlp =$con->Listar("SELECT * FROM gn_novedad n 
        left join gn_periodo p ON n.periodo = p.id_unico 
        left join gn_concepto c ON n.concepto = c.id_unico 
        left join gn_periodo pc ON pc.id_unico = $periodo 
        WHERE n.empleado = $empleado  
        and c.codigo = '160'
        and (p.fechainicio BETWEEN pc.fechainicio and pc.fechafin 
        or p.fechafin BETWEEN pc.fechainicio and pc.fechafin)") ; 
    
    if(count($rowlp)>0){
        $vprima = 0;
        $diasrs = 0;
    }else {
        #DIAS PENDIENTES PRIMA SERVICIOS
        $diasrs = diasPendientes(2, $empleado, $fechaIngreso, $fechar, $fechaSalida);
        $id_cps  = id_concepto('L004');
        guardarNovedad($diasrs, $empleado, $periodo, $id_cps);
        if($rowP[0][7]=='Oficiales'){ 
            //Dias
            $d090 = ROUND((($diasrs*$diasps)/360),2);
            $id_c90  = id_concepto('090');
            guardarNovedad($d090, $empleado, $periodo, $id_c90);
            #Salario + Prima v(804)+Auxilio Transporte(953) + Prima servicios(1002)+Auxilio Alimentación(1005) + Prima Navidad (1011) + Prima Antiguedad(806) + Horas Extrra(1008)
            $vprima = ROUND(($salario +$valorpv+ $auxt + $valorps+ $palim + $valorpn + $valorpa +$valorhe )*$d090/30, 0);
        } else {#Salario + Auxilio Transporte + Auxilio Alimentación + Bonificación Por servicios prestados +  Prima Antiguedad 
            $vprima = ROUND(($salario +$auxt +$palim +$valorbs + $valorpa)*$diasrs/360, 0);
        }
        
        $id_prs  = id_concepto('160');
        guardarNovedad($vprima, $empleado, $periodo, $id_prs);
    }

    #**PRIMA NAVIDAD
    #DIAS PENDIENTES PRIMA N
    $diasrnv = diasPendientes(8, $empleado, $fechaIngreso, $fechar, $fechaSalida);
    $id_cpnv = id_concepto('L005');
    guardarNovedad($diasrnv, $empleado, $periodo, $id_cpnv);
    if($rowP[0][7]=='Oficiales'){ 
        //Dias
        $d039 = ROUND((($diasrnv*$diaspnv)/360),2);
        $id_c39  = id_concepto('039');
        guardarNovedad($d039, $empleado, $periodo, $id_c39);
        #Salario + Prima v(804)+Auxilio Transporte(953) + Prima servicios(1002)+Auxilio Alimentación(1005) + Prima Navidad (1011) + Prima Antiguedad(806) + Horas Extrra(1008)
        $vpnavidad = ROUND(($salario +$valorpv+ $auxt + $valorps+ $palim + $valorpn + $valorpa +$valorhe )*$d039/30, 0);
    } else {
        #Salario + Prima Semestral + Auxilio Transporte + Auxilio Alimentación + Prima Antiguedad + Bonificación Por servicios prestados + Prima de vacaciones
        $vpnavidad = ROUND((($salario + $valorps + $auxt +$palim +$valorpa +$valorbs +$valorpv) * $diasrnv/360), 0);
    }
    
    $id_cpnvv  = id_concepto('158');
    guardarNovedad($vpnavidad, $empleado, $periodo, $id_cpnvv);


    #******BONIFICACION POR SERVICIOS PRESTADOS     
    if($rowP[0][7]=='Libre Nombramiento'){ 
        $diaIngreso = $rowing[0][1];
        $mesIngreso = $rowing[0][2];
        $annoIngreso = $rowing[0][3];
        #DIAS PENDIENTES BONIFICIACION 
        $upb = $con->Listar("SELECT DISTINCT p.fechafin, DATE_FORMAT( p.fechafin, '%Y') FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
        WHERE  n.empleado = $empleado  AND c.codigo = '161'
        AND p.fechainicio >='".$fechaIngreso."'
        ORDER BY p.fechainicio DESC");
        $dias =0;
        if(!empty($upb[0][0])){
            $annoBS = $upb[0][1];
            $ffv    = $annoBS.'-'.$mesIngreso.'-'.$diaIngreso;
            $europeo = true;
            list($yy1, $mm1, $dd1) = explode('-', $ffv);
            list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
            if( $dd1==31) { $dd1 = 30; }
            if(!$europeo) {
                if( ($dd1==30) and ($dd2==31) ) {
                  $dd2=30;
                } else {
                  if( $dd2==31 ) {
                    $dd2=30;
                  }
                }
            }

            //check for invalid date
            if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
              ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
              ($yy1>$yy2) ) {
                return(-1);
                }
            if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
            if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }

            //Calc
            $yy = $yy2-$yy1;
            $mm = $mm2-$mm1;
            $dd = $dd2-$dd1;
            if( $dd2==28 && $mm2=='02') {
                $dd+=2;
            }
            $diasbn += (($yy*360)+($mm*30)+$dd+1 );
        } else {
            $annoBS = $annoIngreso;
            $ffv    = $annoBS.'-'.$mesIngreso.'-'.$diaIngreso;
            $europeo = true;
            list($yy1, $mm1, $dd1) = explode('-', $ffv);
            list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
            if( $dd1==31) { $dd1 = 30; }
            if(!$europeo) {
                if( ($dd1==30) and ($dd2==31) ) {
                  $dd2=30;
                } else {
                  if( $dd2==31 ) {
                    $dd2=30;
                  }
                }
            }

            //check for invalid date
            if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
              ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
              ($yy1>$yy2) ) {
                return(-1);
                }
            if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
            if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }

            //Calc
            $yy = $yy2-$yy1;
            $mm = $mm2-$mm1;
            $dd = $dd2-$dd1;
            if( $dd2==28 && $mm2=='02') {
                $dd +=2;
            }
            $diasbn += (($yy*360)+($mm*30)+$dd+1 );
        }
        

        #Dias No trabajados 
        $id_cdnt = id_concepto('361');
        $diasnt  = valorConceptoFechas($empleado, $ffv, $fechaSalida,$id_cdnt);
        $diasbn -=$diasnt;

        $id_cpbn = id_concepto('L008');
        guardarNovedad($diasbn, $empleado, $periodo, $id_cpbn);
        if($salario>1395158){
            $bon =  ROUND(($salario*35)/100);
        } else {
            $bon =  ROUND(($salario*50)/100);
        }

        $vpbonf  = ROUND(($bon*$diasbn/360), 0);
        $id_cbns = id_concepto('161');
        guardarNovedad($vpbonf, $empleado, $periodo, $id_cbns);
    }
    #RETENCION FTE
    retencion($empleado,$periodo);
     

    #Devengos
    $dvg = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 1 and c.unidadmedida = 1");

    if(empty($dvg[0][0])){
        $tdv = 0;
    } else {
        $tdv = $dvg[0][0];   
    }
    $id_conceptotd = id_concepto('097');
    guardarNovedad($tdv , $empleado, $periodo, $id_conceptotd);

    #Descuentos
    $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 2 and c.unidadmedida = 1");
    if(empty($dv[0][0])){
        $tds = 0;
    } else {
        $tds = $dv[0][0];
    }
    $id_conceptods = id_concepto('140');
    guardarNovedad($tds , $empleado, $periodo, $id_conceptods);


    #Neto
    $np = $tdv -$tds;
    $id_conceptonp = id_concepto('144');
    $ge = guardarNovedad($np , $empleado, $periodo, $id_conceptonp);

    $rta =0;
    if(empty($ge)){
        $rta +=1;
    }   
    echo $rta;
    

} else {
    echo 0;
}