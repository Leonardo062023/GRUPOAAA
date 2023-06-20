<?php 
##############MODIFICACIONES #########################
#23/03/2017 | ERICA G. | MODIFICACION CONSULTA

#Historial de Actualizaciones  (ctl+F con la fecha para acceso rápido)
#     08/02/2017 - Daniel N: Creación del archivo, trae todos los campos de gf_tercero pero visualiza los del informe Listado.
 
require_once './head_listar.php';
require_once ('./Conexion/conexion.php');

$compania = $_SESSION['compania'];
  $sql = "SELECT TR.NOMBREUNO || ' ' || TR.NOMBREDOS || ' ' || TR.APELLIDOUNO || ' ' || 
  TR.APELLIDODOS || ' '  || ' ' || TR.RAZONSOCIAL || ' ' || 
  TR.NUMEROIDENTIFICACION  AS NOMBRE_T,
TR.ID_UNICO AS ID_TER,
TR.NUMEROIDENTIFICACION,
TR.DIGITOVERFICACION,
TI.NOMBRE AS NOMBRE_IDE, 
TRC.NOMBREUNO || ' ' || TRC.NOMBREDOS || ' ' || TRC.APELLIDOUNO || ' ' || 
  TRC.APELLIDODOS || ' '  || ' ' || TRC.RAZONSOCIAL || ' ' || 
  TRC.NUMEROIDENTIFICACION  AS NOMBRE_C,
TRR.NOMBREUNO || ' ' || TRR.NOMBREDOS || ' ' || TRR.APELLIDOUNO || ' ' || 
  TRR.APELLIDODOS || ' '  || ' ' || TRR.RAZONSOCIAL || ' ' || 
  TRR.NUMEROIDENTIFICACION  AS NOMBRE_R,
TR.CARGO, 
TE.NOMBRE AS NOMBRE_TE, 
TRE.NOMBRE AS NOMBRE_TR,
TEN.NOMBRE AS NOMBRE_TEN,
Z.NOMBRE AS NOMBRE_Z, 
P.ID_UNICO AS PERFIL, 
P.NOMBRE AS NOMBRE_P 
FROM
GF_TERCERO TR
LEFT JOIN
GF_TIPO_IDENTIFICACION TI ON TI.ID_UNICO = TR.TIPOIDENTIFICACION
LEFT JOIN 
GF_TERCERO TRC ON TRC.ID_UNICO = TR.CONTACTO
LEFT JOIN 
GF_TERCERO TRR ON TRR.ID_UNICO = TR.REPRESENTANTELEGAL
LEFT JOIN 
GF_TIPO_EMPRESA TE ON TE.ID_UNICO = TR.TIPOEMPRESA 
LEFT JOIN 
GF_TIPO_ENTIDAD TEN ON TR.TIPOENTIDAD = TEN.ID_UNICO 
LEFT JOIN 
GF_TIPO_REGIMEN TRE ON TR.TIPOREGIMEN = TRE.ID_UNICO 
LEFT JOIN 
GF_ZONA Z ON TR.ZONA = Z.ID_UNICO 
LEFT JOIN 
GF_PERFIL_TERCERO PT ON PT.TERCERO = TR.ID_UNICO 
LEFT JOIN 
GF_PERFIL P ON PT.PERFIL = P.ID_UNICO 
WHERE TR.COMPANIA = $compania and rownum <= 20";

$stmt = oci_parse($oracle, $sql);        // Preparar la sentencia
$ok   = oci_execute( $stmt );            // Ejecutar la sentencia

?>
    <title>Listado General de Terceros</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Terceros</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="10%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Perfil</strong></td>
                                        <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                        <td class="cabeza"><strong>Identificación</strong></td>
                                        <td class="cabeza"><strong>Razón Social / Nombre</strong></td>
                                        <td class="cabeza"><strong>Contacto</strong></td>
                                        <td class="cabeza"><strong>Representante Legal</strong></td>
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                        <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                        <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                        <td class="cabeza"><strong>Zona</strong></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="10%"></th>                                        
                                        <th class="cabeza">Perfil</th>
                                        <th class="cabeza">Tipo Identificación</th>
                                        <th class="cabeza">Identificación</th>
                                        <th class="cabeza">Razón Social / Nombre</th>
                                        <th class="cabeza">Contacto</th>
                                        <th class="cabeza">Representante Legal</th>
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Tipo Empresa</th>
                                        <th class="cabeza">Tipo Entidad</th>
                                        <th class="cabeza">Tipo Régimen</th>
                                        <th class="cabeza">Zona</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                  if( $ok == true ){
                                     while  (($row=oci_fetch_assoc($stmt))!=false){
                                    #Igualación de perfil tercero a variable
                                    $pert = $row['PERFIL'];
                                    $arg = 0;
                                    #Selección de formulario 
                                    switch ($row['PERFIL'])
                                    {
                                        case 1:
                                            $arg = 'modificar_TERCERO_COMPANIA.php?id_ter_comp=';
                                        break;
                                        case 2:
                                            $arg = 'EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=';
                                        break;
                                        case 3:
                                            $arg = 'modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat=';
                                        break;
                                        case 4:
                                            $arg = 'modificar_TERCERO_CLIENTE_JURIDICA.php?id_ter_clie_jur=';
                                        break;
                                        case 5:
                                            $arg = 'EDITAR_TERCERO_PROVEEDOR_NATURAL_2.php?id=';
                                        break;
                                        case 6:
                                            $arg = 'EDITAR_TERCERO_PROVEEDOR_JURIDICA_2.php?id=';
                                        break;
                                        case 7:
                                            $arg = 'modificar_GF_ASOCIADO_NATURAL.php?id_asoNat=';
                                        break;
                                        case 8:
                                            $arg = 'modificar_GF_ASOCIADO_JURIDICA.php?id_asociadoJur=';
                                        break;
                                        case 9:
                                            $arg = 'modificar_GF_BANCO_JURIDICA.php?id_bancoJur=';
                                        break;
                                        case 10:
                                            $arg = 'modificar_TERCERO_CONTACTO_NATURAL.php?id_ter_cont_nat=';
                                        break;
                                        case 11:
                                            $arg = 'modificar_GF_TERCERO_ENTIDAD_AFILIACION.php?id=';
                                        break;
                                        case 12:
                                            $arg = 'modificar_GF_TERCERO_ENTIDAD_FINANCIERA.php?id=';
                                        break;
                                    }
                                    $var = "Modificar".$row['PERFIL'];
                                    ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row['ID_TER']?></td>
                                        <td>      
                                             <?php if($row['NUMEROIDENTIFICACION']=='900849655' || $row['NUMEROIDENTIFICACION']=='9999999999'){} else {?>
                                            <a class="campos" href="<?php echo $arg.md5($row['ID_TER'])?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                             <?php } ?>
                                        </td>                                        
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_P']));?></td>                
                                        <td class="campos"><?php echo $row['NOMBRE_IDE']?></td>                
                                        <td class="campos"><?php if(!empty($row['DIGITOVERFICACION'])){ echo $row['NUMEROIDENTIFICACION'].' - '.$row['DIGITOVERFICACION']; }
                                                    else { echo $row['NUMEROIDENTIFICACION']; }?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_T']));?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_C']));?></td>                
                                        <td class="campos"><?php ucwords(mb_strtolower($row['NOMBRE_R']));?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['CARGO']))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_TE']))?></td> 
                                        
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_TEN']))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_TR']))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRE_Z']))?></td>                
                                    </tr>
                                    <?php }
                                    }
                                    ?>
                                </tbody>
                            </table>                            
                            <div align="right">
                                <a href="#"onclick="return abrirMTerceroMenu()" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Ver Terceros por Perfil</a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tercero?</p>
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


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTerceroTodosJson.php?id="+id,
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
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'TERCERO_TODOS.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'TERCERO_TODOS.php';
      });    
  </script>
    </body>
</html>