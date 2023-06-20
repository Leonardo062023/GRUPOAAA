<?php
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
require_once 'head.php';

$action = $_REQUEST['action'];

?>
<title>Registrar Tipo Teléfono</title>
</head>

<body>
  <!-- contenedor principal -->
  <div class="container-fluid text-center">
    <div class="row content">
      <!-- Llamado al menu del formulario -->
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tipo Tel&eacutefono</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <!-- inicio del formulario -->
          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">

            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre " required>
            </div>
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert">
          </form>
          <!-- Fin de división y contenedor del formulario -->
        </div>
      </div>
    </div>
    <!-- Fin del Contenedor principal -->
  </div>
  <!-- Llamado al pie de pagina -->
  <!--Modal Registrar-->
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
  <script type="text/javascript">
    function agregar() {
      jsShowWindowLoad('Agregando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "json/registrarTipoTelefonoJson.php?action=2",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información agregada Correctamente');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
              document.location = 'listar_GF_TIPO_TELEFONO.php';
            })

          } else {
            $("#mensaje").html('No Se Ha Podido Agregar Información');
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