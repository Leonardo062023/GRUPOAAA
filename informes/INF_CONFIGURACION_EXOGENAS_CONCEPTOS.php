<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#11/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
header("Content-Disposition: attachment; filename=Informe_Configuracion_Conceptos_Nomina_Exogenas.xls");
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
#**********Recepción Variables ****************#
$formato    = $_REQUEST['formato'];
$fm = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE id_unico = $formato");
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT    ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo    = $rowC[0][6];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>Informe Configuración Conceptos Nómina-Exogenas</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="4" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>Configuración Conceptos Nómina-Exogenas 
                    <br/>Formato: <?PHP echo $fm[0][1].' - '.$fm[0][2]; ?> 
                    <br/>&nbsp;</strong>
                </th>
                <?php 
                echo '<tr>';
                echo '<th class="cabeza cursor cb">Código Concepto Nómina</th>';
                echo '<th class="cabeza cursor cb">Concepto Nómina</th>';
                echo '<th class="cabeza cursor cb">Código Concepto Exogena </th>';
                echo '<th class="cabeza cursor cb">Concepto Exogena </th>';
                echo '</tr>';
                $sql  = $con->Listar("SELECT c.codigo,c.descripcion,cn.codigo, cn.nombre 
                                      FROM gn_concepto c
                                      LEFT JOIN gf_configuracion_exogenas cf ON cf.cuenta=c.id_unico  
                                      LEFT JOIN gf_concepto_exogenas cn ON cf.concepto_exogenas = cn.id_unico
                                      WHERE c.id_unico BETWEEN 1 AND 722");
                for ($s = 0; $s < count($sql); $s++) {
                    if ($sql[$s][2]==null) {
                       $codExogena='';
                    }else{
                        $codExogena=$sql[$s][2];
                    }
                    if ($sql[$s][3]==null) {
                        $conExogena='';
                     }else{
                         $conExogena=$sql[$s][3];
                     }
                     $codNomina=$sql[$s][0];

                    echo '<tr>';
                    echo '<td>'."'$codNomina'".'</td>';
                    echo '<td>'.utf8_decode($sql[$s][1]).'</td>';
                    echo '<td>'."'$codExogena'".'</td>';
                    echo '<td>'.utf8_decode($conExogena).'</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </body>
    </html>