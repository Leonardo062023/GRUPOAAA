<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#14/12/2017 | Parametrizacion
########################################################################################
require_once('head_listar.php');
require_once('Conexion/ConexionPDO.php');

$con = new ConexionPDO();
$anno=$_SESSION['anno'];
$conexion=$_SESSION['conexion'];

switch ($_SESSION['conexion']) {
    case 1:
        $row = $con->Listar("SELECT cc.id_unico,  
        cc.nombre, 
        cc.movimiento, 
        cc.sigla, 
        tc.nombre, 
        CONCAT(UPPER(pr.sigla), ' - ', LOWER(pr.nombre) ),
        cs.nombre, 
        cc.cantidad_distribucion  
        FROM gf_centro_costo cc  
        LEFT JOIN gf_tipo_centro_costo tc ON tc.id_unico = cc.tipocentrocosto 
        LEFT JOIN gf_centro_costo pr ON pr.id_unico = cc.predecesor 
        LEFT JOIN gf_clase_servicio cs ON cs.id_unico = cc.claseservicio 
        WHERE cc.parametrizacionanno = $anno");
        break;
    case 2:
        $row = $con->Listar("SELECT cc.id_unico,  
        cc.nombre, 
        cc.movimiento, 
        cc.sigla, 
        tc.nombre, 
        CONCAT( CONCAT( UPPER(pr.sigla), ' - '), LOWER(pr.nombre) ),
        cs.nombre, 
        cc.cantidad_distribucion  
        FROM gf_centro_costo cc  
        LEFT JOIN gf_tipo_centro_costo tc ON tc.id_unico = cc.tipocentrocosto 
        LEFT JOIN gf_centro_costo pr ON pr.id_unico = cc.predecesor 
        LEFT JOIN gf_clase_servicio cs ON cs.id_unico = cc.claseservicio 
        WHERE cc.parametrizacionanno = $anno");
        break;

    default:
        # code...
        break;
}

$cd = 0;
?>
<title>Listar Centro Costo</title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Centro Costo</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Movimiento</strong></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Tipo Centro Costo</strong></td>
                                    <td><strong>Predecesor</strong></td>
                                    <td><strong>Clase Servicio</strong></td>
                                    <td><strong>Cantidad Distribución</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Movimiento</th>
                                    <th>Sigla</th>
                                    <th>Tipo Centro Costo</th>
                                    <th>Predecesor</th>
                                    <th>Clase Servicio</th>
                                    <th>Cantidad Distribución</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($row); $i++) {
                                ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[$i][0] ?></td>
                                        <td>
                                            
                                            <a href="#" onclick="javascript:eliminarCentCost(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            
                                            <a href="modificar_CENTRO_COSTO.php?action=1&id=<?php echo base64_encode($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][1])) ?></td>
                                        <td>
                                            <?php
                                            //2 es Sí y 1 es No.
                                            if ($row[$i][2] == 1)
                                                echo "Sí";
                                            else
                                                echo "No";
                                            ?>

                                        </td>
                                        <td><?php echo mb_strtoupper($row[$i][3]) ?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][4])); ?></td>
                                        <td><?php echo ucwords(($row[$i][5])); ?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][6])); ?></td>
                                        <td><?php echo $row[$i][7] ?></td>

                                    </tr>
                                <?php $cd += $row[$i][7];
                                } ?>

                            </tbody>
                        </table>
                        <label>Cantidad Distribución Total: <?php echo number_format($cd, 0, '.', ','); ?></label>
                        <div align="right"><a href="modificar_CENTRO_COSTO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; 
                                              border-color: #1075C1; margin-top: 20px; 
                                              margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>

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
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Centro Costo?</p>
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
                    <p>
                        No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.
                    </p>
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
    <!-- Script que redirige a la página inicial de Centro Costo. -->
    <script type="text/javascript">
         function eliminarCentCost(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificar_CENTRO_COSTOJson.php?action=1",
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
    </script>
    <script type="text/javascript">
        function modal() {
            $("#myModal").modal('show');
        }
    </script>

    <script type="text/javascript">
        $('#ver1').click(function() {
            document.location = 'CENTRO_COSTO.php';
        });
    </script>

    <script type="text/javascript">
        $('#ver2').click(function() {
            document.location = 'CENTRO_COSTO.php';
        });
    </script>

</body>

</html>