<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#05/10/2017 |ERICA G. |GUARDAR DETALLES CUANDO TRAE SOLICITUD NO ESTABA GUARDANDO
#13/03/2017 |ERICA G. |REDIRECCIÓN CUANDO NO GUARDA
#######################################################################################################
  require_once('../Conexion/conexion.php');
  require_once('../estructura_apropiacion.php');
  session_start();
  $usuario = $_SESSION['usuario'];
  $fechaElab = date('Y-m-d');
  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
  $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';

  $numero  = '"'.$mysqli->real_escape_string(''.$_POST['noDisponibilidad'].'').'"';
  $fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  $fechaVen  = '"'.$mysqli->real_escape_string(''.$_POST['fechaVen'].'').'"';
  $descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  $estado = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
  $tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipoComPtal'].'').'"';

  //Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
  $fecha = trim($fecha, '"');
  $fecha_div = explode("/", $fecha);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fecha = $anio.$mes.$dia;

  //Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
  $fechaVen = trim($fechaVen, '"');
  $fecha_div = explode("/", $fechaVen);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fechaVen = $anio.$mes.$dia;
$parametroAnno = $_SESSION['anno'];
  $compania =$_SESSION['compania'];
  $queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania";
  $vario = $mysqli->query($queryVario);
  $row = mysqli_fetch_row($vario);
  $tercero = $row[0];
  $responsable = $row[0];

  

  if(empty($_POST['tercero'])){
      $tercero =$tercero;
  } else {
      $tercero = $_POST['tercero'];
  }

  if(!empty($_POST['responsable'])){
      $responsable = $_POST['responsable'];
  }
  if($descripcion == '""')
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, 
        tipocomprobante, tercero, estado, responsable,usuario, fecha_elaboracion) 
  VALUES($numero, $fecha, $fechaVen, $parametroAnno, $tipocomprobante, "
            . "$tercero, $estado, $responsable, '$usuario', '$fechaElab' )";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, 
        parametrizacionanno, tipocomprobante, tercero, estado, responsable,usuario, fecha_elaboracion ) 
  VALUES($numero, $fecha, $fechaVen, $descripcion, $parametroAnno, $tipocomprobante, "
            . "$tercero, $estado, $responsable, '$usuario', '$fechaElab')";
  }
  
  $resultado = $mysqli->query($insertSQL);


  if($resultado == true)
  {
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 

    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
    detComP.id_unico, detComP.conceptorubro  
      FROM gf_detalle_comprobante_pptal detComP
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;
    
    while($row = mysqli_fetch_row($resultado))
    {

      $saldDisp = 0;
      $totalAfec = 0;
      $queryDetAfe = "SELECT valor   
      FROM gf_detalle_comprobante_pptal   
      WHERE comprobanteafectado = ".$row[5];
      $detAfec = $mysqli->query($queryDetAfe);
      $totalAfe = 0;
      while($rowDtAf = mysqli_fetch_row($detAfec))
      {
        $totalAfec += $rowDtAf[0];
      }
        
      $saldDisp = $row[1] - $totalAfec;
      $valorPpTl = $saldDisp;
      
      if($valorPpTl > 0)
      {
        $descripcion = '"'.$mysqli->real_escape_string(''.$row[0].'').'"';

        $valor = $valorPpTl;
        $rubro = $row[2];
        $tercero = $row[3]; 
        $proyecto = $row[4];
        $idAfectado = $row[5];
        $conceptorubro = $row[6];

        $campo = "";
        $variable = "";
        if(($descripcion != '""') || ($descripcion != NULL))
        {
          $campo = "descripcion,";
          $variable = "$descripcion,";
        }
        $IDRubroFuente = $rubro;
        $saldoDisponible = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente);

        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, 
          tercero, proyecto, comprobanteafectado, conceptorubro, saldo_disponible) 
        VALUES ($variable $valor, $comprobantepptal, $rubro, $tercero, $proyecto, $idAfectado, $conceptorubro, $saldoDisponible)";
        $resultadoInsert = $mysqli->query($insertSQL);
      }

    }

    $_SESSION['id_comp_pptal_ED'] = $idNuevoComprobante;
    $_SESSION['nuevo_ED'] = 1;
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
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
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
<!-- Script que redirige a la página inicial de aprobar solicitud. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../EXPEDIR_DISPONIBILIDAD_PPTAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../EXPEDIR_DISPONIBILIDAD_PPTAL.php';
  });
</script>
<?php } ?>