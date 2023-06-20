<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $ciclo  = '"'.$mysqli->real_escape_string(''.$_POST['ciclo'].'').'"';
  $anno = '"'.$mysqli->real_escape_string(''.$_POST['anno'].'').'"';
  $fechaI  =$mysqli->real_escape_string(''.$_POST['fecha_inicial'].'');
  $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaI");
  $fechaI= $fechaI->format('Y/m/d');
  
  $fechaF  =$mysqli->real_escape_string(''.$_POST['fecha_final'].'');
  $fechaF = DateTime::createFromFormat('d/m/Y', "$fechaF");
  $fechaF= $fechaF->format('Y/m/d');
    
  $primeraFecha  = $mysqli->real_escape_string(''.$_POST['primera_fecha'].'');
  $primeraFecha = DateTime::createFromFormat('d/m/Y', "$primeraFecha");
  $primeraFecha= $primeraFecha->format('Y/m/d');
  
  $segundaFecha  = $mysqli->real_escape_string(''.$_POST['segunda_fecha'].'');
  $segundaFecha = DateTime::createFromFormat('d/m/Y', "$segundaFecha");
  $segundaFecha= $segundaFecha->format('Y/m/d');
  
  $fechaCierre  = $mysqli->real_escape_string(''.$_POST['fecha_cierre'].'');
  $fechaCierre = DateTime::createFromFormat('d/m/Y', "$fechaCierre");
  $fechaCierre= $fechaCierre->format('Y/m/d');
  
  $descripcion  = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  
  if (!file_exists('../documentos/periodo/')) {
        mkdir('../documentos/periodo/', 0777, true);
   } 

  $directorio ='../documentos/periodo/';
  $nombreimagen = $_FILES['file']['name'];
  $ruta = 'documentos/periodo/'.$nombreimagen;
    
  
  if ($ciclo=='""'){
     $ciclo='NULL'; 
  }
  if ($anno=='""'){
     $anno='NULL'; 
  }
  


  if(!empty($_FILES['file']['name'])){ 

 $insert = "INSERT INTO gp_periodo (nombre,ciclo, anno, fecha_inicial, fecha_final, "
          . "primera_fecha, segunda_fecha, fecha_cierre, descripcion, imagen) "
         . "VALUES($nombre, $ciclo,$anno, '$fechaI', '$fechaF', '$primeraFecha', '$segundaFecha', '$fechaCierre',$descripcion, '$ruta')";

    $resultado = $mysqli->query($insert);

    if($resultado == true || $resultado=='1'){     
       move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombreimagen);      
    } 
           
  }else{

    $insert = "INSERT INTO gp_periodo (nombre,ciclo, anno, fecha_inicial, fecha_final, "
          . "primera_fecha, segunda_fecha, fecha_cierre, descripcion) "
         . "VALUES($nombre, $ciclo,$anno, '$fechaI', '$fechaF', '$primeraFecha', '$segundaFecha', '$fechaCierre',$descripcion)";

   echo $resultado = $mysqli->query($insert);     

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
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../LISTAR_GP_PERIODO.php';
      });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location=window.history.back(-1);
      });
    </script>
<?php } ?> 
 