<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php';  ?>
<?php
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
//declaracion que recibe la variable que recibe el ID
$id_destino = " ";
$action = $_REQUEST['action'];
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_REQUEST["id_destino"])) {
  $id_destino = base64_decode(($_GET["id_destino"]));
  //Query o sql de consulta
  $row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_destino  WHERE Id_Unico ='$id_destino'");
}



?>
<?php if ($action == 1) { ?>
  <title>Modificar Concepto Rubro</title>
<?php }

?>
<?php if ($action == 2) { ?>
  <title>Registrar Rubro</title>
<?php }


?>

</head>
<!-- contenedor principal -->
<div class="container-fluid text-center">
  <div class="row content">

    <!-- Llamado al menú del formulario -->
    <?php require_once 'menu.php'; ?>


    <div class="col-sm-10 text-left">
      <?php if ($action == 1) { ?>
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Destino</h2>
      <?php }

      ?>
      <?php if ($action == 2) { ?>
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Destino</h2>
      <?php }


      ?>


      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
        <!-- Inicio del formulario -->
        <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


          <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">


          <div class="form-group" style="margin-top: -10px;">
            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre Destino:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[0][1] ?>" required>
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
        <!-- Fin de división y contenedor del formulario -->
      </div>
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
</div>

<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
</div>

<!-- funcion para validar los campos -->
<script>
  function txtValida(elEvento, permitidos) {
    var numeros = "0123456789";
    var caracteres = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var numeros_caracteres = numeros + caracteres;
    var teclas_especiales = [8, 20]; //Ascii retroseso y espacio
    var num = 0;

    switch (permitidos) {
      case 'num':
        permitidos = numeros;
        num = 1;
        break;
      case 'car':
        permitidos = caracteres;
        break;
      case 'num_car':
        permitidos = numeros_caracteres;
        break;
    }

    var evento = elEvento || window.event;
    var codigoCaracter = evento.charCode || evento.keyCode;
    var caracter = String.fromCharCode(codigoCaracter);

    var tecla_especial = false;

    if (num == 0) {
      for (var i in teclas_especiales) {
        if (codigoCaracter == teclas_especiales[i]) {
          tecla_especial = true;
          break;
        }
      }
    }

    return permitidos.indexOf(caracter) != -1 || tecla_especial;
  }

  function modificar() {
    jsShowWindowLoad('Modificando Datos ...');
    var formData = new FormData($("#form")[0]);
    $.ajax({
      type: 'POST',
      url: "Json/modificarDestinoJson.php?action=3",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        jsRemoveWindowLoad();
        console.log(response);
        if (response == 1) {
          $("#mensaje").html('Información Modificada Correctamente');
          $("#modalMensajes").modal("show");

          document.location = 'listar_GF_DESTINO.php';

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
      url: "Json/modificarDestinoJson.php?action=2",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        jsRemoveWindowLoad();
        console.log(response);
        if (response == 1) {
          $("#mensaje").html('Información Modificada Correctamente');
          $("#modalMensajes").modal("show");

          document.location = 'listar_GF_DESTINO.php';

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