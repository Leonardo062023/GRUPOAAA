<?php

session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);
ob_start();
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

$consulta = "SELECT         lower(t.razonsocial) as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;

$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];


$periodo  = $_REQUEST['sltPeriodo'];
$grupog   = $_REQUEST['sltGrupoG'];
$unidad   = $_REQUEST['sltUnidadE'];

$consulta1 = "  SELECT * FROM gn_periodo WHERE id_unico = $periodo";
    $per = $mysqli->query($consulta1);
    $pe = mysqli_fetch_row($per);

$sql2 = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = $grupog";   

$gg = $mysqli->query($sql2);
$grg = mysqli_fetch_row($gg);

$sql3 = "SELECT id_unico, nombre FROM gn_unidad_ejecutora WHERE id_unico = $unidad";

$ue = $mysqli->query($sql3);
$unie= mysqli_fetch_row($ue);

$cmp = $mysqli->query($consulta);

    $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = $fila['traz'];       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc  = utf8_decode($fila['tnum']);   
    }
$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

 $sql1 = "SELECT   e.codigointerno,
                  e.id_unico,
                  e.tercero,
                  t.id_unico,
                  t.numeroidentificacion, 
                  CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
        
         FROM gn_empleado e
         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
         WHERE e.id_unico !=2";

$cp      = $mysqli->query($sql1);


class PDF extends FPDF
{
    // Cabecera de página  
    function Header()
    { 
        global $nomcomp;
        global $tipodoc;
        global $numdoc;
        global $ruta;
        global $periodo;
        global $pe;
        global $bo;
        global $cp;
        global $grg;
        global $unie;

        // Logo
        if($ruta != '')
        {
        $this->Image('../'.$ruta,20,8,15);
        } 
        $this->SetFont('Arial','B',14);
    
        // Título
        $this->Cell(330,10,utf8_decode(ucwords($nomcomp)),0,0,'C');
        // Salto de línea
        $this->SetFont('Arial','B',8);
        $this->SetX(0);
    
        $this->Ln(4);

        $this->SetFont('Arial','',10);
        $this->Cell(330,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->SetX(0);
    

        $this->Ln(4);

        $this->SetFont('Arial','',8);
        $this->Cell(330,10,utf8_decode('SÁBANA DE PRIMA DE NAVIDAD'),0,0,'C');
    
        $this->SetFont('Arial','',8);
        $this->SetX(0);
        $this->Cell(350,18,utf8_decode('NÓMINA: '. $pe[1]),0,0,'C');
        $this->SetX(0);
        $this->Cell(350,26,utf8_decode('UNIDAD EJECUTORA: '. $unie[1]),0,0,'C');
        $this->SetX(0);
        $this->Ln(30);
        

    }
    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

if(!empty ($unidad)) {
    
    if(empty($periodo) && empty($grupog)){
        
        $sqlgru = "SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_grupo_gestion gg LEFT JOIN  gn_empleado e ON e.grupogestion = gg.id_unico "
                . "LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico WHERE ue.id_unico= '$unidad'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $pdf->SetFont('Arial','',7);
            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  e.unidadejecutora = $unidad  AND e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) "
                    . "FROM gn_novedad n "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_periodo p ON n.periodo = p.id_unico "
                    . "WHERE   e.unidadejecutora = $unidad AND e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2 ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);
        
            $pdf->SetFont('Arial','B',9);
            $filas = 205 / $concn[0] ;
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión:'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');
            $pdf->Ln(6);
        
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Básico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
    
                if($h > $h2){
      
                    $alto = $h;
                    $h2 = $h;
                }else{
      
                    $h2 = $h;
                }
     
                $pdf->SetXY($x+$filas,$y);
    
            }
  
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
  
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
  
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');

            $pdf->Ln($alto);

            $sqlemp =" SELECT DISTINCT e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2 AND e.unidadejecutora = $unidad AND e.grupogestion = '$G[0]' AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 

            $con2 = $mysqli->query($conceptos);
    
            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){
  
                    $novco = "SELECT n.id_unico, sum(n.valor) "
                             . "FROM gn_novedad n "
                             . "LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico "
                             . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                             . "LEFT JOIN gn_periodo p On n.periodo = p.id_unico "
                             . "WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]' AND p.tipoprocesonomina = 2 ORDER BY c.clase ORDER BY c.orden";
                
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
        
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);

                $con2 = $mysqli->query($conceptos);
            }
 
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) "
                . "FROM gn_empleado e "
                . "LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico "
                . "LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico "
                . "LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico "
                . "LEFT JOIN gn_novedad n ON n.empleado = e.id_unico "
                . "WHERE  e.unidadejecutora = $unidad AND vr.estado != 2 AND e.grupogestion = '$G[0]' AND n.concepto = 109 ";
        
            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');

            $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p On n.periodo = p.id_unico
              WHERE  e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2 AND e.unidadejecutora = '$unidad'
              ORDER BY c.orden";


            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                $sumco = "SELECT SUM(n.valor), n.concepto "
                        . "FROM gn_novedad n "
                        . "LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico  "
                        . "LEFT JOIN  gn_vinculacion_retiro vr ON vr.empleado = e.id_unico "
                        . "LEFT JOIN gn_periodo p ON n.periodo = p.id_unico "
                        . "WHERE c.id_unico = '$co3[2]' AND e.unidadejecutora = '$unidad' AND vr.estado !=2 AND e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2 ORDER BY c.orden";
                
                $snov = $mysqli->query($sumco);
            
 
                while($sumanov = mysqli_fetch_row($snov)){

                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }    
        }
        
    }elseif(empty($grupog) && !empty($periodo)){
        
        $sqlgru = "SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_grupo_gestion gg LEFT JOIN  gn_empleado e ON e.grupogestion = gg.id_unico "
                . "LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico WHERE ue.id_unico= '$unidad'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $pdf->SetFont('Arial','',7);

            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  n.periodo = $periodo AND e.grupogestion = '$G[0]' AND e.unidadejecutora = '$unidad'  
              AND p.tipoprocesonomina = 2
              AND  c.clase!=6
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                    WHERE n.periodo =$periodo  AND e.unidadejecutora = '$unidad' AND e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2   AND  c.clase!=6 ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            
            $pdf->SetFont('Arial','B',9);
            
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión:'),0,0,'C');
            $pdf->Cell(10,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Básico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
        
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
            
                if($h > $h2){
                    $alto = $h;
                    $h2 = $h;
                }else{
                    $h2 = $h;
                }
                $pdf->SetXY($x+$filas,$y);
    
            }
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
        
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');

            $pdf->Ln($alto);

            $sqlemp ="  SELECT DISTINCT e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2 AND e.unidadejecutora = '$unidad' AND e.grupogestion = '$G[0]' AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 
            $con2 = $mysqli->query($conceptos);

            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, n.valor FROM gn_novedad n "
                        . "LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]' AND n.periodo = $periodo AND e.grupogestion = '$G[0]'   AND  c.clase!=6 ORDER BY c.orden";
            
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);
     
                $con2 = $mysqli->query($conceptos);
            }
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) "
                . "FROM gn_empleado e "
                . "LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico "
                . "LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico "
                . "LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico "
                . "LEFT JOIN gn_novedad n ON n.empleado = e.id_unico "
                . "WHERE vr.estado != 2 AND e.grupogestion = '$G[0]' AND n.concepto = 109";
        
            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');


             $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  n.periodo = $periodo AND e.unidadejecutora = '$unidad' AND e.grupogestion = '$G[0]' AND p.tipoprocesonomina = 2
                AND  c.clase!=6
              ORDER BY c.orden";

            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                echo $sumco = "SELECT SUM(n.valor),n.concepto "
                        . "FROM gn_novedad n  LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE n.concepto = '$co3[2]'  AND n.periodo = $periodo  AND e.unidadejecutora = '$unidad'";
                echo "<br>";
                $snov = $mysqli->query($sumco);
            
                while($sumanov = mysqli_fetch_row($snov)){

                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        $altura += 10;
                        if($altura >200){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(1); 
                                 $pdf->cellfitscale(50,2,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,3,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(1); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,2,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,3,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }   
        }    
    }elseif(!empty($grupog) && empty($periodo)){
        
        $sqlgru = "SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_grupo_gestion gg LEFT JOIN  gn_empleado e ON e.grupogestion = gg.id_unico "
                . "LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico WHERE ue.id_unico= '$unidad' AND gg.id_unico ='$grupog'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $pdf->SetFont('Arial','',7);
            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND p.tipoprocesonomina = 2
                AND  c.clase!=6
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) 
                   FROM gn_novedad n 
                   LEFT JOIN gn_concepto c ON n.concepto = c.id_unico  
                   LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                   LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                   WHERE   e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND p.tipoprocesonomina = 2
                     AND  c.clase!=6 ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Ln(6);
            
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Basico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
    
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if($h > $h2){
                    $alto = $h;
                    $h2 = $h;
                }else{
                    $h2 = $h;
                }
                $pdf->SetXY($x+$filas,$y);
    
            }
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');

            $pdf->Ln($alto);

            $sqlemp =" SELECT DISTINCT e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 

            $con2 = $mysqli->query($conceptos);

            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, SUM(n.valor) 
                      FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                      WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]'  AND e.grupogestion = $grupog AND p.tipoprocesonomina = 2   AND  c.clase!=6 ORDER BY c.orden";
        
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);

                $con2 = $mysqli->query($conceptos);
            }

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) 
               FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
               LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
               LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
               WHERE  e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND vr.estado !=2 AND n.concepto = 109";
    
            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');

            $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND p.tipoprocesonomina = 2    AND  c.clase!=6
              ORDER BY c.orden";

            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                echo $sumco = "SELECT SUM(n.valor), n.concepto 
                           FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                           LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                           LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                           WHERE c.id_unico = '$co3[2]'   AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND vr.estado != 2 AND p.tipoprocesonomina = 2 
                             AND  c.clase!=6 ORDER BY c.orden";
                           
                $snov = $mysqli->query($sumco);
        
                while($sumanov = mysqli_fetch_row($snov)){

                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }    
        }    
    }else{
        
         $sqlgru = "SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_grupo_gestion gg LEFT JOIN  gn_empleado e ON e.grupogestion = gg.id_unico "
                . "LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico WHERE ue.id_unico= '$unidad' AND gg.id_unico ='$grupog'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();
            

            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico

              WHERE  n.periodo = $periodo AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad'
                AND  c.clase!=6
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) 
                       FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                       WHERE  n.periodo = $periodo AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad'   AND  c.clase!=6
                       ORDER BY c.clase ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión:'),0,0,'C');
            $pdf->Cell(15,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Basico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if($h > $h2){
                    $alto = $h;
                    $h2 = $h;
                }else{
                    $h2 = $h;
                }
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');

            $pdf->Ln($alto);

            $sqlemp =" SELECT  DISTINCT e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad'  AND vr.estado !=2 AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 

            $con2 = $mysqli->query($conceptos);

            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, n.valor 
                      FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]' AND n.periodo = $periodo AND e.grupogestion = $grupog 
                        AND  c.clase!=6 
                        ORDER BY c.orden";
            
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
         
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  

                    }
        
                }
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);

                $con2 = $mysqli->query($conceptos);
            }

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                   WHERE  e.grupogestion = $grupog AND e.unidadejecutora = '$unidad' AND vr.estado != 2 AND n.concepto = 109";

            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');


            echo $con3 = "  SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        WHERE  n.periodo = $periodo AND e.grupogestion = $grupog AND e.unidadejecutora = '$unidad'   AND  c.clase!=6
                        ORDER BY c.orden";

            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                echo $sumco =   "SELECT SUM(n.valor), n.concepto 
                            FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.id_unico = '$co3[2]'  AND n.periodo = $periodo AND e.grupogestion = $grupog AND vr.estado != 2   AND  c.clase!=6 ORDER BY c.orden";
  
                $snov = $mysqli->query($sumco);

                while($sumanov = mysqli_fetch_row($snov)){
     
                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }
        }    
        
    }
        
}else{
   
    if(empty($grupog) && !empty($periodo)){
        
        $sqlgru = "SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_grupo_gestion gg LEFT JOIN gn_empleado e ON e.grupogestion = gg.id_unico WHERE e.id_unico !=2";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico

              WHERE  n.periodo = $periodo AND e.grupogestion = '$G[0]'  
              AND  c.clase!=6
              ORDER BY c.descripcion";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                       WHERE n.periodo =$periodo  AND e.grupogestion = '$G[0]'    AND  c.clase!=6 ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión:'),0,0,'C');
            $pdf->Cell(10,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Básico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,3, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                
                if($h > $h2){
                    $alto   = $h;
                    $h2     = $h;
                }else{
                    $h2     = $alto;
                    $alto   = $alto;
                }
                $pdf->SetXY($x+$filas,$y);

                
    
            }
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');
            $pdf->Ln($alto);

            $sqlemp =" SELECT DISTINCT  e.id_unico, 
                                        e.codigointerno, 
                                        e.tercero, 
                                        t.id_unico,
                                        t.numeroidentificacion, 
                                    (
                                        IF(
                                            CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = ' ',
                                            t.razonsocial,
                                            CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
                                        )
                                    ) as tercero,
                                    ca.salarioactual 
                            FROM gn_novedad n 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            
                            WHERE e.id_unico !=2 AND e.grupogestion = '$G[0]'  AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 
            $con2 = $mysqli->query($conceptos);
            $sb =0;
            $ssa = 0;
            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $sb +=$emple[6];
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   
                $ssa += $emple[6];
                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, n.valor "
                        . "FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico "
                        . "WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]' AND n.periodo = $periodo AND vr.estado != 2   AND  c.clase!=6 ORDER BY c.orden";
        
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);
     
                $con2 = $mysqli->query($conceptos);
            }
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($ssa ,2,'.',',')),1,0,'R');

            $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              WHERE  n.periodo = $periodo AND e.grupogestion = '$G[0]' 
              ORDER BY c.orden";


            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                 $sumco = "SELECT SUM(n.valor),n.concepto 
                    FROM gn_novedad n 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE c.id_unico = '$co3[2]'  AND n.periodo = $periodo  AND e.grupogestion = '$G[0]'
                      AND  c.clase!=6
                      ORDER BY c.orden";
  
                $snov = $mysqli->query($sumco);            
                while($sumanov = mysqli_fetch_row($snov)){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                }
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }
        }

    }elseif(!empty($grupog) && empty($periodo)){
        
        $sqlgru = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = '$grupog'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $pdf->SetFont('Arial','',7);
            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
              WHERE  e.grupogestion = $grupog AND p.tipoprocesonomina = 2 OR c.clase=2  AND e.grupogestion = $grupog AND p.tipoprocesonomina = 2 
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico  
                       LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                       LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                       WHERE   e.grupogestion = $grupog AND p.tipoprocesonomina = 2 ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(30,5, utf8_decode('Grupo de Gestión'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Basico'),0,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 
            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if($h > $h2){
                    $alto = $h;
                    $h2 = $h;
                }else{
                    $h2 = $h;
                }
                $pdf->SetXY($x+$filas,$y);
            }
        
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
        
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');
            $pdf->Ln($alto);

            $sqlemp =" SELECT  DISTINCT  e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
                            WHERE e.id_unico !=2 AND e.grupogestion = $grupog AND vr.estado !=2 AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 
            $con2 = $mysqli->query($conceptos);

            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, SUM(n.valor) 
                         FROM gn_novedad n 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         
                         WHERE n.concepto = '$CO[2]' AND e.id_unico = '$emple[0]' ";
        
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
        
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
            
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);

                $con2 = $mysqli->query($conceptos);
            }
 
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) 
                  FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
                  LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                  LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                  WHERE  e.grupogestion = $grupog AND vr.estado != 2 AND n.concepto = 109";

            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');

            echo $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
              WHERE  e.grupogestion = $grupog AND p.tipoprocesonomina = 2 
              ORDER BY c.orden";


            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                echo $sumco = "SELECT SUM(n.valor), n.concepto 
                              FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                              LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                              LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                              WHERE c.id_unico = '$co3[2]'   AND e.grupogestion = $grupog AND vr.estado !=2 AND p.tipoprocesonomina = 2 ";
  
                $snov = $mysqli->query($sumco);
            
 
                while($sumanov = mysqli_fetch_row($snov)){

                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }
        
            
        }
    }else{
         
        $sqlgru = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = '$grupog'";
        $resgru = $mysqli->query($sqlgru);
   
        while($G = mysqli_fetch_row($resgru)){
        
            $nb=$pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->AliasNbPages();

            $conceptos = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico

              WHERE  n.periodo = $periodo AND e.grupogestion = $grupog  
              ORDER BY c.orden";

            $con = $mysqli->query($conceptos);

            $numero_con = "SELECT COUNT(DISTINCT n.concepto) 
                      FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      WHERE  n.periodo = $periodo AND e.grupogestion = $grupog  ";

            $n_con = $mysqli->query($numero_con);
            $concn = mysqli_fetch_row($n_con);

            $filas = 205 / $concn[0] ;
            
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,5, utf8_decode('Grupo de Gestión:'),0,0,'C');
            $pdf->Cell(10,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,5, utf8_decode($G[1]),0,0,'C');  
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(20,5, utf8_decode('Básico'),0,0,'C');    
            $x =$pdf->GetX();
            $y =$pdf->GetY();    
            $h2 = 0; 

            while ($Tcon = mysqli_fetch_row($con)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if($h > $h2){
                    $alto = $h;
                    $h2 = $h;
                }else{
                    $h2 = $h;
                }
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->SetXY($cx,$cy);
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $con1 = $mysqli->query($conceptos);
        
            while ($Tcon = mysqli_fetch_row($con1)) {
   
                $x =$pdf->GetX();
                $y =$pdf->GetY(); 
                $pdf->SetFont('Arial','B',6);
                $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
                $pdf->SetXY($x+$filas,$y);
            }
            $pdf->Cell(25,$alto, utf8_decode('Firma'),1,0,'C');
            $pdf->Ln($alto);

            $sqlemp =" SELECT  DISTINCT e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND e.grupogestion = $grupog  AND vr.estado !=2 AND n.concepto = 109 AND n.periodo =$periodo";

            $emp = $mysqli->query($sqlemp); 

            $con2 = $mysqli->query($conceptos);

            while($emple =  mysqli_fetch_row($emp)){

                $pdf->Cellfitscale(15,8, utf8_decode($emple[4]),1,0,'C');
                $pdf->Cellfitscale(50,8, utf8_decode($emple[5]),1,0,'C');
                $pdf->Cellfitscale(20,8, utf8_decode(number_format($emple[6],2,'.',',')),1,0,'C');
                $x =$pdf->GetX();  
                $y =$pdf->GetY();   

                while($CO = mysqli_fetch_row($con2)){

                    $novco = "SELECT n.id_unico, n.valor 
                          FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        
                          WHERE c.id_unico = '$CO[2]' AND e.id_unico = '$emple[0]' AND n.periodo = $periodo ORDER BY c.orden";
        
                    $cnov = $mysqli->query($novco);
                    $num_con = mysqli_num_rows($cnov);
       
                    if($num_con > 0){
          
                        $novec = mysqli_fetch_row($cnov);
                        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],2,'.',',')),1,0,'R');
                    }else{

                        $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
                    }
                }
                $pdf->Cellfitscale(25,8, utf8_decode(''),1,0,'R'); 
                $pdf->Ln(8);
                
                $con2 = $mysqli->query($conceptos);
            }    
           

      
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(65,8, utf8_decode('Total:'),1,0,'C');

            $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                   WHERE  e.grupogestion = $grupog AND vr.estado !=2 AND n.concepto = 109";

            $tosal = $mysqli->query($saltot);
            $TOTS = mysqli_fetch_row($tosal);
            $pdf->Cellfitscale(20,8, utf8_decode(number_format($TOTS[0],2,'.',',')),1,0,'R');


            echo $con3 = "SELECT DISTINCT      n.concepto, 
                                   c.descripcion,
                                   c.id_unico
              FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              WHERE  n.periodo = $periodo AND e.grupogestion = $grupog 
              ORDER BY c.orden";


            $co2 = $mysqli->query($con3);

            while($co3 = mysqli_fetch_row($co2)){

                echo $sumco = "SELECT SUM(n.valor), n.concepto 
                          FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                          LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                          WHERE c.id_unico = '$co3[2]'  AND n.periodo = $periodo AND e.grupogestion = $grupog AND vr.estado !=2 ORDER BY c.orden";
  
                $snov = $mysqli->query($sumco);

                while($sumanov = mysqli_fetch_row($snov)){
     
                    /*if($sumanov[1] == 7){
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format(0,2,'.',',')),1,0,'R');  
                    }else{
                        */
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],2,'.',',')),1,0,'R');  
                    //}   
                }
                $snov = $mysqli->query($sumco);
            }
            #**************** FIRMAS *****************#
                        $pdf->Ln(25);
            $altura = $pdf->GetX();
            $firmas = "SELECT   c.nombre, 
                                rd.orden,
                                IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE 

                        FROM gf_responsable_documento rd 
                        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                        WHERE td.nombre = 'Sabana Prima'
                        ORDER BY rd.orden ASC";

                        $fi = $mysqli->query($firmas);
                        $altura = $pdf->GetY();
                        if($altura > 180){
                            $pdf->AddPage();
                            $pdf->Ln(15);
                        } 
                        $pdf->SetFont('Arial','B',8);
                        $y =$pdf->GetY();
                        $x = $pdf->GetX();
                        while($F = mysqli_fetch_row($fi)){

                            if($F[1] ==  1){
                                 $pdf->Cell(60,0.1,'',1);  
                                 $pdf->Ln(2); 
                                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3);
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
                            }else{
                                $pdf->SetXY($x+25, $y); 

                                $pdf->Cell(60,0.1,'',1); 
                                $pdf->Ln(2); 
                                $y1 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y1); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                                $pdf->Ln(3); 
                                $y2 =$pdf->GetY();
                                $pdf->SetXY($x+25, $y2); 
                                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
                            }
                            $x = $pdf->GetX();
                        }
        }
    }
}



ob_end_clean();
$pdf->Output(0,'Sabana_Navidad ('.date('d/m/Y').').pdf',0);     
  
?>