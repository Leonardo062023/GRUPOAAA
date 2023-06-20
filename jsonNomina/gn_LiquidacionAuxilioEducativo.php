<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require '../jsonNomina/funcionesNomina.php';
@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];

$empleador  = $_REQUEST['e'];  
$periodo    = $_REQUEST['p'];  

$rowp        = $con->Listar("SELECT fechainicio, fechafin, dias_nomina FROM  gn_periodo WHERE id_unico = $periodo");
$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        
$diasPeriodo = $rowp[0][2];        

if($empleador ==2){
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) LIMIT 1 ) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), et.tipo  
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
        LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
        WHERE e.id_unico != 2 
        ORDER BY e.id_unico"); 
} else {
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico)) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), et.tipo  
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
        LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
        WHERE e.id_unico = $empleador
        ORDER BY e.id_unico"); 
}
$rta =0;
for ($i=0; $i < count($rowe); $i++) { 
    $empleado = $rowe[$i][0];
    $id_empleado = $rowe[$i][0];

    #* Eliminar COnceptos 
    $ld = "DELETE  n.* FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.periodo = $periodo AND n.empleado = $empleado AND c.codigo in ('097','140', '144') ";
    $resultado1 = $mysqli->query($ld);



    #Devengos
    $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 1 and c.unidadmedida = 1");

    if(empty($dv[0][0])){
        $tdv = 0;
    } else {
        $tdv = $dv[0][0];   
    }
    
    if($tdv !=0){
        $id_conceptotd = id_concepto('097');
        guardarNovedad($tdv , $id_empleado, $periodo, $id_conceptotd);
    }

    #Descuentos
    $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 2 and c.unidadmedida = 1");
    if(empty($dv[0][0])){
        $tds = 0;
    } else {
        $tds = $dv[0][0];
    }
    if($tds !=0){
        $id_conceptods = id_concepto('140');
        guardarNovedad($tds , $empleado, $periodo, $id_conceptods);
    }

    #Neto
    $np = $tdv -$tds;
    if($np !=0){
        $id_conceptonp = id_concepto('144');
        $ge = guardarNovedad($np , $empleado, $periodo, $id_conceptonp);
        
        if(empty($ge)){
            $rta +=1;
        }   
    }
}
    
//echo $rta;
?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>

<div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <label id="mensaje" name="mensaje"></label>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<script>  
$(document).ready(function() {
    let response = 0;
    if(response==0){
        $("#mensaje").html('Información Guardada Correctamente');  
        $("#mdlInfo").modal('show'); 
        $("#ver1").click(function(){
            document.location ='../informes_nomina/generar_INF_SABANA_LIQUIDACIONAE.php?t=1&e=<?=$empleador?>&p=<?=$periodo?>';
        })
        
    } else {
        $("#mensaje").html('No Se Ha Podido Guardar La Información');  
        $("#mdlInfo").modal('show'); 
    }
})
</script>