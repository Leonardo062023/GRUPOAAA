<?php

@session_start();
require ('../json/registrarMovimientoAlmacenJson.php');
require ('../Conexion/conexion.php');
if(!empty($_POST['action'])){
    $action =  $_POST['action'];
}elseif (!empty($_GET['action'])) {
    $action =  $_GET['action'];
}
if($action == 'insert'){
    $estadoM       = '"2"';
    #Validación de campos no obligatorios
    if(!empty($_POST['txtObservacion'])){
        $observaciones = '"'.$_POST['txtObservacion'].'"';
    }else{
        $observaciones = 'NULL';
    }
    if(!empty($_POST['txtDescripcion'])){
        $descripcion   = '"'.$_POST['txtDescripcion'].'"';
    }else{
        $descripcion = 'NULL';
    }

    $proyecto      = '"'.$_POST['sltProyecto'].'"';
    $centrocosto   = '"'.$_POST['sltCentroCosto'].'"';
    $dependencia   = '"'.$_POST['sltDependencia'].'"';
    $responsable   = '"'.$_POST['sltResponsable'].'"';
    #Conversión de fecha
    $fechaT = ''.$_POST['txtFecha'].'';
    $valorF = explode("/",$fechaT);
    $fechaC =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
    $paramA = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
    $numeroC = ''.$_POST['txtNumeroMovimiento'].'';
    $tipoM  = ''.$_POST['sltTipoMovimiento'].'';
    $id_asoc = $_POST['sltNumeroA'];
    if(!empty($id_asoc)){
        $values = explode(",",movimiento::get_values_aso_for_exit($id_asoc));
        $plazoE = $values[0]; $rubropptal = $values[1]; $lugarE = $values[2]; $unidadE = $values[3]; $porcIva = $values[4]; $tercero = $values[5];
    }else{
        $plazoE = $rubropptal = $lugarE = $unidadE = $porcIva = 'NULL';
        $tercero = $_SESSION['usuario_tercero'];
    }
    if(!empty($_POST['sltFuente'])){
        $fuente = $_POST['sltFuente'];
    }else{
        $fuente = 'NULL';
    }
    $result = movimiento::save_data($numeroC, $fechaC, $descripcion, empty($plazoE)?"NULL":$plazoE, $observaciones, $tipoM, $paramA, $responsable, $_SESSION['usuario_tercero'], $dependencia, $centrocosto, empty($rubropptal)?"NULL":$rubropptal, $proyecto, empty($lugarE)?"NULL":$lugarE, empty($unidadE)?"NULL":$unidadE, $estadoM, $porcIva, $compania, "NULL","NULL", $fuente);
    if($result == true){
        $movimiento = movimiento::get_last_id($tipoM, $numeroC);
        $save_auditoria = movimiento::agregarMovimiento($movimiento);
    }
    echo "<html>\n";
    echo "<head>\n";
    echo "\t<meta charset=\"utf-8\">\n";
    echo "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
    echo "\t<script src=\"../js/md5.pack.js\"></script>\n";
    echo "\t<script src=\"../js/jquery.min.js\"></script>\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
    echo "\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "</body>\n";
    echo "</html>\n";
    echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>Información guardada correctamente.</p>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>No se ha podido guardar la información.</p>\n";
    echo "\t\t\t\n</div>";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
    echo "<script src=\"../js/bootstrap.js\"></script>";
    if($result == true){
        $movimiento = movimiento::get_last_id($tipoM, $numeroC);
        if(!empty($id_asoc)){
            $details = movimiento::get_detail_mov($id_asoc);
            for ($a = 0;$a < count($details); $a++) {
                $values  = movimiento::get_values_detail($details[$a]);
                $dataAso = movimiento::obtnerDataAsociado($details[$a]);
                $xc = 0;
                foreach ($dataAso as $rc) {
                    $xc += $rc[0];
                }
                $xxx = $values[1] - $xc;
                if($xxx > 0){
                   $asociadoM= movimiento::save_detail_mov($values[0], $xxx, $values[2], $values[3], $movimiento, $details[$a]);
                    if ($asociadoM==true) {
                        $sqlId = "SELECT MAX(id_unico) FROM gf_detalle_movimiento";
                        $resultId = $mysqli->query($sqlId);
                        $filaId = mysqli_fetch_row($resultId);
                        $id_mov=$filaId[0];
                        $detalle_auditoria = movimiento::agregarDetalleMovimiento($id_mov);
                    }                  
                }
            }
        }
        echo "\n<script type=\"text/javascript\">";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../registrar_GR_SALIDA_ALMACEN.php?movimiento=".md5($movimiento)."';\n";
        echo "\t});";
        echo "</script>";
    }else{
        echo "<script type=\"text/javascript\">";
        echo "\t$(\"#myModal2\").modal('show');\n";
        echo "\t$(\"#ver2\").click(function(){\n";
        echo "\t\t$(\"#myModal2\").modal('hide');\n";
        echo "\t\twindow.history.go(-1)";
        echo "\t});";
        echo "</script>";
    }
}else if($action == 'modify'){
    $id_unico = $_POST['id'];
    $fecha = explode("/",$_POST['txtFecha']);
    $fecha = "'$fecha[2]-$fecha[1]-$fecha[0]'";
    $fecha1=explode("'",$fecha);
    $fechaM="$fecha1[1]";
    $descripcion = '"'.$_POST['txtDescripcion'].'"';
    $observaciones = '"'.$_POST['txtObservacion'].'"';
    $iva = '"'.$_POST['txtIva'].'"';
    $responsable   = '"'.$_POST['sltResponsable'].'"';
    $tercero   = '"'.$_POST['sltResponsable'].'"';
    if(empty($_POST['fuente'])){
        $fuente = 'NULL';
    } else {
        $fuente = $_POST['fuente'];
    }
    if(!empty($_POST['sltProyecto'])){
        $proyecto = $_POST['sltProyecto'];
    }else{
        $proyecto = 'NULL';
    }
    $mod= movimiento::modificarMovEn($id_unico, $fechaM,$_POST['txtObservacion'], 
    $_POST['txtDescripcion'], $_POST['txtIva'], $_POST['sltResponsable'], $_POST['sltResponsable'],$_POST['fuente'], $_POST['sltProyecto']);
    $result = movimiento::modify_data($id_unico, $fecha, $observaciones, 
            $descripcion, $iva, $responsable, $tercero, $fuente, $proyecto);
            
    echo json_encode($result);
}