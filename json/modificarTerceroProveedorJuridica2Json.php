<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código 
#######################################################################################################
require_once('../Conexion/conexion.php');
session_start();

$id         = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$tipoIden   = '"'.$mysqli->real_escape_string(''.$_POST['tipoIdent'].'').'"';
$noIdent    = '"'.$mysqli->real_escape_string(''.$_POST['noIdent'].'').'"';
$digitVerif = '"'.$mysqli->real_escape_string(''.$_POST['digitVerif'].'').'"';
$razoSoci   = '"'.$mysqli->real_escape_string(''.$_POST['razoSoci'].'').'"';
$ciudad     = '"'.$mysqli->real_escape_string(''.$_POST['ciudad'].'').'"';

if(empty($_POST['contacto'])){
    $contacto = 'NULL';
}else{
    $contacto = '"'.$mysqli->real_escape_string(''.$_POST['contacto'].'').'"';
}

if(empty($_POST['tipoReg'])){
    $tipoReg = 'NULL';
}else{
    $tipoReg    = '"'.$mysqli->real_escape_string(''.$_POST['tipoReg'].'').'"';
}

if(empty($_POST['repreLegal']) ){
    $repreLegal = 'NULL';
}else{
    $repreLegal = '"'.$mysqli->real_escape_string(''.$_POST['repreLegal'].'').'"';
}

if(empty($_POST['zona'])){
    $zona = 'NULL';
}else{
    $zona = '"'.$mysqli->real_escape_string(''.$_POST['zona'].'').'"';
}

if(empty($_POST['tipoEmp'])){
    $tipoEmp = 'NULL';
}else{
    $tipoEmp = '"'.$mysqli->real_escape_string(''.$_POST['tipoEmp'].'').'"';
}

if(empty($_POST['tipoEntidad'])){
    $tipoEnt = 'NULL';
}else{
    $tipoEnt = '"'.$mysqli->real_escape_string(''.$_POST['tipoEntidad'].'').'"';
}

if(empty($_POST['sucursal']) ){
    $sucursal = 'NULL';
}else{
    $sucursal = '"'.$mysqli->real_escape_string(''.$_POST['sucursal'].'').'"';
}

if(empty($_POST['correo']) ){
    $email = 'NULL';
}else{
    $email = '"'.$mysqli->real_escape_string(''.$_POST['correo'].'').'"';
}


$updateSQL = "UPDATE gf_tercero  
SET RazonSocial = $razoSoci, NumeroIdentificacion = $noIdent, 
    DigitoVerficacion = $digitVerif, TipoIdentificacion = $tipoIden, 
    Sucursal = $sucursal, RepresentanteLegal = $repreLegal, CiudadIdentificacion = $ciudad, 
    TipoRegimen = $tipoReg, TipoEmpresa = $tipoEmp, TipoEntidad =  $tipoEnt,
    Contacto=$contacto,Zona=$zona, email = $email 
WHERE Id_Unico = $id";
$resultado = $mysqli->query($updateSQL);
$sqlT="select perfil from gf_perfil_tercero where perfil=6 and tercero=$id";
$resultT=$mysqli->query($sqlT);
$perfil=mysqli_fetch_row($resultT);
if(empty($perfil[0])){
  $consulta = "INSERT INTO gf_perfil_tercero(Perfil,Tercero) VALUES(6,$id)";
  $rs = $mysqli->query($consulta);
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
<!--Links para el estilo de la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<!--Abre nuevamente la página de listar para mostrar la información modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../LISTAR_TERCERO_PROVEEDOR_JURIDICA_2.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    window.history.go(-1);
  });
</script>
<?php } ?>