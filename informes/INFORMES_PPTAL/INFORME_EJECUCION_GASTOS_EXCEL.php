<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#26/07/2018 |Erica G. | Encabezados
#29/08/2017 |Erica G. | Encabezado
#28/06/2017 |ERICA G. | ARCHIVO CREADO
#######################################################################################################
?>

<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ejecucion_Gastos.xls");
require'../../Conexion/conexion.php';
ini_set('max_execution_time',0);
require 'consultas.php';
@session_start();
?>

<?php
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
$fecha=date('Y-m-d');
$calendario     = CAL_GREGORIAN;
$mesI           = $mysqli->real_escape_string(''.$_REQUEST['sltmesi'].'');
$mesF           = $mysqli->real_escape_string(''.$_REQUEST['sltmesf'].'');
$codigoI        = $mysqli->real_escape_string(''.$_REQUEST['sltcodi'].'');
$codigoF        = $mysqli->real_escape_string(''.$_REQUEST['sltcodf'].'');
$parmanno       = $mysqli->real_escape_string(''.$_REQUEST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$meses = array('no', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
    'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

 $mesInicial = $meses[(int)$mesI];
 $mesFinal = $meses[(int)$mesF];
 $annoInforme = anno($parmanno);

$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
    $month1 = $meses[$mesI];
    $month2 = $meses[$mesF];

 #************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejecución de Gastos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
        <th colspan="21" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
            <br/>EJECUCION DEL PRESUPUESTO DE GASTOS E INVERSIONES POR PERIODO<br/>RUBROS DEL <?php echo $codigoI.' al '.$codigoF ?>
            <br/>De <?php echo $mesInicial.' A '.$mesFinal .' - '.$annoInforme; ?><br/>&nbsp;</strong></th>
  </tr>
  <tr>
        <td rowspan ="2" align="center"><strong>RUBRO</strong></td>
        <td rowspan ="2" align="center"><strong>DETALLE</strong></td>
        <td rowspan ="2" align="center"><strong>FUENTE</strong></td> 
        <td rowspan ="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
        <td colspan ="4" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
        <td rowspan ="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td colspan ="3" align="center"><strong>DISPONIBILIDADES</strong></td>
        <td colspan ="3" align="center"><strong>REGISTROS</strong></td>
        <td colspan ="3" align="center"><strong>OBLIGACIONES</strong></td>
        <td colspan ="3" align="center"><strong>PAGOS</strong></td>
    </tr>
  <tr>
        <td  align="center"><strong>ADICION</strong></td>
        <td  align="center"><strong>REDUCCION</strong></td>
        <td  align="center"><strong>TRAS.CREDITO</strong></td> 
        <td  align="center"><strong>TRAS.CONT</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
    </tr>
  <?php   
#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro               as codrub, 
                        nombre_rubro            as nomrub,
                        cod_fuente              as codfte,
                        ptto_inicial            as ppti,
                        adicion                 as adi,
                        reduccion               as red,
                        tras_credito            as tcred,
                        tras_cont               as trcont,
                        presupuesto_dfvo        as ppdf,
                        disponibilidades        as disanterior,
                        saldo_disponible        as disactual,
                        disponibilidad_abierta  as disacum, 
                        registros               as reganterior,
                        registros_abiertos      as regactual,
                        registros_otros         as regacum, 
                        total_obligaciones      as oblianterior,
                        reservas                as obliactual,
                        cuentas_x_pagar         as obliacum, 
                        total_pagos             as paganterior,
                        recaudos                as pagactual, 
                        saldos_x_recaudar       as pagosacum 
                        
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

while ($filactas = mysqli_fetch_array($conejc)) 
{

    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['tcred'];
    $p5  = (float) $filactas['trcont'];
    $p6  = (float) $filactas['ppdf'];
    $disan  = (float) $filactas['disanterior'];
    $disac  = (float) $filactas['disactual'];
    $disam  = (float) $filactas['disacum'];
    $regan  = (float) $filactas['reganterior'];
    $regac  = (float) $filactas['regactual'];
    $regam  = (float) $filactas['regacum'];
    $oblan  = (float) $filactas['oblianterior'];
    $oblac  = (float) $filactas['obliactual'];
    $oblam  = (float) $filactas['obliacum'];
    $pagan  = (float) $filactas['paganterior'];
    $pagac  = (float) $filactas['pagactual'];
    $pagam  = (float) $filactas['pagosacum'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && 
            $disan==0 && $disac==0 && $disam==0 && $regan==0 && $regac==0 && $regam==0 && 
            $oblan==0 && $oblac==0 && $oblam==0 && $pagan==0 && $pagac==0 && $pagam==0)
        { } else { ?>
    <tr>
        <td><?php echo $filactas['codrub'];?></td>
        <td><?php echo $filactas['nomrub'];?></td>
        <td align="center"><?php echo $filactas['codfte'];?></td>
        <td align="right" ><?php echo number_format($p1 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p2 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p3 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p4 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p5 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p6 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disan ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disac ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disam ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regan ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regam,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblan,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblam,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagan,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagam,2,'.',',');?></td>
    </tr>
    <?php
    }
}
?>
</table>
<br>
<br>
<br>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
############################### ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $compania = $_SESSION['compania'];
  $res = "SELECT   c.nombre,rd.orden,IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, 
                 tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional 
        FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion trel ON rd.tipo_relacion = trel.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico 
        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
        WHERE LOWER(td.nombre) ='ejecucion presupuestal' 
        AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fecha') 
        AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fecha') 
        AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 #ESTRUCTURA

 if(mysqli_num_rows($res)>0){ 

    while($F = mysqli_fetch_row($res)){
        if($F[1] ==  1){

             ?>

            <tr>
             <th colspan="12" align="center"><strong>
               ______________________________________
               <br>
              <?php echo $F[2]; ?>
              <br>
              <?php echo $F[0]; ?>
              <br>
              <br>
              <br>
              <br>
             </th>
            </tr>
        <?php
          } else{

            ?>
        <tr>
             <th colspan="12" align="center"><strong>
             ______________________________________
               <br>
              <?php echo $F[2]; ?>
              <br>
              <?php echo $F[0]; ?>
              <br>
              <br>
              <br>
              <br>
             </th>
          </tr>
           <?php
        }
     }
 } 

 ?>

<br>
<br>

  <tr>
        <th colspan="4" align="center"><strong>Usuario: <?php echo $usuario; ?></strong></th>
        <th colspan="4" align="center"><strong>Fecha: <?php echo $fechaActual; ?></strong></th>
        <th colspan="4" align="center"><strong>Maquina: <?php echo gethostname(); ?></strong></th>
  </tr>
</table>
</body>
</html>