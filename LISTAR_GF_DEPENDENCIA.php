<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');

require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$resultado = $con->Listar("SELECT d.id_unico, d.nombre, d.sigla, d.movimiento, d.activa , dep.nombre, cc.nombre, td.nombre , dep.sigla 
FROM gf_dependencia d 
LEFT JOIN gf_dependencia dep ON d.predecesor = dep.id_unico 
LEFT JOIN gf_centro_costo cc ON d.centrocosto=cc.id_unico 
LEFT JOIN gf_tipo_dependencia td ON d.tipodependencia=td.id_unico
WHERE d.compania = $compania");
?>
<title>Listar Dependencia</title>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Dependencia</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Movimiento</strong></td>
                                    <td><strong>Activa</strong></td>
                                    <td><strong>Predecesor</strong></td>
                                    <td><strong>Centro Costo</strong></td>
                                    <td><strong>Tipo Dependencia</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Sigla</th>
                                    <th>Movimiento</th>
                                    <th>Activa</th>
                                    <th>Predecesor</th>
                                    <th>Centro Costo</th>
                                    <th>Tipo Dependencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($resultado); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $resultado[$i][0] ?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminarItem(<?php echo $resultado[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="Modificar_GF_DEPENDENCIA.php?action=1&id=<?php echo base64_encode($resultado[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                        <td><?php echo ucwords(strtolower(($resultado[$i][1]))); ?></td>
                                        <td><?php echo strtoupper($resultado[$i][2]); ?></td>
                                        <td><?php if ($resultado[$i][3] == '0') {
                                                echo 'Sí';
                                            } elseif ($resultado[$i][3] == '1') {
                                                echo 'No';
                                            } ?></td>
                                        <td><?php if ($resultado[$i][4] == '0') {
                                                echo 'Sí';
                                            } elseif ($resultado[$i][4] == '1') {
                                                echo 'No';
                                            } ?></td>
                                        <td><?php echo ucwords(strtolower(($resultado[$i][8] . ' - ' . $resultado[$i][5]))); ?></td>
                                        <td><?php echo ucwords(strtolower(($resultado[$i][6]))); ?></td>
                                        <td><?php echo ucwords(strtolower(($resultado[$i][7]))); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"> <div align="right"><a href="Modificar_GF_DEPENDENCIA.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; 
                                              border-color: #1075C1; margin-top: 20px; 
                                              margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
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
                    <p>¿Desea eliminar el registro seleccionado de Dependencia?</p>
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
        function eliminarItem(id) {
            $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "controller/controllerGFDependencia.php?action=1",
                                data: form_data,
                                success: function(response) {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                      
                                            document.location.reload();
                                      
                                    } else if(response == 2){
                                        $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    } else {
                                        $("#mensaje").html('No se puede eliminar la información, ya que la actividad posee Seguimiento(s)');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
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
            document.location = 'LISTAR_GF_DEPENDENCIA.php';
        });
    </script>

    <script type="text/javascript">
        $('#ver2').click(function() {
            document.location = 'LISTAR_GF_DEPENDENCIA.php';
        });
    </script>

</body>

</html>