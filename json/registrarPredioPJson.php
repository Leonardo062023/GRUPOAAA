<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once '../Conexion/conexion.php';
session_start();

#//$nombre        = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
#//$aniocreacion  = '"'.$mysqli->real_escape_string(''.$_POST['txtAnio'].'').'"';
#//$codigo        = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigo'].'').'"';

$participacion = '"'.$mysqli->real_escape_string(''.$_POST['txtParticipacion'].'').'"';
$principal     = '"'.$mysqli->real_escape_string(''.$_POST['sltPrincipal'].'').'"';

#//$estado        = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
#//$estrato       = '"'.$mysqli->real_escape_string(''.$_POST['sltEstrato'].'').'"';
#//$predio        = '"'.$mysqli->real_escape_string(''.$_POST['sltPredioA'].'').'"';

$tercero       = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';

if($nombre=="")
    $nom = "null";
else
    $nom = $_POST['txtNombre'];

if($aniocreacion=="")
    $anio = "null";
else
    $anio = $_POST['txtAnio'];

if($codigo=="")
    $cod = "null";
else
    $cod = $_POST['txtCodigo'];

if($estado=="")
    $esta = "null";
else
    $esta = $_POST['sltEstado'];

if($estrato=="")
    $estr = "null";
else
    $estr = $_POST['sltEstrato'];

if($predio=="")
    $pred = "null";
else
    $pred = $_POST['sltPredioA'];

#$date=date_create($fecha);
#$fec = date_format($date,"Y/m/d");

 echo $sql = "INSERT INTO gp_predio1(nombre,aniocreacion,codigoigac,participacion,principal,estado,estrato,predioaso,tercero)
                    VALUES   ($nom,$anio,$cod,$participacion,$principal,$esta,$estr,$pred,$tercero)";

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
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GR_PREDIO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>