<?php
// require_once('Conexion/conexion.php');
// require_once('head_listar.php');
// $query = "SELECT t.id_unico, u.id_unico, u.nombre, 
// p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final, 
// e.id_unico, e.nombre, 
// tt.id_unico, tt.nombre, 
// t.valor, t.porcentaje_iva, t.porcentaje_impoconsumo 
// FROM gp_tarifa t 
// LEFT JOIN gp_uso u ON t.uso= u.id_unico 
// LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
// LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
// LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
// LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
// LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico";

// $resultado = $mysqli->query($query);

?>
<?php
@session_start();
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head_listar.php';
$con = new ConexionPDO();

$resultado = $con->Listar("SELECT t.id_unico, u.id_unico, u.nombre, 
p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final, 
e.id_unico, e.nombre, 
tt.id_unico, tt.nombre, 
t.valor, t.porcentaje_iva, t.porcentaje_impoconsumo 
FROM gp_tarifa t 
LEFT JOIN gp_uso u ON t.uso= u.id_unico 
LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico");

?>

<title>Listar tarifa</title>
</head>

<body>

  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once('menu.php'); ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Tarifa</h2>
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td class="cabeza" style="display: none;">Identificador</td>
                  <td class="cabeza" width="30px" align="center"></td>
                  <td class="cabeza"><strong>Uso</strong></td>
                  <td class="cabeza"><strong>Periodo</strong></td>
                  <td class="cabeza"><strong>Estrato</strong></td>
                  <td class="cabeza"><strong>Tipo Tarifa</strong></td>
                  <td class="cabeza"><strong>Valor</strong></td>
                  <td class="cabeza"><strong>Porcentaje IVA</strong></td>
                  <td class="cabeza"><strong>Porcentaje Impoconsumo</strong></td>

                </tr>
                <tr>
                  <th class="cabeza" style="display: none;">Identificador</th>
                  <th class="cabeza" width="7%"></th>
                  <th class="cabeza">Uso</th>
                  <th class="cabeza">Periodo</th>
                  <th class="cabeza">Estrato</th>
                  <th class="cabeza">Tipo Tarifa</th>
                  <th class="cabeza">Valor</th>
                  <th class="cabeza">Porcentaje IVA</th>
                  <th class="cabeza">Porcentaje Impoconsumo</th>
                </tr>
              </thead>
              <tbody>
                <?php
                for ($row = 0; $row < count($resultado); $row++) { ?>
                  <tr>
                    <td class="campos" style="display: none;"><?php echo $resultado[$row][0] ?></td>
                    <td>
                      <a class="campos" href="#" onclick="javascript:eliminar(<?php echo $resultado[$row][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a class="campos" href="Modificar_GP_TARIFA.php?action=1&id=<?php echo base64_encode($resultado[$row][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                    </td>
                    <td class="campos"><?php echo ucwords(strtolower(($resultado[$row][2]))); ?></td>
                    <td class="campos"><?php if (empty($resultado[$row][4])) {
                                          echo '';
                                        } else {
                                          echo $resultado[$row][4] . ' - ' . ucwords(strtolower($resultado[$row][5])) . ' - ' . date("d/m/Y", strtotime($resultado[$row][6])) . ' - ' . date("d/m/Y", strtotime($resultado[$row][7]));
                                        } ?></td>
                    <td class="campos"><?php echo ucwords(strtolower(($resultado[$row][9]))); ?></td>
                    <td class="campos"><?php echo ucwords(strtolower(($resultado[$row][11]))); ?></td>
                    <td class="campos"><?php echo $resultado[$row][12]; ?></td>
                    <td class="campos"><?php echo $resultado[$row][13]; ?></td>
                    <td class="campos"><?php echo $resultado[$row][14]; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div align="right"><a href="Modificar_GP_TARIFA.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Divs de clase Modal para las ventanillas de eliminar. -->
  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de tarifa?</p>
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
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

  <!-- Función para la eliminación del registro. -->
  <script type="text/javascript">
    function eliminar(id) {
      var result = '';
      $("#myModal").modal('show');
      $("#ver").click(function() {
        $("#mymodal").modal('hide');
        var form_data = {
          id: id,
          action: 1
        };
        $.ajax({
          type: "POST",
          url: "json/modificar_GP_TARIFAJson.php",
          data: form_data,
          success: function(data) {
            result = JSON.parse(data);
            if (result) {
              $("#myModal1").modal('show');
            } else {
              $("#myModal2").modal('show');
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
      document.location = 'LISTAR_GP_TARIFA.php';
    });
  </script>

  <script type="text/javascript">
    $('#ver2').click(function() {
      document.location = 'LISTAR_GP_TARIFA.php';
    });
  </script>

</body>

</html>