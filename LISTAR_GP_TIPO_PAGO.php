<?php
// require_once('Conexion/conexion.php');
// require_once('head_listar.php');
// $compania = $_SESSION['compania'];
// $query = "SELECT tpg.id_unico, tpg.nombre,tpg.tipo_comprobante,tpc.nombre, 
//     cb.numerocuenta, cb.descripcion, IF(tpg.retencion=1, 'Si', 'No')
//     FROM gp_tipo_pago tpg 
//     LEFT JOIN gf_tipo_comprobante tpc ON tpg.tipo_comprobante = tpc.id_unico 
//     LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico 
//      WHERE tpg.compania = $compania  
//     "; 
// $resultado = $mysqli->query($query);
?>
<?php
@session_start();
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
require_once('head_listar.php');

$compania = $_SESSION['compania'];
$conexion = $_SESSION['conexion'];
switch ($conexion) {
    case 1:
        $resultado = $con->Listar("SELECT tpg.id_unico, tpg.nombre,tpg.tipo_comprobante,tpc.nombre, 
        cb.numerocuenta, cb.descripcion, IF(tpg.retencion=1, 'Si', 'No')
        FROM gp_tipo_pago tpg 
        LEFT JOIN gf_tipo_comprobante tpc ON tpg.tipo_comprobante = tpc.id_unico 
        LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico 
        WHERE tpg.compania = $compania");
        break;
    case 2:
        $resultado = $con->Listar("SELECT tpg.id_unico, tpg.nombre, tpg.tipo_comprobante, tpc.nombre,
        cb.numerocuenta, cb.descripcion, CASE WHEN tpg.retencion = 1 THEN 'Si' ELSE 'No' END
        FROM gp_tipo_pago tpg
        LEFT JOIN gf_tipo_comprobante tpc ON tpg.tipo_comprobante = tpc.id_unico
        LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico
        WHERE tpg.compania = $compania");
        break;
    default:
        break;
}

?>
<title>Listar Tipo Pago</title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Tipo Pago</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px" align="center"></td>
                                    <td class="cabeza"><strong>Nombre</strong></td>
                                    <td class="cabeza"><strong>Tipo Comprobante</strong></td>
                                    <td class="cabeza"><strong>Banco</strong></td>
                                    <td class="cabeza"><strong>Retención</strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza">Tipo Comprobante</th>
                                    <th class="cabeza">Banco</th>
                                    <th class="cabeza">Retención</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($row = 0; $row < count($resultado); $row++) { ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $resultado[$row][0] ?></td>
                                        <td class="campos">
                                            <a href="#" class="campos" onclick="javascript:eliminar(<?php echo $resultado[$row][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a class="campos" href="Modificar_GP_TIPO_PAGO.php?action=1&id=<?php echo base64_encode($resultado[$row][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][1]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][3]))); ?></td>
                                        <td><?php echo $resultado[$row][4] . ' - ' . ucwords(mb_strtolower(($resultado[$row][5]))); ?></td>
                                        <td><?php echo $resultado[$row][6]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Modificar_GP_TIPO_PAGO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
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
                    <p>¿Desea eliminar el registro seleccionado de Tipo Pago?</p>
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
                    url: "json/modificar_GP_TIPO_PAGOJson.php",
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
            document.location = 'LISTAR_GP_TIPO_PAGO.php';
        });
    </script>
</body>

</html>