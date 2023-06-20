<?php
########## MODIFICACIONES ##############
#17/02/2017 | Erica G. *Modificación Búsqueda
########################################

require_once 'head.php';
require_once('Conexion/conexionPDO.php');
$anno = $_SESSION['anno'];
$con = new ConexionPDO();
$action = $_REQUEST['action'];
//consulta para cargar la informacion guardada con ese id
$id_concepto_rubro = " ";
$queryRubro = "";

if ($action == 1) {
  if (isset($_GET["id"])) {
    $id_concepto_rubro = base64_decode(($_REQUEST["id"]));
    $_SESSION['url'] = 'Modificar_GF_CONCEPTO_RUBRO.php?id=' . $id_concepto_rubro;

    $row = $con->Listar("SELECT CR.Id_Unico, CR.Rubro, CR.Concepto,R.Nombre,C.Nombre, R.codi_presupuesto 
    FROM gf_concepto_rubro CR 
    LEFT JOIN gf_rubro_pptal R on CR.Rubro= R.Id_Unico
    LEFT JOIN gf_concepto C on CR.concepto = C.Id_Unico
    WHERE CR.Id_Unico  = '$id_concepto_rubro'");
  }


  $conceptrubro = $row[0][1];
  $concepto = $row[0][2];



  $combRubro = $con->Listar("SELECT id_Unico, nombre, codi_presupuesto "
    . "FROM gf_rubro_pptal WHERE Id_Unico != $conceptrubro "
    . "AND parametrizacionanno = $anno "
    . "ORDER BY codi_presupuesto ASC");


  $combConcepto = $con->Listar("SELECT id_Unico, nombre "
    . "FROM gf_concepto WHERE Id_Unico != $concepto "
    . "AND parametrizacionanno = $anno"
    . "ORDER BY Nombre ASC");
}


if ($action == 2) {

  $anno = $_SESSION['anno'];
$rubro_pptal =$con->Listar( "SELECT Id_Unico, codi_presupuesto, Nombre "
. "FROM gf_rubro_pptal  "
. "WHERE parametrizacionanno = $anno "
. "ORDER BY codi_presupuesto ASC");



$concep=$con->Listar("SELECT Id_Unico,Nombre "
. "FROM gf_concepto "
. "WHERE parametrizacionanno = $anno "
. "ORDER BY Nombre ASC");

}

?>
<!--titulo de  la página-->
<link href="css/select/select2.min.css" rel="stylesheet">
<?php if ($action == 1) { ?>
  <title>Modificar Concepto Rubro</title>
<?php }

?>
<?php if ($action == 2) { ?>
  <title>Registrar Concepto Rubro</title>
<?php }


?>


</head>

<body>


  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-8 text-left">
        <!--titulo del formulario-->
        <?php if ($action == 1) { ?>
          <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Rubro</h2>
        <?php }

        ?>
        <?php if ($action == 2) { ?>
          <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Concepto Rubro</h2>
        <?php }


        ?>


        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">

            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">

            <!--Carga los datos para la modificación-->
            <div class="form-group" style="margin-top: -10px;">
              <label for="rubro" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro:</label>
              <?php if ($action == 1) { ?>
                <select name="rubro" id="rubro" class="select2_single form-control" title="Seleccione el tipo rubro" required>
                  <option value="12"><?php echo $row[0][5] . ' - ' . $row[0][3]; ?></option>

                  <?php for ($i = 0; $i < count($combRubro); $i++) { ?>
                    <option value="<?php echo $combRubro[$i][0] ?>"><?php echo ucwords((mb_strtolower($$combRubro[$i][2] . ' - ' . $combRubro[$i][1])));
                                                                  } ?></option>;
                </select>
              <?php }

              ?>
              <?php if ($action == 2) { ?>
                <select name="rubro" id="rubro" class="select2_single form-control" title="Seleccione el rubro" onchange="llenar()" required>
                  <option value="">Rubro</option>
                  <?php for ($i = 0; $i < count($rubro_pptal); $i++)  { ?>
                    <option value="<?php echo $rubro_pptal[$i][0] ?>"><?php echo  $rubro_pptal[$i][1]  . ' ' . ucwords((mb_strtolower( $rubro_pptal[$i][2] )));
                                                                  } ?></option>;
                </select>
              <?php }


              ?>


            </div>

            <div class="form-group">
              <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>

              <?php if ($action == 1) { ?>
                <select name="concepto" id="concepto" class="select2_single form-control" title="Seleccione el tipo concepto" required>
                  <option value="<?php echo $row[0][2] ?>"><?php echo ucwords((strtolower($row[0][4]))); ?></option>
                  <?php for ($i = 0; $i < count($combConcepto); $i++) { ?>
                    <option value="<?php echo $combConcepto[$i][0] ?>"><?php echo ucwords((mb_strtolower($combConcepto[$i][1])));
                                                                      } ?></option>;
                </select>
              <?php }

              ?>
              <?php if ($action == 2) { ?>
                <select name="concepto" id="concepto" class="select2_single form-control" title="Seleccione el concepto" required>
                  <option value="">Concepto</option>
                  <?php for ($i = 0; $i < count($concep); $i++) { ?>
                    <option value="<?php echo $concep[$i][0] ?>"><?php echo ucwords((mb_strtolower($concep[$i][1])));
                                                                  } ?></option>;
                </select>
              <?php }


              ?>

            </div>


            <div class="form-group" style="margin-top: 20px;">
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
      <!--Información adicional -->
      <div class="col-sm-6 col-sm-2" style="margin-top:-2px;">
        <table class="tablaC table-condensed" style="margin-left: -3px; ">
          <thead>
            <th>
              <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
            </th>
          </thead>
          <tbody>
            <tr>
              <td>
                <a href="GF_CONCEPTO_RUBRO_CUENTA.php?id=<?php echo md5($row[0]); ?>" class="btn btnInfo btn-primary">Cuenta</a><br />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php require_once 'footer.php'; ?>
  <script src="js/select/select2.full.js"></script>
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
        url: "Json/modificarConcepto_Rubro.php?action=3",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información Modificada Correctamente');
            $("#modalMensajes").modal("show");

            document.location = 'listar_GF_CONCEPTO_RUBRO.php';

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
        url: "Json/modificarConcepto_Rubro.php?action=2",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          console.log(response);
          if (response == 1) {
            $("#mensaje").html('Información Modificada Correctamente');
            $("#modalMensajes").modal("show");

            document.location = 'listar_GF_CONCEPTO_RUBRO.php';

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