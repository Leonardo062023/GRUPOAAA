<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();
require ('../Conexion/conexion.php');
require_once ("../Conexion/ConexionPDO.php");
$con = new ConexionPDO();
require ('../fpdf/fpdf.php');
if(!empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) &&
   !empty($_POST['sltProductoFinal'] && !empty($_POST['sltTipo']) && !empty($_POST['sltDepInicial']) && !empty($_POST['sltDepFinal']))){

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

    function obtnerDatosDepndencia($id, $compania){
        require ('../Conexion/conexion.php');
        $sql  = "SELECT UPPER(nombre), sigla FROM gf_dependencia WHERE id_unico = $id AND compania = $compania";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        return $row;
        $mysqli->close();
    }
    $sqlFechaI="SELECT fecha FROM gf_movimiento
    LIMIT 1";
    $fechaIniS = $mysqli->query($sqlFechaI);
    $rowFeI = mysqli_fetch_row($fechaIniS);
    
    $usuario = $_SESSION['usuario'];
    $compa   = $compania = $_SESSION['compania'];

    $fechaInicial = $_POST['txtFechaInicial'];
    $fechaFinal   = $_POST['txtFechaFinal'];

    $fechaI       = $rowFeI[0];
    $fechaF       = convertirFecha($fechaFinal);

    $productoIni  = $_POST['sltProductoInicial'];
    $productoFin  = $_POST['sltProductoFinal'];

    $proI         = obtenerDatosProducto($productoIni, $compania);
    $proF         = obtenerDatosProducto($productoFin, $compania);
    $productoI    = $proI[1]." - ".$proI[0];
    $productoF    = $proF[1]." - ".$proF[0];
    $tipoProducto = $_POST['sltTipo'];
    $depIni       = $_POST['sltDepInicial'];
    if ($tipoProducto==11) {
        $tipoProducto=" IN (2,3)";
    }else{
        $tipoProducto = "=".$_POST['sltTipo'];
    }

    $depFin       = $_POST['sltDepFinal'];

    $depI        = obtnerDatosDepndencia($depIni, $compania);
    $depF        = obtnerDatosDepndencia($depFin, $compania);
    $depenI      = $depI[1];
    $depenF      = $depF[1];


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

        #Funcón cabeza de la página
        function Header(){
            global $nombreCompania;
            global $nitcompania;
            global $numpaginas;
            global $fechaInicial;
            global $fechaFinal;
            global $ruta;
            global $productoI;
            global $productoF;
            global $depenI;
            global $depenF;
            $numpaginas=$this->PageNo();

            $this->SetFont('Arial','B',10);
            $this->SetY(10);
           /*  if($ruta != ''){
                $this->Image('../'.$ruta,15,8,15);
            }
 */
            $this->SetX(35);
            $this->Cell(308,5,utf8_decode($nombreCompania),0,0,'C');
            $this->Ln(5);

            $this->SetX(35);
            $this->Cell(308, 5,"NIT :".$nitcompania,0,0,'C');
            $this->Ln(5);

            $this->SetX(35);
            $this->Cell(308, 5,"INVENTARIO GENERAL DE  PROPIEDAD PLANTA Y EQUIPO POR DEPENDENCIAS",0,0,'C');
            $this->Ln(5);
            $this->SetX(35);
            $this->Cell(308, 5," ENTRE FECHAS : ".$fechaInicial." Y ".$fechaFinal,0,0,'C');
            $this->Ln(5);
            $this->SetX(35);
            $this->Cell(308, 5," ENTRE PRODUCTOS : ".$productoI." Y ".$productoF,0,0,'C');
            $this->Ln(5);

            $this->SetFont('Arial','B',8);
            $this->Cell(28,15,"",1,0,"C");
            $this->Cell(45,15,"",1,0,"C");
            $this->Cell(82,15,"",1,0,"C");
            $this->Cell(10,15,"",1,0,"C");
            $this->Cell(25,15,"",1,0,"C");
            $this->Cell(43,15,"",1,0,"C");
            $this->Cell(43,15,"",1,0,"C");
            $this->Cell(15,15,"",1,0,"C");
            $this->Cell(40,15,"",1,0,"C");
            $this->Ln(5);
            $this->Cell(28,5,"",0,0,"C");
            $this->Cell(45,5,"",0,0,"C");
            $this->Cell(82,5,"",0,0,"C");
            $this->Cell(10,5,"",0,0,"C");
            $this->Cell(25,5,"",0,0,"C");
            $this->Cell(43,5,"ENTRADA",0,0,"C");
            $this->Cell(43,5,utf8_decode("ÚLTIMO MOVIMIENTO"),0,0,"C");
            $this->Cell(15,5,"FECHA",0,0,"C");
            $this->Cell(40,5,"",0,0,"C");
            $this->Ln(5);
            $this->Cell(28,5,utf8_decode("CÓDIGO"),0,0,"C");
            $this->Cell(45,5,"NOMBRE",0,0,"C");
            $this->Cell(82,5,"ESPECIFICACIONES",0,0,"C");
            $this->Cell(10,5,"PLACA",0,0,"C");
            $this->Cell(25,5,"VALOR",0,0,"C");
            $this->Cell(8,5,"MOV",1,0,"C");
            $this->Cell(20,5,"NUMERO",1,0,"C");
            $this->Cell(15,5,"FECHA",1,0,"C");
            $this->Cell(8,5,"MOV",1,0,"C");
            $this->Cell(20,5,"NUMERO",1,0,"C");
            $this->Cell(15,5,"FECHA",1,0,"C");
            $this->Cell(15,5,"ADQ.",0,0,"C");
            $this->Cell(40,5,"RESPONSABLE",0,0,"C");
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

    $sql_dep = "SELECT d.id_unico, d.sigla, UPPER(d.nombre), CONCAT_WS(' - ',UPPER(dp.sigla) , UPPER(dp.nombre)) 
    FROM gf_dependencia d
    LEFT JOIN gf_dependencia dp ON d.predecesor = dp.id_unico 
     WHERE (d.id_unico BETWEEN $depIni AND $depFin) AND d.compania = $compania";
    $res_dep = $mysqli->query($sql_dep);
    $totaltotal =0;
    while($row_dep = mysqli_fetch_row($res_dep)){
        $totald = 0;
        $sql_e = "SELECT p.id_unico , MAX(m.id_unico), (SELECT m2.dependencia FROM gf_movimiento m2 WHERE m2.id_unico = MAX(m.id_unico)) as dependencia from gf_movimiento m 
                    LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                    LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento
                    LEFT JOIN gf_producto p ON mp.producto = p.id_unico 
                  WHERE     (m.fecha    BETWEEN '$fechaI'    AND '$fechaF')
                  AND       (p.id_unico BETWEEN $productoIni AND $productoFin)
                  AND       (m.compania       = $compania)
                  AND       (p.baja IS NULL OR p.baja =0)
                  GROUP BY p.id_unico
                  HAVING dependencia = ".$row_dep[0]; 
        $res_e = $mysqli->query($sql_e);
        if($res_e->num_rows > 0){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,5,"DEPENDENCIA",0,0,"R");
            $pdf->Cell(30,5,$row_dep[1],0,0,"L");
            $pdf->Cell(50,5,utf8_decode($row_dep[2]),0,0,"L");
            $pdf->Cell(50,5,utf8_decode('SEDE: '.$row_dep[3]),0,0,"L");
            $pdf->Ln(5);

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
                          AND       tpm.clase           = 2
                          AND        pln.tipoinventario  $tipoProducto
                          AND       (pln.compania       = $compania)
                          AND       (mov.compania       = $compania)";
                $rs_s  = $mysqli->query($sql_s);
                $row_s = mysqli_fetch_row($rs_s);
                if($rs_s->num_rows > 0){

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
                        WHERE  mpr.producto = ".$row_e[0]." AND pre.fichainventario != 6
                        AND    pln.tipoinventario $tipoProducto");
                $descripcion = "DESCRIPCIÓN: ".$vp[0][2].' ';
                for ($j = 0; $j < count($vp); $j++) {
                    $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                }
                #ÚLTIMO MOVIMIENTO 
                $rowum = $con->Listar(" SELECT tpm.sigla,
                        mov.numero,
                        DATE_FORMAT(mov.fecha,'%d/%m/%Y'), 
                        CONCAT_WS(' ', COALESCE(t.nombreuno, ''),COALESCE(t.nombredos, ''),COALESCE(t.apellidouno, ''),COALESCE(t.apellidodos, ''),COALESCE(t.razonsocial, '')), 
                        CONCAT_WS(' ', COALESCE(t.numeroidentificacion, ''),COALESCE(t.digitoverficacion, '') ) 
                    FROM gf_movimiento mov 
                    LEFT JOIN gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_tercero t ON mov.tercero = t.id_unico 
                    WHERE mov.id_unico = ".$row_e[1] );

                $pdf->SetFont('Arial','',7);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->CellFitScale(28,5,$row_s[1],0,0,"L");
                $pdf->MultiCell(45,5,utf8_decode($row_s[2]),0,"L");
                $h1 = $pdf->GetY() - $y;
                $pdf->SetXY($x+73, $y);

                $pdf->MultiCell(82,5,utf8_decode($descripcion),0,"L");
                $h2 = $pdf->GetY() - $y;
                $pdf->SetXY($x+155, $y);

                $pdf->CellFitScale(10,5,utf8_decode($rw_[0]),0,0,"L");
                $pdf->CellFitScale(25,5, number_format($row_s[4], 2),0,0,"R");
                $pdf->CellFitScale(8,5, $row_s[6],0,0,"L");
                $pdf->CellFitScale(20,5,$row_s[7],0,0,"L");
                $pdf->CellFitScale(15,5,$row_s[8],0,0,"L");
                $pdf->CellFitScale(8,5, $rowum[0][0],0,0,"L");
                $pdf->CellFitScale(20,5,$rowum[0][1],0,0,"L");
                $pdf->CellFitScale(15,5,$rowum[0][2],0,0,"L");
                $pdf->CellFitScale(15,5,$fecha_a,0,0,"L");

                $pdf->MultiCell(40,5,utf8_decode($rowum[0][3]),0,"L");
                $h3 = $pdf->GetY() - $y;
                $pdf->SetXY($x, $y);
                $h = max($h1, $h2, $h3);
                $pdf->Ln($h);

                $totald +=$row_s[4];
                if($pdf->GetY()>170){
                    $pdf->AddPage();
                }
                }
            }
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(165,5,utf8_decode('Total Dependencia: '.$row_dep[1]." ".$row_dep[2]),0,0,'L');
            $pdf->Cell(25,5,number_format($totald, 2, ',', '.'),0,0,'R');
            $pdf->Ln(5);
            $pdf->Cell(330,0.5,'',1,0,'L');
            $pdf->Ln(2);
            $totaltotal +=$totald;       
        }
    }
    $pdf->Cell(330,0.5,'',1,0,'L');
    $pdf->Ln(2);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(165,5,utf8_decode('TOTALES '),0,0,'L');
    $pdf->Cell(25,5,number_format($totaltotal, 2, ',', '.'),0,0,'R');
    $pdf->Ln(5);
    $pdf->Cell(330,0.5,'',1,0,'L');
    $pdf->Ln(2); 


    #Firmas
    $firma = "SELECT c.nombre, IF(CONCAT_WS(' ',
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
    FROM gn_empleado e LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico 
    LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
     LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
     WHERE tr.id_unico = $responInicial";
   $fir = $mysqli->query($firma);
   $nfir = mysqli_fetch_row($fir);

   $firmas = "SELECT   c.nombre, 
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
     LEFT JOIN   td ON rd.tipodocumento = td.id_unico
     WHERE td.nombre = 'Listado De Inventarios'
     ORDER BY rd.orden ASC";
   $fi = $mysqli->query($firmas);

     $pdf->SetFont('Arial','B',9);
     $y =$pdf->GetY();
     $x = $pdf->GetX();
     while($F = mysqli_fetch_row($fi)){
       if($F[1] ==  1){
            $pdf->Cell(60,0.1,'',1);  
            $pdf->Ln(2); 
            $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
           $pdf->Ln(3);
           $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
       }else{
           $pdf->SetXY($x+70, $y); 

           $pdf->Cell(60,0.1,'',1); 
           $pdf->Ln(2); 
           $y1 =$pdf->GetY();
           $pdf->SetXY($x+70, $y1); 
           $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
           $pdf->Ln(3); 
           $y2 =$pdf->GetY();
           $pdf->SetXY($x+70, $y2); 
           $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
       }
     }    
     while($F = mysqli_fetch_row($fi)){
        if($F[1] ==  1){
             $pdf->Cell(60,0.1,'',1);  
             $pdf->Ln(2); 
             $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3);
            $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
        }else{
            $pdf->SetXY($x+70, $y); 
 
            $pdf->Cell(60,0.1,'',1); 
            $pdf->Ln(2); 
            $y1 =$pdf->GetY();
            $pdf->SetXY($x+70, $y1); 
            $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3); 
            $y2 =$pdf->GetY();
            $pdf->SetXY($x+70, $y2); 
            $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
        }
    }    

     
     $pdf->SetXY($x+240, $y); 
     $pdf->Cell(60,0.1,'',1);
     $pdf->Ln(2); 
     $y1 =$pdf->GetY();
     $pdf->SetXY($x+240, $y1);
     $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
     $y1 =$pdf->GetY();
     $pdf->SetXY($x+240, $y1+3);
     $pdf->cellfitscale(50,5,utf8_decode(strtoupper($nfir[0])),0,0,'L');   
     $alto = $pdf->GetY();
         


    while (ob_get_length()) {
      ob_end_clean();
    }

    $pdf->Output(0,"InformeDevolutivosPorDependencia.pdf",0);
}