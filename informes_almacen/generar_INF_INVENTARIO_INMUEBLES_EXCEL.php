<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Existencias_Inventario.xls");
require_once("../Conexion/conexion.php");
require_once ("../Conexion/ConexionPDO.php");
@session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
$con = new ConexionPDO();
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
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
    <head>
        <title>Reporte de Existencias de Inventario</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th colspan="4" bgcolor="skyblue">
                    <?php echo "$nombreCompania<br/>NIT : $nitcompania<br>INVENTARIO GENERAL DE INMUEBLES <br/> ENTRE FECHAS: $fechaInicial Y  $fechaFinal <br/>  ENTRE PRODUCTOS: $productoI Y  $productoF"; ?>
                </th>
            </tr>
            <tr>
                <th align="center">CÓDIGO</th>
                <th align="center">NOMBRE</th>
                <th align="center">ESPECIFICACIONES</th>
                <th align="center">VALOR</th>
            </tr>
        </thead>
        <tbody>


<?php
 $valorto=0;
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
                   
                  ?>
                 <tr>
                 <td style='text-align: left;'>  <?php echo $row_s[1]?></td>
                 <td style='text-align: left;'><?php echo utf8_decode($row_s[2])?></td>
                 <td style='text-align: left;'><?php echo utf8_decode($descripcion)?></td>
                 <td style='text-align: right;'><?php echo number_format($row_s[4], 2);
                
                 ?></td>
                </tr>
                
                 <?php 
             $valorto+=$row_s[4];
            }
          
            
        }
      
?>
       </tbody>
               <tfoot>
                   <tr>
                       <th colspan="3" style="text-align: center;">TOTAL GENERAL:</th>
                       <th align="right"><?php echo number_format($valorto,2,'.',',')?></th>
                   </tr>
               </tfoot>
           </table>
       </body>
 </html>



        
      