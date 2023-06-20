<?php

header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
ob_start();
session_start();
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, 
    tipCom.codigo, tipCom.nombre, comp.tercero ,  UPPER(comp.usuario), DATE_FORMAT(comp.fecha_elaboracion,'%d/%m%/%Y') , 
    t.id_unico, 
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
                    t.apellidodos)) AS NOMBRE,
                IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                    t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                dp.nombre 
      FROM gf_comprobante_pptal comp
      LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
      LEFT JOIN gf_tercero t ON comp.responsable  = t.id_unico 
      LEFT JOIN gf_dependencia_responsable dr ON dr.responsable = t.id_unico 
      LEFT JOIN gf_dependencia dp ON dr.dependencia = dp.id_unico 
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND md5(comp.id_unico) = '$id'";
} else {

    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, 
    tipCom.codigo, tipCom.nombre, comp.tercero ,  UPPER(comp.usuario), DATE_FORMAT(comp.fecha_elaboracion,'%d/%m%/%Y') , 
    t.id_unico, 
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
                    t.apellidodos)) AS NOMBRE,
                IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                    t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                dp.nombre 
      FROM gf_comprobante_pptal comp
      LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
      LEFT JOIN gf_tercero t ON comp.responsable  = t.id_unico 
      LEFT JOIN gf_dependencia_responsable dr ON dr.responsable = t.id_unico 
      LEFT JOIN gf_dependencia dp ON dr.dependencia = dp.id_unico 
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND (comp.id_unico) = " . $_SESSION['id_comp_pptal_ED'];
}


$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$idcomprobante = $rowComp[0];
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$fechaComprobante = $rowComp[2]; //Fecha  
$usuario = $rowComp[9];
$fechaElaboracion = $rowComp[10];

#Parametro CC
$pcc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE indicador= '20210001' AND compania=$compania");

if(!empty($pcc)){
    if($pcc[0][0]=='Si'){
        $parametro_cc = 1;
    }
}
#Parametro Responsable 
$parametro_res = 0;
$pcc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE indicador= '20210002' AND compania=$compania");

if(!empty($pcc)){
    if($pcc[0][0]=='Si'){
        $parametro_res = 1;
    }
}
if($parametro_res==1) {
    $responsable = $rowComp[12].' - '.$rowComp[13];
    $dependencia_resp = $rowComp[14];
}  else {
    $responsable        = '';
    $dependencia_resp   = '';
}
$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = ' . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre , ter.razonsocial, ter.numeroidentificacion , 
    ter.digitoverficacion 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
$comp = $rowLogo[2];
if (empty($rowLogo[4])) {
    $nitcom = $rowLogo[3];
} else {
    $nitcom = $rowLogo[3] . ' - ' . $rowLogo[4];
}

$fecha_div = explode("-", $fechaComp);
$diaS = $fecha_div[2];
$mesS = $fecha_div[1];
$anioS = $fecha_div[0];

$fechaComp = $diaS . '/' . $mesS . '/' . $anioS;

$fecha_divV = explode("-", $fechaVen);
$diaSV = $fecha_divV[2];
$mesSV = $fecha_divV[1];
$anioSV = $fecha_divV[0];

$fechaV = $diaSV . '/' . $mesSV . '/' . $anioSV;
$anio = $_SESSION['anno'];
$anio2 = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = " . $anio;
$anio2 = $mysqli->query($anio2);
$anio1 = mysqli_fetch_row($anio2);
$anio1 = $anio1[0];


class PDF extends FPDF {

    function Header() {

        global $fechaComp;
        global $ruta;
        global $comp;
        global $nitcom;
        global $nombre;
        global $nomcomp;
        global $anio1;
        global $numP;

        $numP = $this->PageNo();

        if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 8, 25);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(100);
        $this->SetXY(28, 13); //EStaba
        $this->SetFont('Arial', 'B', 12);
        $this->MultiCell(175, 7, utf8_decode(mb_strtoupper($comp)), 0, 'C');
        $this->SetX(28);
        $this->Cell(175, 5, utf8_decode('NIT: ' . $nitcom), 0, 0, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(5);
        $this->SetX(28);
        $this->Cell(175, 5, mb_strtoupper($nombre), 0, 0, 'C');
        $this->Ln(5);
        $this->SetX(28);
        $this->Cell(175, 5, utf8_decode('Número: ' . $nomcomp), 0, 0, 'C');


        $this->Ln(10);
    }

// Pie de página
    function Footer() {
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(63, 10, 'Elaborado por: ' . strtoupper($usuario), 0);
        $this->Cell(64, 10, '', 0, 0, 'C');
        $this->Cell(63, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 10);

$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 12);
$pdf->MultiCell(180, 5, utf8_decode('EL SUSCRITO CERTIFICA:'), 0, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);


$pdf->MultiCell(180, 5, utf8_decode("Que una vez revisado el libro "
                . "de control de presupuesto correspondiente a la vigencia fiscal "
                . "del año $anio1 se encontró que existe disponibilidad presupuestal para cubrir "
                . "el siguiente gasto:"), 0, 'J');
$pdf->Ln(2);
//Cabecera para Página 1

$pdf->Cell(20, 5, utf8_decode('Fecha:'), 0, 'L');
$pdf->SetFont('Arial', '', 9);
$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$anioS = $fecha_div[2];
$pdf->Cell(160, 5, utf8_decode($fechaComp), 0, 'L');

$sqlDetall = "SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, 
    rub.nombre nombreRubro, detComP.valor, rubFue.id_unico, fue.nombre, detComP.saldo_disponible, cc.nombre  
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      LEFT JOIN gf_centro_costo cc ON detComP.centro_costo = cc.id_unico 
      where (detComP.comprobantepptal) ='$idcomprobante'";
$detalle = $mysqli->query($sqlDetall);

$pdf->Ln(6);

if($parametro_cc==1){ 
    $pdf->SetFont('Arial', 'B', 9, 0, 'C');
    $pdf->Cell(45, 5, 'Rubro', 1, 0, 'C');
    $pdf->Cell(45, 5, 'Fuente', 1, 0, 'C');
    $pdf->Cell(40, 5, 'Centro Costo', 1, 0, 'C');
    $pdf->Cell(30, 5, 'Saldo Disponible', 1, 0, 'C');
    $pdf->Cell(30, 5, 'Valor', 1, 0, 'C');

    $pdf->Ln(5);

    $totalValor = 0;
    $pdf->SetFont('Arial', '', 8);
    while ($rowDetall = mysqli_fetch_array($detalle)) {
        $totalValor         += $rowDetall[3];
        $saldoDisponible    = $rowDetall[6];
        $codiRub            = $rowDetall[1];
        $nombreRub          = ($rowDetall[2]);
        $fuente             = ($rowDetall[5]);
        $valorR             = number_format($rowDetall[3], 2, '.', ',');
        $saldoDis           = number_format($saldoDisponible, 2, '.', ',');
        
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();

        $pdf->MultiCell(45, 5, utf8_decode($codiRub . ' - ' . $nombreRub), 0, 'L');
        $y2 = $pdf->GetY();
        $h = $y2 - $y1;
        $px = $x1 + 45;

        $pdf->SetXY($px, $y1);
        $pdf->MultiCell(45, 5, utf8_decode($fuente), 0, 'L');
        $y21 = $pdf->GetY();
        $h1  = $y21 - $y1;
        $px  = $px + 45;

        $pdf->SetXY($px, $y1);
        $pdf->MultiCell(40, 5, utf8_decode($rowDetall[7]), 0, 'L');
        $y22 = $pdf->GetY();
        $h2  = $y22 - $y1;
        $alt = max($h, $h1,$h2);

        $pdf->SetXY($x1, $y1);
        $pdf->Cell(45, $alt, utf8_decode(''), 1, 0, 'L');
        $pdf->Cell(45, $alt, utf8_decode(''), 1, 0, 'L');
        $pdf->Cell(40, $alt, utf8_decode(''), 1, 0, 'L');
        $pdf->Cell(30, $alt, utf8_decode($saldoDis), 1, 0, 'R');
        $pdf->Cell(30, $alt, utf8_decode($valorR), 1, 0, 'R');

        $pdf->Ln($alt);
        if ($pdf->GetY() > 220) {
            $pdf->AddPage();
        }
    }
} else {
    $pdf->SetFont('Arial', 'B', 9, 0, 'C');
    $pdf->Cell(60, 5, 'Rubro', 1, 0, 'C');
    $pdf->Cell(60, 5, 'Fuente', 1, 0, 'C');
    $pdf->Cell(35, 5, 'Saldo Disponible', 1, 0, 'C');
    $pdf->Cell(35, 5, 'Valor', 1, 0, 'C');

    $pdf->Ln(5);

    $totalValor = 0;
    $pdf->SetFont('Arial', '', 8);
    while ($rowDetall = mysqli_fetch_array($detalle)) {

        $totalValor += $rowDetall[3];

        $saldoDisponible = $rowDetall[6];
        $codiRub = $rowDetall[1];
        $nombreRub = ($rowDetall[2]);
        $fuente = ($rowDetall[5]);
        $valorR = number_format($rowDetall[3], 2, '.', ',');
        $saldoDis = number_format($saldoDisponible, 2, '.', ',');
        #Impresión de varibles y llamado de metodo
        if (strlen($nombreRub) > 35) {
            $altY = $pdf->GetY();
            if ($altY > 245) {
                $pdf->AddPage();
            }
        }

        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();
        $pdf->MultiCell(60, 5, utf8_decode($codiRub . ' - ' . $nombreRub), 0, 'L');
        $y2 = $pdf->GetY();
        $h = $y2 - $y1;
        $px = $x1 + 60;
        $pdf->SetXY($px, $y1);
        $y11 = $pdf->GetY();
        $x11 = $pdf->GetX();
        $pdf->MultiCell(60, 5, utf8_decode($fuente), 0, 'L');
        $y21 = $pdf->GetY();
        $h1 = $y21 - $y11;
        $px1 = $x11 + 60;
        $pdf->SetXY($px1, $y11);
        $alt = max($h, $h1);

        $pdf->SetX($x1);
        $pdf->Cell(60, $alt, utf8_decode(''), 1, 0, 'L');
        $pdf->Cell(60, $alt, utf8_decode(''), 1, 0, 'L');
        $pdf->Cell(35, $alt, utf8_decode($saldoDis), 1, 0, 'R');
        $pdf->Cell(35, $alt, utf8_decode($valorR), 1, 0, 'R');

        $pdf->Ln($alt);
        $altY = $pdf->GetY();
        if ($altY > 220) {
            $pdf->AddPage();
        }
    }
}



$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(155, 5, 'TOTAL DISPONIBILIDAD:', 0, 0, 'R'); //Rubro
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R'); //Valor Sí.
$pdf->SetFont('Arial', '', 10);
//$descripcion
$pdf->Ln(10);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, utf8_decode('Concepto: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(35);
$pdf->MultiCell(165, 5, utf8_decode($descripcion), 0, 'J');
$pdf->Ln(5);
$y2 = $pdf->GetY();
$h = $y2 - $y;
$pdf->SetXY($x, $y);
$pdf->Cell(190, $h, '', 1, 0, 'L');
$pdf->SetX(10);

$pdf->Ln($h);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, utf8_decode('Son: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(35);
$valorLetras = numtoletras($totalValor);
$pdf->MultiCell(155, 5, utf8_decode($valorLetras), 0, 'J');
$pdf->SetX(10);
$pdf->Ln(3);
if($parametro_res==1){
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 5, utf8_decode('Responsable: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 5, utf8_decode($responsable), 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 5, utf8_decode('Dependencia: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 5, utf8_decode($dependencia_resp), 0, 0, 'L');
    $pdf->Ln(10);
} 

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int) $mesS;
$anioS = $fecha_div[2];


$pdf->SetFont('Arial', 'B', 10);
$ciudadCompania = mb_strtoupper($ciudadCompania, 'utf-8');
$pdf->Cell(60, 5, utf8_decode("NOTA: Este cerficado tiene validez para su utilización hasta 31/12/".$anioS), 0, 'J');
$pdf->Ln(33);


#****************Consulta SQL para Firma****************#
$sqlTipoComp = "SELECT IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     UPPER(t.razonsocial),
     CONCAT_WS(' ',
     UPPER(t.nombreuno),
     UPPER(t.nombredos),
     UPPER(t.apellidouno),
     UPPER(t.apellidodos))) AS NOMBRE, ti.nombre, t.numeroidentificacion, UPPER(car.nombre) , 
     rd.fecha_inicio, rd.fecha_fin , t.tarjeta_profesional 
  FROM gf_tipo_comprobante_pptal tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
  LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE tcp.id_unico = $tipocomprobante 
  AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC";
//$fechaComp
$tipComp = $mysqli->query($sqlTipoComp);
$resultF1 = $mysqli->query($sqlTipoComp);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop - $altofinal;

$c = 0;
while ($cons = mysqli_fetch_row($resultF1)) {
    $c++;
}

$tfirmas = ($c / 2) * 33;


$xt = 10;
while ($firma = mysqli_fetch_row($tipComp)) {

    if (!empty($firma[5])) {
        if ($fechaComprobante <= $firma[5]) {

            if ($xt < 50) {
                #Construcción de linea firma
                $xm = 10;
                $pdf->setX($xm);
                $pdf->SetFont('Arial', 'B', 10);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                $pdf->setX($xm);
                #Obtención de alto final        
                $x2 = $pdf->GetX();
                #Posición final de firma 2    
                $pdf->Ln(0);
                $xt = 120;
            } else {
                $xn = 120;
                $pdf->SetY($y);
                #Construcción de linea firma
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX($xn);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                #alto inicial
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                #Obtención de alto final      
                $x2 = $pdf->GetX();
                #Posición del ancho     
                $posicionY = $y - 20;
                #Ubicación firma 2
                $pdf->SetXY($x2, $posicionY);
                #Posición final de firma
                $xt = 0;
            }
        }
    } elseif (!empty($firma[4])) {

        if ($fechaComprobante >= $firma[4]) {
            if ($xt < 50) {
                #Construcción de linea firma
                $xm = 10;
                $pdf->setX($xm);
                $pdf->SetFont('Arial', 'B', 10);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                $pdf->setX($xm);
                #Obtención de alto final        
                $x2 = $pdf->GetX();
                #Posición final de firma 2    
                $pdf->Ln(0);
                $xt = 120;
            } else {
                $xn = 120;
                $pdf->SetY($y);
                #Construcción de linea firma
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX($xn);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                #alto inicial
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                #Obtención de alto final      
                $x2 = $pdf->GetX();
                #Posición del ancho     
                $posicionY = $y - 20;
                #Ubicación firma 2
                $pdf->SetXY($x2, $posicionY);
                #Posición final de firma
                $xt = 0;
            }
        }
    } else {
        if ($xt < 50) {
            #Construcción de linea firma
            $xm = 10;
            $pdf->setX($xm);
            $pdf->SetFont('Arial', 'B', 10);
            #Linea para firma
            $pdf->Cell(60, 0, '', 1);
            #Varibles x,y
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de cargo de responsable de documento
            if (!empty($firma[6])) {
                $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
            } else {
                $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
            }
            $pdf->setX($xm);
            #Obtención de alto final        
            $x2 = $pdf->GetX();
            #Posición final de firma 2    
            $pdf->Ln(0);
            $xt = 120;
        } else {
            $xn = 120;
            $pdf->SetY($y);
            #Construcción de linea firma
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setX($xn);
            #Linea para firma
            $pdf->Cell(60, 0, '', 1);
            #Varibles x,y
            $x = $pdf->GetX();
            #alto inicial
            $y = $pdf->GetY();
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de cargo de responsable de documento
            if (!empty($firma[6])) {
                $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
            } else {
                $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
            }
            #Obtención de alto final      
            $x2 = $pdf->GetX();
            #Posición del ancho     
            $posicionY = $y - 20;
            #Ubicación firma 2
            $pdf->SetXY($x2, $posicionY);
            #Posición final de firma
            $xt = 0;
        }
    }
}
while (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
?>

