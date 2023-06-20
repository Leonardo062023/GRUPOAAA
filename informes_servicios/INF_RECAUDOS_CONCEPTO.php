<?php

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Recaudos_Concepto.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$tipo = 1;//$_REQUEST['tipo'];
$sql  = "SELECT DISTINCT c.id_unico, LOWER(c.nombre) 
        FROM dbo_recaudos_febrero r 
        LEFT JOIN gp_pago p ON r.id_pago = p.id_unico 
        LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
        LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_concepto_servicio cs ON c.id_unico = cs.concepto 
        WHERE f.parametrizacionanno = $anno ";
$sql2  = "SELECT DISTINCT us.id_unico, us.codigo, us.nombre,es.id_unico, es.codigo, es.nombre 
        FROM dbo_recaudos_febrero r 
        LEFT JOIN gp_pago p ON r.id_pago = p.id_unico 
        LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
        LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_uso us ON uv.uso = us.id_unico 
        LEFT JOIN gp_estrato es ON uv.estrato = es.id_unico 
        WHERE f.parametrizacionanno = $anno ";
$sql3  = "SELECT SUM(dp.valor)
        FROM dbo_recaudos_febrero r 
        LEFT JOIN gp_pago p ON r.id_pago = p.id_unico 
        LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
        LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        WHERE f.parametrizacionanno = $anno  ";

if($tipo==1){
    $fechaI = '2021-02-16';//$_REQUEST['fechaI'];
    $fechaF = '2021-03-17';//$_REQUEST['fechaF'];
    $fi     = '2021-02-16';//fechaC($fechaI);
    $ff     = '2021-03-17';//fechaC($fechaF);
    $t1     = 'Fecha Inicial: '.$fechaI.' - Fecha Final: '.$fechaF;    

    $sql    .= " AND f.fecha_factura BETWEEN '".$fi."' AND '".$ff."' AND f.periodo IS NOT NULL ";
    $sql2   .= " AND f.fecha_factura BETWEEN '".$fi."' AND '".$ff."' AND f.periodo IS NOT NULL ";
    $sql3   .= " AND f.fecha_factura BETWEEN '".$fi."' AND '".$ff."' AND f.periodo IS NOT NULL ";

} else {
    $id_periodo      = $_REQUEST['p'];
    $p = $con->Listar("SELECT DISTINCT id_unico, 
        nombre, 
        DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
        DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
        FROM gp_periodo p 
        WHERE id_unico=".$_REQUEST['p']);
    $t1 ='Periodo: '.ucwords(mb_strtolower($p[0][1])).'  '.$p[0][2].' - '.$p[0][3];

    $sql    .= " AND f.periodo = $id_periodo";
    $sql2   .= " AND f.periodo = $id_periodo";
    $sql3   .= " AND f.periodo = $id_periodo";
}
if(!empty($_REQUEST['uso'])){
    $uso = $_REQUEST['uso'];
    $sql .=" AND uv.uso = $uso";
    $sql2 .=" AND uv.uso = $uso";
    
}
if(!empty($_REQUEST['estrato'])){
    $estrato = $_REQUEST['estrato'];
    $sql    .=" AND uv.estrato = $estrato";
    $sql2   .=" AND uv.estrato = $estrato";
    
}

#* CONTAR CONCEPTOS 
$sql    .=" ORDER BY cs.tipo_servicio, c.id_unico ";
$rowc = $con->Listar($sql);
$nc   = count($rowc)+3;

#* LISTAR USOS Y ESTRATOS 
$row = $con->Listar($sql2);

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 
?>


<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturación</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php 
            echo ' <th colspan="'.$nc.'" align="center"><strong>
                <br/>'.$razonsocial.'
                <br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer.'
                <br/>&nbsp;
                <br/>FACTURACIÓN SERVICIOS PÚBLICOS
                <br/>'.$t1.'
                <br/>&nbsp;</strong>
            </th>';

            echo '<tr>';
            echo '<td><strong>Uso</strong></td>';
            echo '<td><strong>Estrato</strong></td>';
            for ($i=0; $i <count($rowc) ; $i++) { 
                echo '<td><strong>'.ucwords($rowc[$i][1]).'</strong></td>';
            }
            echo '<td><strong>Total</strong></td>';
            echo '</tr>';        
            
            for ($j = 0; $j < count($row); $j++) {
                echo '<tr>';
                echo '<td>'.$row[$j][1].' - '.ucwords(mb_strtolower($row[$j][2])).'</td>';                  
                echo '<td>'.$row[$j][4].' - '.ucwords(mb_strtolower($row[$j][5])).'</td>';                  
                $total1 =0;
                for ($i=0; $i <count($rowc) ; $i++) { 
                    $id_concepto =$rowc[$i][0];
                    $sql4   = $sql3." AND df.concepto_tarifa = $id_concepto  AND uv.uso = ".$row[$j][0]." AND uv.estrato =".$row[$j][3];
                    $rowv    = $con->Listar($sql4);
                    echo '<td>'.number_format($rowv[0][0], 2, ',', '.').'</td>';
                    $total1 +=$rowv[0][0];
                }
                echo '<td>'.number_format($total1, 2, ',', '.').'</td>';
                echo '</tr>';
             }
            
            
            ?>
        </table>
    </body>
</html