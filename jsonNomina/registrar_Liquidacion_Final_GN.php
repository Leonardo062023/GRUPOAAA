<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
session_start();
$compania = $_SESSION['compania'];
$anno = $_SESSION['anno'];

$empleado = $_REQUEST['sltEmpleado'];
$periodo = $_REQUEST['sltPeriodo'];
$hoy = date('Y-m-d');
#Eliminar Conceptos 
  $sql_elm      ="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto IN (74,98,102)";
  $resultado    = $mysqli->query($sql_elm);

  #Buscar Devengos 
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
  
  #Registrar Devengos
  $sql= "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($tdv,'$hoy',$empleado,$periodo,74,4)";  
  $resultado = $mysqli->query($sql);


  #Buscar Descuentos 
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
  
  
  #Registrar Descuentos
  $sql= "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($tds,'$hoy',$empleado,$periodo,98,4)";  
  $resultado = $mysqli->query($sql);

  $np = $tdv -$tds;

  # Registrar Neto
  $sql= "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($np,'$hoy',$empleado,$periodo,102,4)";  
  $resultado = $mysqli->query($sql);

  
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
            document.location ='../informes_nomina/gn_liquidacion_final.php?e=<?=$empleado?>&p=<?=$periodo?>';
        })
        
    } else {
        $("#mensaje").html('No Se Ha Podido Guardar La Información');  
        $("#mdlInfo").modal('show'); 
    }
})
</script>