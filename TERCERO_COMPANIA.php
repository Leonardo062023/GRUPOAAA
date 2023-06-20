<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#21/09/2017 |Erica G. | Agregar Campo Tipo Compañia (1-Pública, 2- Privada)
#######################################################################################################
require_once('Conexion/conexion.php');
@session_start();
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$queryTerceroComp = "SELECT 
T.ID_UNICO , 
T.RAZONSOCIAL , 
T.NUMEROIDENTIFICACION , 
T.DIGITOVERFICACION , 
TI.NOMBRE AS NOMBRE_IDEN, 
S.NOMBRE, 
T.REPRESENTANTELEGAL, 
CI.NOMBRE AS NOMBRE_C, 
TR.NOMBRE AS NOMBRE_RE, 
T.CONTACTO , 
TE.NOMBRE, 
TIPEN.NOMBRE AS NOMBRE_TIPEN, 
T.CODIGO_DANE , 
T.TIPO_COMPANIA, 
TRL.NOMBREUNO || ' ' || TRL.NOMBREDOS || ' ' || TRL.APELLIDOUNO || ' ' || 
            TRL.APELLIDODOS || ' '  || ' ' || TRL.RAZONSOCIAL || ' ' || 
            TRL.NUMEROIDENTIFICACION  AS NOMBRE_REP,
 T.EMAILF, D.NOMBRE, 
 T.DISTRIBUCION_COSTOS 
FROM GF_TERCERO T
LEFT JOIN GF_TIPO_IDENTIFICACION TI ON T.TIPOIDENTIFICACION = TI.ID_UNICO
LEFT JOIN GF_SUCURSAL S ON T.SUCURSAL = S.ID_UNICO
LEFT JOIN GF_TIPO_REGIMEN TR ON T.TIPOREGIMEN = TR.ID_UNICO
LEFT JOIN GF_TIPO_EMPRESA TE ON T.TIPOEMPRESA = TE.ID_UNICO
LEFT JOIN GF_TIPO_ENTIDAD TIPEN ON T.TIPOENTIDAD = TIPEN.ID_UNICO
LEFT JOIN GF_CIUDAD CI ON T.CIUDADIDENTIFICACION = CI.ID_UNICO
LEFT JOIN GF_TERCERO TRL ON TRL.ID_UNICO = T.REPRESENTANTELEGAL 
LEFT JOIN GF_TERCERO TRC ON TRC.ID_UNICO = T.CONTACTO 
LEFT JOIN GF_PERFIL_TERCERO PT ON T.ID_UNICO = PT.TERCERO 
LEFT JOIN GF_PERFIL P ON PT.PERFIL = P.ID_UNICO 
LEFT JOIN GF_DEPARTAMENTO D ON CI.DEPARTAMENTO = D.ID_UNICO 
WHERE P.ID_UNICO = 1 AND T.COMPANIA=$compania";

$stmt = oci_parse($oracle, $queryTerceroComp);        // Preparar la sentencia
$ok   = oci_execute( $stmt );            // Ejecutar la sentencia

?>
<title>Listar Compañía</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php   require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 class="titulolista" align="center" >Compañía</h2>
                <div class="table-responsive" >
                    <div class="table-responsive" >
                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"</td>
                                    <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                    <td class="cabeza"><strong>Número Identificación</strong></td>
                                    <td class="cabeza"><strong>Sucursal</strong></td>
                                    <td class="cabeza"><strong>Razón Social</strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                    <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                    <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                    <td class="cabeza"><strong>Representante Legal</strong></td>
                                    <td class="cabeza"><strong>Ciudad Identificación</strong></td>
                                    <td class="cabeza"><strong>Contacto</strong></td>
                                    <td class="cabeza"><strong>Código DANE</strong></td>
                                    <td class="cabeza"><strong>Tipo Compañia</strong></td>
                                    <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
                                    <td class="cabeza"><strong>Distribución de costos</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Identificación</th>
                                    <th>Número Identificación</th>
                                    <th>Sucursal</th>
                                    <th>Razón Social</th>
                                    <th>Tipo Régimen</th>
                                    <th>Tipo Empresa</th>
                                    <th>Tipo Entidad</th>
                                    <th>Representante Legal</th>
                                    <th>Ciudad Identificación</th>
                                    <th>Contacto</th>               
                                    <th>Código DANE</th>               
                                    <th>Tipo Compañia</th>    
                                    <th>Corrreo Electrónico</th> 
                                    <th>Distribución de costos</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                
                        if( $ok == true ){
    //echo '<option value="' . $file['ID_UNICO'] . '">' . ucwords(mb_strtolower($file['RAZONSOCIAL'])) . ' - ' . $file['NUMEROIDENTIFICACION']. '</option>';
                         while  (($row=oci_fetch_assoc($stmt))!=false){ ?>
                                    <tr>
                                        <td class="oculto"><?php echo $row['ID_UNICO'] ?></td>
                                        <td class="campos">
                                            <a href="#" onclick="javascript:eliminarTerComp(<?php echo $row['ID_UNICO']; ?>);"><li title="Eliminar" class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a href="modificar_TERCERO_COMPANIA.php?id_ter_comp=<?php echo md5($row['ID_UNICO']); ?>"><li title="Modificar" class="glyphicon glyphicon-edit" ></li></a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_IDEN'])); ?></td>
                                        <td class="campos"><?php
                                            echo $row['NUMEROIDENTIFICACION'] . ' - ' . $row['DIGITOVERFICACION'];
                                            ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE'])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['RAZONSOCIAL'])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_RE'])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_TIPEN'])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_5'])); ?></td>
                                        <td class="campos">
                                            <?php if (!empty($row['REPRESENTANTELEGAL'])) {
                                                echo ucwords(mb_strtolower($row['NOMBRE_REP']));
                                            } ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_C'].' - '.$row['NOMBRE'])) ?></td>
                                        <td class="campos">
                                            <?php if (!empty($row['CONTACTO'])) {
                                                echo ucwords(mb_strtolower($row['NOMBRE_REP']));
                                            } ?>
                                        </td>
                                        <td class="campos">
                                            <?php if (!empty($row['CODIGO_DANE'])) {
                                                echo mb_strtoupper($row['CODIGO_DANE']);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($row['TIPO_COMPANIA'])) {
                                                if ($row['TIPO_COMPANIA'] == '2') {
                                                    echo 'Privada';
                                                } else {
                                                    echo 'Pública';
                                                }
                                            } else {
                                                echo 'Pública';
                                            }?>
                                        </td>    
                                        <td class="campos"><?php echo $row['EMAILF']; ?></td>
                                        <td class="campos"><?php 
                                        if($row['DISTRIBUCION_COSTOS']==1){
                                            echo 'Sí'; 
                                        } else {echo 'No';};
                                        ?></td>
                                    </tr>
                                <?php }
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="left">
                                <button onclick="javascript:abrirMTerceroMenu()" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:4px;">Buscar Terceros</button>
                            </div> 
                        </div>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="right">
                                <a href="registrar_TERCERO_COMPANIA.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
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
                    <p>¿Desea eliminar el registro seleccionado de Compañia?</p>
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
                    <button type="button" onclick="cerrar()"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
        function eliminarTerComp(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:1, id:id}
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
        function cerrar(){
            document.location.reload();
        }
    </script>
</body>
</html>

