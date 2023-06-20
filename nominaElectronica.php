<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
   /*  $calendario = CAL_GREGORIAN; */
           /*  $idAnno=$_POST['anno'];
            $anno=$_POST['annio']; */
            //$mes = $_GET['mes'];
 /*            $sqlMes="SELECT id_unico FROM gf_mes
            WHERE numero=$mes
            AND parametrizacionanno=$idAnno";
            $resultMes = $mysqli->query($sqlMes);
            $rowMes = $resultMes->fetch_assoc();
            $idMes = $rowMes['id_unico']; */
            $anno_s = $_SESSION['anno'];
            $calendario = CAL_GREGORIAN;
            $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE id_unico = $anno_s";
            $rsannio = $mysqli->query($annio);
            $filaAnnio = mysqli_fetch_row($rsannio);
             $anioN=$filaAnnio[1];
            @$mes = $_GET['mes'];
            $diaF = cal_days_in_month($calendario, $mes , $anioN); 
            $fechaInicial= "'$anioN-$mes-01'";
            $fechaFinal= "'$anioN-$mes-$diaF'";
            $sqlPeriodo="SELECT id_unico FROM `gf_mes`  
            WHERE parametrizacionanno= $anno_s
            AND numero=$mes";
            $resultP = $mysqli->query($sqlPeriodo); 
            $rowP = mysqli_fetch_row($resultP);
            $periodoNom= $rowP[0];
            $sqlEmp="SELECT GROUP_CONCAT(tercero) FROM `gn_nomina_electronica`  
                     WHERE anno=$anno_s 
                     AND mes=$periodoNom";
            $resultEmp = $mysqli->query($sqlEmp); 
            $rowEmp = mysqli_fetch_row($resultEmp);

            if ($rowEmp[0]==null) {
              $empleadoN="";
            }else{
              $empleadoN="AND t.id_unico NOT IN ($rowEmp[0])";
            }
            

         $query = "SELECT DISTINCT n.empleado, IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL 
         OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
         (t.razonsocial), 
         CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,t.numeroidentificacion,t.id_unico
          FROM gn_novedad n 
                 LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                 LEFT JOIN gn_periodo p ON p.id_unico=n.periodo
                 LEFT JOIN gn_tipo_novedad_nomina tn ON tn.id_unico=c.tipo_novedad_nomina
                 LEFT JOIN gn_empleado e  ON e.id_unico=n.empleado
                 LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                 WHERE p.fechainicio BETWEEN $fechaInicial AND $fechaFinal
                 AND p.fechafin BETWEEN $fechaInicial AND $fechaFinal
                 AND p.parametrizacionanno= $anno_s
                 AND n.concepto=2
                 AND n.valor>0
                 $empleadoN";  
        $resultado = $mysqli->query($query); 

  /*        $query = "SELECT descripcion,codigo FROM gn_concepto";
         $resultado = $mysqli->query($query); */
         
/* <?php echo $mes; ?> */
?>
  <title>Nomina Electrónica</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Nominas</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
            <input type="hidden" name="sltmesi" id="sltmesi" value="<?php echo $mes;?>" > 
            <input type="hidden" name="tercero" id="tercero" value="<?php echo $anioN;?>" >
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
              <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>Empleado</strong></td>
                            <td class="cabeza"><strong>N° Identificacion</strong></td>
                            <td class="cabeza" width="3%"><strong></strong></td>
                            <td class="cabeza" width="8%"><strong>Informe</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>

                            <th class="cabeza"><strong>Empleado</strong></th>
                            <th class="cabeza"><strong>N° Identificacion</strong></th>
                            <td class="cabeza" width="3%"><strong></strong></td>
                            <th class="cabeza" width="8%">><strong>Informe</strong></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                               <td class="campos" style="display: none;"></td>
                              <td>
                                <a class="campos btn btn-primary sendBill" href="jsonNominaE/enviarNominaElectronicaJson2F.php?tercero=<?=$row[3]?>&sltmesi=<?php echo $mes?>">
                                  <i title="Enviar Nomina" class="glyphicon glyphicon-send"></i>
                                </a>
                              </td>
                              <td class="campos" ><?=$row[1]?></td>
                              <td class="campos" ><?=$row[2]?></td>
                              <td class="text-center"><input type="checkbox" name="chkT[]" id="chkT" title='Seleccione si desea transladar el elemento' value="<?=$row[3]?>"></td>
                              <td class="campos" ><a href="informes_nomina/INFORME_NOMINA_ELEFORM.php?tercero=<?=$row[3]?>&sltmesi=<?php echo $mes?>" class="btn btn-primary" style="border-color: #1075C1; " target="_blank"><i class="glyphicon glyphicon-list-alt"></i></a></td>
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div align="right"> <a id="btnST" href="javascript:void(0)" onclick="checked_all()" class="btn btn-primary shadow glyphicon glyphicon-ok" title="Marcar todos"></a>
                    <a id="btnSN" href="javascript:void(0)" onclick="not_checked_all()" class="btn btn-primary shadow glyphicon glyphicon-remove" title="Desmarcar todos"></a>
                    </div>
                    <div align="right"><a onclick="enviarN();" class="btn btn-primary" disabled="disabled" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px" target="_blank"><i class="glyphicon glyphicon-check"></i> Enviar Nominas</a> </div>       
                </div>
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
  <script>
          function checked_all() {
            $('input[type=checkbox]').prop('checked', true);
        }

        /**
         * not_checked_all
         *
         * Función para desmarcar todos los checkbox
         */
        function not_checked_all() {
            $('input[type=checkbox]').prop('checked', false);
        }
        function enviarNo() {
          var mes = <?php echo $mes; ?>;
          var selected = '';     
          $('input[type=checkbox]').each(function(){
                        if (this.checked) {
                            selected += $(this).val()+',';
                        }
                    });
       if(selected.length > 0) {
           var select = selected.substr(0, (selected.length) - 1);
           window.location.href = "jsonNominaE/enviarNominaElectronicaJson.php?ids="+select+"&sltmesi="+mes;  
       }   

          
        }
        </script>
</body>
</html>


