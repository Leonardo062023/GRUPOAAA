<?php
@session_start();
require_once 'head.php';
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$conexion = $_SESSION['conexion'];
$titulo = "";
//consulta para cargar la informacion guardada con ese id
$id = "";
if ($action == 1) {
  $titulo = "Modificar Tipo Operación";
  if (isset($_GET["id"])) {
    $id = base64_decode($_GET["id"]);

    $row = $con->Listar("SELECT Id_Unico, Nombre
    FROM gp_tipo_operacion 
    WHERE Id_Unico = $id");
  }
} else {
  $titulo = "Registrar Tipo Operación";
  // $row = $con->Listar("SELECT Id_Unico, Nombre
  //   FROM gp_tipo_operacion");
}
?>
<title><?php echo $titulo ?></title>
</head>

<body>

  <div class="container-fluid text-center">
    <div class="row content">

      <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <!--Titulo del formulario-->
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>

        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <?php if ($action == 1) { ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
            <?php } else { ?>
              <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
              <?php } ?>
              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


              <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">

              <!--Carga los datos para la modificación-->
              <div class="form-group" style="margin-top: -10px;">
                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo ucwords(strtolower($row[0][1])); ?>" required>
                <?php } else { ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                <?php } ?>
              </div>
              <div class="form-group" style="margin-top: 10px;">
                <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px"><?php echo $titulo ?></button>
              </div>
              <input type="hidden" name="MM_insert">
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
</body>
<script type="text/javascript">
  function agregar() {
    jsShowWindowLoad('Agregando Datos ...');
    var formData = new FormData($("#form")[0]);
    $.ajax({
      type: 'POST',
      url: "json/modificar_GP_TIPO_OPERACIONJson.php?action=2",
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
            document.location = 'LISTAR_GP_TIPO_OPERACION.php';
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
      url: "json/modificar_GP_TIPO_OPERACIONJson.php?action=3",
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
            document.location = 'LISTAR_GP_TIPO_OPERACION.php';
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

</html>