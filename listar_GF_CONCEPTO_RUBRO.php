<?php
########## MODIFICACIONES ##############
#17/02/2017 | Erica G. *Modificación Búsqueda
########################################

require_once('Conexion/conexion.php');
require_once 'head_listar.php';
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
//consulta para cargar la informacion en la tabla
$resultado = $con->Listar("SELECT CR.Id_Unico, CR.Rubro, CR.Concepto, R.Nombre, C.Nombre, R.codi_presupuesto
FROM gf_concepto_rubro CR
LEFT JOIN gf_rubro_pptal R ON CR.Rubro = R.Id_Unico 
LEFT JOIN gf_concepto C ON CR.Concepto= C.Id_Unico 
WHERE C.parametrizacionanno = $anno AND R.parametrizacionanno = $anno 
ORDER BY R.codi_presupuesto ASC"); 


?>
<!--Titulo de página-->

<title>Listar Concepto Rubro</title>
</head>
<body>


  
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
<!--titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Concepto Rubro</h2>

      
        <!--Empieza la tabla -->
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>



              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px"></td>
                <td><strong>Código Rubro</strong></td>
                <td><strong>Rubro</strong></td>
                <td><strong>Concepto</strong></td>

                
              </tr>
              
               <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Código Rubro</th>
                <th>Rubro</th>
                <th>Concepto</th>

                
              </tr>

            </thead>
            <tbody>
              <!--muestra la informacion guardada en la base de datos junto a los iconos de eliminar y modificar-->
              <?php
                 for ($i = 0; $i < count( $resultado); $i++){?>
               <tr>
                <td style="display: none;"></td>
                <td><a class href="#" onclick="javascript:eliminar(<?php echo $resultado[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                <a href="Modificar_GF_CONCEPTO_RUBRO.php?action=1&id=<?php echo base64_encode($resultado[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                <td><?php echo ucwords((mb_strtolower($resultado[$i][5])));?></td>
                <td><?php echo ucwords((mb_strtolower($resultado[$i][3])));?></td>
                <td><?php echo ucwords((mb_strtolower($resultado[$i][4])));?></td>
                
                
                
              </tr>
              <?php } ?>


            </tbody>
          </table>
            <!--Boton que abre de nuevo el formulario de registro-->
          <div align="right"><a href="Modificar_GF_CONCEPTO_RUBRO.php?action=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

        </div>
        
       
      </div>
      
    </div>

  </div>
</div>
<!--Modal que confirmar la eliminación del registro-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de  Concepto Rubro?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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


  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<!--script que envia los datos para eliminar el registro-->
<script type="text/javascript">
      function eliminar(id){
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarConcepto_Rubro.php?action=1",
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
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_CONCEPTO_RUBRO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_CONCEPTO_RUBRO.php';
      });
    
  </script>
<?php require_once 'footer.php' ?>
</body>
</html>

