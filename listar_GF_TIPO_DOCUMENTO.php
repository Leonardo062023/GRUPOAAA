<?php 
require_once 'head_listar.php';
require_once('Conexion/conexionPDO.php');
$compania = $_SESSION['compania'];
//Consulta para el listado de registro de la tabla .
$con = new ConexionPDO();
switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT td.id_unico, td.nombre, td.es_obligatorio, td.consecutivo_unico, td.formato, f.nombre,
      ci.nombre, cd.nombre, td.vigencia FROM gf_tipo_documento td LEFT JOIN gf_clase_informe ci ON td.clase_informe = ci.id_unico
    LEFT JOIN gf_dependencia cd  ON td.dependencia = cd.id_unico LEFT JOIN gf_formato f ON td.formato = f.id_unico 
    WHERE td.compania =$compania");
    break;
    case '2':
      $row = $con->Listar("SELECT td.id_unico, td.nombre, td.es_obligatorio, td.consecutivo_unico, td.formato, f.nombre,
      ci.nombre, cd.nombre, td.vigencia FROM gf_tipo_documento td LEFT JOIN gf_clase_informe ci ON td.clase_informe = ci.id_unico
    LEFT JOIN gf_dependencia cd  ON td.dependencia = cd.id_unico LEFT JOIN gf_formato f ON td.formato = f.id_unico 
    WHERE td.compania =$compania");
    break;
}?>
<title>Listar Tipo Documento</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:5px">Tipo Documento</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="cabeza" style="display: none;">Identificador</td>
                                <td class="cabeza" width="30px"></td>
                                <td class="cabeza"><strong>Nombre</strong></td>
                                <td class="cabeza"><strong>Obligatorio</strong></td>
                                <td class="cabeza"><strong>Consecutivo Único</strong></td>
                                <td class="cabeza"><strong>Formato</strong></td>
                                <td class="cabeza"><strong>Clase Informe</strong></td>
                                <td class="cabeza"><strong>Dependencia</strong></td>
                                <td class="cabeza"><strong>Días de Vigencia</strong></td>
                            </tr>
                            <tr>
                                <th class="cabeza" style="display: none;">Identificador</th>
                                <th class="cabeza" width="7%"></th>
                                <th class="cabeza">Nombre</th>
                                <th class="cabeza">Obligatorio</th>
                                <th class="cabeza">Consecutivo Único</th>
                                <th class="cabeza">Formato</th>
                                <th class="cabeza">Clase Informe</th>
                                <th class="cabeza">Dependencia</th>
                                <th class="cabeza">Días de Vigencia</th>
                            </tr>
                        </thead> 
                        <tbody>
                        <?php for ($i=0; $i <count($row) ; $i++) {?>   <tr>
                            <td class="campos" style="display: none;"><?php echo $row[$i][0]?></td>
                            <td><a class="campos" href="#" onclick="javascript:eliminar(<?php echo $row[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                <a class="campos" href="Modificar_GF_TIPO_DOCUMENTO.php?id_cond=<?php echo ($row[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                            <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][1]));?></td>
                            <td class="campos"><?php   
                                    switch ($row[$i][2]){
                                        case ('1'):
                                            echo 'Sí';
                                        break;;
                                        case('2'):
                                            echo 'No';
                                        break;
                                        default :
                                            echo '';
                                        break;
                                    }
                                ?>
                            </td>
                            <td class="campos"><?php   
                                    switch ($row[$i][3]){
                                        case ('1'):
                                            echo 'Sí';
                                        break;;
                                        case('2'):
                                            echo 'No';
                                        break;
                                        default :
                                            echo '';
                                        break;
                                    }
                                ?>
                            </td>
                            <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][5]));?></td>
                            <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][6]));?></td>
                            <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][7]));?></td>
                            <td class="campos"><?php echo $row[$i][8];?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div align="right">
                        <a href="Registrar_GF_TIPO_DOCUMENTO.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal que confirma la eliminación del registro-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Tipo Documento?</p>
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
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
      function eliminar(id)
      {
        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipo_Documento.php?action==1",
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
        document.location = 'listar_GF_TIPO_DOCUMENTO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TIPO_DOCUMENTO.php';
      });
    
  </script>
<?php require_once 'footer.php' ?>
</body>
</html>

