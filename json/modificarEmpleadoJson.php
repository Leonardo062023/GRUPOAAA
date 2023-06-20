<?php
#03/08/2017 --- Nestor B --- se agrego el campo de salario integral
  require_once('../Conexion/conexion.php');
  require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();

//obtiene los datos que se van a modificar

$tercero            = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
$codigointerno      = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'].'').'"';
$estado             =       $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
$cesantias          =       $mysqli->real_escape_string(''.$_POST['sltCesantias'].'');
$mediopago          =       $mysqli->real_escape_string(''.$_POST['sltMedioP'].'');
$unidadejecutora    =       $mysqli->real_escape_string(''.$_POST['sltUnidadE'].'');
$grupogestion       =       $mysqli->real_escape_string(''.$_POST['sltGrupoG'].'');
$id                 = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$salInt             =       $mysqli->real_escape_string(''.$_POST['salaIn'].'');
$riesgo             =       $mysqli->real_escape_string(''.$_POST['sltRiesgo'].'');
$contrato           =       $mysqli->real_escape_string(''.$_POST['sltContrato'].'');

#$retro              =       $mysqli->real_escape_string(''.$_POST['Retro'].'');


if(empty($codigointerno))
    $codigo = "null";
else
    $codigo = $codigointerno;

if($estado=="")
    $est = "null";
else
    $est = $estado;

if($cesantias=="")
    $ces = "null";
else
    $ces = $cesantias;

if($mediopago=="")
    $medio = "null";
else
    $medio = $mediopago;

if($unidadejecutora=="")
    $unidad = "null";
else
    $unidad = $unidadejecutora;

if($grupogestion=="")
    $grupo = "null";
else
    $grupo = $grupogestion;

  if($grupogestion=="")
    $grupo = "null";
else
    $grupo = $grupogestion;
    
if($contrato=="")
    $contrato = "null";
else
    $contrato = $contrato;
//modificar ne la base de datos
  #$insertSQL = "UPDATE gn_empleado SET tercero = $tercero, codigointerno = $codigointerno, estado = $estado ,cesantias = $cesantias ,mediopago = $mediopago ,unidadejecutora = $unidadejecutora, grupogestion = $grupogestion WHERE id_unico = $id";
  $elm = modificarEmpleado($_POST['id'],$_POST['sltTercero'],$_POST['txtCodigoI'],$_POST['sltEstado'],$_POST['sltCesantias'],$_POST['sltMedioP'],$_POST['sltUnidadE'],$_POST['sltGrupoG'],$_POST['salaIn'],$_POST['sltRiesgo'],$_POST['sltContrato']);
  
  $insertSQL = "UPDATE gn_empleado SET tercero = $tercero, codigointerno = $codigo, "
          . "estado = $est, cesantias = $ces,mediopago = $medio,unidadejecutora = $unidad, grupogestion = $grupo, salInt = $salInt, tipo_riesgo = $riesgo,equivalente_NE = $contrato WHERE id_unico = $id";
  $resultado = $mysqli->query($insertSQL);
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
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GN_EMPLEADO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>