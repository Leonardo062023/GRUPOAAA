<?php
################ MODIFICACIONES ####################
#06/06/2017     | Anderson Alarcon | cambie update   
############################################

require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar
    $valor      = '"'.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'"';
    $detalle    = $mysqli->real_escape_string(''.$_POST['sltDetalle'].'');
    $pago       = $mysqli->real_escape_string(''.$_POST['sltPago'].'');
    $id         = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"'; 

    /*if($detalle==""){
          $insertSQL = "UPDATE gr_detalle_pago_predial SET valor=$valor,pago=$pago WHERE id_unico = $id";
          echo "NO HAY DETALLE";
          echo "mentiras si hay detalle mire $detalle";
    }else{
        if($pago==""){
             $insertSQL = "UPDATE gr_detalle_pago_predial SET valor=$valor, detallefactura=$detalle WHERE id_unico = $id";
        
             echo "No hay pago";
        }else{
             $insertSQL = "UPDATE gr_detalle_pago_predial SET valor=$valor, detallefactura=$detalle, pago=$pago WHERE id_unico = $id";
        }
    }*/
    
    if($detalle=="")
        $det = "null";
    else
        $det = $detalle;

    if(empty($pago))
        $pag = "null";
    else
        $pag = $pago;

//modificar ne la base de datos
 $insertSQL = "UPDATE gr_detalle_pago_predial SET valor=$valor, detallefactura=$det, pago=$pag WHERE id_unico = $id";
  
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
    window.location='../listar_GR_DETALLE_PAGO_PREDIAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>