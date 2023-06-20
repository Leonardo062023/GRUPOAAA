<?php
require_once 'head.php';
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$con = new ConexionPDO();
$id_contrato = " ";
$action = $_REQUEST['action'];
switch ($action) {
  case 1:
    if (isset($_REQUEST["id_contrato"])) {
      $id_contrato = base64_decode($_REQUEST["id_contrato"]);
      $row = $con->Listar("SELECT C.Id_Unico, C.Nombre, TC.Id_Unico, TC.Nombre
      FROM gf_clase_contrato C, gf_tipo_contrato TC
      WHERE C.TipoContrato = TC.Id_Unico
      AND C.Id_Unico = '$id_contrato'");
    }




    $contratos = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_contrato WHERE Id_Unico !=".$row[0][2]." ORDER BY Nombre ASC");


?>
    <!--Titulo de la paginá-->

    <title>Modificar Clase Contrato</title>
    </head>

    <body>



      <div class="container-fluid text-center">
        <div class="row content">
          <?php require_once 'menu.php'; ?>
          <div class="col-sm-10 text-left">
            <!--Titulo del formulario-->
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Clase Contrato</h2>

            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

              <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">

                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


                <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">

                <!--Cargar la información para la modificación-->
                <div class="form-group" style="margin-top: -10px;">
                  <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                  <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="txNombres()" placeholder="Nombre" value="<?php echo ucwords((strtolower($row[0][1]))); ?>" required>
                </div>

                <div class="form-group">
                  <label for="contrato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Contrato:</label>
                  <select name="contrato" id="contrato" class="form-control" title="Seleccione el tipo contrato" required>
                    <option value="<?php echo $row[0][2] ?>"><?php echo ucwords((strtolower($row[0][3]))) ?></option>
                    <?php for ($i = 0; $i < count($contratos); $i++) {  ?>
                      <option value="<?php echo $contratos[$i][0]; ?>"><?php echo ucwords((mb_strtolower($contratos[$i][1]))); ?></option>
                    <?php }
                    ?>
                  </select>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                  <label for="no" class="col-sm-5 control-label"></label>
                  <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                </div>

                <input type="hidden" name="MM_insert">
              </form>
            </div>



          </div>

        </div>
      </div>
    <?php
    break;
  case 2:

    $contratos = $con->Listar("SELECT id_Unico, Nombre FROM gf_tipo_contrato  ORDER BY Nombre ASC");


    ?>
      <!--Titulo de  la paginá-->
      <html>

      <head>
        <title>Registrar Clase Contrato</title>
      </head>

      <body>


        <div class="container-fluid text-center">
          <div class="row content">

            <?php require_once 'menu.php'; ?>

            <div class="col-sm-10 text-left">
              <!--Titulo del formulario-->
              <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Clase Contrato</h2>

              <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

                <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">

                  <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


                  <div class="form-group" style="margin-top: -10px;">
                    <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="txNombres()" placeholder="Nombre" required>
                  </div>

                  <div class="form-group">
                    <label for="contrato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Contrato:</label>
                    <select name="contrato" id="contrato" class="form-control" title="Seleccione el tipo contrato" required>
                      <option value="">Tipo Contrato</option>
                      <?php for ($i = 0; $i < count($contratos); $i++) { ?>
                        <option value="<?php echo $contratos[$i][0] ?>"><?php echo ucwords((strtolower($contratos[$i][1])));
                                                                      } ?></option>;
                    </select>
                  </div>

                  <div class="form-group" style="margin-top: 10px;">
                    <label for="no" class="col-sm-5 control-label"></label>
                    <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                  </div>
                  <input type="hidden" name="MM_insert">
                </form>
              </div>



            </div>

          </div>
        </div>
    <?php
    break;
  default:
    # code...
    break;
}
    ?>

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
    </script>
    <script>
      function cambio(valor) {
        console.log(valor);
        if (valor == 1) {
          $("#divcantidad").css('display', 'block');
        } else {
          console.log('asc');
          $("#divcantidad").css('display', 'none');
          $("#cantidad").val('');
        }
      }
    </script>
    <script>
      function modificar() {
        jsShowWindowLoad('Modificando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
          type: 'POST',
          url: "Json/modificarContratoJson.php?action=3",
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            jsRemoveWindowLoad();
            console.log(response);
            if (response == 1) {
              $("#mensaje").html('Información Modificada Correctamente');
              $("#modalMensajes").modal("show");

              document.location = 'listar.php';

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
          url: "Json/modificarContratoJson.php?action=2",
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            jsRemoveWindowLoad();
            console.log(response);
            if (response == 1) {
              $("#mensaje").html('Información Modificada Correctamente');
              $("#modalMensajes").modal("show");

              document.location = 'listar.php';

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