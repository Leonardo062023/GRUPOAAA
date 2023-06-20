<?php
require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';
$con = new ConexionPDO();

//consulta para cargar la informacion en la tabla
$resultado =  $con->Listar("SELECT id_unico, nombre FROM gf_tipo_telefono  ORDER BY nombre ASC");

?>
<title>Listar Tipo Teléfono</title>
</head>

<body>

  <div class="container-fluid text-center">
    <div class="row content">

      <?php require_once 'menu.php'; ?>

      <div class="col-sm-10 text-left">
        <!--Titulo del formulario-->
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Tipo Teléfono</h2>


        <!--Tabla para mostrar la información-->
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <!--titulos de la tabla-->
                <tr>
                  <td style="display: none;">Identificador</td>
                  <td width="7%"></td>
                  <td><strong>Tipo Teléfono</strong></td>
                </tr>
                <tr>
                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <th>Tipo Teléfono</th>
                </tr>
              </thead>
              <tbody>
                <!--Muestra los registros de la base de datos junto a los iconos de eliminar y modificar -->
                <?php
                for ($rowT = 0; $rowT < count($resultado); $rowT++) { ?>
                  <tr>
                    <td style="display: none;"><?php echo $resultado[$rowT][0] ?></td>
                    <td>
                      <a href="#" onclick="javascript:eliminar(<?php echo $resultado[$rowT][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a href="modificar_GF_TIPO_TELEFONO.php?action=1&id=<?php echo base64_encode($resultado[$rowT][0]) ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                    </td>
                    <td><?php echo ($resultado[$rowT][1]) ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <!--Boton que abre el formulario de registro-->
            <div align="right"><a href="modificar_GF_TIPO_TELEFONO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Modal que confirma la eliminación del registro-->
  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tipo Teléfono?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>



  <!--Lnks para el estilo del formulario-->
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <!--Script que envia los datos necesario para la eliminación del registro-->
  <script type="text/javascript">
    function eliminar(id) {
      $("#myModal").modal('show');
      $("#ver").click(function() {
        jsShowWindowLoad('Eliminando Datos ...');
        $("#mymodal").modal('hide');
        var form_data = {
          action: 1,
          id: id
        };
        $.ajax({
          type: 'POST',
          url: "Json/modificarTipoTelJson.php?action==1",
          data: form_data,
          success: function(response) {
            jsRemoveWindowLoad();
            console.log(response);
            if (response == 1) {
              $("#mensaje").html('Información Eliminada Correctamente');
              $("#myModal1").modal('show');

              //document.location.reload();

            } else if (response == 2) {
              $("#mensaje").html('No Se Ha Podido Eliminar La Información');
              $("#modalMensajes").modal("show");
              $("#Aceptar").click(function() {
                $("#modalMensajes").modal("hide");
              })
            } else {
              $("#mensaje").html('No se puede eliminar la información, ya que la actividad posee Seguimiento(s)');
              $("#modalMensajes").modal("show");
              $("#Aceptar").click(function() {
                $("#modalMensajes").modal("hide");
              })
            }
          }
        });
      });
    }
  </script>
  <script type="text/javascript">
    function modal() {
      $("#myModal").modal('show');
    }
  </script>
  <!--Actualiza la página-->
  <script type="text/javascript">
    $('#ver1').click(function() {
      document.location = 'listar_GF_TIPO_TELEFONO.php';
    });
  </script>

  <script type="text/javascript">
    $('#ver2').click(function() {
      document.location = 'listar_GF_TIPO_TELEFONO.php';
    });
  </script>
  <?php require_once 'footer.php' ?>
</body>

</html>