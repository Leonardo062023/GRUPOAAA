<?php
  require_once('../Conexion/conexion.php');
  require_once('../Conexion/ConexionPDO.php');
  session_start();
  $compania = $_SESSION['compania'];
  $anno 	= $_SESSION['anno'];
  $con 		= new ConexionPDO();
  $hoy		= date('Y-m-d');

  $empleado = $_REQUEST['sltEmpleado'];
  $periodo  = $_REQUEST['sltPeriodo'];
  #Eliminar Conceptos 
  $sql_elm 		="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto IN (74,98,102)";
  $resultado 	= $mysqli->query($sql_elm);

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
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
          
          <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
  <script src="../js/bootstrap.js"></script>
<!-- Script que redirige a la página inicial de Tipo Elemento. -->
<?php if($resultado==true){ ?>
	<script type="text/javascript">
	  $("#myModal1").modal('show');
	  $("#ver1").click(function(){
	    $("#myModal1").modal('hide');
	    window.location='../liquidar_GN_LIQUIDACION_FINALE.php?idP=<?php echo $periodo?>';
	  });
	</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
	    $("#myModal2").modal('hide');
	    window.location='../liquidar_GN_LIQUIDACION_FINALE.php?idP=<?php echo $periodo?>';
	  });
</script>
<?php } ?>