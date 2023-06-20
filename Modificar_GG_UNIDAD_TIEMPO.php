<?php 
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head.php';
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
$id=$_GET['id'];
switch ($_SESSION['conexion']) {
    case '1':
      $rowTipo = $con->Listar("SELECT id_unico, nombre FROM gg_unidad_tiempo WHERE md5(id_unico)= '$id'");
    break;
    case '2':
      $rowTipo = $con->Listar("SELECT id_unico, nombre FROM gg_unidad_tiempo WHERE (id_unico)= '$id'");
  break;
  }
?>
<title>Modificar Unidad Tiempo</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Unidad Tiempo</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                    <?php for ($i=0; $i <count($rowTipo) ; $i++) {?>
                       <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <input name="id" id="id" type="hidden" value="<?php echo $rowTipo[$i][0]?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo ucwords(mb_strtolower($rowTipo[$i][1]));?>">
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
  <!--  MODAL para los mensajes del  modificar -->
  <div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal6" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<div class="modal fade" id="myModal7" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El registro ingresado ya existe..</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProcesos/registrar_GG_UNIDAD_TIEMPOJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                      $("#myModalUpdate").modal('hide');
                                      $("#myModal5").modal('show');
                                      $("#ver5").click(function(){
                                        $("#myModal5").modal('hide');
                                        document.location = 'LISTAR_GG_UNIDAD_TIEMPO.php';
                                    });
                                       
                                    } else {
                                      $("#myModalUpdate").modal('hide'); 
                                      $("#myModal7").modal('show');
                                      $("#ver7").click(function(){

                                      $("#myModal6").modal('hide');
                                      document.location = 'Modificar_GG_UNIDAD_TIEMPO.php?id=<?php echo ($id);?>';
                                      });
                                }
                                }
                            });
                        }
                    </script>

    <?php require_once 'footer.php';?>
</body>
</html>

