<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');

require_once('head_listar.php'); //consultas para la carga de campos
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$resultado =$con->Listar( "SELECT T.Id_Unico,
      TI.Nombre,
      T.NumeroIdentificacion, 
      T.NombreUno,
      T.NombreDos,
      T.ApellidoUno,
      T.ApellidoDos,                   
      TR.Nombre, 
      T.email 
FROM gf_tercero T  
INNER JOIN  gf_tipo_identificacion TI ON  T.TipoIdentificacion = TI.Id_Unico 
INNER JOIN  gf_tipo_regimen TR ON  T.TipoRegimen = TR.Id_Unico
LEFT JOIN gf_perfil_tercero  PT ON PT.tercero = T.Id_Unico
WHERE PT.perfil = 7  AND T.compania = $compania");

?>

<title>Listar Asociado Natural</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Asociado Natural</h2> 
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px" align="center"></td>
                                    <td class="cabeza"><strong>Tipo identificación</strong></td>
                                    <td class="cabeza"><strong>Número identificación</strong></td>
                                    <td class="cabeza"><strong>Primer Nombre</strong></td>
                                    <td class="cabeza"><strong>Segundo Nombre</strong></td>
                                    <td class="cabeza"><strong>Primer Apellido</strong></td>
                                    <td class="cabeza"><strong>Segundo Apellido</strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>                
                                    <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo identificación</th>
                                    <th>Número identificación</th>
                                    <th>Primer Nombre</th>
                                    <th>Segundo Nombre</th>
                                    <th>Primer Apellido</th>
                                    <th>Segundo Apellido</th>
                                    <th>Tipo Régimen</th>         
                                    <th>Corrreo Electrónico</th> 
                                </tr>

                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($resultado); $i++) { ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $resultado [$i][0] ?></td>
                                        <td class="campos">
                                            <a class="" href="#" onclick="javascript:eliminarAsociadoNat(<?php echo  $resultado [$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GF_ASOCIADO_NATURAL.php?action=1&id_asoNat=<?php echo base64_encode( $resultado [$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][1])) ?></td>
                                        <td class="campos"><?php echo ($resultado[$i][2]) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][3])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][4])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][5])) ?></td> 
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][6])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($resultado[$i][7])) ?></td>                          
                                        <td class="campos"><?php echo ($resultado[$i][8]) ?></td>                          
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="left">
                                <button onclick="javascript:abrirMTerceroMenu()" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:4px;">Buscar Terceros</button>
                            </div> 
                        </div>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="right">
                                <a href="modificar_GF_ASOCIADO_NATURAL.php?action=2" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Asociado Natural?</p>
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
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal3" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Se eliminó solo el perfil ya que el tercero tiene movimientos</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function cerrar(){
            document.location.reload();
        }
    </script>
    <script type="text/javascript">
        function eliminarAsociadoNat(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:7, id:id}
                $.ajax({
                  type:"POST",
                  url:"jsonPptal/gf_tercerosJson.php",
                  data: form_data,
                  success: function (data) {
                        jsRemoveWindowLoad();
                        console.log(data);
                        result = JSON.parse(data);
                        if(result==1){
                            $("#myModal1").modal('show');
                        } else if(result==2){ 
                            $("#myModal3").modal('show');
                        } else {
                             $("#myModal2").modal('show');
                        }
                  }
                });
            });
        }
    </script>
    
</body>
</html>

