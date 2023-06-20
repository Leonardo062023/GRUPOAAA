<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Balance_General.xls");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
ob_start();
session_start();
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$anno           = anno($parmanno);
$mesI           = '01';
if(!empty($_POST['sltmesi'])){
	$mesI           = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
}
$diaI           = '01';
$fechaInicial   = $anno.'-'.$mesI.'-'.$diaI;
$mesF           = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$diaF           = cal_days_in_month($calendario, $mesF, $anno); 
$fechaFinal     = $anno.'-'.$mesF.'-'.$diaF;
$fechaComparar  = $anno.'-'.'01-01';
$codigoI        = $mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF        = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');

$bl             = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);

    
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

$meses  = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$month1 = $meses[(int)$mesI];
$month2 = $meses[(int)$mesF];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BALANCE GENERAL</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="3" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/> BALANCE GENERAL
           <br/><?php echo 'Cuentas De '.$codigoI. ' A '.$codigoF?>
            <br/> <?php echo 'Entre '.$month1.' y '.$month2.' de '.$anno?>
            <br/>&nbsp;</strong>
        </th>
  </tr>
    <tr>
        <td><center><strong>CÓDIGO</strong></center></td>
        <td><center><strong>NOMBRE</strong></center></td>
        <td><center><strong>SALDO FINAL</strong></center></td>
    
    </tr>
<?php 

#Consulta Cuentas

$hr ="";
if(!empty($_GET['digitos'])){
    $dig = $_GET['digitos'];
    $hr .=" WHERE LENGTH(numero_cuenta)<=$dig  ";
}
$sql3 = "SELECT DISTINCT 
    numero_cuenta   as numcuen, 
    nombre          as cnom,
    nuevo_saldo     as nsal
from temporal_balance$compania 
$hr  ORDER BY numero_cuenta ASC";
$ccuentas = $mysqli->query($sql3);

$nsald = 0;
while ($filactas = mysqli_fetch_array($ccuentas)) 
{
       # $codd = $codd + 1;
    
        $nsald  = (float)($filactas['nsal']);
        
        if ($nsald == 0 )
        { } else { ?>
        <tr>
            <td><?php echo $filactas['numcuen']; ?></td>
            <td><?php echo ucwords(mb_strtolower($filactas['cnom']));?></td>
            <td><?php echo number_format($nsald,2,'.',',');?></td>
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
             <th colspan="3" align="center"><strong>
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
             <th colspan="3" align="center"><strong>
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
        <th colspan="1" align="center"><strong>Usuario: <?php echo $usuario; ?></strong></th>
        <th colspan="1" align="center"><strong>Fecha: <?php echo $fechaActual; ?></strong></th>
        <th colspan="1" align="center"><strong>Maquina: <?php echo gethostname(); ?></strong></th>
  </tr>
</table>

</body>
</html>     