<?php
#28/09/2021---Se crea Json modificar -ELkin O.
  require_once('../Conexion/conexion.php');
session_start();
#$id = (($_GET["id"]));
//obtiene los datos que se van a modificar
$documento = $_FILES['file'];
$nombre = $_FILES['file']['name'];
$directorio ='../documentos/guias/';
$nombre =$nombre;
$ruta = 'documentos/guias/'.$nombre;
    if (!file_exists('../documentos/guias/')) {
            mkdir('../documentos/guias/', 0777, true);
       }

$empleado               = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$tipodocumento          = '"'.$mysqli->real_escape_string(''.$_POST['sltDocumento'].'').'"';
if($mysqli->real_escape_string(''.$_POST['sltFechaAc'].'')==""){
    $fechactualizacion='null';
  }else{
    $fec1      = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaAc'].'').'"';
    $fecha1    = trim($fec1,'"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];  
    $fechactualizacion = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
   
  }
  $numerofolio     = '"'.$mysqli->real_escape_string(''.$_POST['txtFolio'].'').'"';
  $id         = '"'.$mysqli->real_escape_string(''.$_POST['Ide'].'').'"';

   
//modificar en la base de datos
 $insertSQL = "UPDATE gn_empleado_documento SET empleado=$empleado, tipodocumento=$tipodocumento, fechaactualizacion=$fechactualizacion, numerofolio=$numerofolio, ruta='$ruta' WHERE id_unico = $id ";
 $resultado = $mysqli->query($insertSQL);
 if ($resultado==true || $resultado=='1'){
    // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
     move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
 }

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
    window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>