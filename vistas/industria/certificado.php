<?php
date_default_timezone_set('America/Bogota');
class PDF extends FPDF{
    var $widths;
    var $aligns;
    function SetWidths($w){
        //Set the array of column widths
        $this->widths = $w;
    }
    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns = $a;
    }
    function fill($f){
        //juego de arreglos de relleno
        $this->fill = $f;
    }
    function Row($data){
        //Calcula el alto de l afila
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Realiza salto de pagina si es necesario
        $this->CheckPageBreak($h);
        //Pinta las celdas de la fila
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guarda la posicion actual
            $x = $this->GetX();
            $y = $this->GetY();
            //Pinta el border
            $this->Rect($x, $y, $w, $h, '');
            //Imprime el texto
            $this->MultiCell($w,5,$data[$i],'LR', $a, '');
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
        $this->Ln($h - 5);
    }

    function fila($data){
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, 0, 0, '');
            $this->MultiCell($w,5, $data[$i],'', $a, '');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h - 5);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','', $txt);
        $nb = strlen($s);
        if( $nb > 0 and $s[$nb-1] == '\n' )
            $nb--;
        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;
        while( $i < $nb ){
            $c = $s[$i];
            if( $c == '\n' ){
                $i++;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
                continue;
            }
            if( $c == '' )
                $sep = $i;
            $l += $cw[$c];
            if( $l > $wmax ){
                if( $sep ==-1 ){
                    if($i == $j)
                        $i++;
                }else
                    $i = $sep+1;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetFont('Times', '', 10);
!empty($ruta)?$pdf->Image('./'.$ruta, 95.5, $pdf->GetY(), 20): '';
$pdf->Ln(25);
$pdf->MultiCell(195, 10, 'REPUBLICA DE COLOMBIA'.PHP_EOL.'DEPARTAMENTO DE SANTANDER', 0, 'C');
$pdf->SetFont('Times', 'BI', 11);
$pdf->MultiCell(195, 10, $razonsocial.PHP_EOL.$nombreTipoIden.' : '.$compania['nit'], 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(195, 5, 'EL SUSCRITO SECRETARIO DE HACIENDA DEL MUNICIPIO', 0, 0, 'C');
$pdf->Ln(10);
$pdf->SetFont('Times', 'B', 11);
$pdf->Cell(195, 5, 'CERTIFICA:', 0, 0, 'C');
$pdf->Ln(10);
$pdf->SetFont('Times', '', 11);
$pdf->MultiCell(195, 5, utf8_decode('Que revisados los archivos de Industria y Comercio que reposan en ésta oficina se pudo constatar que el establecimiento comercial con las siguientes características, se encuentra registrado así:'), '0', 'J');
$pdf->Ln(10);
$pdf->Cell(80, 5, 'RAZON SOCIAL:', 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['razon'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, utf8_decode('MATRICULA No:'), 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['cod'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, utf8_decode('REPRESENTANTE LEGAL:'), 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['rep'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, utf8_decode('NIT / C.C.:'), 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['nit'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, utf8_decode('DIRECCIÓN:'), 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['dir'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, utf8_decode('TELÉFONO:'), 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['tel'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, 'REGIMEN TRIBUTARIO:', 0, 0, 'L');
$pdf->Cell(115, 5, $xcontribuyente['tpr'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(80, 5, '	ULTIMO PAGO REGISTRADO:', 0, 0, 'L');
$pdf->Cell(100, 5, $fecha, 0, 0, 'L');
$pdf->Ln(20);
$mes = [ 'no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 	'Septiembre', 'Octubre', 'Noviembre', 	'Diciembre'];
$xnmes = (int) date("m"); $nmes = $mes[$xnmes];
$pdf->MultiCell(195, 5, utf8_decode('La anterior se expide para tramites comerciales, en BARBOSA a los dias '.date("d").' del mes de '.$nmes.' del año '.date("Y").'.'), 0, 'J');
$pdf->Ln(40);
$pdf->SetFont('Times', 'B', 11);
$pdf->Cell(195, 5, 'SECRETARIO DE HACIENDA', '', 0, 'C');
$pdf->Ln(5);
$pdf->Cell(195, 5, 'LUIS ALBERTO BLANCO MANTILLA', '', 0, 'C');
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0,'Certificado'.'.pdf',0);