<?php

    require'../fpdf/fpdf.php';
    require_once('../jpgraph/src/jpgraph.php');
    require_once('../jpgraph/src/jpgraph_pie.php');
    require_once ("../jpgraph/src/jpgraph_pie3d.php");
    require'../Conexion/conexion.php';
    session_start();
    ob_start();
    ini_set('max_execution_time', 360);
    
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];
    
    //Fin Conversión Fecha / Hora
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anioh = $fecha_div[2];
    $mesh = $fecha_div[1];
    $diah = $fecha_div[0];
    $hoy = $diah.'/'.$mesh.'/'.$anioh;
    
    $consulta = "SELECT     t.razonsocial as traz,
                                            t.tipoidentificacion as tide,
                                            ti.id_unico as tid,
                                            ti.nombre as tnom,
                                            t.numeroidentificacion tnum
                FROM gf_tercero t
                LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                WHERE t.id_unico = $compania";

    $cmp = $mysqli->query($consulta);

    
    echo $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
    FROM gf_tercero ter 
    LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
    WHERE ter.id_unico = '.$compania;

    $rutaLogo = $mysqli->query($sqlRutaLogo);
    $rowLogo = mysqli_fetch_array($rutaLogo);
    $ruta = $rowLogo[0];
    $ciudadCompania = $rowLogo[1];
    
    $nomcomp = "";
    $tipodoc = "";
    $numdoc = "";
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = utf8_decode($fila['traz']);
        $tipodoc = utf8_decode($fila['tnom']);
        $numdoc = utf8_decode($fila['tnum']);
    }
    
    class PDF extends FPDF
    {
        
        public function gaficoPDF($datos,$labels,$nombreGrafico = NULL,$ubicacionTamamo = array(),$titulo = NULL)
        { 
            //construccion de los arrays de los ejes x e y
            if(!is_array($datos) || !is_array($ubicacionTamamo)){
                echo "los datos del grafico y la ubicacion deben de ser arreglos";
            }
            elseif($nombreGrafico == NULL){
                echo "debe indicar el nombre del grafico a crear";
            }
            else{ 
                 
                $x = $ubicacionTamamo[0];
                $y = $ubicacionTamamo[1]; 
                $ancho = $ubicacionTamamo[2];  
                $altura = $ubicacionTamamo[3];  
                #Creamos un grafico vacio
                $graph = new PieGraph(1000,600);
                #indicamos titulo del grafico si lo indicamos como parametro
                if(!empty($titulo)){
                    $graph->title->Set($titulo);
                }   
                //Creamos el plot de tipo tarta
                $p1 = new PiePlot3D($datos);
                
                #indicamos la leyenda para cada porcion de la tarta
                $p1->SetLegends($labels);
                $p1->SetCenter(0.5,0.4);
                //Añadirmos el plot al grafico
                $graph->Add($p1);
                //Borramos el grafico anterior antes que se cree uno nuevo
                @unlink ("$nombreGrafico.png"); 
                
                //mostramos el grafico en pantalla
                $graph->Stroke("$nombreGrafico.png"); 
                $this->Image("$nombreGrafico.png",$x,$y,$ancho,$altura);  
            } 
        }
        
        // Cabecera de página
        function Header()
        {
            // Logo
            //$this->Image('logo_pb.png',10,8,33);
            //Arial bold 15
            global $nomcomp;
            global $tipodoc;
            global $numdoc;
            global $fecha1;
            global $fecha2;
            global $numpaginas;
            global $ruta;
            $numpaginas=$numpaginas+1;

            // Logo
            if($ruta != '')
            {
            $this->Image('../'.$ruta,20,8,15);
            } 
                   
            $this->SetX(25);
            $this->SetFont('Arial','B',9);
            $this->Cell(240,5,utf8_decode($nomcomp),0,0,'C');
            $this->Ln(2);

            $this->SetFont('Arial','',10);
            $this->Cell(270,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
                        
            $this->Ln(10);
            $this->SetFont('Arial','B',9);
            $this->SetX(25);
            $this->Cell(240,5,utf8_decode('RESUMEN DE CONTRIBUYENTES INSCRITOS POR AÑO'),0,0,'C');
            $this->SetFont('Arial','B',8);
                       
            $this->Ln(17);

            $this->SetFont('Arial','B',8);
            $this->SetX(10);
                        
            

            $this->SetX(10);
            $this->Ln(9);

        }

        // Pie de página
        
        function Footer()
        {
            // Posición: a 1,5 cm del final
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
            $this->Cell(120,10,utf8_decode(''),0,0,'C');
            $this->Cell(30,10,utf8_decode(''),0,0,'C');
            $this->Cell(75,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }
    
    $pdf = new PDF('L','mm','Letter');

    
    $pdf->SetFont('Arial','B',10);

    $pdf->SetFont('Arial','',8);
    $pdf->SetX(50);
    
    
    $codd       = 0;
    $totales    = 0;
    $valorA     = 0;

    //Consulta el año mas antiguo en la fecha de incripcion de los contribuyentes
    $sql1 = "SELECT MIN(fechainscripcion) FROM gc_contribuyente WHERE fechainscripcion is not null ";
    $res1 = $mysqli->query($sql1);
    $row1 = mysqli_fetch_row($res1);
    
    $fechaA = explode("-",$row1[0]);
    $anioA = $fechaA[0];
    
    //Consulta el año mas reciente en la fecha de incripcion de los contribuyentes
    $sql2 = "SELECT MAX(fechainscripcion) FROM gc_contribuyente";
    $res2 = $mysqli->query($sql2);
    $row2 = mysqli_fetch_row($res2);
    
    $fechaR = explode("-",$row2[0]);
    $anioR = $fechaR[0];
    
    $anioinicial = $anioA;
    
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetX(20);
    $Y = $pdf->GetY();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(13,6,utf8_decode('Año'),1,0,'C');
    $pdf->Cell(22,6,utf8_decode('Contribuyentes'),1,0,'C');
    $pdf->Ln(6);      
    while($anioinicial <= $anioR ){

        echo $sql3 = "SELECT COUNT(c.id_unico) FROM  gc_contribuyente c WHERE year(c.fechainscripcion)= '$anioinicial'";
        $res3 = $mysqli->query($sql3);
        $row3 = mysqli_fetch_row($res3);
        
        $datos[]= $row3[0];
        $labels[]= $anioinicial;
        
        //llenar datos
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(13,6,utf8_decode($anioinicial),1,0,'C');
        $pdf->Cell(22,6,utf8_decode($row3[0]),1,0,'R');
        
        $anioinicial += 1;
        $pdf->Ln(6);
        
    }
     
    $X = $pdf->GetX();
    $y1 = $pdf->SetY($Y);
    $x1 = $pdf->SetX($X);
    
   
    $pdf->SetFont("Arial","B",8);//establecemos propiedades del texto tipo de letra, negrita, tamaño
    //$pdf->Cell(40,10,'hola mundo',1);
    
    //$pdf->Cell(265,5,"GRAFICO REALIZADO CON FPDF Y JGRAPH",0,0,'C');
    $pdf->gaficoPDF($datos, $labels,'Grafico',array(56,30,250,165),'');
    //$pdf->Output(); 
    //nombre del archivo de  descarga
    ob_end_clean();
    $pdf->Output(0,'Resumen de Contribuyentes por Año('.date('d/m/Y').').pdf',0);

?>
