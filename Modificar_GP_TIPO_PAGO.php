<?php
// require_once('Conexion/conexion.php');
// require_once 'head.php';
// $compania = $_SESSION['compania'];
// $id = $_GET['id'];
// $tipo = "SELECT tpg.id_unico, tpg.nombre,tpg.tipo_comprobante,
//     tpc.nombre,tpc.sigla, cb.id_unico, cb.numerocuenta, cb.descripcion, tpg.retencion  
//     FROM gp_tipo_pago tpg 
//     LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpg.tipo_comprobante 
//     LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico 
//     WHERE md5(tpg.id_unico)= '$id'";
// $tipo = $mysqli->query($tipo);
// $rowTipo = mysqli_fetch_row($tipo);
// $tipo = $rowTipo[2];
// $anno = $_SESSION['anno'];
?>
<?php
@session_start();
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
require_once 'head.php';

$compania = $_SESSION['compania'];
$id = base64_decode($_REQUEST['id']);
$action = $_REQUEST['action'];
$conexion = $_SESSION['conexion'];
$titulo = "";

$anno = $_SESSION['anno'];
if ($action == 1) {
  $titulo = "Modificar Tipo Pago";
  $rowTipo = $con->Listar("SELECT tpg.id_unico, tpg.nombre,tpg.tipo_comprobante,
  tpc.nombre,tpc.sigla, cb.id_unico, cb.numerocuenta, cb.descripcion, tpg.retencion  
  FROM gp_tipo_pago tpg 
  LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpg.tipo_comprobante 
  LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico 
  WHERE tpg.id_unico= $id");
  $tipo = $rowTipo[0][2];
} else {
  $titulo = "Registrar Tipo Pago";
}
?>

<title><?php echo $titulo ?></title>
<!-- Librerias de carga para el datapicker -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
</head>

<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <!--Titulo del formulario-->
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 2px"><?php echo $titulo ?></h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -12px" class="client-form">
          <?php if ($action == 1) { ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
            <?php } else { ?>
              <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
              <?php } ?>
              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
              <!--Ingresa la información-->
              <input name="id" id="id" type="hidden" value="<?php echo $rowTipo[0][0] ?>">
              <div class="form-group" style="margin-top: -10px;">
                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required value="<?php echo $rowTipo[0][1] ?>">
                <?php } else { ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                <?php } ?>
              </div>
              <div class="form-group" style="margin-top: -10px">
                <label for="tipoC" class="col-sm-5 control-label">Tipo comprobante:</label>
                <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control" style="width: 300px">
                  <?php
                  if (empty($tipo)) { ?>
                    <option value=''>Tipo comprobante</option>
                    <?php
                    $resultT = $con->Listar("SELECT id_unico,nombre,sigla FROM gf_tipo_comprobante WHERE compania = $compania");
                    for ($t = 0; $t < count($resultT); $t++) { ?>
                      <option value="<?php echo $resultT[$t][0] ?>"><?php echo ucwords(strtolower($resultT[$t][1])) . ' - ' . $resultT[$t][2] ?></option>
                    <?php }
                  } else { ?>
                    <option value="<?php echo $tipo ?>"><?php echo ucwords(strtolower($rowTipo[0][3])) . ' - ' . $rowTipo[0][4] ?></option>
                    <?php
                    $resultT = $con->Listar("SELECT id_unico,nombre,sigla FROM gf_tipo_comprobante WHERE id_unico != $tipo AND compania = $compania");
                    for ($t = 0; $t < count($resultT); $t++) { ?>
                      <option value="<?php echo $resultT[$t][0] ?>"><?php echo ucwords(mb_strtolower($resultT[$t][1])) . PHP_EOL . $resultT[$t][2] ?></option>
                  <?php }
                    echo "<option></option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group" style="margin-top: -10px">
                <label for="banco" class="col-sm-5 control-label">Banco:</label>
                <select name="banco" id="banco" title="Banco" class="col-sm-1 form-control" style="width: 300px">
                  <?php
                  if (empty($rowTipo[0][5])) { ?>
                    <option value=''>Banco</option>
                  <?php
                    $idc = 0;
                  } else { ?>
                    <option value="<?php echo $rowTipo[0][5] ?>"><?php echo  $rowTipo[0][6] . ' - ' . ucwords(mb_strtolower($rowTipo[0][7])) ?></option>
                    <option value=''> - </option>";
                  <?php
                    $idc = $rowTipo[0][5];
                  }
                  $resultT = $con->Listar("SELECT id_unico,numerocuenta,descripcion 
                                    FROM gf_cuenta_bancaria WHERE id_unico != $idc AND parametrizacionanno = $anno");
                  for ($rowB = 0; $rowB < count($resultT); $rowB++) { ?>
                    <option value="<?php echo $resultT[$t][0] ?>"><?php echo $resultT[$t][1] . ' - ' . ucwords(mb_strtolower($resultT[$t][2])) ?></option>
                  <?php }
                  ?>
                </select>
              </div>
              <div class="form-group" style="margin-top: -5px;">
                <label for="retencion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
                <?php
                if ($action == 1) {
                  if ($rowTipo[0][8] == 1) {
                    echo '<input type="radio" name="retencion" id="retencion" value="1" checked="checked">Si'
                      . '<input type="radio" name="retencion" id="retencion" value="2">No';
                  } else {
                    echo '<input type="radio" name="retencion" id="retencion" value="1" >Si'
                      . '<input type="radio" name="retencion" id="retencion" value="2" checked="checked">No';
                  }
                } else {
                  echo '<input type="radio" name="retencion" id="retencion" value="1" >Si'
                    . '<input type="radio" name="retencion" id="retencion" value="2" checked="checked">No';
                } ?>
              </div>
              <div class="form-group" style="margin-top: 10px;">
                <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px"><?php echo $titulo ?></button>
              </div>
              </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalMensajes" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once 'footer.php'; ?>
  <script type="text/javascript" src="js/select2.js"></script>
  <script>
    $("#tipoC").select2({
      allowClear: true
    });
    $("#banco").select2({
      allowClear: true
    });
  </script>
  <script type="text/javascript">
    function agregar() {
      jsShowWindowLoad('Agregando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "json/modificar_GP_TIPO_PAGOJson.php?action=2",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          //console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información agregada correctamente');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
              document.location = 'LISTAR_GP_TIPO_PAGO.php';
            })
          } else if (response == 3) {
            $("#mensaje").html('No se ha podido agregar información. Este registro ya existe. ');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })
          } else {
            $("#mensaje").html('No se ha podido agregar información');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })
          }
        }
      });
    }

    function modificar() {
      jsShowWindowLoad('Modificando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "json/modificar_GP_TIPO_PAGOJson.php?action=3",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          //console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información Modificada Correctamente');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
              document.location = 'LISTAR_GP_TIPO_PAGO.php';
            })
          }else if (response == 3) {
            $("#mensaje").html('No se ha podido agregar información. Este registro ya existe. ');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })
          } else {
            $("#mensaje").html('No Se Ha Podido Modificar Información');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })

          }
        }
      });
    }
  </script>

</body>

</html>