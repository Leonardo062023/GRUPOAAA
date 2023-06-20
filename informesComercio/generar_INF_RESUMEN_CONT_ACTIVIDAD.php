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

class PDF extends FPDF
{
function Header()
{ 
    
    global $fecha1;
    global $fecha2;
    global $cuentaI;
    global $cuentaF;
    global $nombreCompania;
    global $nitcompania;
    global $numpaginas;
    global $ruta;
    global $mesNomn;
    $numpaginas=$this->PageNo();
    
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,8.2,8,20);
        }
    $this->SetX(8.2);
    $this->Cell(190,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(8.2);
    $this->Cell(190, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(8.2);
    $this->Cell(190,5,utf8_decode('RESUMNEN DE CONTRIBUYENTES POR ACTIVIDAD'),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(8.2);
    $this->SetFont('Arial','B',8);
    $this->Cell(20,6,utf8_decode('CODIGO'),1,0,'C');
    $this->Cell(155,6,utf8_decode('ACTIVIDAD'),1,0,'C');
    $this->Cell(20,6,utf8_decode('CANTIDAD'),1,0,'C');
    
    /*$this->Cell(36,9,utf8_decode('SALDO EXTRACTO'),1,0,'C');
    $this->Cell(36,9,utf8_decode('DIFERENCIA'),1,0,'C');*/
    
    $this->Ln(9);
    
    
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
$yp=$pdf->GetY();


//CONSULTA

$sql1 = "SELECT DISTINCT id_unico, codigo, descripcion FROM gc_actividad_comercial ";
$result1 = $mysqli->query($sql1);
$x = 0;
while($act = mysqli_fetch_row($result1)){

    $sql2 = "SELECT COUNT(DISTINCT c.id_unico) FROM gc_contribuyente c 
            LEFT JOIN gc_actividad_contribuyente ac ON ac.contribuyente = c.id_unico
            LEFT JOIN gc_actividad_comercial aco ON ac.actividad = aco.id_unico 
            WHERE aco.id_unico = '$act[0]'";

    $result2 = $mysqli->query($sql2);
    $res2 = mysqli_fetch_row($result2);
    $pdf->SetFont('Arial','',9);
    
    if($res2[0] > 0){
        $pdf->Cell(19,4,utf8_decode($act[1]),0,0,'R');

        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();
        $pdf->MultiCell(150,4,utf8_decode(ucwords(mb_strtolower($act[2]."-".$row[3]))),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y1;
        $px = $x1+153;

        $pdf->SetXY($px,$y1);
        $pdf->Cell(20,4,utf8_decode($res2[0]),0,0,'R');
        $alto = max($h,$h1);
        $pdf->Ln($alto);

        
    }
    $x = $x + $res2[0];
}

$pdf->Ln(1);
     
$pdf->Cell(195,0.5,'',1);
$pdf->Ln(3);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(300,4,utf8_decode('Total: '),0,0,'C');
$pdf->Cell(30,4,utf8_decode($x),0,0,'C');


/*

while($row=mysqli_fetch_array($resultado)){

            //llenar datos
            $pdf->SetX(8.2);
            $pdf->SetFont('Arial','',8.3);

            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(65,4,utf8_decode(ucwords(mb_strtolower($row[1]))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+65;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px2,$yp);
                $h1=$y22-$yp;
            } else {
                $pdf->SetXY($px2,$y2);
            }

            $y1 = $pdf->GetY();
            $x1 = $pdf->GetX();
            $pdf->MultiCell(65,4,utf8_decode(ucwords(mb_strtolower($row[2]."-".$row[3]))),0,'L');
            $y2 = $pdf->GetY();
            $h = $y2-$y1;
            $px = $x1+65;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px,$yp);
                $h=$y2-$yp;
            } else {
                $pdf->SetXY($px,$y1);
            }

            $pdf->Cell(35,4,utf8_decode($row[4]),0,0,'C');
            $pdf->Cell(35,4,utf8_decode($row[5]),0,0,'C');
       
            //salto
            $alto = max($h,$h1);
            $pdf->Ln($alto);
            $paginactual=$numpaginas;


            $yal= $pdf->GetY();
            if($yal>250){
                $pdf->AddPage();
            }
    
}

*/

while (ob_get_length()) {
  ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Resumen de Contribuyentes por Actividad  ('.date('d/m/Y').').pdf',0);
