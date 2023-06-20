<?php 
require'../Conexion/conexion.php';
require_once("../Conexion/ConexionPDO.php");
ini_set('max_execution_time', 360);
session_start();
$con      = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$usuario  = $_SESSION['usuario'];
$hoy      = date('d/m/Y');
$empleado  = $_REQUEST['e'];  
$periodo   = $_REQUEST['p'];  

#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
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


$consulta3 = "SELECT p.id_unico, p.codigointerno, DATE_FORMAT(p.fechainicio,'%d/%m/%Y'), DATE_FORMAT(p.fechafin,'%d/%m/%Y') , tpn.nombre, p.fechainicio, p.fechafin
FROM gn_periodo p 
LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
WHERE (p.id_unico) =  '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);
$codigo = mb_strtoupper($perN[4].' - '.$perN[1]);
$codigo2 = mb_strtoupper($perN[1]);
$fechaI = $perN[2];
$fechaF = $perN[3];
$fechaInicio = $perN[5];
$fechaFin    = $perN[6];       

require'../fpdf/fpdf.php';
ob_start();
class PDF extends FPDF
{
    function Header()
    { 
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $ruta_logo;
        global $codigo;
        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,10,3,30);
        } 
        $this->SetFont('Arial','B',10);
        $this->SetX(25);
        $this->Cell(170,5,utf8_decode(ucwords($razonsocial)),0,0,'C');
        $this->Ln(5);        
        $this->SetFont('Arial','',8);
        $this->SetX(25);
        $this->Cell(170, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); ;
        $this->Ln(5);
        $this->SetX(25);
        $this->Cell(170,5,utf8_decode('VOLANTE DE '.$codigo),0,0,'C');
        $this->Ln(3);
    }
    function Footer(){
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(130,10,utf8_decode(''),0,0,'C');
        $this->Cell(40,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    }
}

$pdf = new PDF('P','mm','Letter'); 
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);
$emp='';
#Consulta Empleados
if(empty($empleado) || $empleado == 2){
$emp .= ' and (n.empleado) != 2';
} else {
$emp .= ' and (n.empleado)= '.$empleado;
}
$sql = "SELECT distinct  e.id_unico, 
        e.tercero, 
        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
        tc.categoria, 
        c.id_unico, 
        c.nombre, 
      (SELECT n.valor FROM `gn_novedad` n 
                           LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                          WHERE n.concepto IN (1)
                         AND (n.periodo)='$periodo'
                         $emp
                          AND c.clase=6 LIMIT 1) as SalarioBaseIn,
        gg.nombre,
        t.numeroidentificacion                            
 FROM gn_novedad n 
LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
LEFT JOIN gf_tercero t on e.tercero = t.id_unico
LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
LEFT JOIN gn_concepto cn ON n.concepto =cn.id_unico 
WHERE n.periodo = '$periodo' AND cn.clase = 1 AND cn.unidadmedida = 1 "; 
$wo ='ORDER BY e.id_unico';
$wh = '';
if(empty($empleado) || $empleado == 2){
$wh .= ' AND e.id_unico != 2';
} else {
$wh .= ' AND e.id_unico = '.$empleado;
}
$row = $con->Listar($sql.' '.$wh.' '.$wo);

for ($i=0; $i <count($row) ; $i++) { 
    $idemp = $row[$i][0];
     #********* Consultas***********#
    #Fecha Vinculación Retiro
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT(fechaacto, '%d/%m/%Y') FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");
    $rowsal = $con->Listar("SELECT fechaacto, DATE_FORMAT(fechaacto, '%d/%m/%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 2 
        ORDER BY fechaacto  DESC LIMIT 1");
    $fechaRetiro = $rowsal[0][0];
    $fechaRetiroI = $rowsal[0][1];

    $id_conceptos = id_concepto('001');
    $salario      = valorc($idemp, $id_conceptos); 
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(11);
    $pdf->Cell(25,18,utf8_decode('NÓMINA:'),0,0,'L');
    $pdf->Cell(24,18,utf8_decode($codigo2),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($row[$i][2].' - '.$row[$i][8]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(25,18,utf8_decode('FECHA INICIAL:'),0,0,'L');
    $pdf->Cell(24,18,utf8_decode($rowing[0][1]),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('CARGO:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($row[$i][5]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(25,19,utf8_decode('FECHA RETIRO:'),0,0,'L');
    $pdf->Cell(24,18,utf8_decode($fechaRetiroI),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($row[$i][7]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(25,19,utf8_decode('SALARIO BASE:'),0,0,'L');
    $pdf->Cell(24,18,number_format($salario, 2),0,0,'L');
    
    $pdf->Ln(15);
    
    
    
    #Cuentas B
    $rowcb = $con->Listar("SELECT DISTINCT cb.numerocuenta, t.razonsocial FROM gn_empleado e 
    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico  
    LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = tr.id_unico 
    LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
    LEFT JOIN gf_tercero t ON cb.banco = t.id_unico 
    WHERE cb.parametrizacionanno = $anno AND e.id_unico =".$idemp);
    #Firmas
    $firma = "SELECT e.id_unico, IF(CONCAT_WS(' ',
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
     FROM gn_empleado e LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico  WHERE e.id_unico = '$idemp'";
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
      LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
      WHERE td.nombre = 'Liquidacion Final'
      ORDER BY rd.orden ASC";
    $fi = $mysqli->query($firmas);
    
   
    #Días Trabajados 
    $id_concepto = id_concepto('007');
    $valordt     = valorc($idemp, $id_concepto); 
    #**********************************************************************************
    #*** FACTORES 
    $rowf = $con->Listar("SELECT DISTINCT n.id_unico, 
         c.codigo, 
         c.descripcion,
         n.valor 
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.empleado =$idemp  AND n.periodo = $periodo  
        AND c.clase IN (8) and c.unidadmedida = 1 AND n.valor !=0 
        ORDER BY c.id_unico");
    
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode('FACTORES DE LIQUIDACIÓN'),1,0,'C');
    $pdf->Ln(8);
    $y1 = $pdf->GetY();
    $pdf->SetFont('Arial','',10);
    for ($f=0; $f <count($rowf) ; $f++) {             
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($rowf[$f][1].' - '.$rowf[$f][2]),0,0,'L');
        $pdf->Cell(85,6,number_format($rowf[$f][3], 2),0,0,'R');
        $pdf->Ln(6);
    }
    $h = $pdf->GetY() -$y1;
    $pdf->SetXY(11, $y1);
    $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
    $pdf->Ln($h);
    if($pdf->GetY()>200){
        $pdf->AddPage();
        $pdf->Ln(10);
    }


    #**Liquidación Cesantías
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN CESANTÍAS'),1,0,'C');

    $pdf->Ln(8);
    $y = $pdf->GetY();
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
    $pdf->Ln(8);
    $pdf->SetXY(11, $y);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,6,utf8_decode('FECHA INGRESO:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,utf8_decode($rowing[0][1]),0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,utf8_decode($rowsal[0][1]),0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,6,utf8_decode('TOTAL DÍAS TRABAJADOS:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,utf8_decode($valordt),0,0,'L');
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $y1 = $pdf->GetY();
    #850
    $id_concepto850 = id_concepto('850');
    $valor850       = valorc($idemp, $id_concepto850); 
    if($valor850 !=0){        
        $nconcepto850 = nombre_concepto('850');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto850),0,0,'L');
        $pdf->Cell(85,6,number_format($valor850, 2),0,0,'R');
        $pdf->Ln(6);
    }
    
    #1030
    $id_concepto1030 = id_concepto('1030');
    $valor1030       = valorc($idemp, $id_concepto1030); 
    if($valor1030 !=0){
        $nconcepto1030 = nombre_concepto('1030');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto1030),0,0,'L');
        $pdf->Cell(85,6,number_format($valor1030, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #184
    $id_concepto184 = id_concepto('184');
    $valor184       = valorc($idemp, $id_concepto184); 
    if($valor184 !=0){
        $nconcepto184 = nombre_concepto('184');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto184),0,0,'L');
        $pdf->Cell(85,6,number_format($valor184, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #C14
    $id_conceptoC14 = id_concepto('C14');
    $valorC14       = valorc($idemp, $id_conceptoC14); 
    if($valorC14 !=0){
        $nconceptoC14 = nombre_concepto('C14');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconceptoC14),0,0,'L');
        $pdf->Cell(85,6,number_format($valorC14, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #170
    $id_concepto170 = id_concepto('170');
    $valor170       = valorc($idemp, $id_concepto170); 
    if($valor170 !=0){
        $nconcepto170 = nombre_concepto('170');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto170),0,0,'L');
        $pdf->Cell(85,6,number_format($valor170, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #171
    $id_concepto171 = id_concepto('171');
    $valor171       = valorc($idemp, $id_concepto171); 
    if($valor171 !=0){
        $nconcepto171 = nombre_concepto('171');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto171),0,0,'L');
        $pdf->Cell(85,6,number_format($valor171, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #189
    $id_concepto189 = id_concepto('189');
    $valor189       = valorc($idemp, $id_concepto189); 
    if($valor189 !=0){
        $nconcepto189 = nombre_concepto('189');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto189),0,0,'L');
        $pdf->Cell(85,6,number_format($valor189, 2),0,0,'R');
        $pdf->Ln(6);
    }


    #188
    $id_concepto188 = id_concepto('188');
    $valor188       = valorc($idemp, $id_concepto188); 
    if($valor188 !=0){
        $nconcepto188 = nombre_concepto('188');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto188),0,0,'L');
        $pdf->Cell(85,6,number_format($valor188, 2),0,0,'R');
        $pdf->Ln(6);
    }


    $h = $pdf->GetY() -$y1;
    $pdf->SetXY(11, $y1);
    $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
    $pdf->Ln($h);
    if($pdf->GetY()>200){
        $pdf->AddPage();
        $pdf->Ln(10);
    }
    #**Liquidación VACACIONES
    $fechaInV = fechaVacaciones($idemp);
    //$fechaInV = c_fecha($fechaInV);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN VACACIONES'),1,0,'C');
    $pdf->Ln(8);
    $y = $pdf->GetY();
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
    $pdf->Ln(8);
    $pdf->SetXY(11, $y);
    $pdf->SetFont('Arial','B',8);

    $pdf->Cell(60,6,utf8_decode('PERIODO LIQUIDAR VACACIONES: '),0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,6,utf8_decode('FECHA INICIAL:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,utf8_decode($fechaInV),0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,6,utf8_decode($fechaRetiroI),0,0,'L');
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $y1 = $pdf->GetY();
    #L003
    $id_conceptoL003 = id_concepto('L003');
    $valorL003       = valorc($idemp, $id_conceptoL003); 
    if($valorL003 !=0){
        $nconceptoL003 = nombre_concepto('L003');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconceptoL003),0,0,'L');
        $pdf->Cell(85,6,number_format($valorL003, 2),0,0,'R');
        $pdf->Ln(6);
    }
    #094
    $id_concepto094 = id_concepto('094');
    $valor094       = valorc($idemp, $id_concepto094); 
    if($valor094 !=0){
        $nconcepto094 = nombre_concepto('094');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto094),0,0,'L');
        $pdf->Cell(85,6,number_format($valor094, 2),0,0,'R');
        $pdf->Ln(6);
    }
    #176
    $id_concepto176 = id_concepto('176');
    $valor176       = valorc($idemp, $id_concepto176); 
    if($valor176 !=0){
        $nconcepto176 = nombre_concepto('176');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto176),0,0,'L');
        $pdf->Cell(85,6,number_format($valor176, 2),0,0,'R');
        $pdf->Ln(6);
    }
    #Parametro 
    $vlp = parametros('dias_primav', $idemp);
    if($vlp !=0){
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode('DÍAS BASE A LIQUIDAR PRIMA VACACIONES'),0,0,'L');
        $pdf->Cell(85,6,number_format($vlp, 2),0,0,'R');
        $pdf->Ln(6);
    }
    #040
    $id_concepto040 = id_concepto('040');
    $valor040       = valorc($idemp, $id_concepto040); 
    if($valor040 !=0){
        $nconcepto040 = nombre_concepto('040');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto040),0,0,'L');
        $pdf->Cell(85,6,number_format($valor040, 2),0,0,'R');
        $pdf->Ln(6);
    }

    #175
    $id_concepto175 = id_concepto('175');
    $valor175       = valorc($idemp, $id_concepto175); 
    if($valor175 !=0){
        $nconcepto175 = nombre_concepto('175');
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($nconcepto175),0,0,'L');
        $pdf->Cell(85,6,number_format($valor175, 2),0,0,'R');
        $pdf->Ln(6);
    }

    $h = $pdf->GetY() -$y1;
    $pdf->SetXY(11, $y1);
    $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
    $pdf->Ln($h);

    if($pdf->GetY()>200){
        $pdf->AddPage();
        $pdf->Ln(10);
    }

    #**Liquidación PRIMA SERVICIOS
    $fechaPS = fechaConcepto($empleado, '160');
    $fechaPS = c_fecha($fechaPS);
    #*CONCEPTOS 
    #L004
    $id_conceptoL004 = id_concepto('L004');
    $valorL004       = valorc($idemp, $id_conceptoL004); 

    #038
    $id_concepto038 = id_concepto('038');
    $valor038       = valorc($idemp, $id_concepto038); 

    #160
    $id_concepto160 = id_concepto('160');
    $valor160       = valorc($idemp, $id_concepto160); 

    IF($valorL004 !=0 || $valor038 !=0 || $valor160!=0){ 
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN PRIMA SERVICIOS'),1,0,'C');
        $pdf->Ln(8);
        $y = $pdf->GetY();
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
        $pdf->Ln(8);
        $pdf->SetXY(11, $y);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,6,utf8_decode('PERIODO LIQUIDAR PRIMA SERVICIOS: '),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA INICIAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaPS),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaRetiroI),0,0,'L');
        $pdf->Ln(8);

        $pdf->SetFont('Arial','',10);
        $y1 = $pdf->GetY();    
        if($valorL004 !=0){
            $nconceptoL004 = nombre_concepto('L004');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconceptoL004),0,0,'L');
            $pdf->Cell(85,6,number_format($valorL004, 2),0,0,'R');
            $pdf->Ln(6);
        }
        #Parametro 
        $vlp = parametros('dias_prima_servicio', $idemp);
        if($vlp !=0){
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode('DÍAS BASE A LIQUIDAR PRIMA DE SERVICIOS'),0,0,'L');
            $pdf->Cell(85,6,number_format($vlp, 2),0,0,'R');
            $pdf->Ln(6);
        }
        
        if($valor038 !=0){
            $nconcepto038 = nombre_concepto('038');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto038),0,0,'L');
            $pdf->Cell(85,6,number_format($valor038, 2),0,0,'R');
            $pdf->Ln(6);
        }    
        
        if($valor160 !=0){
            $nconcepto160 = nombre_concepto('160');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto160),0,0,'L');
            $pdf->Cell(85,6,number_format($valor160, 2),0,0,'R');
            $pdf->Ln(6);
        }


        $h = $pdf->GetY() -$y1;
        $pdf->SetXY(11, $y1);
        $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
        $pdf->Ln($h);
    }
    if($pdf->GetY()>200){
        $pdf->AddPage();
        $pdf->Ln(10);
    }
    #**Liquidación PRIMA NAVIDAD
    $fechaPN = fechaConcepto($empleado, '158');
    $fechaPN = c_fecha($fechaPN);
    #CONCEPTOS 
    #L005
    $id_conceptoL005 = id_concepto('L005');
    $valorL005       = valorc($idemp, $id_conceptoL005); 
    #039
    $id_concepto039 = id_concepto('039');
    $valor039       = valorc($idemp, $id_concepto039); 
    #158
    $id_concepto158 = id_concepto('158');
    $valor158       = valorc($idemp, $id_concepto158); 
    if($valorL005 !=0 || $valor039 !=0 || $valor158 !=0){
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN PRIMA NAVIDAD'),1,0,'C');
        $pdf->Ln(8);
        $y = $pdf->GetY();
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
        $pdf->Ln(8);
        $pdf->SetXY(11, $y);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,6,utf8_decode('PERIODO LIQUIDAR PRIMA SERVICIOS: '),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA INICIAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaPN),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaRetiroI),0,0,'L');
        $pdf->Ln(8);
        $pdf->SetFont('Arial','',10);
        $y1 = $pdf->GetY();
        #L005
        if($valorL005 !=0){
            $nconceptoL005 = nombre_concepto('L005');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconceptoL005),0,0,'L');
            $pdf->Cell(85,6,number_format($valorL005, 2),0,0,'R');
            $pdf->Ln(6);
        }
        #Parametro 
        $vlp = parametros('dias_prima_navidad', $idemp);
        if($vlp !=0){
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode('DÍAS BASE A LIQUIDAR PRIMA DE NAVIDAD'),0,0,'L');
            $pdf->Cell(85,6,number_format($vlp, 2),0,0,'R');
            $pdf->Ln(6);
        }

        #039
        if($valor039 !=0){
            $nconcepto039 = nombre_concepto('039');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto039),0,0,'L');
            $pdf->Cell(85,6,number_format($valor039, 2),0,0,'R');
            $pdf->Ln(6);
        }

        #158
        if($valor158 !=0){
            $nconcepto158 = nombre_concepto('158');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto158),0,0,'L');
            $pdf->Cell(85,6,number_format($valor158, 2),0,0,'R');
            $pdf->Ln(6);
        }


        $h = $pdf->GetY() -$y1;
        $pdf->SetXY(11, $y1);
        $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
        $pdf->Ln($h);
        if($pdf->GetY()>200){
            $pdf->AddPage();
            $pdf->Ln(10);
        }
    }
    #**Liquidación PRIMA ANTIGUEDAD 
    $fechaPA = fechaConcepto($empleado, '150');
    $fechaPA = c_fecha($fechaPA);
    #cONCEPTOS
    #L006
    $id_conceptoL006 = id_concepto('L006');
    $valorL006       = valorc($idemp, $id_conceptoL006); 
    #L007
    $id_conceptoL007 = id_concepto('L007');
    $valorL007       = valorc($idemp, $id_conceptoL007); 
     #150
    $id_concepto150 = id_concepto('150');
    $valor150       = valorc($idemp, $id_concepto150); 

    if($valorL006 !=0 || $valorL007 !=0 || $valor150 !=0) {

        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN PRIMA ANTIGUEDAD'),1,0,'C');
        $pdf->Ln(8);
        $y = $pdf->GetY();
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
        $pdf->Ln(8);
        $pdf->SetXY(11, $y);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,6,utf8_decode('PERIODO LIQUIDAR PRIMA SERVICIOS: '),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA INICIAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaPA),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaRetiroI),0,0,'L');
        $pdf->Ln(8);
        $pdf->SetFont('Arial','',10);
        $y1 = $pdf->GetY();
        #L006
        if($valorL006 !=0){
            $nconceptoL006 = nombre_concepto('L006');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconceptoL006),0,0,'L');
            $pdf->Cell(85,6,number_format($valorL006, 2),0,0,'R');
            $pdf->Ln(6);
        }

        #L007
        if($valorL007 !=0){
            $nconceptoL007 = nombre_concepto('L007');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconceptoL007),0,0,'L');
            $pdf->Cell(85,6,number_format($valorL007, 2),0,0,'R');
            $pdf->Ln(6);
        }

        #150
        if($valor150 !=0){
            $nconcepto150 = nombre_concepto('150');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto150),0,0,'L');
            $pdf->Cell(85,6,number_format($valor150, 2),0,0,'R');
            $pdf->Ln(6);
        }

        $h = $pdf->GetY() -$y1;
        $pdf->SetXY(11, $y1);
        $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
        $pdf->Ln($h);

        if($pdf->GetY()>250){
            $pdf->AddPage();
            $pdf->Ln(10);
        }
    }
    #**Liquidación BONIFICACIÓN POR SERVICIOS PRESTADOS 
    $fechaBSP = fechaConcepto($empleado, '161');
    $fechaBSP = c_fecha($fechaBSP);
    #cONCEPTOS 
    #L008
    $id_conceptoL008 = id_concepto('L008');
    $valorL008       = valorc($idemp, $id_conceptoL008); 
    #161
    $id_concepto161 = id_concepto('161');
    $valor161       = valorc($idemp, $id_concepto161); 
    if($valorL008 !=0 || $valor161 !=0){

        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode('LIQUIDACIÓN BONIFICACIÓN POR SERVICIOS PRESTADOS'),1,0,'C');
        $pdf->Ln(8);
        $y = $pdf->GetY();
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode(''),1,0,'C');
        $pdf->Ln(8);
        $pdf->SetXY(11, $y);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,6,utf8_decode('PERIODO LIQUIDAR PRIMA SERVICIOS: '),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA INICIAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaBSP),0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,6,utf8_decode('FECHA FINAL:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30,6,utf8_decode($fechaRetiroI),0,0,'L');
        $pdf->Ln(8);
        $pdf->SetFont('Arial','',10);
        $y1 = $pdf->GetY();
        #L008
        if($valorL008 !=0){
            $nconceptoL008 = nombre_concepto('L008');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconceptoL008),0,0,'L');
            $pdf->Cell(85,6,number_format($valorL008, 2),0,0,'R');
            $pdf->Ln(6);
        }
        #161 
        if($valor161 !=0){
            $nconcepto161 = nombre_concepto('161');
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($nconcepto161),0,0,'L');
            $pdf->Cell(85,6,number_format($valor161, 2),0,0,'R');
            $pdf->Ln(6);
        }

        $h = $pdf->GetY() -$y1;
        $pdf->SetXY(11, $y1);
        $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
        $pdf->Ln($h);

        if($pdf->GetY()>220){
            $pdf->AddPage();
            $pdf->Ln(10);
        }
    }

    #OTROS DEVENGOS
    $rowod = $con->Listar("SELECT DISTINCT c.codigo,  c.descripcion, SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.empleado = $idemp  and n.periodo =$periodo
        AND c.clase = 1 AND c.unidadmedida = 1
        AND c.codigo NOT IN ('007','850','1030','184','C14','170','171','L003', '094', '176','040','175','160','L004',
        '038','160','158','L005','039','158','150','L006','L007','150','161','L008','161')
        GROUP BY n.empleado, n.periodo, n.concepto");
    if(count($rowod)>0){
        $tod = 0;
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(11);
        $pdf->Cell(185,8,utf8_decode('OTROS DEVENGOS'),1,0,'C');
        $pdf->Ln(8);
        $y1 = $pdf->GetY();
        $pdf->SetFont('Arial','',10);
        for ($od=0; $od <count($rowod) ; $od++) { 
            $pdf->SetX(11);
            $pdf->Cell(100,6,utf8_decode($rowod[$od][0].' - '.$rowod[$od][1]),0,0,'L');
            $pdf->Cell(85,6,number_format($rowod[$od][2], 2),0,0,'R');
            $pdf->Ln(6);
        }
        $h = $pdf->GetY() -$y1;
        $pdf->SetXY(11, $y1);
        $pdf->Cell(185,$h,utf8_decode(''),1,0,'C');
        $pdf->Ln($h);
        if($pdf->GetY()>220){
            $pdf->AddPage();
            $pdf->Ln(10);
        }
    }

     #Descuentos
    $dv = $con->Listar("SELECT c.codigo, c.descripcion, n.valor FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $idemp 
        AND c.clase = 2 and c.unidadmedida = 1");
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(185,8,utf8_decode('DESCUENTOS'),1,0,'C');
    $pdf->Ln(8);
    $yd = $pdf->GetY();
    $tds = 0;
    $pdf->SetFont('Arial','',10);
    for ($i=0; $i < count($dv); $i++) { 
        $pdf->SetX(11);
        $pdf->Cell(100,6,utf8_decode($dv[$i][0].' - '.$dv[$i][1]),0,0,'L');
        $pdf->Cell(85,6,number_format($dv[$i][2], 2),0,0,'R');
        $pdf->Ln(6);
        $tds += $dv[$i][2];
    }
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(100,8,utf8_decode('TOTAL DESCUENTOS'),0,0,'L');
    $pdf->Cell(85,8,number_format($tds, 2),0,0,'R');
    $pdf->Ln(8);
    $hd = $pdf->GetY()-$yd;
    $pdf->SetXY(11, $yd);
    $pdf->Cell(185,$hd ,utf8_decode(''),1,0,'C');
    $pdf->Ln($hd );

    #Devengos
    $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $idemp 
        AND c.clase = 1 and c.unidadmedida = 1");

    if(empty($dv[0][0])){
        $tdv = 0;
    } else {
        $tdv = $dv[0][0];   
    }
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(100,8,utf8_decode('TOTAL DEVENGOS'),1,0,'L');
    $pdf->Cell(85,8,number_format($tdv, 2),1,0,'R');
    $pdf->Ln(8);

    #Neto
    $np = $tdv -$tds;

    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(11);
    $pdf->Cell(100,8,utf8_decode('NETO A PAGAR'),1,0,'L');
    $pdf->Cell(85,8,number_format($np, 2),1,0,'R');
    $pdf->Ln(15);


    #*FIRMAS
    $pdf->SetFont('Arial','BI',9);
    $pdf->cellfitscale(35,5,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(150,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,0,'L');
    $pdf->Ln(15);

    #*******FIRMAS  
    $pdf->SetFont('Arial','B',8);
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
    $pdf->SetXY($x+140, $y); 
    $pdf->Cell(60,0.1,'',1);
    $pdf->Ln(2); 
    $y1 =$pdf->GetY();
    $pdf->SetXY($x+140, $y1);
    $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
    $alto = $pdf->GetY();
        


}

ob_end_clean();     
$pdf->Output(0,'LiquidacionFinal'.$row[0][2].'pdf',0);

function valorc($empleado, $id_concepto){
    global $con;
    global $periodo;
    $c = $con->Listar("SELECT IF(SUM(valor) is null, 0, SUM(valor)) FROM gn_novedad WHERE periodo = $periodo and concepto = $id_concepto and empleado = $empleado");
    return $c[0][0];

}
function id_concepto($codigo){
    global $con;    
    $c = $con->Listar("SELECT id_unico FROM gn_concepto WHERE codigo = '$codigo'");
    return $c[0][0];
}
function nombre_concepto($codigo){
    global $con;    
    $c = $con->Listar("SELECT descripcion FROM gn_concepto WHERE codigo = '$codigo'");
    return $c[0][0];
}
function parametros($nombrecampo, $empleado){
    global $con;    
    global $anno;
    $c = $con->Listar("SELECT $nombrecampo FROM gn_parametros_liquidacion gn 
        LEFT JOIN gn_empleado e ON e.id_unico = $empleado 
        LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
        WHERE gn.vigencia = $anno AND gn.tipo_empleado = et.tipo");
    return $c[0][0];
}

function fechaVacaciones($empleado){
    global $con;
    global $fechaFin;
    global $fechaRetiro;
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT(fechaacto, '%d/%m/%Y') FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <'$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");
    $rowuv = $con->Listar("SELECT DATE_FORMAT(fechafin, '%d/%m/%Y') FROM gn_vacaciones where empleado = $empleado and fechafindisfrute <'$fechaRetiro'");
    $fechaIv = '';
    if(empty($rowuv[0][0])){
        $fechaIv = $rowing[0][0];
    } else {
        $fechaIv = $rowuv[0][0];
    }
    return $fechaIv ;
}


function c_fecha ($fecha){
    $fecha_div  = explode("-", $fecha);
    $anio       = trim($fecha_div[0]);
    $mes        = trim($fecha_div[1]);
    $dia        = trim($fecha_div[2]);
    $fechaC = $dia."/".$mes."/".$anio;
    return ($fechaC);
}

function fechaConcepto($empleado, $concepto){
    global $con;
    global $fechaFin;
    global $fechaRetiro;
    global $periodo;
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT(fechaacto, '%d/%m/%Y') FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <'$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");
    $rowuv = $con->Listar(" SELECT DATE_ADD(p.fechafin,INTERVAL 1 DAY), p.fechafin
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        where empleado = $empleado and c.codigo = '$concepto'
        and n.periodo <$periodo 
        AND p.fechafin<'$fechaRetiro'
        ORDER BY  p.fechafin DESC");
    $fechaIv = '';
    if(empty($rowuv[0][0])){
        $fechaIv = $rowing[0][0];
    } else {
        $fechaIv = $rowuv[0][0];
    }
    return $fechaIv ;
}