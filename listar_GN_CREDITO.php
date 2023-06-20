<?php
#08/03/2017 --- Nestor B --- se modificó el método para cambiar el formato de fecha para que no genere error cuando las fechas sean vacías
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT    c.id_unico,
                    c.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    c.tipoproceso,
                    tpn.id_unico,
                    tpn.nombre,
                    c.entidad,
                    ter.id_unico,
                    ter.razonsocial,
                    c.numerocredito,
                    c.fecha,
                    c.periodoinicia,
                    c.valorcredito,
                    c.numerocuotas,
                    c.valorcuota,
                    p.id_unico,
                    p.codigointerno
                   
                FROM gn_credito c	 
                LEFT JOIN	gn_empleado e               ON c.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_proceso_nomina tpn  ON c.tipoproceso = tpn.id_unico
                LEFT JOIN   gf_tercero ter              ON c.entidad = ter.id_unico
                LEFT JOIN   gn_periodo p                 ON c.periodoinicia = p.id_unico";
    $resultado = $mysqli->query($sql);
    
?>
    <title>Listar Crédito</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Crédito</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Entidad</strong></td>
                                        <td class="cabeza"><strong>Tipo Proceso</strong></td>
                                        <td class="cabeza"><strong>No. Crédito</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Valor Crédito</strong></td>
                                        <td class="cabeza"><strong>Período Inicial</strong></td>
                                        <td class="cabeza"><strong>No. Cuotas</strong></td>
                                        <td class="cabeza"><strong>Valor Cuota</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza"   style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza"  >Empleado</th>
                                        <th class="cabeza"  >Entidad</th>
                                        <th class="cabeza"  >Tipo Proceso</th>
                                        <th class="cabeza"  >No. Crédito</th>
                                        <th class="cabeza"  >Fecha</th>
                                        <th class="cabeza"  >Valor Crédito</th>
                                        <th class="cabeza"  >Período Inicial</th>
                                        <th class="cabeza"  >No. Cuotas</th>
                                        <th class="cabeza"  >Valor Cuota</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        $cfec = $row[13];
                                        if(!empty($row[13])||$row[13]!=''){
                                        $cfec = trim($cfec, '"');
                                        $fecha_div = explode("-", $cfec);
                                        $aniof = $fecha_div[0];
                                        $mesf = $fecha_div[1];
                                        $diaf = $fecha_div[2];
                                        $cfec = $diaf.'/'.$mesf.'/'.$aniof;
                                      }else{
                                        $cfec='';
                                      }
                                        
                                        
                                      
                                        $cid   = $row[0];
                                        $cemp  = $row[1];
                                        $eid   = $row[2];
                                        $eter  = $row[3];
                                        $tid1  = $row[4];
                                        $ter1  = $row[5];
                                        $ctip  = $row[6];
                                        $tpid  = $row[7];
                                        $tpnom = $row[8];
                                        $cent  = $row[9];
                                        $tid2  = $row[10];
                                        $ter2  = $row[11];
                                        $cncr  = $row[12];
                                        #$cfec  = $row[13];
                                        $cper  = $row[14];
                                        $cval  = $row[15];
                                        $cncu  = $row[16];
                                        $cvcu  = $row[17];
                                        $perr  = $row[19];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_CREDITO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ter1?></td>                
                                        <td class="campos"><?php echo $ter2?></td>                
                                        <td class="campos"><?php echo $tpnom?></td>                
                                        <td class="campos"><?php echo $cncr?></td>                
                                        <td class="campos"><?php echo $cfec?></td>                
                                        <td class="campos"><?php echo $cval?></td>                
                                        <td class="campos"><?php echo $perr?></td>                
                                        <td class="campos"><?php echo $cncu?></td>                
                                        <td class="campos"><?php echo $cvcu?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_CREDITO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Crédito?</p>
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
                  url:"json/eliminarCreditoJson.php?id="+id,
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
        document.location = 'listar_GN_CREDITO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_CREDITO.php';
      });    
  </script>
    </body>
</html>