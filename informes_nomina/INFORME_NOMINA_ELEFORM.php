<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();
$calendario = CAL_GREGORIAN;
$idAnno=$_SESSION['anno'];
$annno   = $_SESSION['anno'];
$sqlAnnio="SELECT anno FROM gf_parametrizacion_anno
WHERE id_unico=$annno";
$rAnnio= $mysqli->query($sqlAnnio);
$reAnno = mysqli_fetch_row($rAnnio);
$anno=$reAnno[0];
@$mes = $_GET['sltmesi'];
$sqlMes="SELECT id_unico FROM gf_mes
WHERE numero=$mes
AND parametrizacionanno=$idAnno";
$resultMes = $mysqli->query($sqlMes);
$rowMes = $resultMes->fetch_assoc();
$idMes = $rowMes['id_unico'];
@$tercero = $_GET['tercero'];
$diaF = cal_days_in_month($calendario, $mes , $anno); 
$fechaInicial= "'$anno-$mes-01'";
$fechaFinal= "'$anno-$mes-$diaF'";
$fechaInicial1= "$anno-$mes-01";
$fechaFinal1= "$anno-$mes-$diaF";
$paramA     = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuarioEnv = $_SESSION['usuario'];
$fechaActual=date('Y-m-d h:i:s');



    //---Consulta para datos empleado----//
    $sqlEm=" SELECT e.id_unico,t.numeroidentificacion, t.nombreuno AS primernombre, t.nombredos AS otronombre, t.apellidouno AS  primerapellido,
    t.apellidodos AS segundoapellido,ti.codigo_fe as tipoidentificacion,e.tipo_riesgo AS riesgopension, e.salInt AS salariointegral,
    c.codigo_dian as ciudad,d.direccion,vr.fechaacto as fechaingreso,(SELECT fechaacto FROM gn_vinculacion_retiro where estado=2 AND empleado=e.id_unico LIMIT 1) AS fecharetiro,tc.equivalente_NE as tipocuenta, cb.numerocuenta, cat.salarioactual,te.equivalente_NE as tipotrabajador,te.equivalenteSubtipoTrabajador_NE as subtipotrabajador,tcn.equivalente_NE as tipocontrato,tpne.equivalente_NE as periodoNomina,mp.equivalente_NE as mediopago,t1.equivalente_NE as entidadbancaria
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
    


    #------------------------------------------------------------------------#
    



    #------------------------------------------------------------------------#
    




    #------------------------------------------------------------------------#
    

    #------------------------------------------------------------------------#
    


    #------------------------------------------------------------------------#
   


    #------------------------------------------------------------------------#
    

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



    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    { 
        function Header(){ 
            global $fechaFinal;
            global $fechaInicial;
            $this->SetY(10);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode('INFORME DE DATOS NOMINA ELECTRONICA POR MES'),0,0,'C');
            $this->ln(5);
            $this->Cell(200,5,('Del '.$fechaInicial.' Al  '.$fechaFinal),0,0,'C');
            $this->ln(5);
        }    
             
    }
    $pdf = new PDF('P','mm','Letter');   
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetY(20);
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln();
    $pdf->Cell(200,5,("NOMINA ELECTRONICA"),1,0,"C");
    $pdf->Ln();
    $pdf->Cell(70,5,("NOMBRE EMPLEADO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[2].' '.$rowE[3].' '.$rowE[4].' '.$rowE[5]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("TIPO IDENTIFICACION"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[6]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("ALTO RIESGO PENSION"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[7]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("SALARIO INTEGRAL"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[8]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("CIUDAD"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[9]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("TIPO CUENTA"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[13]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("NUMERO CUENTA"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[14]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("SALARIO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,'$'.utf8_decode(ucwords(mb_strtolower(number_format($rowE[15])))),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("TIPO TRABAJADOR"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[16]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("SUBTIPO TRABAJADOR"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[17]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("TIPO CONTRATO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[18]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("PERIODO NOMINA"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[19]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("METODO PAGO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[20]),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("ENTIDAD BANCARIA"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[21]),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("DEVENGADOS"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(70,5,("AUXILIO TRANSPORTE"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,'$'.utf8_decode(ucwords(mb_strtolower(number_format($auxTransporte)))),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("SUELDO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,'$'.utf8_decode(ucwords(mb_strtolower(number_format($sueldo)))),1,0);
    $pdf->Ln();
    $pdf->Cell(70,5,("DIAS TRABAJADOS"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($diasTrabajados),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("HORAS EXTRAS"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
//Consulta para horas extras diurnas ordinarias
    $sqlHEDO="SELECT c.equivalente_NE as tipo,round(n.valor,0) AS valor,round(n1.valor,0) as cantidad
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
    $tipoHED=$rowHED[0];
    $pdf->Cell(200,5,("Equivalente Dian=H_EXTRA_DIURNA-Codigo Concepto=050"),1);

    $pdf->SetFont('Arial','',10);   
    $pdf->Ln();
        while($rowHED=mysqli_fetch_row($horasD)){
            $tipoHED=$rowHED[0];
            $valorHED1=$rowHED[1];
            $horasExtrasDiurnas1=$rowHED[2];
            $valorHED = (int)$valorHED1;
            $horasExtrasDiurnas = (int)$horasExtrasDiurnas1;
            $pdf->Cell(70,5,('Tipo NE: '.$tipoHED),1,0); 
            $pdf->Cell(70,5,('Valor: '.$valorHED),1,0);
            $pdf->Cell(60,5,('Cantidad: '.$horasExtrasDiurnas),1,0);
            $pdf->Ln();
            $valorDF+=$valorHED;
        }
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,('Total: '.$valorDF),1,0);
    $pdf->Ln();


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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_EXTRA_NOCTURNA-Codigo Concepto=051"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
        while($rowHEN=mysqli_fetch_row($horasN)){
            $tipoHEN=$rowHEN[0];
            $valorHEN1=$rowHEN[1];
            $valorHEN=(int)$valorHEN1;
            $horasExtrasNocturnas1=$rowHEN[2];
            $horasExtrasNocturnas=(int)$horasExtrasNocturnas1;
            $pdf->Cell(70,5,('Tipo NE: '.$tipoHEN),1,0); 
            $pdf->Cell(70,5,('Valor: '.$valorHEN),1,0);
            $pdf->Cell(60,5,('Cantidad: '.$horasExtrasNocturnas),1,0);
            $pdf->Ln();
            $valorHENTo+=$valorHEN;
        }
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,('Total: '.$valorHENTo),1,0);
    $pdf->Ln();


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
                    AND p1.parametrizacionanno=$paramA
                    GROUP BY c.id_unico";
    $horasR=$mysqli->query($sqlHERN);

    $valorHERTo=0;


    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_RECARGO_NOCTURNA-Codigo Concepto=052"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
    while($rowHER=mysqli_fetch_row($horasR)){
        $tipoHER=$rowHER[0];
        $valorHER1=$rowHER[1];
        $valorHER=(int)$valorHER1;
        $horasExtrasReNocturnas1=$rowHER[2];
        $horasExtrasReNocturnas=(int)$horasExtrasReNocturnas1;
        $pdf->Cell(70,5,('Tipo NE: '.$tipoHER),1,0); 
        $pdf->Cell(70,5,('Valor: '.$valorHER),1,0);
        $pdf->Cell(60,5,('Cantidad: '.$horasExtrasReNocturnas),1,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $valorHERTo+=$valorHER;
    }
    $pdf->Cell(200,5,('Total: '.$valorHERTo),1,0);
    $pdf->Ln();


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

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_EXTRA_DIURNA_DOM_FEST-Codigo Concepto=055"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
    while($rowHEDD=mysqli_fetch_row($horasDD)){
        $tipoHEDDF=$rowHEDD[0];
        $valorHEDDF1=$rowHEDD[1];
        $valorHEDDF=(int)$valorHEDDF1;
        $horasExtrasDiurnaDomFes1=$rowHEDD[2];
        $horasExtrasDiurnaDomFes=(int)$horasExtrasDiurnaDomFes1;
        $pdf->Cell(70,5,('Tipo NE: '.$tipoHEDDF),1,0); 
        $pdf->Cell(70,5,('Valor: '.$valorHEDDF),1,0);
        $pdf->Cell(60,5,('Cantidad: '.$horasExtrasDiurnaDomFes),1,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $valorHEDDFTo+=$valorHEDDF;
    }

    $pdf->Cell(200,5,('Total: '.$valorHEDDFTo),1,0);
    $pdf->Ln();




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

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_REC_DIURNO_DOM_FEST-Codigo Concepto=053"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
    while ($rowERDF=mysqli_fetch_row($horasERDF)) {
        $tipoHEDD=$rowERDF[0];
        $valorHEDD1=$rowERDF[1];
        $valorHEDD=(int)$valorHEDD1;
        $horasExtrasRecargoDiurnoDomFes1=$rowERDF[2];
        $horasExtrasRecargoDiurnoDomFes=(int)$horasExtrasRecargoDiurnoDomFes1;
        $pdf->Cell(70,5,('Tipo NE: '.$tipoHEDD),1,0); 
        $pdf->Cell(70,5,('Valor: '.$valorHEDD),1,0);
        $pdf->Cell(60,5,('Cantidad: '.$horasExtrasRecargoDiurnoDomFes),1,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $valorHEDDTo+=$valorHEDD;
    }
    $pdf->Cell(200,5,('Total: '.$valorHEDDTo),1,0);
    $pdf->Ln();


   
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_EXT_NOCT_DOM_FEST-Codigo Concepto=060"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
    while ($rowHENDF=mysqli_fetch_row($horasHENDF)) {
        $tipoHENDF=$rowHENDF[0];
        $valorHENDF1=$rowHENDF[1];
        $valorHENDF=(int)$valorHENDF1;
        $horasExtrasNocturnaDomFes1=$rowHENDF[2];
        $horasExtrasNocturnaDomFes=(int)$horasExtrasNocturnaDomFes1;
        $pdf->Cell(70,5,('Tipo NE: '.$tipoHENDF),1,0); 
        $pdf->Cell(70,5,('Valor: '.$valorHENDF),1,0);
        $pdf->Cell(60,5,('Cantidad: '.$horasExtrasNocturnaDomFes),1,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $valorHENDFTo+=$valorHENDF;
    }

    $pdf->Cell(200,5,('Total: '.$valorHENDFTo),1,0);
    $pdf->Ln();


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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Equivalente Dian=H_REC_NOCT_DOM_FEST-Codigo Concepto=054"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Ln();
    while ($rowHRNDF=mysqli_fetch_row($horasHRNDF)) {
        $tipoHRNDF=$rowHRNDF[0];
        $valorHRNDF1=$rowHRNDF[1];
        $valorHRNDF=(int)$valorHRNDF1;
        $horasExtrasRecargoNocturnoDomFes1=$rowHRNDF[2];
        $horasExtrasRecargoNocturnoDomFes=(int)$horasExtrasRecargoNocturnoDomFes1;
        $pdf->Cell(70,5,('Tipo NE: '.$tipoHRNDF),1,0); 
        $pdf->Cell(70,5,('Valor: '.$valorHRNDF),1,0);
        $pdf->Cell(60,5,('Cantidad: '.$horasExtrasRecargoNocturnoDomFes),1,0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Ln();
        $valorHRNDFTo+=$valorHRNDF;
    }
    
    $pdf->Cell(200,5,('Total: '.$valorHRNDFTo),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(170,5,("Total Horas Extras"),1,0,"C");

     $valorTotalHoras=$valorDF+$valorHENTo+$valorHERTo+$valorHEDDFTo+$valorHEDDTo+$valorHENDFTo
    +$valorHRNDFTo;
    
    $pdf->Cell(30,5,($valorTotalHoras),1,0,"C");
    $pdf->Ln();
    $pdf->Cell(200,5,("VACACIONES"),1,0,"C");
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln();
    if($fechaInicio!=null || $fechaFin!=null || $dias!=null  ){
    $pdf->Cell(200,5,("NORMALES"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicio),1);
    $pdf->Cell(40,5,('Fecha Fin: '.$fechaFin),1);
    $pdf->Cell(40,5,('Dias: '.$dias),1);
    $pdf->Cell(40,5,('Tipo Vac: '.$tipoVac),1);
    $pdf->Cell(40,5,('Valor Vac: '.$valorVacaciones),1);
    $pdf->Ln();
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
    if ($diasPrima!=null) {
        $diasPrima=$diasPrima;
    }else{
        $diasPrima=0;
    }

            
        
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("PRIMAS"),1,0,"C");
    $pdf->Ln();
    if($diasPrima!=null || $pagoPrima!=null || $dias!=null  ){
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(60,5,('CANTIDAD DIAS: '.$diasPrima),1);
    $pdf->Cell(60,5,('PAGO: '.$pagoPrima),1);
    $pdf->Cell(80,5,('PAGO No Salarial: '.$pagoNSPrima),1);
    $pdf->Ln();
    }

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("INCAPACIDADES"),1,0,"C");
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln();

    
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
//valor vacaciones
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
        $pdf->Cell(200,5,("COMUN"),1);
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40,5,('Tipo: '.$tipoIncaComun),1);
        $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialIncaComun),1);
        $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinIncaComun),1);
        $pdf->Cell(40,5,('Cantidad Dias: '.$diasIncaComun),1);
        $pdf->Cell(40,5,('Pago: '.$valorIncaComun),1);
        $pdf->Ln();
        $valorIncaComunTo+=$valorIncaComun;
    }

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
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(200,5,("LABORAL"),1);
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40,5,('Tipo: '.$tipoIncaLaboral),1);
        $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialIncaLaboral),1);
        $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinIncaLaboral),1);
        $pdf->Cell(40,5,('Cantidad Dias: '.$diasIncaLaboral),1);
        $pdf->Cell(40,5,('Pago: '.$valorIncaLaboral),1);
        $pdf->Ln();
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
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(200,5,("PROFESIONAL"),1);
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40,5,('Tipo: '.$tipoIncaProfesional),1);
        $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialIncaProfesional),1);
        $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinIncaProfesional),1);
        $pdf->Cell(40,5,('Cantidad Dias: '.$diasIncaProfesional),1);
        $pdf->Cell(40,5,('Pago: '.$valorIncaProfesional),1);
        $pdf->Ln();
        $valorIncaProfesionalTo+=$valorIncaProfesional;
    }

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("LICENCIAS"),1,0,"C");
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln();

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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Licencia de maternidad o paternidad"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(40,5,('Tipo: '.$tipoLicenciaMP),1);
    $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialLicenciaMP),1);
    $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinLicenciaMP),1);
    $pdf->Cell(40,5,('Cantidad Dias: '.$diasLicenciaMP),1);
    $pdf->Cell(40,5,('Pago: '.$valorLicenciaMP),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Licencia remunerada"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(40,5,('Tipo: '.$tipoLicenciaR),1);
    $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialLicenciaR),1);
    $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinLicenciaR),1);
    $pdf->Cell(40,5,('Cantidad Dias: '.$diasLicenciaR),1);
    $pdf->Cell(40,5,('Pago: '.$valorLicenciaR),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Licencia No remunerada"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(40,5,('Tipo: '.$tipoLicenciaNR),1);
    $pdf->Cell(40,5,('Fecha Inicio: '.$fechaInicialLicenciaNR),1);
    $pdf->Cell(40,5,('Fecha Fin: '.$fechaFinLicenciaNR),1);
    $pdf->Cell(40,5,('Cantidad Dias: '.$diasLicenciaNR),1);
    $pdf->Cell(40,5,('Pago: '.$valorLicenciaNR),1);
    $pdf->Ln();
    $valorLicenciaNRTo+=$valorLicenciaNR;
}

$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("OTROS DEVENGOS"),1,0,"C");
$pdf->Ln();
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
while ($rowViaticoManu=mysqli_fetch_row($viaticoManu)) {
    $valorViaticoManu1=$rowViaticoManu[0];
    $tipoViaticoManu=$rowViaticoManu[1];
    $valorViaticoManu=(int)$valorViaticoManu1;
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Viático manutención y alojamiento=VIATICO_MANU_ALOJ_S"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoViaticoManu),1);
    $pdf->Cell(100,5,('Valor: '.$valorViaticoManu),1);
    $pdf->Ln();
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

while ($rowViaticoManuNS=mysqli_fetch_row($viaticoManuNS)) {
    $valorViaticoManuNS1=$rowViaticoManuNS[0];
    $tipoViaticoManuNS=$rowViaticoManuNS[1];
    $valorViaticoManuNS=(int)$valorViaticoManuNS1;
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Viático manutención y alojamiento no salarial=VIATICO_MANU_ALOJ_NS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoViaticoManuNS),1);
    $pdf->Cell(100,5,('Valor: '.$valorViaticoManuNS),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Bonificación salarial=BONIFICACION_S"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoBoniS),1);
    $pdf->Cell(100,5,('Valor: '.$tipoBoniS),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Bonificación no salarial=BONIFICACION_NS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoBoniNS),1);
    $pdf->Cell(100,5,('Valor: '.$valorBoniNS),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Auxilios no salariales=AUXILIO_NS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoAuxiliosNS),1);
    $pdf->Cell(100,5,('Valor: '.$valorAuxiliosNS),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Compensaciones ordinarias=COMPENSACION_O"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoCompensacionO),1);
    $pdf->Cell(100,5,('Valor: '.$valorCompensacionO),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Compensaciones extraordinarias=COMPENSACION_E"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoCompensacionEX),1);
    $pdf->Cell(100,5,('Valor: '.$valorCompensacionEX),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Valor que el trabajador recibe como concepto salarial=PAGO_S"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoConceptoSAL),1);
    $pdf->Cell(100,5,('Valor: '.$valorConceptoSAL),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Valor que el trabajador recibe como concepto no salarial=PAGO_NS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoConceptoNSAL),1);
    $pdf->Cell(100,5,('Valor: '.$valorConceptoNSAL),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Alimentación salarial=PAGO_ALIMENTACION_S"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoAlimentacionSAL),1);
    $pdf->Cell(100,5,('Valor: '.$valorAlimentacionSAL),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Alimentación no salarial=PAGO_ALIMENTACION_NS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoAlimentacionNSAL),1);
    $pdf->Cell(100,5,('Valor: '.$valorAlimentacionNSAL),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Pago tercero=PAGO_TERCERO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoPagoTer),1);
    $pdf->Cell(100,5,('Valor: '.$valorPagoTer),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Dotación=DOTACION"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoDotacion),1);
    $pdf->Cell(100,5,('Valor: '.$valorDotacion),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Teletrabajo=TELETRABAJO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoTeletrabajo),1);
    $pdf->Cell(100,5,('Valor: '.$valorTeletrabajo),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Reintegro =REINTEGRO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoReintegroDevengos),1);
    $pdf->Cell(100,5,('Valor: '.$valorReintegroDevengos),1);
    $pdf->Ln();
    $valorReintegroDevengosTo+=$valorReintegroDevengos;
}


$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("DEDUCCIONES"),1,0,"C");
$pdf->SetFont('Arial','B',10);
$pdf->Ln();
 //Pago Salud
    $sqlSalud="SELECT n.valor AS valor
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
    while($rowPagoSalud=mysqli_fetch_row($saludP)){
    $valorSalud1=$rowPagoSalud[0];
    $valorSalud=(int)$valorSalud1;
    $valorSaludTo+=$valorSalud;
    }
    //Pago Pension
    $sqlPension="SELECT n.valor AS valor
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
    while($rowPagoPension=mysqli_fetch_row($pensionP)){
    $valorPension1=$rowPagoPension[0];
    $valorPension=(int)$valorPension1;
    $valorPensionTo+=$valorPension;
    }
$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("Pago Salud"),1);
$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Cell(200,5,('Valor: '.$valorSaludTo),1);
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("Pago Pension"),1);
$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Cell(200,5,('Valor: '.$valorPensionTo),1);
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("Deducciones porcentuales"),1,0,"C");
$pdf->SetFont('Arial','B',10);
$pdf->Ln();

  //Consulta para Deducciones Porcentuales
        //Fondo de solidaridad pensional/Porcentajes?
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
while ( $rowDeduccionSP=mysqli_fetch_row($deduccionSP)) {

    
        $valorDeduccionSP1=$rowDeduccionSP[0];
        $tipoDeduccionSP=$rowDeduccionSP[1]; 
        $valorDeduccionSP=(int)$valorDeduccionSP1;
        $porcentajeDeduccionSP=1;
         $pdf->SetFont('Arial','B',10);
         $pdf->Cell(200,5,("Fondo de solidaridad pensional =DEDUCCION_SP"),1);
         $pdf->Ln();
         $pdf->SetFont('Arial','',10);
         $pdf->Cell(60,5,('Tipo: '.$tipoDeduccionSP),1);
         $pdf->Cell(60,5,('Porcentaje: '.$porcentajeDeduccionSP),1);
          $pdf->Cell(80,5,('Valor Deduccion: '.$valorDeduccionSP),1);
         $pdf->Ln();
         $valorDeduccionSPTo+=$valorDeduccionSP;

}
       

        //Fondo de subsistencia/Porcentajes?
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
while ( $rowDeduccionSub=mysqli_fetch_row($deduccionSub)) {
  
        $valorDeduccionSub1=$rowDeduccionSub[0];
        $tipoDeduccionSub=$rowDeduccionSub[1]; 
        $valorDeduccionSub=(int) $valorDeduccionSub1;
        $porcentajeDeduccionSub=1;
         $pdf->SetFont('Arial','B',10);
         $pdf->Cell(200,5,("Fondo de subsistencia =DEDUCCION_SUB"),1);
         $pdf->Ln();
         $pdf->SetFont('Arial','',10);
         $pdf->Cell(60,5,('Tipo: '.$tipoDeduccionSub),1);
         $pdf->Cell(60,5,('Porcentaje: '.$porcentajeDeduccionSub),1);
          $pdf->Cell(80,5,('Valor Deduccion: '.$valorDeduccionSub),1);
         $pdf->Ln();
         $valorDeduccionSubTo+=$valorDeduccionSub;
}
      

        //Sindicato/Porcentajes?
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
        while( $rowDeduccionSin=mysqli_fetch_row($deduccionSin)){
     
        $valorDeduccionSin1=$rowDeduccionSin[0];
        $tipoDeduccionSin=$rowDeduccionSin[1]; 
        $valorDeduccionSin=(int) $valorDeduccionSin1;
        $porcentajeDeduccionSin=1;
         $pdf->SetFont('Arial','B',10);
         $pdf->Cell(200,5,("Sindicato =SINDICATO"),1);
         $pdf->Ln();
         $pdf->SetFont('Arial','',10);
         $pdf->Cell(60,5,('Tipo: '.$tipoDeduccionSin),1);
         $pdf->Cell(60,5,('Porcentaje: '.$porcentajeDeduccionSin),1);
          $pdf->Cell(80,5,('Valor Deduccion: '.$valorDeduccionSin),1);
         $pdf->Ln();
         $valorDeduccionSinTo+=$valorDeduccionSin;
        }




$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("Deducciones Valor"),1,0,"C");
$pdf->SetFont('Arial','B',10);
$pdf->Ln();


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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Sanción pública =SANCION_PUBLIC"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoSancionP),1);
    $pdf->Cell(100,5,('Valor: '.$valorSancionP),1);
    $pdf->Ln();
    $valorSancionPTo+=$valorSancionP;
}



//Sanción privada
$sqlSancionPri="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                 FROM gn_novedad n 
                 LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                 LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                 WHERE c.equivalente_NE='SANCION_PRIV'
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Sanción privada =SANCION_PRIV"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoSancionPri),1);
    $pdf->Cell(100,5,('Valor: '.$valorSancionPri),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Pago tercero =PAGO_TERCERO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoPagoTerDe),1);
    $pdf->Cell(100,5,('Valor: '.$valorPagoTerDe),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Anticipo =ANTICIPO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoAnticipo),1);
    $pdf->Cell(100,5,('Valor: '.$valorAnticipo),1);
    $pdf->Ln();
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
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(200,5,("Otra deducción =OTRA_DEDUCCION"),1);
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100,5,('Tipo: '.$tipoOtraDed),1);
        $pdf->Cell(100,5,('Valor: '.$valorOtraDed),1);
        $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Pensión voluntaria =PENSION_VOLUNTARIA"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoPensionV),1);
    $pdf->Cell(100,5,('Valor: '.$valorPensionV),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Retención en la fuente =RETENCION_FUENTE"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoRetenF),1);
    $pdf->Cell(100,5,('Valor: '.$valorRetenF),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Ahorro fomento a la construcción =AFC"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoFomentoC),1);
    $pdf->Cell(100,5,('Valor: '.$valorFomentoC),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Cooperativa =COOPERATIVA"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoCooperativa),1);
    $pdf->Cell(100,5,('Valor: '.$valorCooperativa),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Embargo fiscal =EMBARGO_FISCAL"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoEmbargoF),1);
    $pdf->Cell(100,5,('Valor: '.$valorEmbargoF),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Planes complementarios =PLAN_COMPLEMENTARIOS"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoPlanesC),1);
    $pdf->Cell(100,5,('Valor: '.$valorPlanesC),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Educación =EDUCACION"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoEducacion),1);
    $pdf->Cell(100,5,('Valor: '.$valorEducacion),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Reintegro =REINTEGRO"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoReintegro),1);
    $pdf->Cell(100,5,('Valor: '.$valorReintegro),1);
    $pdf->Ln();
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
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("Deuda =DEUDA"),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,('Tipo: '.$tipoDeuda),1);
    $pdf->Cell(100,5,('Valor: '.$valorDeuda),1);
    $pdf->Ln();
    $valorDeudaTo+=$valorDeuda;
}
$deduccionTotal=$valorSaludTo+$valorPensionTo+$valorSancionPTo+$valorSancionPriTo+$valorPagoTerDeTo
+$valorAnticipoTo+$valorOtraDedTo+$valorRetenFTo+$valorFomentoCTo+$valorCooperativaTo+$valorEmbargoFTo
+$valorPlanesCTo+$valorEducacionTo+$valorReintegroTo+$valorDeudaTo+$valorDeduccionSPTo+ $valorDeduccionSinTo+$valorDeduccionSubTo;
$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,5,("Valor Total Deduccionnes: ".$deduccionTotal),1);
//$pdf->Ln();
//$pdf->Cell(200,5,($empleado),1);
//$pdf->Ln();
//$pdf->MultiCell(200,5,utf8_decode($sqlAuxT) ,0,'L');
//$pdf->Ln();


    ob_end_clean();     
    $pdf->Output(0,'eje.pdf',0);

    



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
   



//Consulta para Licencias 





    //Otros Conceptos
    //Otros Devengados



    //Consultas de deducciones 
   
    //Consulta para Deducciones Porcentuales
    //Fondo de solidaridad pensional/Porcentajes?
    /* $sqlDeducSP="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                    LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                    WHERE c.equivalente_NE='DEDUCCION_SP'
                    AND n.empleado=$rowE[0]
                    AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $deduccionSP=$mysqli->query($sqlDeducSP);
    $rowDeduccionSP=mysqli_fetch_row($deduccion);
    $valorDeduccionSP=$rowDeduccionSP[0];
    $tipoDeduccionSP=$rowDeduccionSP[1]; */

    //Fondo de subsistencia/Porcentajes?
    /* $sqlDeducSub="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                    LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                    WHERE c.equivalente_NE='DEDUCCION_SUB'
                    AND n.empleado=$rowE[0]
                    AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $deduccionSub=$mysqli->query($sqlDeducSub);
    $rowDeduccionSub=mysqli_fetch_row($deduccionSub);
    $valorDeduccionSub=$rowDeduccionSub[0];
    $tipoDeduccionSub=$rowDeduccionSub[1]; */
    //Sindicato/Porcentajes?
    /* $sqlDeducSin="SELECT n.valor AS valor,c.equivalente_NE AS tipo
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                    LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                    WHERE c.equivalente_NE='SINDICATO'
                    AND n.empleado=$rowE[0]
                    AND p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                    AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                    AND p.parametrizacionanno=$paramA";

    $deduccionSin=$mysqli->query($sqlDeducSin);
    $rowDeduccionSin=mysqli_fetch_row($deduccionSin);
    $valorDeduccionSin=$rowDeduccionSin[0];
    $tipoDeduccionSin=$rowDeduccionSin[1]; */

echo $sueldo;

?>