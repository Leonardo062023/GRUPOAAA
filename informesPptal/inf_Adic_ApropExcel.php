  <?php
  ###################################MODIFICACIONES#############################################################
  #                          INFORME ADICION A APROPIACION GENERAL#
  #############################################################################################################
  #21/06/2017 | ERICA G. | CAMBIO CODIGO
  #############################################################################################################
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=Informe_Adicion_Apropiacion.xls");
  session_start();
  require_once("../Conexion/conexion.php");
  require_once("../Conexion/ConexionPDO.php");
  $con = new ConexionPDO();
  $conexion = $_SESSION['conexion'];
  $id = base64_decode($_REQUEST['id']);
  switch ($conexion) {
    case 1:
      $rowComp = $con->Listar("SELECT 
            comp.numero, 
            DATE_FORMAT(comp.fecha, '%d/%m/%Y'),
            comp.descripcion, 
            tipCom.codigo, 
            tipCom.nombre,
            comp.tipocomprobante
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = $id");
      break;
    case 2:
      $rowComp = $con->Listar("SELECT 
            comp.numero, 
            TO_CHAR(comp.fecha, 'DD/MM/YYYY'),
            comp.descripcion, 
            tipCom.codigo, 
            tipCom.nombre,
            comp.tipocomprobante
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = $id");
      break;
    default:
      break;
  }

  $numero          = $rowComp[0][0];
  $fecha           = $rowComp[0][1];
  $descripcion     = $rowComp[0][2];
  $tipocomprobante = mb_strtoupper($rowComp[0][3]) . ' - ' . ucwords(mb_strtolower($rowComp[0][4]));
  $tipocomprobante1 = $rowComp[0][5];

  ?>
  <html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Adición a Apropiación</title>
  </head>

  <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="5" bgcolor="skyblue">
          <CENTER><strong><?php echo $tipocomprobante ?></strong></CENTER>
        </td>
      </tr>
      <tr>
        <td align="left" bgcolor="skyblue"><strong>Número</strong></td>
        <td align="left" bgcolor="skyblue"><strong><?php echo $numero ?></strong></td>
        <td align="left" bgcolor="skyblue"><strong>Fecha:</strong></td>
        <td align="left" colspan="2" bgcolor="skyblue"><strong><?php echo $fecha ?></strong></td>
      </tr>
      <tr>

      </tr>
      <tr>
        <td><strong>CÓDIGO</strong></td>
        <td><strong>RUBRO</strong></td>
        <td><strong>FUENTE</strong></td>
        <td><strong>CRÉDITO</strong></td>
        <td><strong>CONTRACRÉDITO</strong></td>
      </tr>
      <?PHP

      $detalle = $con->Listar("SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, fue.nombre nombreFuente, detComP.valor, rub.tipoclase, rub.nombre      
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente
      left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase
      where detComP.comprobantepptal =$id");

      $totalValor = 0;
      $totalCredito = 0;
      $totalContacredito = 0;
      for ($rowDetall = 0; $rowDetall < count($detalle); $rowDetall++) {
        $ingresos = 0;
        $gastos = 0;
        if ($detalle[$rowDetall][4] == 6)
          switch ($conexion) {
            case 1:
              $ingresos = $detalle[$rowDetall][3];
              break;
            case 2:
              $ingresos = str_replace(',', '.', $detalle[$rowDetall][3]);
              break;
            default:
              break;
          }

        elseif ($detalle[$rowDetall][4] == 7)
        switch ($conexion) {
          case 1:
            $gastos = $detalle[$rowDetall][3];
            break;
          case 2:
            $gastos = str_replace(',', '.', $detalle[$rowDetall][3]);
            break;
          default:
            break;
        }
          
        $totalCredito = $totalCredito + $gastos;
        $totalContacredito = $totalContacredito + $ingresos;
      ?>
        <tr>
          <td align="right"><?php echo $detalle[$rowDetall][1] ?></td>
          <td><?php echo ($detalle[$detalle[$rowDetall]][5]) ?></td>
          <td><?php echo ($detalle[$detalle[$rowDetall]][2]) ?></td>
          <?php
          $gastos = str_replace(',', '.', $gastos);
          $ingresos = str_replace(',', '.', $ingresos);
          ?>
          <td><?php echo number_format($gastos, 2); ?></td>
          <td><?php echo number_format($ingresos, 2); ?></td>
        </tr>
      <?php
      }
      ?>
      <tr>
        <td colspan="3"><strong>TOTALES:</strong></td>
        <?php
        $totalCredito = str_replace(',', '.', $totalCredito);
        $totalContacredito = str_replace(',', '.', $totalContacredito);
        ?>
        <td><strong><?php echo number_format($totalCredito, 2); ?></strong></td>
        <td><strong><?php echo number_format($totalContacredito, 2); ?></strong></td>

      </tr>
      <tr>
        <td colspan="5"><strong>DESCRIPCIÓN: </strong><?php echo $descripcion ?></td>
      </tr>
    </table>
    <?php
    switch ($conexion) {
      case 1:
        $firma = $con->Listar("SELECT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),ti.nombre,t.numeroidentificacion, car.nombre
        FROM gf_tipo_comprobante_pptal tcp
        LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
        LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
        LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
        LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
        LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
        LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
        WHERE tcp.id_unico = $tipocomprobante1");
        break;
      case 2:
        $firma = $con->Listar("SELECT
        t.nombreuno || ' ' || t.nombredos || ' ' || t.apellidouno || ' ' || t.apellidodos,
        ti.nombre,
        t.numeroidentificacion,
        car.nombre
        FROM gf_tipo_comprobante_pptal tcp
        LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico
        LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento
        LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
        LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
        LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
        LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
        WHERE tcp.id_unico = $tipocomprobante1");
        break;
      default:
        # code...
        break;
    }

    ?>
    <br>
    <br>
    <br>
    <center>
      <label>_____________________________________</label><br>
      <label><b><?php echo $firma[0][0] ?></b></label><br>
      <label><b><?php echo $firma[0][3] ?></b></label>
    </center>
  </body>

  </html>