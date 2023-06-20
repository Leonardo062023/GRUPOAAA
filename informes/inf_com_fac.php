<?php 

require_once("../Conexion/conexion.php");
require'../Conexion/ConexionPDO.php';
require '../code128.php';
$con = new ConexionPDO();
header("Content-Type: text/html;charset=utf-8");
ini_set('max_execution_time', 0);
ob_start();
session_start();    # Session
list($rep, $dir_t, $ciu_t, $tel_t) = array("", "", "", "");
$compania = $_SESSION['compania'];
# Array de meses del año
$meses    = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
# Consulta para obtener los datos de compañia
# @$sqlC
$sqlC = "SELECT     ter.razonsocial,
                    ti.nombre,
                    ter.numeroidentificacion,
                    ter.ruta_logo,
                    dr.direccion,
                    tl.valor
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti  ON ti.id_unico  = ter.tipoidentificacion
        LEFT JOIN   gf_direccion           dr  ON dr.tercero   = ter.id_unico
        LEFT JOIN   gf_telefono            tl ON tl.tercero    = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# Cargue de variables de compañia
$razonsocial    = $rowCompania[0];
$nombreTipoIden = $rowCompania[1];
$numeroIdent    = $rowCompania[2];
$ruta           = $rowCompania[3];
$direccion      = $rowCompania[4];
$telefono       = $rowCompania[5];
# Captura de id de factura
$factura = $_GET['factura'];
# Consulta para obtener los datos de factura
# @sqlF {String}
$sqlF = "SELECT     fat.id_unico,
                    tpf.nombre,
                    fat.numero_factura,
                    CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
                    fat.fecha_factura,
                    date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
                    IF(ter.razonsocial IS NULL OR ter.razonsocial ='', 
                        CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos), ter.razonsocial
                    ) AS 'NOMBRE',
                    CONCAT_WS(' ', ter.numeroidentificacion, ter.digitoverficacion),
                    fat.descripcion,
                    tpf.resolucion,
                    gdr.direccion,
                    gtl.valor,
                    gci.nombre,
                    ter.nombre_comercial, 
                    IF(fat.forma_pago =1, 'Contado', IF(fat.forma_pago =2,'Crédito','')), 
                    fp.nombre,fat.cuotas, fat.abono , fat.fecha_vencimiento, 
                    uv.codigo_interno, uv.codigo_ruta , 
                    IF(t.razonsocial IS NULL OR t.razonsocial ='', 
                        CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos), t.razonsocial
                    ) 
        FROM        gp_factura      AS fat
        LEFT JOIN   gp_tipo_factura AS tpf ON tpf.id_unico         = fat.tipofactura
        LEFT JOIN   gf_tercero      AS ter ON ter.id_unico         = fat.tercero
        LEFT JOIN   gf_direccion    AS gdr ON gdr.tercero          = ter.id_unico
        LEFT JOIN   gf_telefono     AS gtl ON gtl.tercero          = ter.id_unico
        LEFT JOIN   gf_ciudad       AS gci ON gdr.ciudad_direccion = gci.id_unico
        LEFT JOIN   gf_forma_pago   AS fp  ON fat.metodo_pago = fp.id_unico 
        LEFT JOIN   gp_unidad_vivienda_medidor_servicio uvms ON uvms.id_unico = fat.unidad_vivienda_servicio
        LEFT JOIN   gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN   gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        WHERE       md5(fat.id_unico) = '$factura'";
$resultF = $mysqli->query($sqlF);
$rowF    = mysqli_fetch_row($resultF);
# Cargue de variables de factura
$fat_id      = $rowF[0];  $tip_fat     = $rowF[1];  $num_fat     = $rowF[2];
$dia_fat     = $rowF[3];  $fecha_fat   = $rowF[4];  $fechaV_fat  = $rowF[5];
$tercero_fat = $rowF[6];  $num_ter_f   = $rowF[7];  $desc_fat    = $rowF[8];
$resolucion  = $rowF[9];  $dir_t       = $rowF[10]; $tel_t       = $rowF[11];
$ciu_t       = $rowF[12]; $nomComerc   = $rowF[13]; $forma_p     = $rowF[14];
$metodo_p    = $rowF[15]; $cuotas      = $rowF[16]; $abono       = $rowF[17];
$fecha_v     = $rowF[18]; $usuariosp   = $rowF[19].' - '.$rowF[20].' '.$rowF[21];
# Consulta de representante legal
$str_r = "SELECT    gtr.representantelegal,
                    (
                      IF(
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos) = '',
                        grp.razonsocial,
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos)
                      )
                    ) AS nom,
                    gtl.valor,
                    gdr.direccion,
                    gci.nombre
          FROM      gf_tercero   AS gtr
          LEFT JOIN gf_tercero   AS grp ON gtr.representantelegal = grp.id_unico
          LEFT JOIN gf_telefono  AS gtl ON gtl.tercero            = grp.id_unico
          LEFT JOIN gf_direccion AS gdr ON gdr.tercero            = grp.id_unico
          LEFT JOIN gf_ciudad    AS gci ON gdr.ciudad_direccion   = gci.id_unico
          WHERE     gtr.id_unico = $tercero_fat";
$res_r = $mysqli->query($str_r);
if($res_r->num_rows > 0){
    $row_r = $res_r->fetch_row();
    $rep   = $row_r[1];
    $tel_t = $row_r[2];
    $dir_t = $row_r[3];
    $ciu_t = $row_r[4];
}

if(empty($rep)){
    $rep = $tercero_fat;
}

$rep = !empty($nomComerc)?$rep.' / '.$nomComerc:$rep;
# Clase de diseño de formato
class PDF extends FPDF{
    function header(){
    }
}
//$pdf = new PDF('P','mm',array(140,210));   
$pdf=new PDF_Code128('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->AliasNbPages();
if($ruta != ''){
  $pdf->Image('../'.$ruta,10,8,30);
}
$pdf->SetFont('Helvetica','B',10);
$pdf->SetXY(40,15);
$pdf->MultiCell(140,5,utf8_decode($razonsocial),0,'C');
$pdf->SetX(10);
$pdf->MultiCell(200,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono")),0,'C');
$pdf->SetFont('Helvetica','B',9);
$pdf->Cell(200, 5, utf8_decode($resolucion), 0, 0,'C');
$pdf->Ln(2);
$pdf->MultiCell(200,5,utf8_decode(ucwords(strtoupper($tip_fat))).' NRO: '.$num_fat,0,'C');
$pdf->Ln(5);


$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('FECHA:'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode(date("d/m/Y", strtotime($fecha_fat))), 1, 0,'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('FECHA VENCIMIENTO'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($fechaV_fat), 1, 0,'L');
$pdf->Ln(5);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('SEÑOR(ES)'),0, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(60, 5, utf8_decode($tercero_fat),  0,'L');
$y1 = $pdf->GetY();
$a  = $y1-$y;
$pdf->SetXY($x+93, $y);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('RAZÓN SOCIAL'), 0, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(60, 5, utf8_decode($rep), 0,'L');
$y2 = $pdf->GetY();
$a2 = $y2-$y;
$pdf->SetXY($x, $y);
$alt = max($a1,$a2);
$pdf->Cell(33, $alt,'', 1, 0,'L');
$pdf->Cell(60, $alt,'', 1, 0,'L');
$pdf->Cell(42, $alt,'', 1, 0,'L');
$pdf->Cell(60, $alt,'', 1, 0,'L');
$pdf->Ln($alt);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('NIT / CC'), 0, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($num_ter_f), 0, 0,'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('DIRECCIÓN'), 0, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(60, 5, utf8_decode($dir_t), 0,'L');
$y2 = $pdf->GetY();
$a2 = $y2-$y;

$pdf->SetXY($x, $y);
$alt = max($a2,$a2);
$pdf->Cell(33, $alt,'', 1, 0,'L');
$pdf->Cell(60, $alt,'', 1, 0,'L');
$pdf->Cell(42, $alt,'', 1, 0,'L');
$pdf->Cell(60, $alt,'', 1, 0,'L');
$pdf->Ln($alt);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('TELÉFONO:'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($tel_t), 1, 0,'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('CIUDAD'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($ciu_t), 1, 0,'L');
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('FORMA DE PAGO:'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($forma_p), 1, 0,'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('MÉTODO DE PAGO'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($metodo_p), 1, 0,'L');
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(33, 5, utf8_decode('CUOTAS:'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($cuotas), 1, 0,'L');
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(42, 5, utf8_decode('ABONO'), 1, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(60, 5, utf8_decode($abono), 1, 0,'L');
$pdf->Ln(5);


if (empty($desc_fat) || $desc_fat === 'NULL'){
    $desc_fat = '';
}
$pdf->SetFont('Helvetica', 'B', 10);
$y1 = $pdf->GetY();
$pdf->Cell(33, 5, utf8_decode('OBSERVACIONES:'), 0, 0,'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(162, 5, utf8_decode($desc_fat),0,'L');
$y2 = $pdf->GetY();
$h  = $y2- $y1;
$pdf->SetY($y1);
$pdf->MultiCell(195, $h, '', 1, 'L');
if(!empty($usuariosp)){
    $y1 = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(33, 5, utf8_decode('USUARIO:'), 0, 0,'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->MultiCell(195, 5, utf8_decode($usuariosp),0,'L');
    
    $y2 = $pdf->GetY();
    $h  = $y2- $y1;
    $pdf->SetY($y1);
    $pdf->MultiCell(195, $h, '', 1, 'L');
}
$pdf->Ln(8);

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(20, 5, 'CODIGO', 'LTR', 0, 'C');
$pdf->Cell(60, 5, 'PRODUCTO', 'LTR', 0, 'C');
$pdf->Cell(20, 5, 'UNIDAD', 'LTR', 0, 'C');
$pdf->Cell(20, 5, 'CANTIDAD', 'LTR', 0, 'C');
$pdf->Cell(25, 5, 'PRECIO', 'LTR', 0, 'C');
$pdf->Cell(25, 5, 'DESCUENTO', 'LTR', 0, 'C');
$pdf->Cell(25, 5, 'TOTAL', 'LTR', 0, 'C');
$pdf->Ln(5);
$pdf->Cell(20, 5, '', 'LBR', 0, 'C');
$pdf->Cell(60, 5, '', 'LBR', 0, 'C');
$pdf->Cell(20, 5, '', 'LBR', 0, 'C');
$pdf->Cell(20, 5, '', 'LBR', 0, 'C');
$pdf->Cell(25, 5, 'UNITARIO', 'LBR', 0, 'C');
$pdf->Cell(25, 5, '', 'LBR', 0, 'C');
$pdf->Cell(25, 5, '', 'LBR', 0, 'C');
$pdf->Ln(5);

list($sumV, $sumIva, $sumImpo, $sumD) = array(0, 0, 0, 0);

$str = "SELECT      pln.codi,
                    conp.nombre,
                    dtf.cantidad,
                    dtf.valor,
                    dtf.iva,
                    dtf.impoconsumo,
                    dtf.ajuste_peso,
                    dtf.valor_total_ajustado,
                    dtf.descuento, 
                    ud.nombre 
        FROM        gp_detalle_factura AS dtf
        LEFT JOIN   gp_concepto        AS conp ON conp.id_unico = dtf.concepto_tarifa
        LEFT JOIN   gf_plan_inventario AS pln  ON conp.plan_inventario = pln.id_unico
        LEFT JOIN   gf_unidad_factor   AS ud   ON dtf.unidad_origen = ud.id_unico 
        WHERE       md5(dtf.factura) = '".$_REQUEST['factura']."'";
$res  = $mysqli->query($str);
$data = $res->fetch_all(MYSQLI_NUM);
foreach ($data as $row){
    $sub      = $row[3] * $row[2];
    $sumV    += $sub;
    $sumIva  += ($row[4] * $row[2]);
    $sumImpo += ($row[5] * $row[2]);
    $pdf->SetFont('Helvetica','',8);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(20, 5, utf8_decode($row[0]), 0, 0,'L');
    $pdf->MultiCell(60, 5, utf8_decode($row[1]),  0,'L');
    $y2 = $pdf->GetY();
    $a2 = $y2-$y;

    $pdf->SetXY($x+80, $y);
    $alt = max($a2,$a2);
    $pdf->Cell(20, 5, utf8_decode($row[9]), 0, 0,'L');
    $pdf->Cell(20, 5, utf8_decode($row[2]), 0, 0,'L');
    $pdf->Cell(25, 5, utf8_decode(number_format($row[3], 2)), 0, 0,'R');
    $pdf->Cell(25, 5, utf8_decode(number_format($row[8], 2)), 0, 0,'R');
    $pdf->Cell(25, 5, utf8_decode(number_format($sub, 2)), 0, 0,'R');
    $pdf->Ln($alt);
}

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(135, 5, '', 'LTR', 0, 'C');
$pdf->Cell(30, 5, 'TOTAL','TLR', 0, 'L');
$pdf->Cell(30, 5, number_format($sumV, 2), 'TLR', 0, 'R');
$pdf->Ln(5);
$pdf->Cell(135, 5, '', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR IVA','LR', 0, 'L');
$pdf->Cell(30, 5, number_format($sumIva, 2), 'LR', 0, 'R');
$pdf->Ln(5);
$xxx = $sumIva + $sumV + $sumImpo;
$pdf->Cell(135, 5, '', 'LRB', 0, 'C');
$pdf->Cell(30, 5, 'NETO A PAGAR','LRB', 0, 'L');
$pdf->Cell(30, 5, number_format($xxx, 2), 'LRB', 0, 'R');
$pdf->Ln(5);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(115, 5, 'Esta Factura se asimila a la Letra de Cambio ART.774 Acepto la presente y declaro haber recibido el material mencionado', '', 0, 'L');
$pdf->Ln(15);
$pdf->SetFont('Helvetica', '', 9);
$pdf->SetX(20);
$pdf->Cell(50, 5, '', 'B', 0,'C');
$pdf->SetX(80);
$pdf->Cell(50, 5, '', 'B', 0,'C');
$pdf->SetX(135);
$pdf->Cell(50, 5, '', 'B', 0,'C');
$pdf->Ln(5);
$pdf->SetX(20);
$pdf->Cell(50, 5, 'ELABORO', '', 0, 'C');
$pdf->SetX(80);
$pdf->Cell(50, 5,   utf8_decode('AUTORIZACIÓN'), '', 0, 'C');
$pdf->SetX(135);
$pdf->Cell(50, 5, 'RECIBIDO CLIENTE', '', 0, 'C');
$pdf->Ln(25);


#colilla
$pdf->SetY(210);
if(empty($abono) && empty($cuotas)){ 
    $xt1     =  number_format($xxx, 0);
    $xt      = str_replace(',','',$xt1);
} elseif(!empty($cuotas) && empty($abono)) {
    $xt1     = number_format(0, 0);
    $xt      = str_replace(',','',$xt1);
} elseif(!empty($abono)){
    $xt1     = number_format($abono, 0);
    $xt      = str_replace(',','',$xt1);
} else {
    $xt1     =  number_format($xxx, 0);
    $xt      = str_replace(',','',$xt1);
}


$y = $pdf->GetY();
$x = $pdf->GetX();

if($ruta != ''){
  $pdf->Image('../'.$ruta,$x,$y,20);
}
$pdf->SetFont('Helvetica','B',8);
$pdf->SetX(35);
$pdf->MultiCell(140,4,utf8_decode($razonsocial),0,'L');
$pdf->SetX(35);
$pdf->MultiCell(100,4,utf8_decode(ucwords(strtoupper($tip_fat))).' NRO: '.$num_fat,0,'L');
$pdf->SetY($y+4);
$pdf->MultiCell(195,4,utf8_decode('Usuario: '.$usuariosp),0,'R');
$pdf->MultiCell(195,4,utf8_decode('Valor A Pagar: '.$xt1),0,'R');
$yf = $pdf->GetY();
$pdf->SetXY($x, $y);
$h = $yf -$y;
$pdf->Cell(195, $h+5, '', 1, 0, 'C');
$pdf->Ln($h+5);
$yc = $pdf->GetY();
$pdf->Cell(120, 30, '', 1, 0, 'C');
$pdf->Cell(75, 30, '', 1, 0, 'C');
$pdf->Ln(30);
$pdf->SetY($yc+25);

$pdf->SetFont('Helvetica','',3);
$pdf->Cell(120, 5, '', 0, 0, 'L');
$pdf->Cell(30, 5, utf8_decode('FAVOR NO COLOCAR SELLOS NI FIRMAS EN EL CÓDIGO DE BARRAS'), 0, 0, 'L');
$pdf->Cell(45, 5, 'ESPACIO PARA EL TIMBRE Y REGISTRO BANCO', 0, 0, 'R');
$pdf->SetY($yc);
$yc = $pdf->GetY();

$ct     = strlen($xt);
if($ct < 10){
    $xt = str_pad($xt, 10, "0", STR_PAD_LEFT);
}   


$fechart = explode('-',$fecha_v);
$dia     = $fechart[2];
$mes     = $fechart[1];
$anio    = $fechart[0];
$fechart = $anio.$mes.$dia;

$ref     = $num_fat;
$cr      = strlen($ref);
if($cr < 8){
    $ref = str_pad($ref, 8, "0", STR_PAD_LEFT);
}
$rowCod = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE indicador ='20210004'");
$codigoEAN = $rowCod[0][0];

    $format_barcode = "415".$codigoEAN."8020".$ref."3900".$xt."96".$fechart;
    $barcode = "(415)$codigoEAN(8020)$ref(3900)$xt(96)$fechart"; 
 /*   $pdf->setFillColor(0, 0, 0);          	
	$pdf->Code128(45,$yc,$format_barcode,130,25);		
    $pdf->SetXY(60, $yc+24);
    $pdf->SetFont('Arial','',7); 
    $pdf->Cell(100,9, utf8_decode($barcode),'',0,'C');
*/

## COlilla

$pdf->setFillColor(0, 0, 0);            
    $pdf->Code128(15,$yc+3,$format_barcode,110,20);       
    $pdf->SetXY(20, $yc+20);
    $pdf->SetFont('Arial','',7); 
    $pdf->Cell(100,9, utf8_decode($barcode),'',0,'C');


ob_end_clean();     
$pdf->Output(0,'Comprobante_Factura_.pdf',0);

?>