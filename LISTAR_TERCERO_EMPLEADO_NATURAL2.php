<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#24/08/2017 |Erica G. | Tarjeta Profesional
#######################################################################################################
require_once ('Conexion/conexion.php');
require 'Conexion/ConexionPDO.php';   
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$sql = "SELECT
    T.ID_UNICO ID_T ,
    T.NUMEROIDENTIFICACION NUMIDENT_T ,
    T.NOMBREUNO NOMU_T ,
    T.NOMBREDOS NOMD_T ,
    T.APELLIDOUNO APEU_T ,
    T.APELLIDODOS APED_T , 
    TI.ID_UNICO ID_TI ,
    TI.NOMBRE TIPO ,
    REG.ID_UNICO ID_REG ,
    REG.NOMBRE NOM_REG ,
    Z.ID_UNICO ID_Z ,
    Z.NOMBRE NOM_Z , 
    T.TARJETA_PROFESIONAL TPROF , 
    T.EMAILF AS EMAILF 
FROM GF_TERCERO T 
LEFT JOIN GF_TIPO_IDENTIFICACION TI ON T.TIPOIDENTIFICACION = TI.ID_UNICO 
LEFT JOIN GF_TIPO_REGIMEN REG ON T.TIPOREGIMEN = REG.ID_UNICO 
LEFT JOIN GF_ZONA Z ON T.ZONA = Z.ID_UNICO
LEFT JOIN GF_PERFIL_TERCERO PT ON PT.TERCERO = T.ID_UNICO
WHERE PT.PERFIL = 2 AND T.COMPANIA = $compania";
$stmt = oci_parse($oracle, $sql);        // Preparar la sentencia
$ok   = oci_execute( $stmt );            // Ejecutar la sentencia
 ?>
<title>Listar Empleado Natural</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px;">
 	        <h2 align="center" class="titulolista">
                    Empleado Natural
 		</h2>
        	<div class="table-responsive contTabla">
                    <div class="table-responsive contTabla">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                    <td class="cabeza"><strong>Número Identificación</strong></td>
                                    <td class="cabeza"><strong>Primer Nombre</strong></td>
                                    <td class="cabeza"><strong>Segundo Nombre</strong></td>
                                    <td class="cabeza"><strong>Primer Apellido</strong></td>
                                    <td class="cabeza"><strong>Segundo Apellido</strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                    <td class="cabeza"><strong>Zona</strong></td>
                                    <td class="cabeza"><strong>Tarjeta Profesional</strong></td>
                                    <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
                                </tr>                            
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Identificación</th>
                                    <th>Número Identificación</th>
                                    <th>Primer Nombre</th>
                                    <th>Segundo Nombre</th>
                                    <th>Primer Apellido</th>
                                    <th>Segundo Apellido</th>
                                    <th>Tipo Régimen</th>
                                    <th>Zona</th>
                                    <th>Tarjeta Profesional</th>
                                    <th>Corrreo Electrónico</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                            if( $ok == true ){
                              while  (($row=oci_fetch_assoc($stmt))!=false){ ?>
                                    <tr>
                                        <td class="oculto"><?php echo $row['ID_UNICO']?></td>
                                        <td class="campos">
                                            <a href="#" onclick="javascript:eliminarContrato(<?php echo $row['ID_T'];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=<?php echo $row['ID_T'];?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['TIPO']));?></td>
                                        <td class="campos"><?php echo $row['NUMIDENT_T'];?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMU_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMD_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['APEU_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['APED_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOM_REG']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOM_Z']));?></td>
                                        <td class="campos"><?php echo $row['TPROF'];?></td>
                                        <td class="campos"><?php echo $row['EMAILF'];?></td>
                                    </tr>
                                <?php  }
                                }?>
                            </tbody>
                        </table>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="left">
                                <button onclick="javascript:abrirMTerceroMenu()" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:4px;">Buscar Terceros</button>
                            </div> 
                        </div>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="right">
                                <a href="TerceroEmpleadoNatural2.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
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
              <p>¿Desea eliminar el registro seleccionado de Tercero Empleado Natural?</p>
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
            <button type="button" onclick="cerrar()" class="btn" style="" data-dismiss="modal" >Aceptar</button>
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
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarContrato(id) {
        
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                jsShowWindowLoad('Eliminando Informaci1ón...');
                var form_data = {action:3, perfil:2, id:id}
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
    <script type="text/javascript">
        function cerrar() {
            document.location.reload();
        }
    </script>
 </body>
 </html>