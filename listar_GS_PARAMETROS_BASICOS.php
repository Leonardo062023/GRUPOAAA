<?php
##Llamado a archivos necesarios
require_once ('head_listar.php');
require_once('Conexion/conexionPDO.php');
//Consulta para el listado de registro de la tabla .
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT * FROM gs_parametros_basicos  WHERE compania = $compania");
    break;
    case '2':
      $row = $con->Listar("SELECT * FROM gs_parametros_basicos  WHERE compania = $compania");
    break;
}
?>
<title>Listar Parámetros Básicos</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="text-left col-sm-10">
                <h2 class="titulolista" align="center" style="margin-top:0px">Parámetros Básicos</h2>				
                <div class="table-responsive contabla">
                    <div class="table-responsive contabla">
                        <table id="tabla" class="table table-stripe table-condensed display" cellpadding="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none">Identificador</td>
                                    <td width="30px"></td>
                                    <td class="cabeza"><strong>Indicador</strong></td>
                                    <td class="cabeza"><strong>Nombre</strong></td>
                                    <td class="cabeza"><strong>Valor</strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none">Identificador</th>
                                    <th width="7%"></th>
                                    <th class="cabeza">Indicador</th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                 for ($i=0; $i <count($row) ; $i++) {?>
                                    <tr>
                                        <td class="campos" style="display:none"></td>	
                                        <td class="campos">
                                            <a id="a_delete" class="campos" href="#" onclick="eliminarParametrosBasicos(<?php echo $row[$i][0]; ?>)">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a id="a_update" class="campos" href="modificar_GS_PARAMETROS_BASICOS.php?id=<?php echo md5($row[$i][0]); ?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo (($row[$i][1])) ?></td>
                                        <td class="campos"><?php echo (($row[$i][2])) ?></td>
                                        <td class="campos"><?php echo (($row[$i][3])) ?></td>
                                    </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="registrar_GS_PARAMETROS_BASICOS.php" class="btn btn-primary btnNuevoLista">Registrar Nuevo</a>
                        </div>
                    </div>
                </div>										
            </div>
        </div>
    </div>
<?php require_once 'footer.php' ?>
    <!-- Modales para eliminar -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Parámetros Básicos?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">

    <script src="js/bootstrap.min.js"></script>
    <script>
    function eliminarParametrosBasicos(id) {
        var result = " ";
        $("#myModal").modal('show');
        $("#ver").click(function () {
            $.ajax({
                type: 'GET',
                url: "json/eliminarParametrosBasicosJson.php?id=" + id,
                success: function (data) {
                    result = JSON.parse(data)
                    if (result == true)
                        $("#myModal1").modal('show');
                    else
                        $("#myModal2").modal('show');
                }
            });
        });
    }
    </script>
    <script>
        $('#ver1').click(function () {
            document.location.reload();
        });
        $('#ver2').click(function () {
            document.location.reload();
        });
    </script>
</body>
</html>
