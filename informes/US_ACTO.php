<?php
header("Content-Type: text/html;charset=utf-8");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../numeros_a_letras.php');
session_start(); 
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$sqlC = "SELECT     ter.razonsocial,
                    ti.nombre,
                    ter.numeroidentificacion,
                    ter.ruta_logo,
                    dr.direccion,
                    tl.valor, ter.nombre_comercial 
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti  ON ti.id_unico  = ter.tipoidentificacion
        LEFT JOIN   gf_direccion           dr  ON dr.tercero   = ter.id_unico
        LEFT JOIN   gf_telefono            tl ON tl.tercero    = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# Cargue de variables de compañia
$razonsocial    = $rowCompania[6];
$nombreTipoIden = $rowCompania[1];
$numeroIdent    = $rowCompania[2];
$ruta           = $rowCompania[3];
$direccion      = $rowCompania[4];
$telefono       = $rowCompania[5];

#* DATOS DOCUMENTO
$row = $con->Listar("SELECT codigo_interno, codigo_ruta, sector, direccion, usuario, uso, estrato, 
    total_acdto_alc  - (ifnull(ROUND(acueducto_cargo_fijo), 0)+
        ifnull(ROUND(acueducto_consumo_basico), 0)+
        ifnull(ROUND(acueducto_consumo_complementario), 0)+
        ifnull(ROUND(acueducto_consumo_suntuario), 0)+
        ifnull(ROUND(acueducto_subsido_cargo_fijo)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_basico)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_complementario)*-1, 0)+
        ifnull(ROUND(acueducto_subsido_suntuario)*-1, 0)+
        ifnull(ROUND(acueducto_mora), 0)+
        ifnull(ROUND(alcantarillado_cargo_fijo), 0)+
        ifnull(ROUND(alcantarillado_consumo_basico), 0)+
        ifnull(ROUND(alcantarillado_consumo_complementario), 0)+
        ifnull(ROUND(alcantarillado_consumo_suntuario), 0)+
        ifnull(ROUND(alcantarillado_subsido_cargo_fijo)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_basico)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_complementario)*-1, 0)+
        ifnull(ROUND(alcantarillado_subsido_suntuario)*-1, 0)+
        ifnull(ROUND(deuda_anterior), 0)+
        ifnull(ROUND(financiacion), 0)+
        ifnull(ROUND(abonos), 0)+
        ifnull(ROUND(ajuste_peso), 0)
       ) as dif from gp_facturacion_servicios 
       HAVING dif =-4836.00 
       ORDER BY sector, codigo_ruta");
 


require'../fpdf/fpdf.php';
ob_start();
# Clase de diseño de formato
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
            $this->Rect($x, $y, $w, $h, $style);
            //Imprime el texto
            $this->MultiCell($w,5,$data[$i],'LR', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
        $this->Ln($h - 5);
    }

    function fila($data){
        //Calcula el alto de l afila
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
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
            $this->Rect($x, $y, 0, 0, $style);
            //Imprime el texto
            $this->MultiCell($w,5, $data[$i],'', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
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
            $nb–;
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

    #Funcón cabeza de la página
    function header(){
        #Redeclaración de varibles
        global $razonsocial; 
        global $nombreTipoIden; 
        global $ruta;           
        global $numeroIdent;    
        global $direccion;   
        global $telefono;    
        global $sigla;       
        global $tipo;    
        global $numero;    
        global $resolucion;
        
        if($ruta != ''){
          $this->Image('../'.$ruta,10,5,30);
        }
        $this->SetY(10);
        $this->SetFont('Arial','',8);
        $this->SetX(90);
        $this->Cell(50,5,utf8_decode('EMPODUITAMA S.A. E.S.P '),0,0,'R');
        $this->Cell(5,5,utf8_decode(''),0,0,'L');
        $this->Cell(20,5,utf8_decode('TEL. 098 7602711'),0,0,'L');
        $this->ln(3);
        $this->SetX(90);
        $this->Cell(50,5,utf8_decode('EDIFICIO MULTICENTRO '),0,0,'R');
        $this->Cell(5,5,utf8_decode(''),0,0,'L');
        $this->Cell(20,5,utf8_decode('TEL. 098 7604400'),0,0,'L');
        $this->ln(3);
        
        $this->SetX(90);
        $this->Cell(50,5,utf8_decode('DUITAMA COLOMBIA'),0,0,'R');
        $this->Cell(5,5,utf8_decode(''),0,0,'L');
        $this->Cell(20,5,utf8_decode('FAX. 098 7605304'),0,0,'L');
        $this->ln(3);

        $this->SetX(90);
        $this->Cell(50,5,utf8_decode('CALLE 16 14-68'),0,0,'R');
        $this->Cell(5,5,utf8_decode(''),0,0,'L');
        $this->Cell(20,5,utf8_decode('E. empoduitama@hotmail.com'),0,0,'L');
        $this->ln(3);

        $this->SetX(90);
        $this->Cell(50,5,utf8_decode('NIT.'),0,0,'R');
        $this->Cell(5,5,utf8_decode(''),0,0,'L');
        $this->Cell(20,5,utf8_decode('891855578-7'),0,0,'L');
        $this->ln(3);
    }
}

$pdf = new PDF('P','mm','Letter');      #Creación del objeto pdf
$nb=$pdf->AliasNbPages();                       #Objeto de número de pagina
$pdf->AddPage();                                #Agregar página
$pdf->SetFont('Arial','',10);
$pdf->Ln(10);
$num = 1046;
for ($i=0; $i <count($row) ; $i++) { 
    #** valores 
    $rowv = $con->Listar("SELECT DISTINCT acueducto_cargo_fijo, acueducto_subsido_cargo_fijo*-1 FROM gp_facturacion_servicios 
    where uso = '".$row[$i][5]."' and estrato ='".$row[$i][6]."' 
    AND acueducto_cargo_fijo != 0 and acueducto_subsido_cargo_fijo IS NOT NULL");
    $num = $num+1;
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode($num.'-2021'),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Duitama, 13 de Abril de 2021'),0,0,'L');
    $pdf->Ln(15);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Señor(a):'),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode($row[$i][4]),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Dirección: '.$row[$i][3]),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Código de Referencia: '.$row[$i][0]),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Ruta: '.$row[$i][2]),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Código Ruta: '.$row[$i][1]),0,0,'L');
    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Respetado Usuario:'),0,0,'L');
    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->Cell(185,5,utf8_decode('REF: COBRO DE VALOR CARGO FIJO  CORRESPONDIENTE AL PERIODO DE MARZO DE 2021'),0,0,'R');
    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->MultiCell(185,5,utf8_decode('Nos permitimos informarle, que por motivos ajenos a nuestra voluntad,  por una falla técnica al momento de generar la factura correspondiente al PERIODO DE MARZO DE 2021, no le fue cobrado el valor del CARGO FIJO para el servicio de acueducto.'),0,'J');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->MultiCell(185,5,utf8_decode('Por lo anterior, la empresa cargará el valor respectivo en la facturación del período de abril de 2021, sin ocasionar ningún costo por mora ni interés alguno.'),0,'J' );
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('El valor que le será cobrado según el estrato y uso que le aplica es:'),0,0,'L' );

    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->Cell(45,10,utf8_decode('VALOR CARGO FIJO'),1,0,'L' );
    $pdf->Cell(40,10,'$'.number_format($rowv[0][0], 2),1,0,'R' );
    $pdf->Ln(10);
    $pdf->SetX(15);
    $y = $pdf->GetY();
    $pdf->MultiCell(45,5,utf8_decode('VALOR SUBSIDIO O SOBREPRECIO (+/-)'),1,'L' );
    $pdf->SetXY(60,$y);
    $pdf->Cell(40,10,utf8_decode('$'.number_format($rowv[0][1], 2)),1,0,'R' );
    $pdf->Ln(10);
    $pdf->SetX(15);
    $tt = $rowv[0][0]+$rowv[0][1];
    $pdf->Cell(45,10,utf8_decode('TOTAL A COBRAR'),1,0,'L' );
    $pdf->Cell(40,10,utf8_decode('$'.number_format($tt, 2)),1,0,'R' );
    $pdf->Ln(10);
    $pdf->Ln(8);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Agradezco su atención y comprensión.'),0,0,'L' );
    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Atentamente, '),0,0,'L' );
    $pdf->Ln(20);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('Área de Comercialización'),0,0,'L' );
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('EMPODUITAMA S.A. E.S.P.'),0,0,'L' );
    $pdf->Ln(10);
    $pdf->SetX(15);
    $pdf->Cell(200,5,utf8_decode('C.C. Archivo.'),0,0,'L' );
    $pdf->AddPage();
    $pdf->Ln(5);
}
ob_end_clean();                                             #Limpieza del buffer
$pdf->Output(0,'Informe_Inconsistencias.pdf',0);       #Salida del documento

