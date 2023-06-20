<?php

header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);

##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#CREACION PDF, HEAD AND FOOTER
$fecI = $_POST['sltFechaI'];
$fecF = $_POST['sltFechaF'];


    if(!empty($fecI)){
        $hoy = trim($fecI, '"');
        $fecha_div = explode("/", $hoy);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fechaI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }

    if(!empty($fecF)){
        $hoy1 = trim($fecF, '"');
        $fecha_div2 = explode("/", $hoy1);
        $anio1 = $fecha_div2[2];
        $mes1 = $fecha_div2[1];
        $dia1 = $fecha_div2[0];
        $fechaF = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }
    
   
    $FI = $fechaI;
    $FF = $fechaF;
    
    $tipo = "CONCEPTO USO DEL SUELO CCB";
    

class PDF extends FPDF
{
function Header()
{ 
    
    global $FI;
    global $FF;
    global $nombreCompania;
    global $nitcompania;
    global $ruta;
    global $mesNomn;
    global $tipo;   
    global $numpaginas;
    $numpaginas=$this->PageNo();
    
   
    
   
    
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,23,8,20);
        }
    $this->SetX(23);
    $this->Cell(190,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(23);
    $this->Cell(190, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(23);
   
    $this->Cell(190,10,utf8_decode($tipo),0,0,'C');
    $this->Ln(10);
    $this->SetFont('Arial','B',10);
    $this->Cell(90,10,utf8_decode('Fecha Inicial:'),0,0,'R');
    $this->SetFont('Arial','',10);
    $this->Cell(20,10,utf8_decode($FI),0,0,'C');
    $this->SetFont('Arial','B',10);
    $this->Cell(30,10,utf8_decode('Fecha Final:'),0,0,'C');
    $this->SetFont('Arial','',10);
    $this->Cell(20,10,utf8_decode($FF),0,0,'C');
    $this->Ln(5);
    $this->Ln(8);
    //ENTRE
    

    
    $this->SetX(19);
    $this->SetFont('Arial','B',8);
    $this->Cell(40,9,utf8_decode('ESTABLECIMIENTO'),1,0,'C');
    $this->Cell(30,9,utf8_decode('CONCEPTO'),1,0,'C');
    $this->Cell(40,9,utf8_decode('PROPIETARIO'),1,0,'C');
    $this->Cell(40,9,utf8_decode('RESPONSABLE'),1,0,'C');
    $this->Cell(30,9,utf8_decode('OBSERVACIONES'),1,0,'C');
    $this->Ln(10);  

   }  
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    $this->SetX(15);
    $this->Cell(40,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(50,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(50,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(40,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('P','mm','Letter');   
$pdf->AddPage();
$pdf->AliasNbPages();
$yp = $pdf->GetY();

    $sql = "SELECT IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) tercero,
                    tr.numeroidentificacion,
                    e.nombre, e.cod_mat,
                    ce.conceptoCCB,
                    ce.fecha_inicio,
                    ce.fecha_fin,
                    ce.fecha_migracion,
                    ce.observaciones, ce.responsable                               
                    FROM gc_concepto_establecimiento ce  
                    LEFT JOIN gc_establecimiento e ON ce.establecimiento = e.id_unico
                    LEFT JOIN gc_contribuyente c ON e.contribuyente = c.id_unico
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
            WHERE  ce.fecha_migracion BETWEEN '$FI' AND '$FF' ORDER BY ce.fecha_migracion ASC";

 $resultado = $mysqli->query($sql);
 while($row=mysqli_fetch_array($resultado)){

     $yp=$pdf->GetY();
    if($yp > 250){
        $pdf->AddPage();
    }
                       
                        //llenar datos
                        $pdf->SetX(19);
                        $pdf->SetFont('Arial','',8);
                        $x =$pdf->GetX();
                        $y =$pdf->GetY();
                        $pdf->MultiCell(40,4, utf8_decode($row[2]),0,'L');
                        $y1 = $pdf->GetY();
                        $h = $y1 - $y; 


                        $pdf->SetXY($x+40,$y);
                        $x1 =$pdf->GetX();  
                        $pdf->MultiCell(30,6, strtoupper(utf8_decode($row[4])),'','L');
                        $y2 = $pdf->GetY();
                        $h2 = $y2 - $y; ;

                        $pdf->SetXY($x1+30,$y);
                        $x2 =$pdf->GetX();               
                        $pdf->MultiCell(40,6, strtoupper(utf8_decode($row[0]." - ".$row[1])),'','L');
                        $y3 = $pdf->GetY();
                        $h3 = $y3 - $y; 
                        
                        $pdf->SetXY($x2+40,$y);
                        $x3 =$pdf->GetX(); 
                        $pdf->MultiCell(40,6,utf8_decode($row[9]),'','C');
                        $y4 = $pdf->GetY();
                        $h4 = $y4 - $y; 
                        
                        $pdf->SetXY($x3+40,$y);
                        $x4 =$pdf->GetX(); 
                        $pdf->Cell(20,6,utf8_decode($row[8]),'','C');
                        $y5 = $pdf->GetY();
                        $h5 = $y5 - $y;
                       
                      

                       //  $px = $x+30;                       
                       //  if($numpaginas>$paginactual){
                       //     $pdf->SetXY($px,$yp);
                       //     $h6=$y6-$yp;
                       // } else {
                       //     $pdf->SetXY($px,$y);
                       // }                            

                       
                        $alt = max($h, $h2, $h3, $h4, $h5);
                        $pdf->SetXY($x, $y);
                        $pdf->Cell(40,$alt, utf8_decode(''),'',0,'C');
                        $pdf->Cell(30,$alt,utf8_decode(''),'',0,'C');
                        $pdf->Cell(40,$alt,utf8_decode(''),'',0,'C');
                        $pdf->Cell(40,$alt,utf8_decode(''),'',0,'C');
                        $pdf->Cell(30,$alt,utf8_decode(''),'',0,'C');

                        $alto = $alt;
                        $pdf->Ln($alto);
                       
     }

    ob_end_clean();
    
 
 $pdf->Output(0,'LISTADO '. $tipo.' ('.date('d/m/Y').').pdf',0);
?>