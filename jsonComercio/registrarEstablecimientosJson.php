<?php


session_start();
require_once ('../Conexion/conexion.php');

$contribuyente=$_POST['contribuyente'];
$consultaId="SELECT id_unico FROM gc_contribuyente WHERE md5(id_unico)='$contribuyente'";
$rid=$mysqli->query($consultaId);
$id=mysqli_fetch_array($rid);
$idContri=$id[0];

if (!empty($_POST['nombre'])){
    $nombre="'".$mysqli->real_escape_string(''.$_POST['nombre'].'')."'";      
}else{
    $nombre="null";
}

$fecha=$_POST['fecha'];
$fechaI = DateTime::createFromFormat('d/m/Y', "$fecha");
$fechaI= $fechaI->format('Y/m/d');

if(!empty($_POST['estrato'])){
  $estrato=$_POST['estrato'];
}else{
  $estrato= "null";
}



if (!empty($_POST['direccion'])){
    $direccion="'".$mysqli->real_escape_string(''.$_POST['direccion'].'')."'";      
}else{
    $direccion="null";
}

if (!empty($_POST['codigo'])){
    $codigo="'".$mysqli->real_escape_string(''.$_POST['codigo'].'')."'";      
}else{
    $codigo="null";
}

$ciudad=$_POST['ciudad'];

if (!empty($_POST['barrio'])){
    $barrio=$mysqli->real_escape_string(''.$_POST['barrio'].'');      
}else{
    $barrio="null";
}

if (!empty($_POST['localizacion'])){
    $localizacion=$mysqli->real_escape_string(''.$_POST['localizacion'].'');      
}else{
    $localizacion="null";
}

if (!empty($_POST['tipoEntidad'])){
    $tipoEntidad=$mysqli->real_escape_string(''.$_POST['tipoEntidad'].'');      
}else{
    $tipoEntidad="null";
}

if (!empty($_POST['tamannoEntidad'])){
    $tamannoEntidad=$mysqli->real_escape_string(''.$_POST['tamannoEntidad'].'');      
}else{
    $tamannoEntidad="null";
}




 $sql="INSERT INTO gc_establecimiento(contribuyente,nombre,fechainicioAct,estrato,direccion,cod_catastral,ciudad,barrio,localizacion,tipo_entidad,tamanno_entidad) VALUES($idContri,$nombre,'$fechaI',$estrato,$direccion,$codigo,$ciudad,$barrio,$localizacion,$tipoEntidad,$tamannoEntidad)";

$resultado=$mysqli->query($sql); ?>
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
            		<p>Información guardada correctamente.</p>
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
  	        	<p>No se ha podido guardar la información.</p>
      	    </div>
          	<div id="forma-modal" class="modal-footer">
            		<button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
          	</div>
        	</div>
      </div>
  </div>

  <script type="text/javascript" src="../js/md5.pack.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

  <?php if($resultado == true){ ?>
  <script type="text/javascript">
    	$("#myModal1").modal('show');
    	$("#ver1").click(function(){
      	$("#myModal1").modal('hide');
        window.history.go(-1);
    	});
  </script>
  <?php }else{ ?>
  <script type="text/javascript">
    	$("#myModal2").modal('show');
    	$("#ver2").click(function(){
    		window.history.go(-1);
    	});
  </script>
<?php }
 ?>

