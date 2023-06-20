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
$tipo1 = $_POST['sltTipo'];

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
    if($tipo1 == 1){
        $tipo = "NUEVOS CONTRIBUYENTES";
        $xxxx = 1;
        
    }else{
        $tipo = "NUEVOS ESTABLECIMIENTOS";
        $xxxx = 2; 
    }
 
     

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
    global $xxxx;
    $numpaginas=$numpaginas+1;
    
   
    
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
    

    
    if($xxxx == 1){ 
        $this->SetX(19);
        $this->SetFont('Arial','B',8);
        $this->Cell(55,9,utf8_decode('NOMBRE'),1,0,'C');
        $this->Cell(25,9,utf8_decode('IDENTIFICACIÓN'),1,0,'C');
        $this->Cell(25,9,utf8_decode('MATRÍCULA'),1,0,'C');
        $this->Cell(25,9,utf8_decode('ESTADO'),1,0,'C');
        $this->Cell(30,9,utf8_decode('FECHA MIGRACIÓN'),1,0,'C');
        $this->Cell(25,9,utf8_decode('MATRÍCULA CCB'),1,0,'C');

        $this->Ln(10);  
      }else{
       $this->SetX(19);
       $this->SetFont('Arial','B',8);
        $this->Cell(55,9,utf8_decode('NOMBRE ESTABLECIMIENTO'),1,0,'C');
        $this->Cell(25,9,utf8_decode('MATRÍCULA EST.'),1,0,'C');
        $this->Cell(55,9,utf8_decode('CONTRIBUYENTE'),1,0,'C');
        $this->Cell(25,9,utf8_decode('IDENTIFICACIÓN'),1,0,'C');
        $this->Cell(30,9,utf8_decode('FECHA MIGRACIÓN'),1,0,'C');

      $this->Ln(10);  

    }
    
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

 if($xxxx==1){

    $sql = "SELECT   
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
                    tr.apellidodos)) nombre,
                    tr.numeroidentificacion,
                    c.codigo_mat,
                    ec.nombre estado,
                    c.fecha_migra,
                    c.cod_camaraC                              
                    FROM gc_contribuyente c  
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    LEFT JOIN gc_estado_contribuyente ec ON c.estado = ec.id_unico
                    WHERE  c.fecha_migra BETWEEN '$FI' AND '$FF' ORDER BY c.fecha_migra DESC, nombre";
         $resultado = $mysqli->query($sql);
         while($row=mysqli_fetch_array($resultado)){
                       
                        //llenar datos
                        $pdf->SetX(19);
                        $pdf->SetFont('Arial','',8);
                        $x =$pdf->GetX();
                        $y =$pdf->GetY();
                        $pdf->MultiCell(55,4, utf8_decode($row[0]),0,'L');
                                         
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;         
                        $alto = $alto + $h;
                        $px = $x+55;
                       
                        if($numpaginas>$paginactual){
                           $pdf->SetXY($px,$yp);
                           $h=$y2-$yp;
                       } else {
                           $pdf->SetXY($px,$y);
                       }
                      
                        $pdf->Cell(25,$h,utf8_decode(ucwords(mb_strtolower($row[1] ))),0,'C');
                         $pdf->Cell(25,$h,utf8_decode($row[2]),0,0,'C');

                        $pdf->Cell(25,$h,utf8_decode($row[3]),0,0,'C');
                        $pdf->Cell(30,$h,utf8_decode($row[4]),0,0,'C');

                        $pdf->Cell(25,$h,utf8_decode($row[5]),0,0,'C');
             
                        //salto
                        $alto = $h;
                        $pdf->Ln($alto + 3);
                        $paginactual=$numpaginas;
                    }
         
     
 }else{

    $sql ="SELECT   e.nombre, e.cod_mat,
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
                                tr.apellidodos)) ,
                                tr.numeroidentificacion,                                
                                e.fecha_migra                               
                    FROM gc_contribuyente c  
                    LEFT JOIN gc_establecimiento e ON c.id_unico = e.contribuyente
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    WHERE  e.fecha_migra BETWEEN '$FI' AND '$FF' ORDER BY e.fecha_migra DESC, e.nombre ASC";

       $resultado = $mysqli->query($sql);
    while($row=mysqli_fetch_array($resultado)){
                       
                        //llenar datos
                        $pdf->SetX(19);
                        $pdf->SetFont('Arial','',8);
                        $x =$pdf->GetX();
                        $y =$pdf->GetY();
                        $pdf->MultiCell(55,4, utf8_decode($row[0]),0,'L');
                                         
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;         
                        $alto = $alto + $h;
                        $px = $x+55;
                       
                        if($numpaginas>$paginactual){
                           $pdf->SetXY($px,$yp);
                           $h=$y2-$yp;
                       } else {
                           $pdf->SetXY($px,$y);
                       }
                       
                       $pdf->Cell(25,$h,utf8_decode($row[1]),0,0,'C');

                        $x =$pdf->GetX();
                        $y =$pdf->GetY();
                        $pdf->MultiCell(55,4, utf8_decode($row[2]),0,'L');
                                         
                        $y2 = $pdf->GetY();
                        $h1 = $y2 - $y;         
                        $alto = $alto + $h1;
                        $px = $x+55;
                       
                        if($numpaginas>$paginactual){
                           $pdf->SetXY($px,$yp);
                           $h1=$y2-$yp;
                       } else {
                           $pdf->SetXY($px,$y);
                       }
                                            

                        $pdf->Cell(25,$h1,utf8_decode($row[3]),0,0,'C');
                        $pdf->Cell(30,$h1,utf8_decode($row[4]),0,0,'C');

                       
             
                        //salto
                        $alto = max($h,$h1);
                        $pdf->Ln($alto+3);
                        $paginactual=$numpaginas;
                    }
              
 }



    while (ob_get_length()) {
        ob_end_clean();
    }

 
 $pdf->Output(0,'LISTADO '. $tipo.' ('.date('d/m/Y').').pdf',0);
    