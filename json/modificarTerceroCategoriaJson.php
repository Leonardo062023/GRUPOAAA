<?php
#17/03/2017 --- Nestor B --- se modificó la ruta del botón aceptar para que regrese a donde fue llamado

  require_once('../Conexion/conexion.php');
  require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();

//obtiene los datos que se van a modificar
$id             = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$empleado           = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$categoria          = '"'.$mysqli->real_escape_string(''.$_POST['sltCategoria'].'').'"';
#Validación de campo nulo o lleno Fecha Modificación
if($mysqli->real_escape_string(''.$_POST['sltFechaM'].'')=="")
   $fechamodificacion = "null";
else
   $fechamodificacion  = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaM'].'').'"';   
#Validación de campo nulo o lleno Fecha Cancelación
if($mysqli->real_escape_string(''.$_POST['sltFechaM'].'')=="")
   $fechacancelacion = "null";
else
   $fechacancelacion = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaC'].'').'"';
#Validación de campo nulo o lleno Estado
if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')=="")
   $estado = "null";
else
   $estado = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';

//modificar ne la base de datos
$elm = modificarTerceroCat($_POST['id'],$_POST['sltFechaM'],$_POST['sltFechaC'],$_POST['sltEmpleado'],$_POST['sltCategoria'],$_POST['sltEstado']);
  $insertSQL = "UPDATE gn_tercero_categoria SET empleado = $empleado, fechamodificacion = $fechamodificacion, 
          fechacancelacion = $fechacancelacion, categoria = $categoria, estado = $estado WHERE id_unico = $id";
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
    //window.location='../listar_GN_TERCERO_CATEGORIA.php';
    window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    //window.location='../listar_GN_TERCERO_CATEGORIA.php';
    window.history.go(-1);
  });
</script>
<?php } ?>