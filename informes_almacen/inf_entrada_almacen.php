<?php

header("Content-Type: text/html;charset=utf-8");
session_start();
require '../Conexion/conexion.php';
require_once("../Conexion/ConexionPDO.php");
require '../numeros_a_letras.php';
require_once ('../modelAlmacen/movimiento.php');
$con    = new ConexionPDO(); 
//Captura de variables
$mov = $_GET['mov'];
$compania = $_SESSION['compania'];
//Array para igualar los numeros de meses
$meses = array('no','01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
$movimiento = new mov();
//Consulta para obtener los datos de la compañia
$rowC = $movimiento->data_compania($compania);
$razonSocial = $rowC[0]; $tipoIdent = $rowC[1]; $numeroIdent = $rowC[2]; $ruta = $rowC[3];//Razon social, tipo de identificación, numero de identificación, Ruta de logo
//Consulta para obtener los datos del movimiento
$rowMov = $movimiento->data_movimiento($mov);
$id_mov = $rowMov[0]; $tipo_mov = $rowMov[1]; $numero_mov = $rowMov[2]; $dia_letras = $rowMov[3]; $n_dia = $rowMov[4];$n_mes = $rowMov[5]; $anno = $rowMov[6];
$ciudad = $rowMov[7]; $tipo_doc_aso = $rowMov[8]; $tercero = $rowMov[10]; $dependencia = $rowMov[11]; $descripcion = $rowMov[12]; $observaciones = $rowMov[13]; $id_tipo_mov = $rowMov[14];
$tercero2 = $rowMov[15]; $id_tercero2 = $rowMov[16];
$rowTer   = $movimiento->data_tercero($id_tercero2);
if(!empty($tipo_doc_aso)) {
    switch ($tipo_doc_aso){
        case '1':
            $factura = $rowMov[9];
            $remision = "0";
            break;
        case '2':
            $remision = $rowMov[9];
            $factura = "0";
            break;
        case '3':
            $remision = "NO APLICA";
            $factura = "NO APLICA";
            break;
    }
}else{
    $remision = "NO APLICA";
    $factura = "NO APLICA";
}
//Consulta para obtener el detalle asociado de este movimiento, el cual a de ser la orden de compra
//dta hace referencia al detalle asociado, mov_a hace referencia al movimiento asociado
$rowD = $movimiento->data_asociado($id_mov);
$id_aso = $rowD[0]; $num_aso = $rowD[1]; $dia_l_aso = $rowD[2]; $dia_n_aso = $rowD[3]; $mes_aso = $rowD[4]; $anno_aso = $rowD[5];

if($_REQUEST['t']==1) { 
    @ob_start();
    //Archivos adjuntos
    require '../fpdf/fpdf.php';
    class PDF_MC_Table extends FPDF{
        var $widths;
        var $aligns;
        function SetWidths($w){
            $this->widths=$w;   //Obtenemos un  array con los anchos de las columnas
        }
        function SetAligns($a){
            $this->aligns=$a;   //Obtenemos un array con los alineamientos de las columnas
        }
        function fill($f){
            $this->fill=$f;     //Juego de arreglos de relleno
        }

        function Row_none($data){
            //Calculo del alto de una fila
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 4*$nb;
            //Si una pagina tiene salto de linea
            $this->CheckPageBreak($h);
            //Dibujar las celdas de las fila
            for($i=0;$i<count($data);$i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guardamos las posiciones actuales
                $x = $this->GetX();
                $y = $this->GetY();
                //Dibujamos el borde
                /** @var String $style */
                $this->Rect(0, 0, 0, 0, '');
                //Imprimimos el texto
                /** @var String $fill */
                $this->MultiCell($w, 4, $data[$i], '', $a, '');
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Go to the next line
            $this->Ln($h - 4);
        }

        function Row($data){
            //Calculo del alto de una fila
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 5*$nb;
            //Si una pagina tiene salto de linea
            $this->CheckPageBreak($h);
            //Dibujar las celdas de las fila
            for($i=0;$i<count($data);$i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guardamos las posiciones actuales
                $x = $this->GetX();
                $y = $this->GetY();
                //Dibujamos el borde
                /** @var String $style */
                $this->Rect($x, $y, $w, $h, '');
                //Imprimimos el texto
                /** @var String $fill */
                $this->MultiCell($w, 4, $data[$i], 'LTR', $a, '');
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Go to the next line
            $this->Ln($h - 5);
        }
        function CheckPageBreak($h){
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY()+$h>$this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
        }

        function NbLines($w, $txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
            if($w == 0)
                $w = $this->w-$this->rMargin-$this->x;
            $wmax=($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
            $s=str_replace('\r','',$txt);
            $nb=strlen($s);
            if($nb > 0 and $s[$nb-1] == '\n')
                $nb--;
            $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
            while($i < $nb){
                $c=$s[$i];
                if($c == '\n'){
                    $i++; $sep = -1; $j = $i; $l = 0; $nl++;
                    continue;
                }
                if($c == '')
                    $sep = $i;
                $l += $cw[$c];
                if($l > $wmax){
                    if($sep == -1){
                        if($i == $j)
                            $i++;
                    }else
                        $i = $sep + 1;
                    $sep = -1; $j = $i; $l = 0; $nl++;
                }else
                    $i++;
            }
            return $nl;
        }

        #Funcón cabeza de la página
        function header(){
            #Redeclaración de varibles
            global $razonSocial;    #Nombre de compañia
            global $tipoIdent;      #Tipo de identificación
            global $numeroIdent;    #Nombre de comprobante
            global $ruta;           #Ruta de logo
            global $tipo_mov;       #Tipo de movimiento nombre
            global $numero_mov;     #Número de movimiento
            #Validación cuando la variable $ruta, la obtiene la ruta del logo no esta vacia
            if($ruta != '')  {
                $this->Image('../'.$ruta,10,10,18);
            }
            #Razón social
            $this->SetFont('Arial','B',10);
            $this->SetXY(40,15);
            $this->MultiCell(140,5,utf8_decode(strtoupper($razonSocial)),0,'C');
            #Tipo documento y número de documento
            $this->SetXY(40,18);
            $this->Ln(1);
            $this->SetFont('Arial','B',9);
            $this->Cell(200,5,utf8_decode(strtoupper($tipoIdent).':'." ".$numeroIdent),0,0,'C');
            #Tipo de comprobante y número de comprobante
            $this->Ln(4);
            $this->SetFont('Arial','B',9);
            $this->Cell(200,5,utf8_decode('OFICINA ALMACEN GENERAL'),0,0,'C');
            $this->Ln(4);
            $this->SetFont('Arial','B',9);
            $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tipo_mov." ".'Nª:')))." ".$numero_mov,0,0,'C');
            $this->Ln(6);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(70,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(70,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
            $this->Cell(60,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF_Mc_Table('P', 'mm', 'Letter');       #Creación del objeto pdf
    $nb=$pdf->AliasNbPages();       #Objeto de número de pagina
    $pdf->AddPage();                #Agregar página
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetWidths(array(115, 45, 40));
    $pdf->SetAligns(array('L', 'R', 'L'));
    $pdf->Row_none(array(strtoupper("$ciudad $dia_letras, $n_dia $meses[$n_mes] $anno"), utf8_decode('FACTURA Nº:'), $factura));
    $pdf->Ln(4);
    $pdf->SetWidths(array(35, 80, 45, 40));
    $pdf->SetAligns(array('R', 'L', 'R', 'L'));
    $pdf->Row_none(array(utf8_decode('ORDEN DE COMPRA:'), utf8_decode(strtoupper("$num_aso del $dia_l_aso, $dia_n_aso $meses[$mes_aso] $anno_aso")), utf8_decode('REMISIÓN Nº:'), $remision));
    $pdf->Ln(4);
    $pdf->SetWidths(array(35, 80, 45, 40));
    $pdf->SetAligns(array('R', 'L', 'R', 'L'));
    $pdf->Row_none(array(utf8_decode('TERCERO:'), strtoupper(utf8_decode($tercero2)), utf8_decode($rowTer[0].':'), $rowTer[1]));
    $pdf->Ln(4);
    $pdf->SetWidths(array(35, 80, 45, 40));
    $pdf->SetAligns(array('R', 'L', 'R', 'L'));
    $pdf->Row_none(array(utf8_decode('DIRECCIÓN:'), strtoupper(utf8_decode($rowTer[2])), utf8_decode('TELEFONO:'), $rowTer[3]));
    $pdf->Ln(4);
    $pdf->SetWidths(array(35, 165));
    $pdf->SetAligns(array('R', 'L'));
    $pdf->Row_none(array(utf8_decode('DESCRIPCIÓN:'), utf8_decode($descripcion)));
    $pdf->Ln(4);
    $pdf->SetWidths(array(35, 165));
    $pdf->SetAligns(array('R', 'L'));
    $pdf->Row_none(array(utf8_decode('OBSERVACIONES:'), utf8_decode($observaciones)));

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(10, 5, '', 'LRT', 0,'C');
    $pdf->Cell(80, 5, utf8_decode('PLAN'), 'LRT', 0, 'C');
    $pdf->Cell(30, 5, '', 'LRT', 0, 'C');
    $pdf->Cell(25, 5, '', 'LRT', 0, 'C');
    $pdf->Cell(25, 5, 'VALOR', 'LRT', 0, 'C');
    $pdf->Cell(30, 5, '', 'LRT', 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 5, utf8_decode('Nª'), 'LRB', 0,'C');
    $pdf->Cell(80, 5, 'INVENTARIO', 'LRB', 0, 'C');
    $pdf->Cell(30, 5, 'UNIDAD', 'LRB', 0, 'C');
    $pdf->Cell(25, 5, 'CANTIDAD', 'LRB', 0, 'C');
    $pdf->Cell(25, 5, 'UNITARIO', 'LRB', 0, 'C');
    $pdf->Cell(30, 5, 'SUBTOTAL', 'LRB', 0, 'C');
    $devoltivos = array();

    $sqlP = "SELECT   dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva, pni.tipoinventario, u.nombre , uf.nombre, 
            pni.ficha 
            FROM      gf_detalle_movimiento dtm
            LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
            LEFT JOIN gf_unidad_factor u ON dtm.unidad_origen = u.id_unico 
            LEFT JOIN gf_unidad_factor uf ON pni.unidad = uf.id_unico 
            WHERE     dtm.movimiento = $id_mov";
    $resultP = $mysqli->query($sqlP);
    $a = 0; $valorTU = 0; $valorTI = 0; $valorTAA = 0;
    $pdf->Ln(2);
    while ($rowP = mysqli_fetch_row($resultP)) {
        if(empty($rowP[6])){
            $unid = $rowP[7];
        } else {
            $unid = $rowP[6];
        }
        $a++;
        $valorTU  += ($rowP[3] * $rowP[2]); $valorTI += ($rowP[4] * $rowP[2]);
        $valorT   = ($rowP[3] + $rowP[4]) * $rowP[2];
        $valorTAA += $valorT;
        $valorTA  = number_format($valorT, 2, '.' , ',');
        $valorA   = number_format($rowP[3]+$rowP[4], 2, ',', '.');
        $valorI   = number_format($rowP[4], 2, ',', '.');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetWidths(array(10, 80,30,  25, 25, 30));
        $pdf->SetAligns(array('C', 'L','L', 'C',  'R', 'R'));
        $pdf->Row_none(array($a,utf8_decode(mb_strtoupper($rowP[1])), $unid, number_format($rowP[2], 2), $valorA, $valorTA));
        if(!empty($rowP[8])){
            $devoltivos[] = $rowP[0];
        }
    }
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(170, 5, 'TOTAL', 'LTRB', 0, 'C');
    $pdf->Cell(30, 5, number_format($valorTAA, 2, ',', '.'), 'LTRB', 0, 'R');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(200, 5,'RESUMEN', 1, 0, 'C');
    $pdf->Ln(5);
    $pdf->SetAligns(array('C', 'C'));
    $pdf->SetWidths(array(140, 60));
    $pdf->Row(array('GRUPO', 'TOTAL'));
    $pdf->Ln(5);

    $rowg = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi,1, 5)
         FROM gf_detalle_movimiento dm 
    LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
    where dm.movimiento =$id_mov");
    $valt = 0;

    for ($g=0; $g <count($rowg) ; $g++) { 
        $rowvg = $con->Listar("SELECT DISTINCT SUM((dm.valor*dm.cantidad)+(if(dm.iva IS NULL, 0, dm.iva)*dm.cantidad))
         FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
        where dm.movimiento = $id_mov and pi.codi like '".$rowg[$g][0]."%'");
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetWidths(array(140, 60));
        $pdf->SetAligns(array( 'L','R'));
        $pdf->Row_none(array($rowg[$g][0], number_format($rowvg[0][0], 2, ',' , '.')));
        $pdf->Ln(3);
    }
    $pdf->Ln(5);
    if(count($devoltivos) > 0){
        $xxx = 0;
        $yyy = 0;
        
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(200, 5,'LISTADO DE DEVOLUTIVOS', 1, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetAligns(array('C', 'C', 'C', 'C'));
        $pdf->SetWidths(array(70, 20, 30, 80));
        $pdf->Row(array('ELEMENTO PLAN', 'PLACA', 'VALOR', utf8_decode('DESCRIPCIÓN')));
        $pdf->Ln(5);
        for ($i = 0; $i < count($devoltivos); $i++) {
            $rowDet = $movimiento->data_producto($devoltivos[$i]);
            foreach ($rowDet as $rowDD) {

                $valorT=$rowDD[3]+$rowDD[4];
                $xxx += $valorT;
                #*** BUSCAR ESPECIFICACIONES DEL PRODUCTO;
                $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                        FROM      gf_movimiento_producto mpr
                        LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                        LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                        LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                        LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                        WHERE  mpr.detallemovimiento = ".$devoltivos[$i]." AND pre.fichainventario != 6");
                $descripcion = "";
                for ($j = 0; $j < count($vp); $j++) {
                    if(!empty($vp[$j][1])){
                        $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                    }
                }
        

              
                $pdf->SetFont('Arial', '', 6);
                $pdf->SetAligns(array('L', 'R', 'R', 'L'));
                $pdf->SetWidths(array(70, 20, 30, 80));
               $pdf->Row_none(array(utf8_decode($rowDD[1]), $rowDD[2], number_format($valorT, 2, ',', '.'), utf8_decode($descripcion)));
                $pdf->Ln(3);
            }
        }
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetAligns(array('C', 'R', 'R'));
        $pdf->SetWidths(array(90, 30));
        $pdf->Row(array('TOTAL', number_format($xxx, 2, ',', '.')));
        $pdf->Ln(8);
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetAligns(array('R', 'L'));
    $pdf->SetWidths(array(35, 165));
    $pdf->Row(array(utf8_decode('SON:'), utf8_decode(numtoletras($valorTAA))));
    $pdf->Ln(30);
    $yy1 = $pdf->GetY();
    $pdf->SetFont('Arial', 'B', 8);
   
    $data_firmas = $movimiento->data_firmas($id_tipo_mov);
    $xxx = 0;
    $nd = count($data_firmas);
    foreach($data_firmas as $row_firma){
        if($nd ==1){
            $pdf->SetX(80);
            $pdf->Cell(60, 0, '', 'B');
            $pdf->Ln(3);
            $pdf->SetX(15);
            $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'C');
            $pdf->Ln(5);
            $pdf->SetX(15);
            $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'C');
            $pdf->Ln(15);
        } else {
            if($xxx == 1){
                $yyy = $yy1;
            }
            $xxx++;
            if($xxx % 2 == 0){
                $pdf->SetXY(140, $yyy);
                $pdf->Cell(60, 0, '', 'B');
                $pdf->Ln(3);
                $pdf->SetX(140);
                $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->SetX(140);
                $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
                $pdf->Ln(40);
            }else{
                $yyy = $pdf->GetY();
                $pdf->Cell(60, 0, '', 'B');
                $pdf->Ln(3);
                $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
            }
        }
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(200,5,"HE RECIBIDO A COMFORMIDAD LOS ELEMENTOS DESCRITOS ANTERIORMENTE",0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(200,5,"CUIDA LA  VIBRANTE ESENCIA DE LA VIDA",0,0,'C');
    #Final del documento
    while (ob_get_length()) {
        ob_end_clean();#Limpieza del buffer
    }
    #Salida del documento
    $nombre_doc = utf8_decode("informeEntradaAlmacenNª$numero_mov.pdf");
    $pdf->Output(0,$nombre_doc,0);
} else { 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Entrada_Almacen.xls");    
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Entrada Almacén</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">

        <th colspan="6" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonSocial ?>
            <br/><?php echo $tipoIdent.' : '.$numeroIdent ?>
            <br/>&nbsp;
            <br/>OFICINA ALMACEN GENERAL;
            <br/><?php echo $tipo_mov." Nª:".$numero_mov ?>
            <br/>&nbsp;                 
            </strong> 
        </th>
        <tr></tr>    
        <?php 
        echo '<tr>';
        echo '<td colspan="3"><strong>'.$ciudad.' '.$dia_letras.' '.$n_dia.' '.$meses[$n_mes].' '.$anno.'</strong></td>';
        echo '<td colspan="3"><strong>FACTURA Nº: '.$factura.'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>ORDEN DE COMPRA: '.$num_aso.' del '.$dia_l_aso.' '.$dia_n_aso.' '.$meses[$mes_aso].' '.$anno_aso.'</strong></td>';
        echo '<td colspan="3"><strong>REMISIÓN Nº: '.$remision.'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>TERCERO: '.$tercero2.'</strong></td>';
        echo '<td colspan="3"><strong>'.$rowTer[0].': '.$rowTer[1].'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>DIRECCIÓN: '.$rowTer[2].'</strong></td>';
        echo '<td colspan="3"><strong>TELÉFONO: '.$rowTer[3].'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="6"><strong>DESCRIPCIÓN: '.$descripcion.'</strong></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="6"><strong>OBSERVACIONES: '.$observaciones.'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td><strong>Nª</strong></td>';
        echo '<td><strong>PLAN INVENTARIO</strong></td>';
        echo '<td><strong>UNIDAD</strong></td>';
        echo '<td><strong>CANTIDAD</strong></td>';
        echo '<td><strong>VALOR UNITARIO</strong></td>';
        echo '<td><strong>SUBTOTAL</strong></td>';
        echo '</tr>';
        $devoltivos = array();
        $sqlP = "SELECT   dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva, pni.tipoinventario, u.nombre 
            FROM      gf_detalle_movimiento dtm
            LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
            LEFT JOIN gf_unidad_factor u ON dtm.unidad_origen = u.id_unico 
            WHERE     dtm.movimiento = $id_mov";
        $resultP = $mysqli->query($sqlP);
        $a = 0; $valorTU = 0; $valorTI = 0; $valorTAA = 0;
        while ($rowP = mysqli_fetch_row($resultP)) {
            $a++;
            $valorTU  += ($rowP[3] * $rowP[2]); $valorTI += ($rowP[4] * $rowP[2]);
            $valorT   = ($rowP[3] + $rowP[4]) * $rowP[2];
            $valorTAA += $valorT;
            $valorTA  = number_format($valorT, 2, '.' , ',');
            $valorA   = number_format($rowP[3]+$rowP[4], 2, '.', ',');
            $valorI   = number_format($rowP[4], 2, '.', ',');
            if($rowP[5] == 2){
                $devoltivos[] = $rowP[0];
            }
            echo '<tr>';
            echo '<td>'.$a.'</td>';
            echo '<td>'.mb_strtoupper($rowP[1]).'</td>';
            echo '<td>'.($rowP[6]).'</td>';
            echo '<td>'.number_format($rowP[2], 0).'</td>';
            echo '<td>'.$valorA.'</td>';
            echo '<td>'.$valorTA.'</td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="5"><strong>TOTAL</strong></td>';
        echo '<td><strong>'.number_format($valorTAA, 2, '.', ',').'</strong></td>';
        echo '</tr>';


        $rowg = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi,1, 5)
             FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
        where dm.movimiento =$id_mov");
        $valt = 0;
        echo '<tr>';
        echo '<td colspan="6"><strong><center><br/>&nbsp;RESUMEN<br/>&nbsp;</center></strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>GRUPO</strong></td>';
        echo '<td colspan="3"><strong>TOTAL</strong></td>';
        echo '</tr>';
        for ($g=0; $g <count($rowg) ; $g++) { 
            $rowvg = $con->Listar("SELECT DISTINCT SUM((dm.valor*dm.cantidad)+(if(dm.iva IS NULL, 0, dm.iva)*dm.cantidad))
             FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            where dm.movimiento = $id_mov and pi.codi like '".$rowg[$g][0]."%'");
            echo '<tr>';
            echo '<td colspan="3">'.$rowg[$g][0].'</td>';
            echo '<td colspan="3">'.number_format($rowvg[0][0], 2, ',' , '.').'</td>';
            echo '</tr>';
        }

        if(count($devoltivos) > 0){
            $xxx = 0;
            $yyy = 0;
            echo '<tr>';
            echo '<td colspan="6"><strong><center>LISTADO DE DEVOLUTIVOS</center></strong></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td colspan="2"><strong>ELEMENTO PLAN</strong></td>';
            echo '<td><strong>PLACA</strong></td>';
            echo '<td><strong>VALOR</strong></td>';
            echo '<td colspan="2"><strong>DESCRIPCIÓN</strong></td>';
            echo '</tr>';

            for ($i = 0; $i < count($devoltivos); $i++) {
                $rowDet = $movimiento->data_producto($devoltivos[$i]);
                foreach ($rowDet as $rowDD) {
                    $xxx += $rowDD[3]; 
                    #*** BUSCAR ESPECIFICACIONES DEL PRODUCTO;
                    $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                            FROM      gf_movimiento_producto mpr
                            LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                            LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                            LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                            LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                            LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                            WHERE  mpr.detallemovimiento = ".$devoltivos[$i]." AND pre.fichainventario != 6");
                    $descripcion = "";
                    for ($j = 0; $j < count($vp); $j++) {
                        $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                    }
                    echo '<tr>';
                    echo '<td colspan="2">'.$rowDD[1].'</td>';
                    echo '<td>'.$rowDD[2].'</td>';
                    echo '<td>'.number_format($rowDD[3], 2, '.', ',').'</td>';
                    echo '<td colspan="2">'.$descripcion.'</td>';
                    echo '</tr>';
                }
            }
            echo '<tr>';
            echo '<td colspan="3"><strong>TOTAL</strong></td>';
            echo '<td><strong>'.number_format($xxx, 2, '.', ',').'</strong></td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="6"><strong>SON:'.utf8_decode(numtoletras($valorTAA)).'</strong></td>';
        echo '</tr>';

        $data_firmas = $movimiento->data_firmas($id_tipo_mov);
        
        $ix = 0;
        $nd = count($data_firmas);
        foreach($data_firmas as $row_firma){
            if($ix == 0){
                if($nd == 1){
                    echo '<tr>';
                    echo '<td colspan="6"><strong><center><br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;'.$row_firma[0].'<br/>&nbsp;'.$row_firma[1].'<br/>&nbsp;</center></strong></td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<td colspan="6"><strong><center><br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;'.$row_firma[0].'<br/>&nbsp;'.$row_firma[1].'<br/>&nbsp;</center></strong></td>';
                    $ix++  ;  
                }
                
            } else{
                echo '<td colspan="6"><strong><center><br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;'.$row_firma[0].'<br/>&nbsp;'.$row_firma[1].'<br/>&nbsp;</center></strong></td>';
                echo '</tr>';
                $ix = 0;
            }          

        }
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="6"><strong><center>HE RECIBIDO A COMFORMIDAD LOS ELEMENTOS DESCRITOS ANTERIORMENTE</center></strong></td>';
        echo '</tr>';
        echo '<tr>';        
        echo '<td colspan="6"><strong><center>CUIDA LA  VIBRANTE ESENCIA DE LA VIDA</center></strong></td>';
        echo '</tr>';


        ?>
    </table>
    </body>
    </html>

<?php             
} ?>