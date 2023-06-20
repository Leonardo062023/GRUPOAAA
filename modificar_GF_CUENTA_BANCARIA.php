<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#Inclusión de formato | 8/02/2017 | 05:02
######################################################################################################
require_once('Conexion/conexionPDO.php');
require_once 'head.php';
$con = new ConexionPDO();

$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];

$id_cuentaB = $_GET["id_cuentaB"];


//$id = $_GET['id'];
$action = $_REQUEST['action'];
$titulo = "";
if ($action == 1) {
  $titulo = "Modificar Cuenta Bancaria";
  $id_cuentaB = base64_decode(($id_cuentaB));
  if (isset($_GET["id_cuentaB"])) {
    $row = $con->Listar("SELECT cb.id_unico, 
            cb.banco, 
            t.id_unico, t.razonsocial, 
            t.numeroidentificacion, t.tipoidentificacion,  
            ti.nombre, 
            cb.numerocuenta, 
            cb.descripcion, cb.tipocuenta, 
            tc.nombre, cb.cuenta, 
            c.nombre, c.codi_cuenta, 
            cb.recursofinanciero, rf.nombre, 
            rf.codi, cb.formato, 
            fc.nombre, d.id_unico, d.nombre 
        FROM gf_cuenta_bancaria cb
        LEFT JOIN gf_tercero t ON cb.banco=t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_tipo_cuenta tc ON cb.tipocuenta = tc.id_unico
        LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
        LEFT JOIN gf_recurso_financiero rf ON cb.recursofinanciero = rf.id_unico
        LEFT JOIN gf_formato fc ON cb.formato = fc.id_unico 
        LEFT JOIN gf_tipo_destinacion d ON cb.destinacion = d.id_unico 
        WHERE cb.Id_Unico ='$id_cuentaB'");
  }
  $bancos = $con->Listar("SELECT t.id_unico, t.razonsocial, t.tipoidentificacion, t.numeroidentificacion, t.digitoverficacion,ti.nombre 
        FROM gf_tercero t
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_perfil_tercero pt ON pt.tercero=t.id_unico
        WHERE t.tipoidentificacion = ti.id_unico 
        AND t.id_unico = pt.tercero 
        AND pt.perfil = 9 
        AND t.id_unico != " . $row[0][1] . "
        AND t.compania = $compania 
        ORDER BY razonsocial ASC");


  //tipo cuenta lleno
  $tcuenta = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_cuenta  WHERE id_unico != " . $row[0][9] . " ORDER BY nombre ASC");

  //tipo cuenta vacio
  $tcuentav = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_cuenta ORDER BY nombre ASC");
  //echo "SELECT id_unico, nombre FROM gf_tipo_cuenta ORDER BY nombre ASC";
} else {
  $bancos = $con->Listar("SELECT t.id_unico, t.razonsocial, t.tipoidentificacion, t.numeroidentificacion, t.digitoverficacion,ti.nombre 
        FROM gf_tercero t
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_perfil_tercero pt ON pt.tercero=t.id_unico
        WHERE t.tipoidentificacion = ti.id_unico 
        AND t.id_unico = pt.tercero 
        AND pt.perfil = 9 
        AND t.compania = $compania 
        ORDER BY razonsocial ASC");

  $titulo = "Registrar Cuenta Bancaria";
  //tipo cuenta lleno
  $tcuenta = $con->Listar(" SELECT id_unico, nombre FROM gf_tipo_cuenta  WHERE id_unico != " . $row[0][9] . " ORDER BY nombre ASC");

  //tipo cuenta vacio
  $tcuentav = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_cuenta ORDER BY nombre ASC");
}

?>
<title><?php echo $titulo ?></title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>

<script>
  $().ready(function() {
    var validator = $("#form").validate({
      ignore: "",
      errorPlacement: function(error, element) {

        $(element)
          .closest("form")
          .find("label[for='" + element.attr("id") + "']")
          .append(error);
      },
    });

    $(".cancel").click(function() {
      validator.resetForm();
    });
  });
</script>
<style>
  label#banco-error,
  #numC-error,
  #descrip-error,
  #tipoC-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

  }
</style>
<!-- contenedor principal -->
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-7 text-left" style="margin-top: -20px; margin-left: -10px">
      <h2 id="forma-titulo3" align="center" style=" margin-bottom: 5px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 10px" class="client-form">
      <a href="listar_GF_CUENTA_BANCARIA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <?php if ($action == 1) { ?>
          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
          <?php } else { ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
            <?php } ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
              <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">

              <!-- Banco -->
              <div class="form-group" style="margin-top: -10px;">
                <input type="hidden" name="banco" id="banco" value="<?php echo $row[0][1]  ?>" required title="Seleccione el banco">
                <label for="banco" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco:</label>
                <?php if ($action == 1) { ?>
                  <select name="banco1" id="banco1" class="select2_single form-control" title="Seleccione el banco" required onchange="llenar();">
                    <option value="<?php echo $row[0][1] ?>"><?php echo ucwords(mb_strtolower($row[0][3] . "(" . $row[0][4] . ")")); ?></option>
                    <?php for ($b = 0; $b < count($bancos); $b++) { ?>
                      <option value="<?php echo $bancos[$b][0] ?>">
                      <?php echo ucwords((mb_strtolower($bancos[$b][1] . "(" . $bancos[$b][3] . ")")));
                    } ?>
                      </option>;
                  </select>
                <?php } else { ?>
                  <select name="banco1" id="banco1" class="select2_single form-control" title="Seleccione el banco" required onchange="llenar();">
                    <option value="">Seleccione el banco</option>
                    <?php for ($b = 0; $b < count($bancos); $b++) { ?>
                      <option value="<?php echo $bancos[$b][0] ?>">
                      <?php echo ucwords((mb_strtolower($bancos[$b][1] . "(" . $bancos[$b][3] . ")")));
                    } ?>
                      </option>;
                  </select>
                <?php } ?>
              </div>

              <!-- Numero de cuenta-->
              <div class="form-group" style="margin-top: 10px;">
                <label for="numC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Cuenta:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="numC" id="numC" class="form-control" maxlength="15" title="Ingrese número cuenta" value="<?php echo $row[0][7] ?>" onkeypress="return txtValida(event,'num')" placeholder="Número Cuenta" required>
                <?php } else { ?>
                  <input type="text" name="numC" id="numC" class="form-control" maxlength="15" title="Ingrese número cuenta" onkeypress="return txtValida(event,'num')" placeholder="Número Cuenta" required>
                <?php } ?>
              </div>
              <!--- Descripcion -->
              <div class="form-group" style="margin-top: -10px;">
                <label for="descrip" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="descrip" id="descrip" class="form-control" maxlength="500" title="Ingrese la descripción" value="<?php echo ucwords(mb_strtolower($row[0][8])) ?>" onkeypress="return txtValida(event,'car')" placeholder="Descripción" required="required">
                <?php } else { ?>
                  <input type="text" name="descrip" id="descrip" class="form-control" maxlength="500" title="Ingrese la descripción" onkeypress="return txtValida(event,'car')" placeholder="Descripción" required="required">
                <?php } ?>
              </div>

              <!-- Tipo cuenta-->
              <div class="form-group" style="margin-top: -10px;">
                <label for="tipoC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong> Tipo Cuenta:</label>
                <?php if ($action == 1) { ?>
                  <select name="tipoC" id="tipoC" class="form-control" title="Seleccione el tipo cuenta" required>
                    <option value="<?php echo $row[0][9] ?>"><?php echo ucwords(mb_strtolower($row[0][10])); ?></option>
                    <?php for ($tc = 0; $tc < count($tcuentav); $tc++) { ?>
                      <option value="<?php echo $tcuentav[$tc][0] ?>"><?php echo ucwords((mb_strtolower($tcuentav[$tc][1])));
                                                                    } ?></option>
                      <option value=""></option>
                  </select>
                <?php } else { ?>
                  <select name="tipoC" id="tipoC" class="form-control" title="Seleccione el tipo cuenta" required>
                    <option value="">Tipo Cuenta</option>
                    <?php for ($t = 0; $t < count($tcuentav); $t++) { ?>
                      <option value="<?php echo $tcuentav[$t][0] ?>"><?php echo ucwords((mb_strtolower($tcuentav[$t][1])));
                                                                    } ?></option>


                  </select>
                <?php } ?>
              </div>

              <!--Formato -->
              <div class="form-group" style="margin-top: -10px;">
                <label for="sltFormato" class="col-sm-5 control-label">Formato:</label>
                <?php if ($action == 1) { ?>
                  <select name="sltFormato" id="sltFormato" class="select2_single form-control" title="Seleccione el formato para cheque">
                    <?php
                    echo "<option value=" . $row[0][17] . ">" . $row[0][18] . "</option>";
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_formato WHERE id_unico != " . $row[0][17] . " ");
                    for ($r = 0; $r < count($resultF); $r++) { ?>
                      <option value="<?php echo $resultF[$r][0] ?>"><?php echo ucwords(mb_strtolower(($resultF[$r][1]))) ?> </option>
                    <?php }
                    ?>
                  </select>
                <?php } else { ?>
                  <select name="sltFormato" id="sltFormato" class="select2_single form-control" title="Seleccione el formato para cheque">
                    <option value="">Seleccione el formato</option>
                    <?php
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_formato");
                    for ($rf = 0; $rf < count($resultF); $rf++) { ?>
                      <!---echo '<option value="">'.ucwords(mb_strtolower(($resultF[$rf][1]))).'</option>';-->
                      <option value="<?php echo $resultF[$rf][0] ?>"><?php echo ucwords(mb_strtolower(($resultF[$rf][1]))) ?> </option>
                    <?php }
                    ?>
                  </select>
                <?php } ?>
              </div>
              <!-- Recurso financiero -->
              <div class="form-group" style="margin-top: -0px;">
                <label for="sltRecurso" class="col-sm-5 control-label">Recurso Financiero:</label>
                <?php if ($action == 1) { ?>
                  <select name="sltRecurso" id="sltRecurso" class="select2_single form-control" title="Seleccione Recurso Financiero">
                    <?php
                    echo "<option value=" . $row[0][14] . ">" . $row[0][15] . "</option>";
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno AND id_unico != " . $row[0][14] . " ");
                    //echo "SELECT DISTINCT id_unico,nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno AND id_unico != ".$row[0][14]."";
                    for ($fila = 0; $fila < count($resultF); $fila++) { ?>
                      <option value="<?php echo $resultF[$fila][0] ?>"><?php echo ucwords(mb_strtolower(($resultF[$fila][1]))) ?> </option>
                      <!--echo "<option value=".$resultF[$fila][0].">".ucwords(mb_strtolower(($resultF[$fila][1])))."</option>";-->
                    <?php }
                    ?>
                  </select>
                <?php } else { ?>
                  <select name="sltRecurso" id="sltRecurso" class="select2_single form-control" title="Seleccione Recurso Financiero">
                    <option value=''> Seleccione el recurso financiero</option>
                    <?php
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno");
                    for ($filaf = 0; $filaf < count($resultF); $filaf++) { ?>
                      <option value="<?php echo $resultF[$filaf][0] ?>"><?php echo ucwords(mb_strtolower(($resultF[$filaf][1]))) ?> </option> <!--echo "<option value="">".ucwords(mb_strtolower(($resultF[$filaF][1])))."</option>";-->
                    <?php } ?>
                  </select>
                <?php } ?>
              </div>

              <!-- Destinacion-->
              <div class="form-group" style="margin-top: -0px;">
                <label for="sltDestinacion" class="col-sm-5 control-label">Destinación:</label>
                <?php if ($action == 1) { ?>
                  <select name="sltDestinacion" id="sltDestinacion" class="select2_single form-control" title="Seleccione Destinación">
                    <?php
                    echo "<option value=" . $row[0][19] . ">" . $row[0][20] . "</option>";
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_tipo_destinacion WHERE id_unico != " . $row[0][19] . " ");
                    echo "SELECT DISTINCT id_unico,nombre FROM gf_tipo_destinacion WHERE id_unico != " . $row[0][19] . " ";
                    for ($f = 0; $f < count($resultF); $f++) { ?>
                      <option value="<?php echo $resultF[$f][0] ?>"> <?php echo ucwords(mb_strtolower(($resultF[$f][1]))); ?></option>
                      <!---echo "<option value=".$resultF[$f][0].">".ucwords(mb_strtolower(($resultF[$f][1])))."</option>";-->
                    <?php }
                    ?>
                  </select>
                <?php } else { ?>
                  <select name="sltDestinacion" id="sltDestinacion" class="select2_single form-control" title="Seleccione Destinación">
                    <option value=""> Seleccione la destinación </option>

                    <?php
                    $resultF = $con->Listar("SELECT DISTINCT id_unico,nombre FROM gf_tipo_destinacion");
                    for ($ff = 0; $ff < count($resultF); $ff++) { ?>
                      <option value="<?php echo $resultF[$ff][0] ?>"><?php echo ucwords(mb_strtolower(($resultF[$ff][1]))) ?></option>
                      <!---echo "<option value="">".ucwords(mb_strtolower(($resultF[$ff][1])))."</option>";-->
                    <?php }
                    ?>
                  <?php } ?>
                  </select>
              </div>
              <div align="center">
                <button type="submit" class="btn btn-primary sombra" style="margin-left: -47px"><?php echo $titulo ?></button>
              </div>
              <input type="hidden" name="MM_insert">
            </form>
      </div>
    </div>
    <!-- Botones de consulta -->
    <div class="col-sm-7 col-sm-3" style="margin-top:-22px; margin-left: 10px">
      <table class="tablaC table-condensed" style="margin-left: -30px">
        <thead>
          <th>
            <h2 class="titulo" align="center">Consultas</h2>
          </th>
          <th>
            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
          </th>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="btnConsultas">
                <a href="#">
                  MOVIMIENTO CONTABLE
                </a>
              </div>
            </td>
            <td>
              <a href="modificar_GF_BANCO_JURIDICA.php?action=2"><button class="btn btn-primary btnInfo">BANCOS</button></a>
            </td>
          </tr>
          <tr>
            <td>
              <div class="btnConsultas">
                <a href="#">
                  CHEQUERAS
                </a>
              </div>
            </td>
            <td>
              <a href="GF_TIPO_CUENTA.php">
                <button class="btn btn-primary btnInfo">
                  TIPO CUENTA
                </button>
              </a><br />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Fin de Contenedor Principal -->
    <?php require_once('footer.php'); ?>
  </div>
</div>
<div class="modal fade" id="modalMensajes" role="dialog" align="center">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
</body>

</html>
<script src="js/select/select2.full.js"></script>
<script>
  $(document).ready(function() {
    $(".select2_single").select2({
      allowClear: true,
    });
  });

  function agregar() {
    jsShowWindowLoad('Agregando Datos ...');
    var formData = new FormData($("#form")[0]);
    $.ajax({
      type: 'POST',
      url: "json/modificarCuentaBancariaJson.php?action=2",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        jsRemoveWindowLoad();
        console.log(response);
        if (response == 1) {
          $("#mensaje").html('Información agregada Correctamente');
          $("#modalMensajes").modal("show");
          $("#Aceptar").click(function() {
            $("#modalMensajes").modal("hide");
            document.location = 'listar_GF_CUENTA_BANCARIA.php';
          })

        } else {
          $("#mensaje").html('No se ha podido agregar información');
          $("#modalMensajes").modal("show");
          $("#Aceptar").click(function() {
            $("#modalMensajes").modal("hide");
          })

        }
      }
    });
  }

  function modificar() {
    jsShowWindowLoad('Modificando Datos ...');
    var formData = new FormData($("#form")[0]);
    $.ajax({
      type: 'POST',
      url: "json/modificarCuentaBancariaJson.php?action=3",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        jsRemoveWindowLoad();
        console.log(response);

        if (response == 1) {
          $("#mensaje").html('Información Modificada Correctamente');
          $("#modalMensajes").modal("show");
          $("#Aceptar").click(function() {
            $("#modalMensajes").modal("hide");
            document.location = 'listar_GF_CUENTA_BANCARIA.php';
          })
        } else {
          $("#mensaje").html('No Se Ha Podido Modificar Información');
          $("#modalMensajes").modal("show");
          $("#Aceptar").click(function() {
            $("#modalMensajes").modal("hide");
          })

        }
      }
    });
  }
</script>
<script>
  function llenar() {
    var banco = document.getElementById('banco1').value;
    document.getElementById('banco').value = banco;
  }
</script>