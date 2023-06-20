<?php 
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];

$resultado =$con->Listar("SELECT P.Id_Unico, 
P.Anno, 
P.SalarioMinimo, 
P.MinDepreciacion, 
P.UVT, 
P.CajaMenor, 
E.Id_Unico, 
E.Nombre, 
P.minimacuantia, 
P.menorcuantia, 
P.menorcuantia_m, 
P.mayorcuantia 
FROM gf_parametrizacion_anno P 
LEFT JOIN gf_estado_anno E ON P.EstadoAnno = E.Id_Unico 
WHERE P.compania = $compania") ;

?>
<head>
    <title>Listar Parametrizacion Año</title>
</head>
    <body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Parametrización Año</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Año</strong></td>
                                    <td><strong>Salario Mínimo</strong></td>
                                    <td><strong>Mínimo Depreciación</strong></td>
                                    <td><strong>UVT</strong></td>
                                    <td><strong>Caja Menor</strong></td>
                                    <td><strong>Estado Año</strong></td>
                                    <td><strong>Mínima Cuantía</strong></td>
                                    <td><strong>Menor Cuantía</strong></td>
                                    <td><strong>Mayor Cuantía</strong></td>
                                    
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Año</th>
                                    <th>Salario Mínimo</th>
                                    <th>Mínimo Depreciación</th>
                                    <th>UVT</th>
                                    <th>Caja Menor</th>
                                    <th>Estado Año</th>
                                    <th>Mínima Cuantía</th>
                                    <th>Menor Cuantía</th>
                                    <th>Mayor Cuantía</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php for ($i = 0; $i < count( $resultado); $i++){?>
                                <tr>
                                    <td style="display: none;"><?php echo $resultado[$i][0]?></td>
                                    <td>
                                      <a href="#" onclick="javascript:eliminarParam(<?php echo $resultado[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                      <a href="modificar_GF_PARAMETRIZACION_ANNO.php?action=1&id_param=<?php echo base64_encode($resultado[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ($resultado[$i][1])?></td>
                                    <td><?php echo ($resultado[$i][2])?></td>
                                    <td><?php echo ($resultado[$i][3])?></td> 
                                    <td><?php echo ($resultado[$i][4])?></td> 
                                    <td><?php echo ($resultado[$i][5])?></td> 
                                    <td><?php echo ($resultado[$i][7])?></td> 
                                    <td><?php echo ($resultado[$i][8])?></td> 
                                    <td><?php echo ($resultado[$i][9].' - '.$resultado[$i][10])?></td> 
                                    <td><?php echo ($resultado[$i][11])?></td> 
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="modificar_GF_PARAMETRIZACION_ANNO.php?action=2" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
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
              <p>¿Desea eliminar el registro seleccionado de Parametrización Año?</p>
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
              <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
      function eliminarParam(id)
      {
        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarParamAnnoJson.php?action=1",
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
      function modal()
      {
         $("#myModal").modal('show');
      }
    </script>
    <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_PARAMETRIZACION_ANNO.php';
      });
    </script>
    <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_PARAMETRIZACION_ANNO.php';
      });
    </script>
</body>
</html>

