<?php 
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes

require_once 'head_listar.php';
require_once('Conexion/conexionPDO.php');
//Consulta para el listado de registro de la tabla .
$con = new ConexionPDO();

switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_estado_chequera ORDER BY Nombre ASC");
    break;
    case '2':
      $row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_estado_chequera ORDER BY Nombre ASC");
    break;
}
 ?>
 	<title>Listar Estado Chequera</title>
 </head>
 <body>

 	<div class="container-fluid text-center">
 		
 		<div class="row content">
 			
 			<?php require_once 'menu.php';  ?>

 			<div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Estado Chequera</h2>
 				
 				<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
 				
 					<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
 						
 						<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
 								
 								<thead>
 								
 								<tr>
 									<td style="display: none;">Identificador</td>
 									<td width="30px" align="center"></td>
 									<td><strong>Nombre</strong></td>
 								</tr>

 								<tr>
 									<th style="display: none;">Identificador</th>
 									<th width="7%"></th>
                	<th>Nombre</th>
 								</tr>

 							</thead>
 							<tbody>
               <?php	
 							for ($i=0; $i <count($row) ; $i++) {?> 
               							<tr>

                							<td style="display: none;"><?php echo $row[$i][0]?></td>
                							<td>
                								<a href="#" onclick="javascript:eliminarEstadoChequera(<?php echo $row[$i][0];?>);">
                									<i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                								</a>
                								<a href="EDITAR_GF_ESTADO_CHEQUERA.php?id_estC=<?php echo ($row[$i][0]);?>">
                									<i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                								</a>
                							</td>
                							<td><?php echo ucwords(mb_strtolower($row[$i][1]));?></td>
                
              							</tr>
              					<?php } ?>

 							</tbody>	

 						</table>

 						<div align="right">
 							<a href="GF_ESTADO_CHEQUERA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px"/>
 								Registrar Nuevo
 							</a> 
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
          <p>¿Desea eliminar el registro seleccionado de Estado Chequera?</p>
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

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  
  <script type="text/javascript">
        function eliminarEstadoChequera(id)
        {
        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarEstadoChequeraJson.php?action==1",
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
        document.location = 'LISTAR_GF_ESTADO_CHEQUERA.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GF_ESTADO_CHEQUERA.php';
      });
    
  </script>	

 </body>
 </html>