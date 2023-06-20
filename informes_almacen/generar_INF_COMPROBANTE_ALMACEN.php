<?php
#************ 04/09/2021- Elkin O- Se crea informe comprobantes almacen***********#
setlocale(LC_TIME, 'es_ES');
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0); 
session_start();
$con  = new ConexionPDO();
$fechaIni= $_REQUEST['fechaini'];
$fechaFin= $_REQUEST['fechafin'];
$movIni= $_REQUEST['sltTci'];
$movFin= $_REQUEST['sltTcf'];
$clase= $_REQUEST['sltClase'];

  if($clase==11){
    $claseM='7,2,6,3';
  }else{
    $claseM=$_REQUEST['sltClase'];
  }

$anno = $_SESSION['anno'];
$tipo = $_REQUEST['t'];
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];


#Funcion fecha

function convertirFecha($fecha){
    $fecha = explode("/", $fecha);
    return $fecha[2]."-".$fecha[1]."-".$fecha[0];
}
#   ************   Datos Compañia   ************    #
$rowC = $con->Listar("SELECT
ter.razonsocial,
ter.nombre_comercial,         
UPPER(ti.nombre),
ter.numeroidentificacion,
ciudad.nombre,
dir.direccion,
tel.valor,
ter.ruta_logo,
ter.email
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN gf_ciudad ciudad ON ciudad.id_unico = ter.ciudadidentificacion
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][0];
$nombreEm = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$ciudad = $rowC[0][4];
$direccinTer = $rowC[0][5];
$telefonoTer = $rowC[0][6];
$ruta_logo   = $rowC[0][7];
$email   = $rowC[0][8];
$t     = ''; 

##Fechas
$months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$fechactM = $months[(int) date("m")];
$fechact ='('.date('d'). ') días del mes de '.$fechactM.' de '.date(Y);
$fechaActEncabezado =date('d').' de '.$fechactM.' de '.date(Y);
$fecha_div = explode("/",$fechaIni);
$fecha_div1 = explode("/",$fechaFin);
$mesFI = $months[(int) $fecha_div[1]];
$mesFF = $months[(int) $fecha_div1[1]];
$fechaInicial = $fecha_div[0].' de '.$mesFI.' de '.$fecha_div[2];
$fechaFinal = $fecha_div1[0].' de '.$mesFI.' de '.$fecha_div1[2];

$fechaI       = convertirFecha($fechaIni);
$fechaF       = convertirFecha($fechaFin);

#**************** Consultas en general****************#
 $sql_tip = "SELECT DISTINCT tm.sigla, tm.nombre
                    FROM gf_detalle_movimiento dt
                    LEFT JOIN gf_movimiento m ON m.id_unico=dt.movimiento
                    LEFT JOIN gf_tipo_movimiento tm ON tm.id_unico=m.tipomovimiento
                    WHERE  m.fecha BETWEEN '$fechaI' AND  '$fechaI'
                    AND tm.sigla BETWEEN '$movIni' AND '$movFin'
                    AND tm.clase IN ($claseM)
                    GROUP BY tm.nombre ASC";

    $tipo_mo = $mysqli->query($sql_tip);


 #   ************   Armado de documento   ************    #

    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header(){ 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $ruta_logo;
            global $fechaIni;
            global $fechaFin;
            $numpaginas=$this->PageNo();
            $this->SetFont('Arial','B',10);
             
             if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,10,5,28);
            } 
            $this->SetY(15);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode(ucwords($razonsocial)),0,0,'C');
            $this->ln(5);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower('NIT '.$numeroIdent))),0,0,'C');
            $this->ln(5);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode(ucwords(' LISTADO DE COMPROBANTES DIARIOS')),0,0,'C');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode('Fecha: '.$fechaIni),0,0,'C');
            $this->ln(10);
            
        }      
        function Footer(){
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','',8);
            $this->ln(1);
            $this->Cell(150,5,utf8_decode('Elaboro: '.$usuario),0,0,"L"); 
            $this->ln(1);
            $this->Cell(0,5,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0,"R");
            
        }
             
    }

    $pdf = new PDF('P','mm',array(215,340));   
    $pdf->AddPage();
    $pdf->AliasNbPages(); 
    $pdf->SetY(40);
    $pdf->SetFont('Arial','B',8);
    $pdf->Ln();
    $pdf->SetX(15);
    $pdf->Cell(200,5,('Fecha '.$fechaInicial),0,0,"L");
    $pdf->Ln();

   
   while($row_M = mysqli_fetch_row($tipo_mo)){

    $pdf->SetFont('Arial','B',9);
    $pdf->SetX(15);
    $pdf->Cell(345,5,utf8_decode(strtoupper($row_M[1])),0,0,"L");
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->Cell(50,5,utf8_decode("NUMERO"),0,0,"C");
    $pdf->Cell(60,5,"DETALLE DEL COMPROBANTE",0,0,"L");
    $pdf->Cell(40,5,"DESTINO DEL COMPROBANTE",0,0,"C");
    $pdf->Cell(50,5,"VALOR",0,0,"C");
    $pdf->Ln(5);
      
    $sql_de="SELECT DISTINCT m.numero,m.descripcion,m.observaciones,
                      (SELECT sum(dt.valor*dt.cantidad) FROM gf_detalle_movimiento dt where dt.movimiento = m.id_unico) as ValT,m.fecha,tm.sigla,m.id_unico
                      FROM gf_movimiento m
                      LEFT JOIN gf_tipo_movimiento tm ON tm.id_unico=m.tipomovimiento
                      WHERE  m.fecha BETWEEN '$fechaI' AND  '$fechaI'
                      AND tm.sigla BETWEEN '$movIni' AND '$movFin'
                      AND tm.clase IN ($claseM)
                      GROUP BY m.id_unico  DESC";
      $detaM = $mysqli->query($sql_de); 
      
     
    while($row_D = mysqli_fetch_array($detaM)){
       
             if($row_M[0]==$row_D[5]){
                
             
                 // Valor total por tipo
                 $valorT=$con->Listar("SELECT DISTINCT     SUM(dt.valor * dt.cantidad)AS valort
                 FROM gf_movimiento m
                        LEFT JOIN gf_detalle_movimiento dt
                               ON m.id_unico = dt.movimiento
                        LEFT JOIN gf_tipo_movimiento tm
                               ON tm.id_unico = m.tipomovimiento
                         WHERE m.fecha BETWEEN '$fechaI' AND  '$fechaI'
                        AND tm.sigla = '$row_D[5]' 
                        GROUP BY tm.id_unico ");
               

                     
                     $pdf->SetX(15);
                   $pdf->SetFont('Arial','',8);
                   $x = $pdf->GetX();
                   $y = $pdf->GetY();
                   

                   $pdf->SetXY($x+17, $y);
                   $pdf->MultiCell(60,5,utf8_decode($row_D[0]) ,0,'L');
                   $h1 = ($pdf->GetY()-$y);
                   $pdf->SetXY($x+50, $y);
                   $pdf->MultiCell(60,5,utf8_decode($row_D[1]),0,'L');
                   $h2 = ($pdf->GetY()-$y);
                   $pdf->SetXY($x+125, $y);
                   $pdf->MultiCell(40,5,($row_D[2]),0,'L');
                   $h3 = ($pdf->GetY()-$y);
                   $pdf->SetXY($x+170, $y);
                   $pdf->MultiCell(60,5,number_format($row_D[3], 2),0,'L');
                   $h4 = ($pdf->GetY()-$y);
                   $pdf->SetFont('Arial','B',8);
                  
                   $alt = max($h1, $h2,$h3,$h4);
                   $pdf->SetXY($x, $y);
                   $pdf->Cell(33, $alt,'' ,0,0,'L');
                   $pdf->Cell(33,$alt,'' ,0,0,'L');
                   $pdf->Cell(33,$alt,'' ,0,0,'L');
                   $pdf->Cell(33,$alt,'' ,0,0,'L');
                   $pdf->Cell(33,$alt,'' ,0,0,'L');
                   $pdf->Cell(35,$alt,'' ,0,0,'L');
                   $pdf->Ln($alt);
                   if($pdf->GetY()>230){
                       $pdf->AddPage();
                   }  
              
                }      
        }

      
        $pdf->SetFont('Arial','B',8);
        $pdf->Ln(1);
        $pdf->SetX(15);
        $pdf->Cell(165,5,utf8_decode("Total por tipo"),0,0,"R");
        $pdf->Cell(25,5,number_format($valorT[0][0], 2),0,0,"C");
        $pdf->Ln(5);
       $valorFinal+=$valorT[0][0];


 }
 $pdf->SetFont('Arial','B',8);
 $pdf->Ln(2);
 $pdf->SetX(15);
 $pdf->Cell(165,5,utf8_decode('TOTAL GENERAL'),0,0,"R");
 $pdf->Cell(25,5,number_format($valorFinal, 2),0,0,"C");
 $pdf->Ln(5);


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
    $pdf->SetFont('Arial','',8);
    $pdf->Ln(20);
    if($firma[0][1]==1){
    $y =$pdf->GetY();
    $x = $pdf->GetX();
    $pdf->SetXY($x+20, $y-4); 
    $pdf->Cell(60,0.1,'',1);  
    $pdf->Ln(3); 
    $pdf->SetXY($x+20, $y-4); 
    $pdf->cellfitscale(200,5,utf8_decode($firma[0][2]),0,0,'L');
    $pdf->Ln(3);    
    $pdf->SetFont('Arial','B',8);
    $pdf->SetXY($x+20, $y); 
    $pdf->cellfitscale(50,5,utf8_decode($firma[0][0]),0,0,'L'); 
    }

    $pdf->SetXY($x+120, $y-4); 
    $pdf->Cell(60,0.1,'',1);  
    $pdf->Ln(3); 
    $pdf->SetXY($x+120, $y-4); 
    $pdf->cellfitscale(200,5,utf8_decode('RECIBIDO'),0,0,'L');   

    while (ob_get_length()) {
        ob_end_clean();
      }
    ob_end_clean();     
    $pdf->Output(0,'Listado_Comprobantes_Almacen.pdf',0);

           

    ?>
    
   
        
<?php ?>