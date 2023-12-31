<?php
require_once './Conexion/conexion.php';
session_start();
require_once './head_listar.php';

$sql = "SELECT 		cs.id_unico,
                        cs.numero_contrato,
                        cs.unidad_vivienda_servicio,
                        uvs.id_unico,
                        uvs.unidad_vivienda,
                        uv.id_unico,
                        uv.tipo_unidad,
                        tu.id_unico,
                        tu.nombre,
                        uvs.tipo_servicio,
                        ts.id_unico,
                        ts.nombre,
                        cs.formato,
                        f.id_unico,
                        f.nombre,
                        cs.fecha_contrato
        FROM		gp_contrato_servicios cs	 
        LEFT JOIN	gp_unidad_vivienda_servicio uvs on cs.unidad_vivienda_servicio = uvs.id_unico
        LEFT JOIN	gp_unidad_vivienda uv 		on uvs.unidad_vivienda = uv.id_unico
        LEFT JOIN	gp_tipo_unidad_vivienda tu	on uv.tipo_unidad = tu.id_unico
        LEFT JOIN	gp_tipo_servicio ts 		on uvs.tipo_servicio = ts.id_unico
        LEFT JOIN	gf_formato f 			on cs.formato = f.id_unico";
$resultado = $mysqli->query($sql);
?>

<title>Listar Contrato Servicios</title>
</head>
<body>
      <div class="container-fluid text-center">
      <div class="row content">
      <?php require_once './menu.php'; ?>
           <div class="col-sm-10 text-left">
           <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Contrato Servicios</h2>
               <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                   <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                       <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                             <thead>
                                 <!--Cabecera de las columnas -->
                             <tr>
                                 <td style="display: none;">Identificador</td>
                                 <td width="7%" class="cabeza"></td>
                                 <td class=""><strong>Número Contrato</strong></td>
                                 <td class=""><strong>Unidad Vivienda - Servicio</strong></td>
                                 <td class=""><strong>Formato</strong></td>
                                 <td class=""><strong>Fecha</strong></td>
                             </tr>
                                <!--Nombres de los campos -->
                             <tr>
                                 <th style="display: none;">Identificador</th>
                                 <th width="7%" class="cabeza"></th>
                                 <th class=""><strong>Número Contrato</strong></th>
                                 <th class=""><strong>Unidad Vivienda - Servicio</strong></th>
                                 <th class=""><strong>Formato</strong></th>
                                 <th class=""><strong>Fecha</strong></th>
                             </tr>
                             </thead>
                             <?php 
                              while ($row = mysqli_fetch_row($resultado)) { ?>
                              <tr>
                                  <td style="display: none;"><?php echo $row[0]?></td>
                                     <td>
                                         <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                          <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                         </a>
                                         <a href="modificar_GP_CONTRATO_SERVICIOS.php?id=<?php echo md5($row[0]);?>">
                                          <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                         </a>
                                     </td>
                                  <td><?php echo($row[1])?></td>
                                  <td><?php echo($row[8].' - '.$row[11])?></td>
                                  <td><?php echo($row[14])?></td>
                                  <td><?php $fec = $row[15];
                                            $fec = trim($fec, '"');
                                            $fecha_div = explode("-", $fec);
                                            $aniof = $fecha_div[0];
                                            $mesf = $fecha_div[1];
                                            $diaf = $fecha_div[2];
                                            $fec = $diaf.'/'.$mesf.'/'.$aniof;
                                            echo($fec)?></td>
                                </tr>
                               <?php }
                               ?>
                                </tbody>
                            </table>
 
          <div align="right"><a href="registrar_GP_CONTRATO_SERVICIOS.php" class="btn btn-primary sombra" 
               style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 
               20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
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
          <p>¿Desea Eliminar el registro de Contrato Servicios?</p>
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
          <p>No se pudo eliminar la información.</p>
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
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarContratoServiciosJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
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
        document.location = 'listar_GP_CONTRATO_SERVICIOS.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GP_CONTRATO_SERVICIOS.php';
      });
    
  </script>

</body>
</html>