<?php
#06/10/2021-Elkin O-Se crea el informe sobre vienes inmbuebles. 
ini_set('max_execution_time', 0);
session_start();
ob_start();
require ('../Conexion/conexion.php');
require_once ("../Conexion/ConexionPDO.php");
$con = new ConexionPDO();
require ('../fpdf/fpdf.php');
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) &&  !empty($_POST['sltProductoFinal'])){

    function convertirFecha($fecha){
        $fecha = explode("/", $fecha);
        return $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }

    function obtenerDatosProducto($producto, $compania){
        require ('../Conexion/conexion.php');
        $sql = "SELECT     pln.nombre  AS NOM_PLAN,
                           UPPER(pes.valor)   AS SERIE
                FROM       gf_producto pr
                LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                WHERE      fic.elementoficha   = 6
                AND        pr.id_unico         = $producto
                AND        pln.compania        = $compania
                ORDER BY   pr.id_unico DESC";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        return $row;
        $mysqli->close();
    }



    $usuario = $_SESSION['usuario'];
    $compa   = $compania = $_SESSION['compania'];

    $fechaInicial = $_POST['txtFechaInicial'];
    $fechaFinal   = $_POST['txtFechaFinal'];

    $fechaI       = convertirFecha($fechaInicial);
    $fechaF       = convertirFecha($fechaFinal);

    $productoIni  = $_POST['sltProductoInicial'];
    $productoFin  = $_POST['sltProductoFinal'];

    $proI         = obtenerDatosProducto($productoIni, $compania);
    $proF         = obtenerDatosProducto($productoFin, $compania);
    $productoI    = $proI[1]." - ".$proI[0];
    $productoF    = $proF[1]." - ".$proF[0];

    
    $comp = "SELECT UPPER(t.razonsocial), t.numeroidentificacion, t.digitoverficacion, t.ruta_logo
             FROM gf_tercero t WHERE id_unico = $compa";
    $comp = $mysqli->query($comp);
    $comp = mysqli_fetch_row($comp);
    $nombreCompania = $comp[0];

    if(empty($comp[2])) {
        $nitcompania = $comp[1];
    } else {
        $nitcompania = $comp[1].' - '.$comp[2];
    }

    $ruta = $comp[3];

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
            //Calculate the height of the row
            $nb = 0;
            for($i = 0; $i < count($data); $i++)
                $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 6 * $nb;
            //Issue a page break first if needed
            $this->CheckPageBreak($h);
            //Draw the cells of the row
            for($i = 0; $i < count($data); $i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Save the current position
                $x = $this->GetX();
                $y = $this->GetY();
                //Draw the border
                //$this->Rect($x,$y,$w,$h,$style);
                //Print the text

                $this->MultiCell($w, 6, $data[$i], 0, $a, $fill);
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Go to the next line
            $this->Ln($h-6);
        }

        function CheckPageBreak($h){
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY() + $h > $this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
        }

        function NbLines($w,$txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
            if($w == 0)
                $w = $this->w-$this->rMargin-$this->x;
            $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
            $s  = str_replace('\r','',$txt);
            $nb = strlen($s);
            if($nb > 0 and $s[$nb-1] == '\n')
                $nb–;
            $sep =-1;
            $i  = 0;
            $j  = 0;
            $l  = 0;
            $nl = 1;
            while($i<$nb){
                $c = $s[$i];
                if($c == '\n'){
                    $i++;
                    $sep =-1;
                    $j = $i;
                    $l = 0;
                    $nl++;
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
                        $i = $sep+1;
                    $sep =-1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                }else
                    $i++;
                }
            return $nl;
        }

        #Función cabeza de la página
        function Header(){
            global $nombreCompania;
            global $nitcompania;
            global $numpaginas;
            global $fechaInicial;
            global $fechaFinal;
            global $ruta;
            global $productoI;
            global $productoF;

            $numpaginas=$this->PageNo();

            $this->SetFont('Arial','B',10);
            $this->SetY(10);
            if($ruta != ''){
                $this->Image('../'.$ruta,15,8,15);
            }

            $this->SetX(35);
            $this->Cell(308,5,utf8_decode($nombreCompania),0,0,'C');
            $this->Ln(5);

            $this->SetX(35);
            $this->Cell(308, 5,"NIT :".$nitcompania,0,0,'C');
            $this->Ln(5);

            $this->SetX(35);
            $this->Cell(308, 5,"INVENTARIO GENERAL DE INMUEBLES",0,0,'C');
            $this->Ln(5);
            $this->SetX(35);
            $this->Cell(308, 5," ENTRE FECHAS : ".$fechaInicial." Y ".$fechaFinal,0,0,'C');
            $this->Ln(5);
            $this->SetX(35);
            $this->Cell(308, 5," ENTRE PRODUCTOS : ".$productoI." Y ".$productoF,0,0,'C');
            $this->Ln(5);

            $this->SetFont('Arial','B',8);
            $this->Cell(28,15,"",1,0,"C");
            $this->Cell(95,15,"",1,0,"C");
            $this->Cell(157,15,"",1,0,"C");
            $this->Cell(45,15,"",1,0,"C");
         
            $this->Ln(5);
            $this->Cell(28,5,"",0,0,"C");
            $this->Cell(95,5,"",0,0,"C");
            $this->Cell(157,5,"",0,0,"C");
            $this->Cell(45,5,"",0,0,"C");
            
            $this->Ln(5);
            $this->Cell(28,5,utf8_decode("CÓDIGO"),0,0,"C");
            $this->Cell(95,5,"NOMBRE",0,0,"C");
            $this->Cell(157,5,"ESPECIFICACIONES",0,0,"C");
            $this->Cell(45,5,"VALOR",0,0,"C");
           
            $this->Ln(5);
        }

        function Footer(){
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
            $this->Cell(70);
            $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
            $this->Cell(60);
            $this->Cell(30,10,utf8_decode('Usuario: '.$usuario),0);
            $this->Cell(70);
            $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
        }
    }

    $pdf = new PDF('L','mm','Legal');
    $nb  = $pdf->AliasNbPages();                    #Objeto de número de pagina
    $pdf->AddPage();                                #Agregar página
    $pdf->SetFont('Arial','B',9);
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

        $sql_e = "SELECT p.id_unico , MAX(m.id_unico), (SELECT m2.dependencia FROM gf_movimiento m2 WHERE m2.id_unico = MAX(m.id_unico)) as dependencia from gf_movimiento m 
                    LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                    LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento
                    LEFT JOIN gf_producto p ON mp.producto = p.id_unico 
                  WHERE     (m.fecha    BETWEEN '$fechaI'    AND '$fechaF')
                  AND       (p.id_unico BETWEEN $productoIni AND $productoFin)
                  AND       (m.compania       = $compania)
                  AND       (p.baja IS NULL OR p.baja =0)
                  GROUP BY p.id_unico";

        $res_e = $mysqli->query($sql_e);
        if($res_e->num_rows > 0){
            while($row_e = mysqli_fetch_row($res_e)){
                $str   = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                $res   = $mysqli->query($str);
                $rw_   = mysqli_fetch_row($res);

                $sql_s = "SELECT    pro.id_unico,
                                pln.codi,
                                pln.nombre,
                                pro.descripcion,
                                pro.valor,
                                tpm.clase,
                                tpm.sigla,
                                mov.numero,
                                DATE_FORMAT(mov.fecha,'%d/%m/%Y'),
                                IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                   ter.razonsocial,
                                   CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS NOMBRE,
                                CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion),
                                DATE_FORMAT(pro.fecha_adquisicion, '%d/%m/%Y') 
                          FROM      gf_movimiento_producto mpr
                          LEFT JOIN gf_producto            pro ON mpr.producto           = pro.id_unico
                          LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento  = dtm.id_unico
                          LEFT JOIN gf_movimiento          mov ON dtm.movimiento         = mov.id_unico
                          LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento     = tpm.id_unico
                          LEFT JOIN gf_tercero             ter ON mov.tercero            = ter.id_unico
                          LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
                          LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento     = pln.id_unico
                          WHERE     pro.id_unico        = $row_e[0]
                          AND       (pln.compania       = $compania)
                          AND       (mov.compania       = $compania)";
                $rs_s  = $mysqli->query($sql_s);
                $row_s = mysqli_fetch_row($rs_s);
                

                if(!empty($row_s[11])){
                    $fecha_a = $row_s[11];
                }else{
                    $fecha_a = $row_s[8];
                }
                //DESCRIPCION
                $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie, pr.descripcion 
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                    LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                    LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                    LEFT JOIN gf_producto pr ON mpr.producto = pr.id_unico 
                        WHERE  mpr.producto = ".$row_e[0]." AND pre.fichainventario != 6");
                $descripcion = "DESCRIPCIÓN: ".$vp[0][2].' ';
                for ($j = 0; $j < count($vp); $j++) {
                    $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                }
                #ÚLTIMO MOVIMIENTO 
                $rowum = $con->Listar(" SELECT tpm.sigla,
                        mov.numero,
                        DATE_FORMAT(mov.fecha,'%d/%m/%Y'), 
                        CONCAT(COALESCE(t.nombreuno, ''),' ',COALESCE(t.nombredos, ''),' ',COALESCE(t.apellidouno, ''),' ',COALESCE(t.apellidodos, ''),'',COALESCE(t.razonsocial, '')),
                        CONCAT_WS(' ', COALESCE(t.numeroidentificacion, ''),COALESCE(t.digitoverficacion, '') ) 
                    FROM gf_movimiento mov 
                    LEFT JOIN gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_tercero t ON mov.tercero = t.id_unico 
                    WHERE mov.id_unico = ".$row_e[1] );

                $pdf->SetFont('Arial','',7);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->CellFitScale(30,5,$row_s[1],0,0,"L");
                $pdf->MultiCell(100,5,utf8_decode($row_s[2]),0,"L");
                $h1 = $pdf->GetY() - $y;
                $pdf->SetXY($x+124, $y);
                $pdf->MultiCell(157,5,utf8_decode($descripcion),0,"L");
                $h2 = $pdf->GetY() - $y;
                $pdf->SetXY($x+299, $y);
                $pdf->CellFitScale(25,5, number_format($row_s[4], 2),0,0,"R");
               
           $valorto+=$row_s[4];
                
                $h3 = $pdf->GetY() - $y;
                $pdf->SetXY($x, $y);
                $h = max($h1, $h2, $h3);
                $pdf->Ln($h);
                $totald +=$row_s[4];
                if($pdf->GetY()>170){
                    $pdf->AddPage();
                }

                
            }
           
            //Total final
            $pdf->SetFont('Arial','B',8);
            $pdf->CellFitScale(330,0.5,'',1,0,'L');
            $pdf->Ln(1);
            $pdf->CellFitScale(295,5,("TOTAL GENERAL"),0,0,"R");
            $pdf->CellFitScale(30,5, number_format($valorto, 2),0,0,"R");
           //Firmas
            $pdf->SetFont('Arial','B',9);
            $y =$pdf->GetY();
            $x = $pdf->GetX();
            $pdf->SetXY($x-180, $y+30); 
            $pdf->Cell(60,0.1,'',1);  
            $pdf->Ln(3); 
            $pdf->SetXY($x-180, $y+30); 
            $pdf->cellfitscale(200,5,utf8_decode($firma[0][2]),0,0,'L');
            $pdf->Ln(3);    
            $pdf->SetXY($x-180, $y+34); 
            $pdf->cellfitscale(50,5,utf8_decode($firma[0][0]),0,0,'L'); 
            
        
        
        }
       
    while (ob_get_length()) {
      ob_end_clean();
    }

    
    $pdf->Output(0,"InformeInventarioGeneralDeInmuebles.pdf",0);
}