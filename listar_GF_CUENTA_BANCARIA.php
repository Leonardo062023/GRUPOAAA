<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#03/03/2017 |ERICA G. |MODIFICACION CONSULTA
######################################################################################################

require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';
$con = new ConexionPDO();

$anno = $_SESSION['anno'];
$conexion = $_SESSION['conexion'];
$row = $con->Listar("SELECT   cb.id_unico, cb.banco, t.id_unico, t.razonsocial, 
    t.numeroidentificacion, t.tipoidentificacion,  ti.nombre, cb.numerocuenta, 
    cb.descripcion, cb.tipocuenta, tc.nombre, cb.cuenta, c.nombre, c.codi_cuenta, 
    cb.recursofinanciero, rf.nombre, rf.codi, cb.formato, fc.nombre, 
    d.id_unico, LOWER(d.nombre) 
  FROM gf_cuenta_bancaria cb
  LEFT JOIN gf_tercero t ON cb.banco=t.id_unico
  LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
  LEFT JOIN gf_tipo_cuenta tc ON cb.tipocuenta = tc.id_unico
  LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
  LEFT JOIN gf_recurso_financiero rf ON cb.recursofinanciero = rf.id_unico
  LEFT JOIN gf_formato fc ON cb.formato = fc.id_unico 
  LEFT JOIN gf_tipo_destinacion d ON cb.destinacion = d.id_unico 
  WHERE cb.parametrizacionanno = $anno 
  ");

?>
<title>Listar Cuenta Bancaria</title>

<body>
  <div class="container-fluid">
    <div class="row content">
      <?php require_once('menu.php'); ?>
      <div class="col-sm-10 text-left">
        <h2 class="titulolista" align="center">Cuenta Bancaria</h2>
        <div class="table-responsive contTabla">
          <div class="table-responsive contTabla">
            <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td style="display: none;">Identificador</td>
                  <td width="30px"></td>
                  <td><strong>Banco</strong></td>
                  <td><strong>Número Cuenta</strong></td>
                  <td><strong>Descripción</strong></td>
                  <td><strong>Tipo Cuenta</strong></td>
                  <td><strong>Formato</strong></td>
                  <td><strong>Recurso Financiero</strong></td>
                  <td><strong>Destinación</strong></td>
                </tr>
                <tr>
                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <th>Banco</th>
                  <th>Número Cuenta</th>
                  <th>Descripción</th>
                  <th>Tipo Cuenta</th>
                  <th>Formato</th>
                  <th>Recurso Financiero</th>
                  <th>Destinación</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i = 0; $i < count($row); $i++) { ?>
                  <tr>
                    <td style="display: none;"><?php echo $row[$i][0] ?></td>
                    <td>
                      <a href="#" onclick="javascript:eliminarCuentaB(<?php echo $row[$i][0]; ?>);">

                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                      </a>
                      <a href="modificar_GF_CUENTA_BANCARIA.php?action=1&id_cuentaB=<?php echo base64_encode($row[$i][0]); ?>">
                        <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                      </a>
                    </td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][3]) . " (" . ($row[$i][4]) . ")"); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][7])); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][8])); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][10])); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][18])); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][15])); ?></td>
                    <td><?php echo ucwords(mb_strtolower($row[$i][20])); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div align="right">
              <a href="modificar_GF_CUENTA_BANCARIA.php?action=2" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Formularios Modales -->
  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Cuenta Bancaria?</p>
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
  <?php require_once('footer.php');  ?>
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    function eliminarCuentaB(id) {
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
          url: "Json/modificarCuentaBancariaJson.php?action=1",
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

  <script type="text/javascript">
    $('#ver1').click(function() {
      document.location = 'listar_GF_CUENTA_BANCARIA.php';
    });
  </script>

  <script type="text/javascript">
    $('#ver2').click(function() {
      document.location = 'listar_GF_CUENTA_BANCARIA.php';
    });
  </script>

  <?php require_once 'footer.php'; ?>
</body>

</html>