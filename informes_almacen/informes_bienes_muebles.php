<?php

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeBienesMuebles.xls");

session_start();
require_once ("../Conexion/conexion.php");
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) &&
   !empty($_POST['sltProductoFinal'])){

    function convertirFecha($fecha){
        $fecha = explode("/", $fecha);
        return $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }

    $usuario = $_SESSION['usuario'];
    $compa   = $compania = $_SESSION['compania'];

    $fechaInicial = $_POST['txtFechaInicial'];
    $fechaFinal   = $_POST['txtFechaFinal'];

    $fechaI       = convertirFecha($fechaInicial);
    $fechaF       = convertirFecha($fechaFinal);

    $productoIni  = $_POST['sltProductoInicial'];
    $productoFin  = $_POST['sltProductoFinal'];


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
    $html .= "<tr>";
    $html .= "<th colspan=\"12\" align=\"center\">".$nombreCompania."</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th colspan=\"12\" align=\"center\">NIT: $nitcompania</th>";
    $html .= "<tr>";
    $html .= "<th colspan=\"12\" align=\"center\">BIENES MUEBLES</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th colspan=\"3\" rowspan=\"3\">DESCRIPCIÓN</th>";
    $html .= "<th colspan=\"3\"  rowspan=\"3\">FECHA ADQ</th>";
    $html .= "<th colspan=\"3\"  rowspan=\"3\">DEPENDENCIA</th>";
    $html .= "<th colspan=\"3\" rowspan=\"3\">VALOR</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    $html .= "<tr>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "</tr>";


    $totaltotal = 0;

        $totald = 0;
        $sql_e = "SELECT   DISTINCT pro.id_unico,
                            pln.codi,
                            pln.nombre,
                            pro.descripcion,
                            pro.valor,
                            tpm.clase,
                            tpm.sigla,
                            mov.numero,
                            mov.fecha,
                            IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                               ter.razonsocial,
                               CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS NOMBRE,
                            CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion),
                            pro.fecha_adquisicion,
                            dep.nombre
                  FROM      gf_movimiento_producto     mpr
                  LEFT JOIN gf_producto                pro ON mpr.producto           = pro.id_unico
                  LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento  = dtm.id_unico
                  LEFT JOIN gf_movimiento              mov ON dtm.movimiento         = mov.id_unico
                  LEFT JOIN gf_tipo_movimiento         tpm ON mov.tipomovimiento     = tpm.id_unico
                  LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento     = pln.id_unico
                  LEFT JOIN gf_tercero                 ter ON mov.tercero            = ter.id_unico
                  LEFT JOIN gf_tipo_identificacion     tip ON ter.tipoidentificacion = tip.id_unico
                  LEFT JOIN gf_dependencia             dep ON mov.dependencia=dep.id_unico
                  WHERE    (mov.fecha    BETWEEN '$fechaI'    AND '$fechaF')
                  AND       (pro.id_unico BETWEEN $productoIni AND $productoFin)
                  AND       (tpm.clase          = 3)
                  AND       (pln.tipoinventario = 2)
                  AND       (pln.compania       = $compania)
                  AND       (mov.compania       = $compania)
                  AND       (pro.baja IS NULL OR pro.baja =0)";
        $res_e = $mysqli->query($sql_e);
        if($res_e->num_rows > 0){
           
            while($row_e = mysqli_fetch_row($res_e)){
                $str   = "SELECT DISTINCT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                $res   = $mysqli->query($str);
                $rw_   = mysqli_fetch_row($res);

                $sql_s = "SELECT    tpm.sigla,
                                    mov.numero,
                                    DATE_FORMAT(mov.fecha,'%d/%m/%Y'),
                                    IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                    OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS NOMBRE,
                                    CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion)
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
                          AND       (pln.tipoinventario = 2)
                          AND       (pln.compania       = $compania)
                          AND       (mov.compania       = $compania)";
                $rs_s  = $mysqli->query($sql_s);
                $row_s = mysqli_fetch_row($rs_s);
                $desc  = str_replace("\n",' ',$row_e[3]);

                $mov_s   = "";
                $num_s   = "";
                $fecha_s = "";

                if(mysqli_num_rows($rs_s) > 0){
                    $mov_s   = $row_s[0];
                    $num_s   = $row_s[1];
                    $fecha_s = $row_s[2];
                }

                if(!empty($row_e[11])){
                    $fecha_a = $row_e[11];
                }else{
                    $fecha_a = $row_e[9];
                }


                $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $fecha_div = explode("-",$fecha_a);
                $mesFF = $months[(int) $fecha_div[1]];
                $fechaFinaal = $fecha_div[2].'-'.$mesFF.'-'.$fecha_div[0];


                $html .= "<tr>";
                $html .= "<td colspan=\"3\" align=\"left\">".ucwords(utf8_decode($row_e[2].','.$desc.','.'           Placa Sistema: '.$rw_[0]))."</td>";
                $html .= "<td colspan=\"3\" align=\"center\"> $fechaFinaal</td>";
                $html .= "<td colspan=\"3\" align=\"left\">".$row_e[12]."</td>";
                $html .= "<td colspan=\"3\" align=\"right\">".number_format($row_e[4], 2, '.', ',')."</td>";
                $html .= "</tr>";
                $totald += $row_e[4];
            }
        
            $totaltotal +=$totald;

        }

    $html .= "<tr>";
    $html .= '<td  align="right" colspan="9"><strong><br/>TOTAL<br/>&nbsp;</i></strong></td>';
    $html .='<td colspan="3" align="right"><strong><i>'.number_format($totald, 2, '.', ',').'</i></strong></td>';
    $html .= "</tr>";
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</body>";
    $html .= "</html>";

    echo $html;
}?>