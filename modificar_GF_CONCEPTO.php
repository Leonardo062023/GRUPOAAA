<?php
require_once 'head.php';
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$action = $_REQUEST['action'];
$con = new ConexionPDO();
$id_concepto = base64_decode(($_REQUEST["id_concepto"]));
$queryConcepto = $con->Listar("SELECT c.id_unico,c.nombre,
cc.id_unico,cc.nombre, 
c.amortizable, ts.id_unico, ts.nombre  
FROM gf_concepto c 
LEFT JOIN gf_clase_concepto cc ON c.clase_concepto = cc.id_unico  
LEFT JOIN gp_tipo_servicio ts ON c.tipo_servicio = ts.id_unico 
WHERE c.id_unico ='$id_concepto'");

?>

<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
  label #nombre-error,
  #sltTipoConcepto-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
  }
</style>
<script>
  $().ready(function() {
    var validator = $("#form").validate({
      ignore: "",
      errorPlacement: function(error, element) {
        $(element).closest("form").find("label[for='" + element.attr("id") + "']").append(
          error);
      },
    });
    $(".cancel").click(function() {
      validator.resetForm();
    });
  });
</script>
<?php if ($action == 1) { ?>
  <title>Modificar Concepto</title>
<?php }



?>
<?php if ($action == 2) { ?>
  <title>Registrar Concepto</title>
<?php }



?>
</head>

<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <?php if ($action == 1) { ?>
          <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-2px">Modificar Concepto
          </h2>
        <?php }

        ?>
        <?php if ($action == 2) { ?>
          <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-2px">Registrar Concepto
          </h2>
        <?php }

        ?>

        <a href="listar_GF_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">
          <?php echo $queryConcepto[0][1] ?></h5>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top:-15px" class="client-form">
          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos
              marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <input type="hidden" name="id" value="<?php echo $queryConcepto[0][0] ?>">
            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <?php if ($action == 1) { ?>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" value="<?php echo $queryConcepto[0][1] ?>" required>
              <?php }

              ?>
              <?php if ($action == 2) { ?>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" required>
              <?php }

              ?>

            </div>
            <div class="form-group" style="margin-top: -10px;">
              <label for="sltTipoConcepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
              <?php if ($action == 1) { ?>
                <select name="sltTipoConcepto" id="sltTipoConcepto" class="select2_single form-control" title="Seleccione tipo de concepto">
                  <?php
                  if (empty($queryConcepto[0][2])) {
                    echo '<option value=""> - </option>';
                    $sql1 = $con->Listar("select id_unico,nombre from gf_clase_concepto");
                  } else {
                    echo '<option value="' . $queryConcepto[0][2] . '">' . ucfirst(strtolower($queryConcepto[0][3])) . '</option>';
                    $sql1 = $con->Listar("select id_unico,nombre from gf_clase_concepto");
                  }

                  for ($i = 0; $i < count($sql1); $i++) {
                    echo '<option value="' . $sql1[$i][0] . '">' . ucfirst(strtolower($sql1[$i][1])) . '</option>';
                  }
                  ?>
                </select>
              <?php }

              ?>
              <?php if ($action == 2) { ?>
                <select name="sltTipoConcepto" id="sltTipoConcepto" class="select2_single form-control" title="Seleccione tipo de concepto" required="required">
                  <?php
                  echo '<option value="">Tipo Concepto</option>';
                  $sql = $con->Listar("select id_unico,nombre from gf_clase_concepto");

                  for ($i = 0; $i < count($sql); $i++) {
                    echo '<option value="' . $sql[$i][0] . '">' . ucwords(strtolower($sql[$i][1])) . '</option>';
                  }
                  ?>
                </select>
              <?php }

              ?>

            </div>
            <div class="form-group">
              <label for="sltTipoServicio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Servicio:</label>
              <?php if ($action == 1) { ?>
                <select name="sltTipoServicio" id="sltTipoServicio" class="select2_single form-control" title="Seleccione Servicio">
                  <?php
                  if (empty($queryConcepto[0][5])) {
                    echo '<option value=""> - </option>';
                    $sql1 = $con->Listar("select id_unico,nombre from gp_tipo_servicio");
                  } else {
                    echo '<option value="' . $queryConcepto[0][5] . '">' . ucfirst(strtolower($queryConcepto[0][6])) . '</option>';
                  
                    $sql1 = $con->Listar("select id_unico,nombre from gp_tipo_servicio where id_unico !=$queryConcepto[0][5]");
                  }

                  for ($i = 0; $i < count($sql1); $i++) {
                    echo '<option value="' . $sql1[$i][0] . '">' . ucwords(strtolower($sql1[$i][1])) . '</option>';
                  }
                  ?>
                </select>
              <?php }

              ?>
              <?php if ($action == 2) { ?>
                <select name="sltTipoServicio" id="sltTipoServicio" class="select2_single form-control" title="Seleccione Servicio">
                  <?php
                  echo '<option value="">Tipo Servicio</option>';
                  $sqlcon = $con->Listar("select id_unico,nombre from gp_tipo_servicio");
                  for ($i = 0; $i < count($sqlcon); $i++) {
                    echo '<option value="' . $sqlcon[$i][0] . '">' . ucwords(strtolower($sqlcon[$i][1])) . '</option>';
                  }
                  ?>
                </select>
              <?php }

              ?>

            </div>
            <div class="form-group" style="margin-top:-10px">
              <label for="factorBase" class="col-sm-5 control-label">Amortizable:</label>
              <?php if ($queryConcepto[0][4] == 1) { ?>
                <label for="si" class="radio-inline"><input type="radio" name="rdamrt" value="1" checked>Si</label>
                <label for="no" class="radio-inline"><input type="radio" name="rdamrt" id="rdamrt" value="2">No</label>
              <?php } else if ($queryConcepto[0][4] == 2 || empty($queryConcepto[0][4])) { ?>
                <label for="si" class="radio-inline"><input type="radio" name="rdamrt" value="1">Si</label>
                <label for="no" class="radio-inline"><input type="radio" name="rdamrt" id="rdamrt" value="2" checked>No</label>
              <?php } ?>
            </div>

            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
              <?php if ($action == 1) { ?>

                <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
              <?php }

              ?>
              <?php if ($action == 2) { ?>

                <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
              <?php }

              ?>

            </div>
            <input type="hidden" name="MM_insert">
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php require_once 'footer.php'; ?>
  <script src="js/select/select2.full.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });

    function modificar() {
      jsShowWindowLoad('Modificando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "Json/modificarConceptoJson.php?action=3",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información Modificada Correctamente');
            $("#modalMensajes").modal("show");

            document.location = 'listar_GF_CONCEPTO.php';

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

    function agregar() {
      jsShowWindowLoad('Agregando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "Json/modificarConceptoJson.php?action=2",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información Modificada Correctamente');
            $("#modalMensajes").modal("show");

            document.location = 'listar_GF_CONCEPTO.php';

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
</body>

</html>