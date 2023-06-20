<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();

$concepto  = $_POST['sltConcepto'];
$anioSelec  = $_POST['sltAnnio'];
$conceptoS = "SELECT codigo, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE codigo=$concepto";
$ConS = $mysqli->query($conceptoS);
$C = mysqli_fetch_row($ConS);

$sqlAnno="SELECT anno FROM gf_parametrizacion_anno
WHERE id_unico=$anioSelec";
$resultAnno = $mysqli->query($sqlAnno);
$rowAnno = $resultAnno->fetch_assoc();
$annoNum = $rowAnno['anno'];
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Concepto_Empleados.xls");
    require_once("../Conexion/conexion.php");
    @session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <html>
        <head>
            <title>Informe Conceptos</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="1" bgcolor="skyblue">
                        <?php echo utf8_decode("Concepto: ".$C[1]); ?>
                    </th>
                </tr>
                <tr>
                    <th align="center"> <?php echo utf8_decode("Empleado"); ?></th>
                    <th align="center"> <?php echo utf8_decode("CONCEPTO: ".$C[1]); ?></th>
                    <th align="center"> <?php echo utf8_decode("Denominacion"); ?></th>
                    <th align="center"> <?php echo utf8_decode("Grado"); ?></th>
                    <th align="center"> <?php echo utf8_decode("Cargo"); ?></th>
                    <th align="center"> <?php echo utf8_decode("Codigo Cargo"); ?></th>

                </tr>
            </thead>
            <tbody>
            <?php

            $sqlIE="SELECT  CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  ) as Nombre,
                    SUM(n.valor) as valor, 
                     ca.codigointerno as Denominacion, 
                     ca.nombre as Grado, 
                    car.nombre as cargo,
                    car.codigo as codigocar
                      FROM gn_novedad n 
                     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                     LEFT JOIN gn_concepto con ON n.concepto = con.id_unico 
                     LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                     LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                     LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                     LEFT JOIN gf_cargo_tercero ct ON ct.tercero=e.tercero
                     LEFT JOIN gf_cargo car ON car.id_unico=ct.cargo
                     left join gf_tercero tr on tr.id_unico= e.tercero
                     where con.codigo=$concepto 
                     AND P.parametrizacionanno=$anioSelec
                     GROUP BY e.id_unico";
 $resultIE=$mysqli->query($sqlIE);
 while ($rowIE=mysqli_fetch_row($resultIE)) {
 
            ?>
<tr>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[0]); ?></td>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[1]); ?></td><br>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[2]); ?></td><br>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[3]); ?></td><br>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[4]); ?></td><br>
        <td style='text-align: left;'><?php echo utf8_decode($rowIE[5]); ?></td><br>

          
            </tr>
            <?php
}
            ?>
            </tbody>
        </table>
    </body>
    </html>




