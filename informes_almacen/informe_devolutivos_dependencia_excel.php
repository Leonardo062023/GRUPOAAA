<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeDevolutivosPorDependencia.xls");

session_start();
require_once ("../Conexion/conexion.php");
require_once ("../Conexion/ConexionPDO.php");
$con = new ConexionPDO();
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
    if ($tipoProducto==11) {
        $tipoProducto=" IN (1,2,3,4,5)";
    }else{
        $tipoProducto = "=".$_POST['sltTipo'];
    }
    
    $depIni       = $_POST['sltDepInicial'];
    $depFin       = $_POST['sltDepFinal'];

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
    
    $totalt = 0;

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "<head>";
    $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "<title>Informe de Propiedad Planta y Equipo de Almacen por Dependencia</title>";
    $html .= "</head>";
    $html .= "<body>";
    $html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $html .= "<thead>";
    $html .= "<th colspan=\"13\" align=\"center\">".$nombreCompania."<br/>NIT: $nitcompania&nbsp;<br/>
            INVENTARIO GENERAL DE  PROPIEDAD PLANTA Y EQUIPO POR DEPENDENCIAS<br/>
            ENTRE FECHAS : ".$fechaInicial." Y ".$fechaFinal."<br/>
            ENTRE PRODUCTOS : ".$productoI." Y ".$productoF."</th>";

    $html .= "<tr>";
    $html .= "<th rowspan=\"2\">CODIGO</th>";
    $html .= "<th rowspan=\"2\">NOMBRE</th>";
    $html .= "<th rowspan=\"2\">ESPECIFICACIONES</th>";
    $html .= "<th rowspan=\"2\">SERIE</th>";
    $html .= "<th rowspan=\"2\">VALOR</th>";
    $html .= "<th colspan=\"3\">ENTRADA</th>";
    $html .= "<th colspan=\"3\">ÚLTIMO MOVIMIENTO</th>";
    $html .= "<th rowspan=\"2\">FECHA<br/>ADQ.</th>";
    $html .= "<th rowspan=\"2\">RESPONSABLE</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>MOV</th>";
    $html .= "<th>NUMERO</th>";
    $html .= "<th>FECHA</th>";
    $html .= "<th>MOV</th>";
    $html .= "<th>NUMERO</th>";
    $html .= "<th>FECHA</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    $sql_dep = "SELECT d.id_unico, d.sigla, UPPER(d.nombre), CONCAT_WS(' - ',UPPER(dp.sigla) , UPPER(dp.nombre)) 
    FROM gf_dependencia d
    LEFT JOIN gf_dependencia dp ON d.predecesor = dp.id_unico 
     WHERE (d.id_unico BETWEEN $depIni AND $depFin) AND d.compania = $compania";
    $res_dep = $mysqli->query($sql_dep);
    $totaltotal = 0;
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
            $html .= "<tr>";
            $html .= "<td colspan=\"2\" align=\"right\"><strong>DEPENDENCIA $row_dep[1]</strong></td>";
            $html .= "<td colspan=\"5\" align=\"left\"><strong>$row_dep[2]</strong></td>";
            $html .= "<td colspan=\"6\" align=\"left\"><strong>SEDE: $row_dep[3]</strong></td>";
            $html .= "</tr>";
            
            while($row_e = mysqli_fetch_row($res_e)){
                $str   = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                $res   = $mysqli->query($str);
                $rw_   = mysqli_fetch_row($res);
                #Entrada
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
                        AND        pln.tipoinventario  $tipoProducto");
                $descripcion = "DESCRIPCIÓN: ".$vp[0][2].' ';
                for ($j = 0; $j < count($vp); $j++) {
                    $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                }

                $html .= "<tr>";
                $html .= "<td align=\"center\">$row_s[1]</td>";
                $html .= "<td align=\"left\">".utf8_decode($row_s[2])."</td>";
                $html .= "<td align=\"left\">".$descripcion."</td>";
                $html .= "<td align=\"right\">".$rw_[0]."</td>";
                $html .= "<td align=\"right\">".number_format($row_s[4], 2, '.', ',')."</td>";                
                $html .= "<td align=\"center\">$row_s[6]</td>";
                $html .= "<td align=\"right\">$row_s[7]</td>";
                $html .= "<td align=\"center\">$row_s[8]</td>";

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

                $html .= "<td align=\"center\">".$rowum[0][0]."</td>";
                $html .= "<td align=\"right\">".$rowum[0][1]."</td>";
                $html .= "<td align=\"center\">".$rowum[0][2]."</td>";

                $html .= "<td align=\"center\">$fecha_a</td>";
                $html .= "<td align=\"left\">".$rowum[0][3]."</td>";
                $html .= "</tr>";
                $totald += $row_s[4];
            }
            $html .= "<tr>";
            $html .= '<td colspan="4"><strong><i>Total: '.$row_dep[1].' - '.$row_dep[2] .'</i></strong></td>';
            $html .='<td align="right"><strong><i>'.number_format($totald, 2, '.', ',').'</i></strong></td>';
            $html .= '<td colspan="8"></td>';
            $html .= "</tr>";
            $totaltotal +=$totald;

        }
    }
    $html .= "<tr>";
    $html .= '<td colspan="4"><strong><br/>TOTALES<br/>&nbsp;</i></strong></td>';
    $html .='<td align="right"><strong><i>'.number_format($totaltotal, 2, '.', ',').'</i></strong></td>';
    $html .= '<td colspan="8"></td>';
    $html .= "</tr>";

    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</body>";
    $html .= "</html>";

    echo $html;
}?>