<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('../Conexion/conexion.php');
session_start();

$tipoI      =  $_POST['tipoI'];
$numId      =  $_POST['numId'];
$primerN    =  $_POST['primerN'];
$primerA    =  $_POST['primerA'];
$id         =  $_POST['id'];

if(empty($_POST['segundoN'])){
    $segundoN = 'NULL';
} else {
    $segundoN   =  $_POST['segundoN'];
}
if(empty($_POST['segundoA'])){
    $segundoA = 'NULL';
} else {
    $segundoA   =  $_POST['segundoA'];
}
if(empty($_POST['correo'])){
    $email  = 'NULL';
} else {
    $email   =  $_POST['correo'];
}
if(empty($_POST['zona'])){
    $zona  = 'NULL';
} else {
    $zona   =  $_POST['zona'];
}
if(empty($_POST['tarjetaP'])){
    $tarjeta  = 'NULL';
} else {
    $tarjeta   =  $_POST['tarjetaP'];
}
if(empty($_POST['regimen'])){
    $regimen  = 'NULL';
} else {
    $regimen   =  $_POST['regimen'];
}
 $updateSQL = "UPDATE gf_tercero 
                SET TipoIdentificacion=$tipoI, 
                NumeroIdentificacion= $numId,
                NombreUno='$primerN',
                NombreDos='$segundoN',
                ApellidoUno='$primerA',
                ApellidoDos='$segundoA',
                email = '$email', 
                zona = $zona, 
                tiporegimen = $regimen, 
                tarjeta_profesional = '$tarjeta' 
                WHERE Id_Unico = $id ";
$updateT = oci_parse($oracle, $updateSQL);        // Preparar la sentencia
$resultado = oci_execute($updateT); 


  $sqlP="select perfil from gf_perfil_tercero where perfil=2 and tercero=$id";
  $selP = oci_parse($oracle, $sqlP);        // Preparar la sentencia
  $per = oci_execute($selP); 
  if($per==false){
    $sqlPer = "INSERT INTO gf_perfil_tercero(Perfil,Tercero) VALUES (2,$id)";
    $insertP = oci_parse($oracle, $sqlPer);        // Preparar la sentencia
    $resPe = oci_execute($insertP); 
  }
 ?>
 <!-- Estructura de impresión de modales -->
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

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../LISTAR_TERCERO_EMPLEADO_NATURAL2.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../LISTAR_TERCERO_EMPLEADO_NATURAL2.php';
  });
</script>
<?php } ?>