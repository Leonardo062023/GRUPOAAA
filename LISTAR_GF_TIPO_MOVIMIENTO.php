<?php
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
require_once 'head_listar.php';

$compania = $_SESSION['compania'];
$resultado = $con->Listar("SELECT tm.id_unico, tm.nombre, tm.costea, c.id_unico, c.nombre, te.id_unico, te.nombre, tp.id_unico, tp.nombre, tpd.id_unico, tpd.nombre, tm.sigla 
            FROM gf_tipo_movimiento tm 
            LEFT JOIN gf_clase c ON tm.clase=c.id_unico 
            LEFT JOIN gs_tipo_elemento te ON tm.tipoelemento = te.id_unico 
            LEFT JOIN gs_tipo_persona tp ON tm.tipopersona=tp.id_unico 
            LEFT JOIN gf_tipo_documento tpd ON tm.tipo_documento= tpd.id_unico 
            WHERE     tm.compania = $compania");
?>
<title>Listar Tipo Movimiento</title>
<style>
    table.dataTable thead th,
    table.dataTable thead td {
        padding: 1px 18px;
    }

    .dataTables_wrapper .ui-toolbar {
        padding: 2px;
    }
</style>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px">Tipo Movimiento</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Costea</strong></td>
                                    <td><strong>Clase</strong></td>
                                    <td><strong>Tipo Elemento</strong></td>
                                    <td><strong>Tipo Persona</strong></td>
                                    <td><strong>Tipo Documento</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th><strong>Sigla</strong></th>
                                    <th><strong>Nombre</strong></th>
                                    <th><strong>Costea</strong></th>
                                    <th><strong>Clase</strong></th>
                                    <th><strong>Tipo Elemento</strong></th>
                                    <th><strong>Tipo Persona</strong></th>
                                    <th><strong>Tipo</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($row = 0; $row < count($resultado); $row++) { ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $resultado[$row][0] ?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $resultado[$row][0]; ?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="Modificar_GF_TIPO_MOVIMIENTO.php?action=1&id=<?php echo base64_encode($resultado[$row][0]); ?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                            </a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtoupper(($resultado[$row][11]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][1]))); ?></td>
                                        <td><?php if ($resultado[$row][2] == 1) {
                                                echo 'Sí';
                                            } else {
                                                echo 'No';
                                            } ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][4]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][6]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][8]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($resultado[$row][10]))); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="Modificar_GF_TIPO_MOVIMIENTO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo
                            </a>
                        </div>
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
                    <p>¿Desea eliminar el registro seleccionado de tipo movimiento?</p>
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
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- Función para la eliminación del registro. -->
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
                    url: "Json/modificar_GF_TIPO_MOVIMIENTOJson.php?action==1",
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

        function modal() {
            $("#myModal").modal('show');
        }

        $('#ver1').click(function() {
            document.location = 'LISTAR_GF_TIPO_MOVIMIENTO.php';
        });

        $('#ver2').click(function() {
            document.location = 'LISTAR_GF_TIPO_MOVIMIENTO.php';
        });
    </script>
</body>

</html>