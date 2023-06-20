<?php
require_once 'head_listar.php'; 
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();


 //trae toda la informacion que esta en la base de datos ordenadas por nombre
$queryContrato = $con->Listar( "SELECT C.Id_Unico, C.Nombre, TC.Nombre
FROM gf_clase_contrato C, gf_tipo_contrato TC
WHERE C.TipoContrato = TC.Id_Unico ORDER BY C.Nombre ASC"); 


?>
<!--Titulo de la página-->

<title>Listar Clase Contrato</title>

</head>
<body>
  
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php';?>
    <div class="col-sm-9 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Clase Contrato</h2>

      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>


            <!--Titulos de la tabla-->
              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px"></td>
                <td><strong>Nombre</strong></td>
                <td><strong>Tipo Contrato</strong></td>

                
              </tr>
              <tr>
              	<th style="display:none"></th>
              	<th width="30px"></th>
              	<th>Nombre</th>
              	<th>Tipo Contrato</th>
              </tr>

            </thead>
            <tbody>
              <!--Muestra la información que esta guardada en la base de datos junto a los iconos de eliminar y modificar-->
              <?php
                 for ($i = 0; $i < count($queryContrato); $i++){?>
               <tr>
                <td style="display: none;"><?php echo $queryContrato[$i][0]?></td>
                
                                            </a>
                <td><a class"" href="#" onclick="javascript:eliminarContrato(<?php echo $queryContrato[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                <a href="modificarContrato.php?action=1&id_contrato=<?php echo base64_encode($queryContrato[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                <td><?php echo ucwords((strtolower($queryContrato[$i][1])))?></td>
                <td><?php echo ucwords((strtolower($queryContrato[$i][2])))?></td>
                
                
                
              </tr>
              <?php } ?>


            </tbody>
          </table>
          <!--Abre el formulaario de registro-->
          
          <div align="right"><a href="modificarContrato.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; 
                                              border-color: #1075C1; margin-top: 20px; 
                                              margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>

        </div>
        
       
      </div>
      
    </div>

  </div>
</div>
<!--Modal que confirma la eliminacion del registro-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Contrato?</p>
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
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<?php require_once 'footer.php';?>
<!--links de estilo de la pagina-->
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Script que envia los datos necesarios para la eliminacion del registro-->
<script type="text/javascript">
           function eliminarContrato(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarContratoJson.php?action=1",
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
  
  <!--metodo para refrescar la paginá-->
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar.php';
      });
    
  </script>

</body>
</html>

