<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once 'head_listar.php';
	//llamado de la clase de conexion
  require_once('Conexion/conexionPDO.php');
  //Consulta para el listado de registro de la tabla .
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  switch ($_SESSION['conexion']) {
      case '1':
        $row = $con->Listar("SELECT Id_Unico, Nombre,equivalente_NE FROM gf_tipo_cuenta ORDER BY Nombre ASC");
      break;
      case '2':
        $row = $con->Listar("SELECT Id_Unico, Nombre,equivalente_NE FROM gf_tipo_cuenta ORDER BY Nombre ASC");
      break;
  }
  
 ?>
 <!-- Titulo del formulario -->
 <title>Listar Tipo Cuenta</title>	
 <!-- Fin de titulo del formulario -->
 </head>
 <body>
 	<!-- Contnedor principal -->
 	<div class="container-fluid text-center">
 		<!-- Contenedor y fila de contenido -->
 		<div class="row content">
 			<!-- Lllamado al menu -->
 			<?php require_once 'menu.php'; ?>
      <!-- Contenedor del formulario -->
 			<div class="col-sm-10 text-left">
 			  <!-- Titulo del formulario-->
 			  <h2 id="forma-titulo3" align="center" style="margin-bottom:20px; margin-right:4px; margin-left:4px;">Tipo Cuenta</h2>
        <!-- Fin de Titulo de formulario -->
        <!-- Incio de Primer contnedor responsive -->
 				<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
 					<!-- Inicio de segundo contenedor responsive -->
 					<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
 					  <!-- Inicio de tabla de listar -->
 						<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
 							<!-- Inicio de cabeza de  formulario -->
 							<thead>
 								<!-- Inicio de campos de filtrado-->
 								<tr>
 									<td style="display: none;">Identificador</td>
 									<td width="30px" align="center"></td>
 									<td><strong>Nombre</strong></td>
                   <td><strong>Equivalente Nómina Electrónica:</strong></td>
 								</tr>
                <!-- Fin de campos de filtrado -->
                <!-- Inicio de titulos de la tabla-->
 								<tr>
 									<th style="display: none;">Identificador</th>
 									<th width="7%"></th>
                	<th>Nombre</th>
                  <th>Equivalente Nómina Electrónica:</th>
 								</tr>
                <!-- Fin de titulos de la tabla -->
                <!-- Fin de cabeza de tabla-->
 							</thead>
              <!-- Inicio del cuerpo de la tabla -->
 							<tbody>
 								
               <?php
                //Ciclo de impresión de registros existentes
               for ($i=0; $i <count($row) ; $i++) {?>
               			<tr>

                			<td style="display: none;"><?php echo $row[$i][0]?></td>
                			<td>
                					<a href="#" onclick="javascript:eliminarTipoCuenta(<?php echo $row[$i][0];?>);">
                								<i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                					</a>
                					<a href="EDITAR_GF_TIPO_CUENTA.php?id_tipoC=<?php echo ($row[$i][0]);?>">
                								<i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                					</a>
                			</td>
                			<td><?php echo ucwords(mb_strtolower($row[$i][1]));?></td>
                      <td><?php echo ($row[$i][2]);?></td>
                
              		  </tr>
              	<?php } 
                //Fin ciclo de impresión de registros existentes
                ?>
              <!-- Fin de cuerpo de la tabla -->
 							</tbody>	
            <!-- Fin de tabla -->
 						</table>
            <!-- Inicio de bóton de nuevo registro -->
 						<div align="right">
              <!-- Inico de etiqueta de nuevo registro-->
 							<a href="GF_TIPO_CUENTA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">
 								Registrar Nuevo
              <!-- Fin de etiqueta de nuevo registro -->
 							</a> 
 						</div>
            <!-- Fin de bóton de nuevo registro -->
          <!-- Fin de Segundo contenedor responsive -->
 					</div>
        <!-- Fin de Primer contenedor responsive -->
 				</div>
      <!-- Fin de contenedor del formulario -->
 			</div>
      <!-- Fin de fila de contenido -->
 		</div>
  <!-- Fin de contenedor principal -->
 	</div>
<!-- Inicio de Modal-->
 	<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Cabeza de Modal -->
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <!-- Fin de Modal -->
        <!-- Inicio de Cuerpo modal -->
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tipo Cuenta?</p>
        </div>
        <!-- Inicio de Pie de modal-->
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
        <!-- Fin de Pie de modal-->
      </div>
      <!-- Fin de Cuerpo modal-->
    </div>
  </div>
  <!-- Fin de Modal -->
  <!-- Inicio de Modal -->
  <div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <!-- Inicio de Cuerpo de modal-->
      <div class="modal-content">
      <!-- Inicio de Cabeza de modal-->
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <!-- Fin de cabeza de  modal-->
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <!-- Inicio pie de modal -->
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
        <!-- Fin pie de modal-->
      </div>
      <!-- Fin de Cuerpo de modal -->
    </div>
  </div>
  <!-- Inicio modal -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
    <!-- Inicio contenido modal-->
      <div class="modal-content">
      <!-- Inicio cabeza modal-->
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <!-- Fin cabeza modal -->
        <!-- Inicio cuerpo de modal-->
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <!-- Fin de cuerpo modal-->
        <!-- Inicio pie modal-->
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
        <!-- Fin de modal -->
        <!-- Fin contenido modal-->
      </div>
    </div>
  </div>
<!-- Fin de modal -->
  <?php require_once 'footer.php'; ?>
  <!-- Llamado de librerias -->
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <!-- Fin de llamado de librerias-->
  <script type="text/javascript">
    function eliminarTipoCuenta(id)
    {
        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipoCuentaJson.php?action==1",
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
      //Función para mostrar modal
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
      //Función para redirigir modal 1 al formulario listar
      $('#ver1').click(function(){
        document.location = 'LISTAR_GF_TIPO_CUENTA.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GF_TIPO_CUENTA.php';
      });
    
  </script>

 </body>
 </html>