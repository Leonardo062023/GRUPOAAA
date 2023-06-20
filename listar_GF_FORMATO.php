<?php
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
@session_start();
$compania = $_SESSION['compania'];
require_once('Conexion/conexionPDO.php');
//Consulta para el listado de registro de la tabla .
$con = new ConexionPDO();

switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT fot.id_unico, fot.nombre, fot.codigo_calidad, fot.version, TO_CHAR(fot.fechaversion,'DD-MM-YYYY'),
      fot.esCheque, fot.descripcion, p.ruta FROM gf_formato fot LEFT JOIN gf_plantilla p ON fot.plantilla = p.id_unico 
      WHERE compania = $compania");
    break;
    case '2':
      $row = $con->Listar("SELECT fot.id_unico, fot.nombre, fot.codigo_calidad, fot.version, TO_CHAR(fot.fechaversion,'DD-MM-YYYY'),
      fot.esCheque, fot.descripcion, p.ruta FROM gf_formato fot LEFT JOIN gf_plantilla p ON fot.plantilla = p.id_unico 
      WHERE compania = $compania");
    break;
}

?>
<title>Listar Formato</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="text-left col-sm-10">
                <h2 class="titulolista" align="center" style="margin-top:0px">Formato</h2>				
                <div class="table-responsive contabla">
                    <div class="table-responsive contabla">
                        <table id="tabla" class="table table-stripe table-condensed display" cellpadding="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none">Identificador</td>
                                    <td width="30px"></td>
                                    <td class="cabeza"><strong>Nombre</strong></td>
                                    <td class="cabeza"><strong>Código Calidad</strong></td>
                                    <td class="cabeza"><strong>Versión</strong></td>
                                    <td class="cabeza"><strong>Fecha versión</strong></td>
                                    <td class="cabeza"><strong>Es formato de cheque?</strong></td>
                                    <td class="cabeza"><strong>Descripción</strong></td>										
                                    <td class="cabeza"><strong>Plantilla</strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none">Identificador</th>
                                    <th width="7%"></th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza">Código Calidad</th>
                                    <th class="cabeza">Versión</th>
                                    <th class="cabeza">Fecha versión</th>
                                    <th class="cabeza">Es formato de cheque?</th>
                                    <th class="cabeza">Descripción</th>
                                    <th class="cabeza">Plantilla</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php	
 							for ($i=0; $i <count($row) ; $i++) {?> 
                                <tr>
                                    <td class="campos" style="display:none"></td>	
                                    <td class="campos">
                                        <a class="campos" href="#<?php echo $row[$i][0]; ?>" onclick="eliminarFormato(<?php echo $row[$i][0]; ?>)">
                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        <a class="campos" href="modificar_GF_FORMATO.php?id=<?php echo ($row[$i][0]) ?>">
                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                        </a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][1])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][2])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][3])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][4])) ?></td>
                                    <?php
                                    $esCheque = "";
                                    if (!empty($row[$i][5])) {
                                        if($row[$i][5]==1) {
                                            $esCheque = 'SI';
                                        } else { 
                                            $esCheque = 'NO';
                                        }
                                    } else {
                                        $esCheque = 'NO';
                                    }
                                    ?>											
                                    <td class="campos"><?php echo ucwords(mb_strtolower($esCheque)) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[$i][6])) ?></td>
                                    <td class="campos">
                                        <?php if(!empty($row[$i][7])){?>
                                        <a href="<?php echo 'documentos/plantillas/'.$row[$i][7]; ?>" target="_blank"><i title="Ver" class="glyphicon glyphicon-search"></i></a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="registrar_GF_FORMATO.php"  class="btn btn-primary btnNuevoLista">Registrar Nuevo</a>
                        </div>
                    </div>
                </div>										
            </div>
        </div>
    </div>
    <?php require_once 'footer.php' ?>
</body>
<div style="margin-top: -10px">
    <?php require_once('footer.php'); ?>
</div>
<!-- Modales para eliminar -->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Formato?</p>
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
<script src="js/bootstrap.min.js"></script>
<script>
                        function eliminarFormato(id) 
                        {
                            $("#myModal").modal('show');
                            $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "Json/procesos_GF_FORMATO.php?action==1",
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
<script>
    $('#ver1').click(function () {
        document.location.reload();
    });
    $('#ver2').click(function () {
        document.location.reload();
    });
</script>
</html>
