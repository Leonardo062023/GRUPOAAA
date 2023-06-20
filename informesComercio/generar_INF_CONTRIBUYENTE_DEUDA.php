<?php
#creado : | Nestor Bautista | 01/08/2018 | 
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);

#$tipo = $_POST['sltOpcion'];
$anno = $_SESSION['anno'];
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

echo $anio = "SELECT anno FROM  gf_parametrizacion_anno WHERE id_unico = '$anno'";
$ann = $mysqli->query($anio);
$ANI = mysqli_fetch_row($ann);
echo "<br/>";
$AN = $ANI[0] - 1;

if($tipo ==1){
    $tip = "";
}elseif($tipo ==2){
    $tip = "ACTIVOS";
}elseif($tipo ==3){
    $tip = "INACTIVOS";
}

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
    global $tip;
    $numpaginas=$this->PageNo();
    
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,20,8,20);
        }
    $this->SetX(8.2);
    $this->Cell(330,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(8.2);
    $this->Cell(330, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(8.2);
    $this->Cell(330,5,utf8_decode('LISTADO CONTRIBUYENTES DEUDORES '.$tip),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(15,7,utf8_decode('COD MAT'),1,0,'C');
    $this->Cell(85,7,utf8_decode('CONTRIBUYENTE'),1,0,'C');
    $this->Cell(85,7,utf8_decode('REPRESENTANTE LEGAL'),1,0,'C');
    $this->Cell(50,7,utf8_decode('DIRECCION'),1,0,'C');
    $this->Cell(20,7,utf8_decode('TELEFONO'),1,0,'C');
    $this->Cell(65,7,utf8_decode('AÑOS DEUDA'),1,0,'C');
    

    /*$this->Cell(36,9,utf8_decode('SALDO EXTRACTO'),1,0,'C');
    $this->Cell(36,9,utf8_decode('DIFERENCIA'),1,0,'C');*/
    
    $this->Ln(7);
    
    
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
    $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('L','mm','Legal');   
$pdf->AddPage();
$pdf->AliasNbPages();
$yp=$pdf->GetY();



$sql = "SELECT c.id_unico,
               IF(CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) = '',
                (t.razonsocial),
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                c.codigo_mat,
                t.representantelegal,
                c.dir_correspondencia,
                c.telefono,
                c.fechainscripcion
                
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        WHERE c.estado = 1 ";




$resultado=$mysqli->query($sql);


while($row=mysqli_fetch_array($resultado)){

    if(!empty($row[6])){
        $yp=$pdf->GetY();
        if($yp > 180){
            $pdf->AddPage();
        }


        $fechaIDiv = explode('-',$row[6]);
        $annoI = $fechaIDiv[0];
        
        #Consulta el representante legal del contribuyente 
        if(!empty($row[3])){
            $Representante = "SELECT    IF(CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos) 
                                    IS NULL OR CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos) = '',
                                    (t.razonsocial),
                                    CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos)) AS NOMBRE
                        FROM gf_tercero t 
                        WHERE t.id_unico = '$row[3]'";

            $repre = $mysqli->query($Representante);
            $RE = mysqli_fetch_row($repre);
        }else{
            $RE[0] = "";
        }


        #consulta las activades del contribuyente
        $deuda = "SELECT GROUP_CONCAT(DISTINCT ac.vigencia ORDER BY ac.vigencia) FROM  gc_anno_comercial ac
                        LEFT JOIN gc_declaracion d ON d.periodo = ac.id_unico
                        LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
                        WHERE ac.vigencia BETWEEN '$annoI' AND '$AN' AND ac.mes = 0
                        AND ac.id_unico NOT IN(SELECT de.periodo FROM gc_recaudo_comercial rco
                        LEFT JOIN gc_declaracion de ON rco.declaracion = de.id_unico
                        WHERE contribuyente = $row[0] AND de.clase = 1)  
                        AND ac.id_unico NOT IN(SELECT de.periodo
                        FROM gc_declaracion de
                        LEFT JOIN gc_detalle_declaracion dtd ON de.id_unico = dtd.declaracion                                           
                        WHERE de.contribuyente = $row[0] AND de.clase = 1)
                        ORDER BY ac.vigencia ASC ";
        $deu = $mysqli->query($deuda);

        $deuda2 = "SELECT COUNT(DISTINCT ac.vigencia) FROM  gc_anno_comercial ac

                    LEFT JOIN gc_declaracion d ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
                    WHERE ac.vigencia BETWEEN '$annoI' AND '$AN' AND ac.mes = 0 AND  ac.id_unico NOT IN(SELECT de.periodo FROM gc_recaudo_comercial rco LEFT JOIN gc_declaracion de ON rco.declaracion = de.id_unico WHERE contribuyente = $row[0] AND de.clase = 1)";

        $deu2 = $mysqli->query($deuda2);            
        $ndeu = mysqli_fetch_row($deu2);
        echo "<br/>";
        if($ndeu[0] > 0){
            //llenar datos
            $pdf->SetFont('Arial','',9);
            $ypr=$pdf->GetY();

            
            $pdf->SetX(20);
            $xpr=$pdf->GetX();
            $pdf->Cell(15,5,utf8_decode(ucwords(mb_strtolower($row[2]))),0,0,'C');

            $x1 = $pdf->GetX();
            $y1 = $pdf->GetY();
            $pdf->MultiCell(85,5,utf8_decode(ucwords(mb_strtolower($row[1]))),0,'J');
            $y2 = $pdf->GetY();
            $h1 = $y2-$y1;
            
            $px1 = $x1+85;

            $pdf->SetXY($px1,$ypr);
            

            $x2=$pdf->GetX();
            $y2=$pdf->GetY();
            $pdf->MultiCell(85,5,utf8_decode(ucwords(mb_strtolower($RE[0]))),0,'J');
            $y22=$pdf->GetY();
            $h2 = $y22-$y2;
            $px2 = $x2+85;
            
            $pdf->SetXY($px2,$ypr);
            

            $x3=$pdf->GetX();
            $y3=$pdf->GetY();
            $pdf->MultiCell(50,5,utf8_decode(ucwords(mb_strtolower($row[4]))),0,'J');
            $y33=$pdf->GetY();
            $h3 = $y33-$y3;
            $px3 = $x3+50;
            
            $pdf->SetXY($px3,$ypr);
            

            $pdf->Cell(20,4,utf8_decode(ucwords(mb_strtolower($row[5]))),0,0,'C');

            $x5 = $pdf->GetX();
            $y5 = $pdf->GetY();
            while($DE = mysqli_fetch_row($deu)){
                $pdf->SetX($x5);
                $yx = $pdf->GetY();
                $pdf->MultiCell(65,5,utf8_decode(ucwords(mb_strtolower($DE[0]))),0,'J');
                $y7 = $pdf->GetY();
                $pdf->SetXY($x5+69,$yx);
                $hx = $y7 - $yx;
                $yyyy = $pdf->GetY();
                if($yyyy <= 170){
                    $pdf->Ln($hx);    
                }
                
                
                $yp1=$pdf->GetY();
                if($yp1 > 170){
                    $h4 = $yp1-$ypr;
                    $alto = max($h1,$h2,$h3,$h4);
                    $pdf->SetXY($xpr,$ypr);
                    $pdf->Cell(15,$alto,utf8_decode(''),1,0,'C');
                    $pdf->Cell(85,$alto,utf8_decode(' '),1,0,'C');
                    $pdf->Cell(85,$alto,utf8_decode(' '),1,0,'C');
                    $pdf->Cell(50,$alto,utf8_decode(' '),1,0,'C');
                    $pdf->Cell(20,$alto,utf8_decode(' '),1,0,'C');
                    $pdf->Cell(65,$alto,utf8_decode(' '),1,0,'C');
                    $pdf->AddPage();
                    $pdf->SetX(20);
                    $xpr=$pdf->GetX();
                    $ypr=$pdf->GetY();
                    $y5 = $pdf->GetY();
                }
            }
            
            $y44 = $pdf->GetY();
            $h4  = $y44-$y5;
            $px5 = $x5+65;
            $pdf->SetXY($px5,$ypr); 
            $alto = max($h1,$h2,$h3,$h4);
            
            
            $pdf->SetXY($xpr,$ypr);
            $pdf->Cell(15,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(85,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(85,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(50,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(20,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(65,$alto,utf8_decode(' '),1,0,'C');
            
            $pdf->Ln($alto); 
        }
        
    
    }

    
}



while (ob_get_length()) {
    ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Listado Actividad Contribuyente ('.date('d/m/Y').').pdf',0);
?>