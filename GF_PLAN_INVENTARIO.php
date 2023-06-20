<?php
/*require_once('head_listar.php');
require_once('Conexion/conexion.php');
$compania = $_SESSION['compania'];
$queryPlanInv = "SELECT    pi.id_unico, pi.codi, pi.Nombre, pi.tienemovimiento, ti.nombre, uf.nombre, 
                              (select CONCAT_WS(' - ', codi,Nombre) from gf_plan_inventario where id_unico = pi.predecesor) predecesor, 
                              ta.nombre, fch.descripcion, pi.xCantidad, pi.xFactura, pi.iva_descontable
                    FROM      gf_plan_inventario AS pi 
                    LEFT JOIN gf_tipo_inventario AS ti  ON pi.tipoinventario = ti.id_unico
                    LEFT JOIN gf_unidad_factor   AS uf  ON pi.unidad         = uf.id_unico
                    LEFT JOIN gf_tipo_activo     AS ta  ON pi.tipoactivo     = ta.id_unico
                    LEFT JOIN gf_ficha           AS fch ON pi.ficha          = fch.id_unico
                    WHERE     pi.compania = $compania ORDER BY pi.codi";
$resultado = $mysqli->query($queryPlanInv);*/ ?>
<?php
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
require_once 'head_listar.php';

$compania = $_SESSION['compania'];

$resultado = $con->Listar("SELECT
pi.id_unico,
pi.codi,
pi.Nombre,
CASE WHEN pi.tienemovimiento=2 THEN 'Si' ELSE  'No' END,
ti.nombre,
uf.nombre,
(
  SELECT codi || ' - ' || Nombre
  FROM gf_plan_inventario
  WHERE id_unico = pi.predecesor
) predecesor,
ta.nombre,
fch.descripcion,
CASE WHEN pi.xCantidad = 1 THEN 'Si' ELSE  'No' END,
CASE WHEN pi.xFactura = 1 THEN 'Si' ELSE  'No' END,
CASE WHEN pi.iva_descontable = 1 THEN 'Si' ELSE  'No' END
FROM
gf_plan_inventario pi
LEFT JOIN
gf_tipo_inventario ti ON pi.tipoinventario = ti.id_unico
LEFT JOIN
gf_unidad_factor uf ON pi.unidad = uf.id_unico
LEFT JOIN
gf_tipo_activo ta ON pi.tipoactivo = ta.id_unico
LEFT JOIN
gf_ficha fch ON pi.ficha = fch.id_unico
WHERE
pi.compania = $compania
ORDER BY
pi.codi");

?>
<title>Listar Plan Inventario</title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Plan Inventario</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-15px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px" align="center"></td>
                                    <td class="cabeza"><strong>Código</strong></td>
                                    <td class="cabeza"><strong>Nombre</strong></td>
                                    <td class="cabeza"><strong>Movimiento</strong></td>
                                    <td class="cabeza"><strong>Tipo Inventario</strong></td>
                                    <td class="cabeza"><strong>Unidad Factor</strong></td>
                                    <td class="cabeza"><strong>Predecesor</strong></td>
                                    <td class="cabeza"><strong>Tipo Activo</strong></td>
                                    <td class="cabeza"><strong>Ficha</strong></td>
                                    <td class="cabeza"><strong>Plan Inv. Padre</strong></td>
                                    <td class="cabeza"><strong>Ind. Capacidad</strong></td>
                                    <td class="cabeza"><strong>Concepto Facturable</strong></td>
                                    <td class="cabeza"><strong>Maneja Iva Descontable</strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza">Código</th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza">Movimiento</th>
                                    <th class="cabeza">Tipo Inventario</th>
                                    <th class="cabeza">Unidad Factor</th>
                                    <th class="cabeza">Predecesor</th>
                                    <th class="cabeza">Tipo Activo</th>
                                    <th class="cabeza">Ficha</th>
                                    <th class="cabeza">Plan Inv. Padre</th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($row = 0; $row < count($resultado); $row++) {  ?>
                                    <tr>
                                        <td class="campos" style="display: none;"></td>
                                        <td class="campos">
                                            <a href="#" onclick="javascript:eliminarPlanInv(<?php echo $resultado[$row][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="modificar_GF_PLAN_INVENTARIO.php?action=1&id_plan_inv=<?php echo base64_encode($resultado[$row][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                        <td class="campos"><?php echo ($resultado[$row][1]) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][2])) ?></td>
                                        <td class="campos">
                                            <?php //2 es Sí. 1 es No.
                                            echo $resultado[$row][3];

                                            ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][4])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][5])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][6])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][7])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$row][8])) ?></td>
                                        <td class="campos">
                                            <?php
                                            $padre = $con->Listar("select plan_padre from gf_plan_inventario_asociado where plan_hijo = " . $resultado[$row][0] . " ");

                                            $planPadre = $con->Listar("SELECT codi || ' - ' || nombre FROM gf_plan_inventario WHERE id_unico = " . $padre[0][0] . " ");

                                            echo ucwords(mb_strtolower($planPadre[0][0]));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $resultado[$row][9];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $resultado[$row][10];

                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $resultado[$row][11];
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="modificar_GF_PLAN_INVENTARIO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
    <div class="modal fade" id="myModal" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Plan Inventario?</p>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No es posible eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarPlanInv(id) {
            var result = '';
            var form_data = {
                id_unico: id,
                action: 'delete'
            };
            $("#myModal").modal('show');
            $("#ver").click(function() {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "POST",
                    url: "controller/controllerGFPlanInventario.php",
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
            document.location = 'GF_PLAN_INVENTARIO.php';
        });

        $('#ver2').click(function() {
            document.location = 'GF_PLAN_INVENTARIO.php';
        });
    </script>
</body>

</html>