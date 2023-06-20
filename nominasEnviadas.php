<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');


    $query = "SELECT t.numeroidentificacion,t.nombreuno,t.nombredos, t.apellidouno,t.apellidodos, DATE_FORMAT(ne.fecha_envio,'%d/%m/%Y') as fechaenvio,
    CONCAT(m.mes,'-',pa.anno) as mesenviado,ne.cune,CONCAT(ne.prefijo,'',ne.consecutivo) as conse
    FROM gn_nomina_electronica ne 
    LEFT JOIN gf_tercero t ON t.id_unico=ne.tercero
    LEFT JOIN gf_mes m ON m.id_unico=ne.mes
    LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=ne.anno
    ORDER BY t.apellidouno ASC";
$resultado = $mysqli->query($query);
/* <?php echo $mes; ?> */
?>
  <title>Nomina Electrónica</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Nominas Enviadas</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>N° Identificacion</strong></td>
                            <td class="cabeza"><strong>Empleado</strong></td>
                             <td class="cabeza"><strong>Consecutivo Nomina</strong></td>
                            <td class="cabeza"><strong>Fecha de Envio</strong></td>
                            <td class="cabeza"><strong>Mes Enviado</strong></td>
                            <td class="cabeza"><strong>Codigo Cune</strong></td>


                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>
                            <th class="cabeza"><strong>N° Identificacion</strong></th>
                            <th class="cabeza"><strong>Empleado</strong></th>
                             <th class="cabeza"><strong>Consecutivo Nomina</strong></th>
                            <th class="cabeza"><strong>Fecha de Envio</strong></th>
                            <th class="cabeza"><strong>Mes Enviado</strong></th>
                             <th class="cabeza"><strong>Codigo Cune</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                               <td class="campos" style="display: none;"></td>
                              <td>
                                
                                </a>
                              </td>
                              <td class="campos" ><?=$row[0]?></td>
                              <td class="campos" ><?=$row[1].' '.$row[2].' '.$row[3].' '.$row[4]?></td>
                              <td class="campos" ><?=$row[8]?></td>
                              <td class="campos" ><?=$row[5]?></td>
                              <td class="campos" ><?=$row[6]?></td>
                              <td class="campos" ><?=$row[7]?></td>
                        
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
</div>
  <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;"></h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once ('footer.php'); ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script src="js/facturacion_electronica/facturacion.js"></script>
</body>
</html>


