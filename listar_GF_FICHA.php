<?php
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
require_once 'head_listar.php';

$resultado = $con->Listar("SELECT id_unico,descripcion FROM gf_ficha");

?>

<title>Listar Ficha</title>
</head>

<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once('menu.php'); ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Ficha</h2>
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-5px">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td style="display: none;">Identificador</td>
                  <td width="30px" align="center"></td>
                  <td><strong>Descripción</strong></td>
                </tr>
                <tr>
                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                for ($row=0; $row < count($resultado); $row++) {  ?>
                  <tr>
                    <td style="display: none;"><?php echo $resultado[$row][0] ?></td>
                    <td>
                      <a href="#" onclick="javascript:eliminarFicha(<?php echo $resultado[$row][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                      </a><a href="modificar_GF_FICHA.php?action=1&id=<?php echo base64_encode($resultado[$row][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                    </td>
                    <td><?php echo ucwords((strtolower($resultado[$row][1]))); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div align="right">
              <a href="modificar_GF_FICHA.php?action=2" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;
       color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Ficha?</p>
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
          <button type="button" id="ver2" class="btn" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    function eliminarFicha(id) {
      var result = '';
      $("#myModal").modal('show');
      $("#ver").click(function() {
        $("#mymodal").modal('hide');
        var form_data = {
          id_unico: id,
          action: 'delete'
        };
        $.ajax({
          type: "POST",
          url: "controller/controllerGFFicha.php",
          data: form_data,
          success: function(data) {
            result = JSON.parse(data);
            if (result == true) {
              $("#myModal1").modal('show');
            } else {
              $("#myModal2").modal('show');
            }
          }
        });
      });
    }

    function modal() {
      $("#myModal").modal('show');
    }

    $('#ver1').click(function() {
      document.location = 'listar_GF_FICHA.php';
    });

    $('#ver2').click(function() {
      document.location = 'listar_GF_FICHA.php';
    });
  </script>
</body>

</html>