<?php

session_start();
require_once("../Conexion/conexion.php");
require_once('../Conexion/ConexionPDO.php');
$con  = new ConexionPDO();


list($proI, $proF, $fechaini, $fecha1, $hoy, $compania, $usuario)
    = array($_POST["sltEin"], $_POST["sltEfn"], $_POST["fechaini"], $_POST["fechaini"], date("d/m/Y"), $_SESSION['compania'], $_SESSION['usuario']);
#Firma Almacen
    $firma=$con->Listar("SELECT   c.nombre, 
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
  LEFT JOIN gf_tipo_documento   td ON rd.tipodocumento = td.id_unico
  WHERE td.nombre = 'Listado De Inventarios'
  ORDER BY rd.orden ASC");
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN   
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN   
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];


$ff       = explode("/", $fechaini);
$fecha    = "$ff[2]-$ff[1]-$ff[0]";

list($xCant, $xValorT, $predecesorN, $cantidadg, $valorg, $cont, $npredA) = array(0, 0, 0, 0, 0,0, '');
$row = $con->Listar("SELECT    gpl.id_unico as id, 
        gpl.codi as codi, 
        UPPER(gpl.nombre), 
        UPPER(gum.nombre), 
        gplp.id_unico, 
        gplp.codi as codip, 
        gplp.nombre
    FROM      gf_plan_inventario AS gpl
    LEFT JOIN gf_unidad_factor   AS gum ON gpl.unidad = gum.id_unico 
    LEFT JOIN gf_plan_inventario gplp ON gpl.predecesor = gplp.id_unico 
    WHERE     (gpl.id_unico BETWEEN $proI AND $proF)
    AND       gpl.compania = $compania ORDER BY gpl.codi");

if($_REQUEST['t']==1){
    require'../fpdf/fpdf.php';
    
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
                $this->Rect($x, $y, 0, 0, '');
                //Imprime el texto
                $this->MultiCell($w,5, $data[$i],'', $a, '');
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
        function Footer(){
            global $hoy;
            global $usuario;
            $this->SetY(-18);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(30,10,utf8_decode(''),0,0,'L');
            $this->Cell(100,10,utf8_decode(''),0,0,'C');
            $this->Cell(30,10,utf8_decode(''),0,0,'C');
            $this->Cell(30,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
        function Header(){
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $fecha1;
            global $ruta_logo;
            $this->SetFont('Arial','B',10);
            if($ruta_logo != '')
             {
               $this->Image('../'.$ruta_logo,10,5,28);
             }
            $this->SetY(10);
            $this->Cell(200,5,utf8_decode($razonsocial),0,0,'C');
            $this->Ln(5);
            $this->Cell(200,5,utf8_decode($nombreIdent.':'.$numeroIdent),0,0,'C');
            $this->Ln(5);
            $this->Cell(200,5,utf8_decode($direccinTer.' Tel:'.$telefonoTer),0,0,'C');
            $this->Ln(6);
            $this->Cell(200,5,utf8_decode('INFORME EXISTENCIAS DE INVENTARIO POR GRUPO'),0,0,'C');
            $this->Ln(5);
            $this->Cell(200,5,utf8_decode('HASTA '.$fecha1),0,0,'C');
            $this->Ln(7);
            $this->SetFont('Arial','B',9);
            $this->SetX(9);
            $this->Cell(30,10,utf8_decode('CÓDIGO'),1,0,'C');
            $this->Cell(90,10,utf8_decode('ELEMENTO'),1,0,'C');
            $this->Cell(25,10,utf8_decode('UNIDAD'),1,0,'C');
            $this->Cell(20,10,utf8_decode('CANTIDAD'),1,0,'C');
            $this->Cell(30,10,utf8_decode('VALOR'),1,0,'C');
            $this->Ln(10);
        }
    }


    $pdf = new PDF('P','mm','A4');
    $nb  = $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    
    $pdf->SetFont('Arial','B',9);
    for ($pl=0; $pl < count($row) ; $pl++) { 
        list($xsaldo, $xvalor) = array(0, 0);
        $str_x = "SELECT    gtm.clase, gdm.cantidad, gdm.valor,gdm.iva
                  FROM      gf_detalle_movimiento AS gdm
                  LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                  LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
                  WHERE     (gdm.planmovimiento = ".$row[$pl][0].")
                  AND       (gtm.clase IN (2,3))
                  AND       (gmv.fecha <= '$fecha')
                  AND       (gmv.compania = $compania)
                  ORDER BY  gmv.fecha, gdm.hora, gtm.clase";
        $res_x = $mysqli->query($str_x);
        $dat_x = $res_x->fetch_all(MYSQLI_NUM);
        foreach ($dat_x as $rowX){
            switch ($rowX[0]) {
               case 2:
                $xsaldo += $rowX[1];
                $xvalor += ($rowX[2]+$rowX[3])*$rowX[1];
                break;

            case 3:
                $xsaldo -= $rowX[1];
                $xvalor -= ($rowX[2]+$rowX[3])*$rowX[1];
                break;
            }
        }
        
        $xsaldo = ROUND($xsaldo, 2);
        $xvalor = ROUND($xvalor, 2);
        if($xsaldo >0 ){
            $predecesor = $row[$pl][4];
            $npred      = $row[$pl][5].' '.$row[$pl][6];
            if($cont ==0){
                $npredA = $row[$pl][5].' '.$row[$pl][6];
            }
            if($predecesor != $predecesorN){
                if($cont!=0){
                    $pdf->SetX(9);
                    $pdf->SetFont('Arial','BI',9);
                    $pdf->Cell(145,5,('TOTAL GRUPO: '.$npredA),1,0,'L');
                    $pdf->Cell(20,5,($cantidadg),1,0,'C');
                    $pdf->Cell(30,5,number_format($valorg, 2 , ',', '.'),1,0,'C');
                    $pdf->Ln(5);
                    $cantidadg  = 0;
                    $valorg     = 0;
                }     
                $pdf->SetX(9);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(195,5,($npred),1,0,'L');
                $pdf->Ln(5);
                $npredA = $row[$pl][5].' '.$row[$pl][6];
                $predecesorN = $predecesor;
            } 
            $pdf->SetFont('Arial','',8);
            $pdf->SetX(9);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->CellFitScale(30,5,utf8_decode($row[$pl][1]),0,0,'L');
            $pdf->MultiCell(90,5,($row[$pl][2]),0,'L');
            $y1 = $pdf->GetY();
            $pdf->SetXY($x+120, $y);
            $pdf->CellFitScale(25,5,utf8_decode($row[$pl][3]),0,0,'C');
            $pdf->CellFitScale(20,5,utf8_decode($xsaldo), 0,0,'C');
            $pdf->CellFitScale(30,5,number_format($xvalor, 2 , ',', '.'),0,0,'C');
            $alt = $y1-$y;
            $pdf->SetXY($x, $y);
            $pdf->Cell(30,$alt,'',1,0,'L');
            $pdf->Cell(90,$alt,'',1,0,'L');
            $pdf->Cell(25,$alt,'',1,0,'L');
            $pdf->Cell(20,$alt,'',1,0,'L');
            $pdf->Cell(30,$alt,'',1,0,'R');
            $pdf->Ln($alt);
            if($pdf->GetY()>230){
                $pdf->AddPage();
            }

            $cantidadg  += $xsaldo;
            $valorg     += $xvalor;
            $cont ++; 
            $xCant      += $xsaldo;
            $xValorT    += $xvalor;
        }
        if($pl == count($row)-1 ){
            $pdf->SetFont('Arial','BI',9);
            $pdf->SetX(9);
            $pdf->Cell(145,5,('TOTAL GRUPO: '.$npred),1,0,'L');
            $pdf->Cell(20,5,utf8_decode($cantidadg),1,0,'C');
            $pdf->Cell(30,5,number_format($valorg, 2 , ',', '.'),1,0,'C');
            $pdf->Ln(5);
            $cantidadg  = 0;
            $valorg     = 0;
        }

    }
    $pdf->SetFont('Arial','B',9);
     $pdf->SetX(9);
    $pdf->Cell(145,5,utf8_decode('TOTALES'),1,0,'L');
    $pdf->Cell(20,5,utf8_decode($xCant),1,0,'C');
    $pdf->Cell(30,5,number_format($xValorT, 2 , ',', '.'),1,0,'C');
    $pdf->Ln(25); 
$pdf->cellfitscale(65,5,utf8_decode(' '),0,0,'L');   
  $pdf->Cell(60,0.1,'',1); 
   $pdf->cellfitscale(65,5,utf8_decode(' '),0,0,'L');   
  $pdf->Ln(2); 
  $pdf->cellfitscale(72,5,utf8_decode(' '),0,0,'L');   
  $pdf->cellfitscale(124,5,utf8_decode('Firma Responsable de Almacen'),0,0,'L'); 
       

    ob_end_clean();
    $pdf->Output(0,'Informe_Existencias_Inventario_Grupo('.date('d/m/Y').').pdf',0);
}else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=InformeExistenciasInventarioGrupo.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Existencias Inventario Grupo</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="5" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
            <br/>&nbsp;
            <br/>INFORME EXISTENCIAS DE INVENTARIO POR GRUPO
            <br/>&nbsp;
            <br/>HASTA <?= $fecha1;?>
            <br/>&nbsp;                 
            </strong> 
        </th>
        <tr>
            <td><strong>CÓDIGO</strong></td>
            <td><strong>ELEMENTO</strong></td>
            <td><strong>UNIDAD</strong></td>    
            <td><strong>CANTIDAD</strong></td>    
            <td><strong>VALOR</strong></td>    
        </tr>
        <?php 
        
        for ($pl=0; $pl < count($row) ; $pl++) { 
            list($xsaldo, $xvalor) = array(0, 0);
            $str_x = "SELECT    gtm.clase, gdm.cantidad, gdm.valor,gdm.iva
                      FROM      gf_detalle_movimiento AS gdm
                      LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                      LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
                      WHERE     (gdm.planmovimiento = ".$row[$pl][0].")
                      AND       (gtm.clase IN (2,3))
                      AND       (gmv.fecha <= '$fecha')
                      AND       (gmv.compania = $compania)
                      ORDER BY  gmv.fecha, gdm.hora, gtm.clase";
            $res_x = $mysqli->query($str_x);
            $dat_x = $res_x->fetch_all(MYSQLI_NUM);
            foreach ($dat_x as $rowX){
                switch ($rowX[0]) {
                    case 2:
                        $xsaldo += $rowX[1];
                        $xvalor += ($rowX[2]+$rowX[3])*$rowX[1];
                        break;
        
                    case 3:
                        $xsaldo -= $rowX[1];
                        $xvalor -= ($rowX[2]+$rowX[3])*$rowX[1];
                        break;
                }
            }
            
            $xsaldo = ROUND($xsaldo, 2);
            $xvalor = ROUND($xvalor, 2);
            if($xsaldo > 0){
                $predecesor = $row[$pl][4];
                $npred      = $row[$pl][5].' '.$row[$pl][6];
                if($cont ==0){
                    $npredA = $row[$pl][5].' '.$row[$pl][6];
                }
                if($predecesor != $predecesorN){
                    if($cont!=0){
                        echo '<tr><td colspan="3"><strong><i>TOTAL GRUPO: '.$npredA.'</i></strong></td>
                            <td><strong><i>'.$cantidadg.'</i></strong></td>
                            <td><strong><i>'.number_format($valorg, 2 , ',', '.').'</i></strong></td>
                        </tr>';
                        $cantidadg  = 0;
                        $valorg     = 0;
                    }                    
                    echo '<tr><td colspan="5"><strong>'.$npred.'</strong></td></tr>';
                    $npredA = $row[$pl][5].' '.$row[$pl][6];
                    $predecesorN = $predecesor;
                } 
                echo '<tr>
                    <td>'.$row[$pl][1].'</td>
                    <td>'.$row[$pl][2].'</td>
                    <td>'.$row[$pl][3].'</td>
                    <td>'.$xsaldo.'</td>
                    <td>'.number_format($xvalor, 2 , ',', '.').'</td>
                </tr>';

                $cantidadg  += $xsaldo;
                $valorg     += $xvalor;
                $cont ++; 
                $xCant      += $xsaldo;
                $xValorT    += $xvalor;
            }
            if($pl == count($row)-1 ){
                echo '<tr><td colspan="3"><strong><i>TOTAL GRUPO: '.$npred.'</i></strong></td>
                    <td><strong><i>'.$cantidadg.'</i></strong></td>
                    <td><strong><i>'.number_format($valorg, 2 , ',', '.').'</i></strong></td>
                </tr>';
                $cantidadg  = 0;
                $valorg     = 0;
            }

        }
        echo '<tr><td colspan="3"><strong>TOTALES</strong></td>
            <td><strong>'.$xCant.'</strong></td>
            <td><strong>'.number_format($xValorT, 2 , ',', '.').'</strong></td>
        </tr>';
        echo '<tr>
        <td colspan="5" style="text-align: center;"><strong><br>____________________________________<br>Firma Responsable de Almacen</strong></td>
        </tr>';
        ?>
    </table>
    </body>
    </html>

    
<?php } ?>