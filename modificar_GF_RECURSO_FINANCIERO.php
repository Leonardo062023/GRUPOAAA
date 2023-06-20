<?php
//llamado a la clase de conexion
require_once('Conexion/conexionPDO.php');
require_once 'head.php';
$con = new ConexionPDO();

$action = $_REQUEST['action'];
$titulo = "";

//declaracion que recibe la variable que recibe el ID
$id_recurso = $_GET["id_recurso"];

if ($action == 1) {
  $titulo = "Modificar Recurso Financiero";
  //validacion preguntando si la variable enviada del listar viene vacia
  if (isset($_GET["id_recurso"])) {
    $id_recurso = base64_decode(($id_recurso));
    //Query o sql de consulta 
    $row = $con->Listar("SELECT R.Id_Unico, R.Nombre, R.codi, R.tiporecursofinanciero, TR.Nombre, TR.id_unico
                 FROM gf_recurso_financiero R
                 LEFT JOIN gf_tipo_recurso_financiero TR ON R.tiporecursofinanciero = TR.Id_Unico
                 WHERE (R.Id_Unico) = '$id_recurso'");
  }

  //consultas para llenar los combos
  $tipoR = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_recurso_financiero WHERE id_unico != " . $row[0][5] . " ORDER BY Nombre ASC");
} else {
  $titulo = "Registrar Recurso Financiero";
  //consultas para llenar los combos
  $tipoR = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_recurso_financiero ORDER BY Nombre ASC");
}

?>


<title><?php echo $titulo ?> </title>
</head>

<!-- contenedor principal -->
<div class="container-fluid text-center">
  <div class="row content">
    <!-- Llamado al menú del formulario -->
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
      <a href="listar_GF_RECURSO_FINANCIERO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>

        <!-- Inicio del formulario -->
        <?php if ($action == 1) { ?>
          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:modificar()">
          <?php  } else { ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
            <?php } ?>
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="dato" value="<?php echo $row[0][2]; ?>" />
              <input type="hidden" name="id" value="<?php echo $row[0][0] ?>">

              <div class="form-group" style="margin-top: -10px;">
                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[0][1] ?>" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Nombre" required>
                <?php } else {  ?>
                  <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Nombre" required>
                <?php } ?>
              </div>

              <div class="form-group" style="margin-top: -22px;">
                <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
                <?php if ($action == 1) { ?>
                  <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" title="Ingrese el código" value="<?php echo $row[0][2] ?>" onkeypress="return txtValida(event,'sin_espcio')" placeholder="Codigo" onblur="return existente()" required>
                <?php } else { ?>
                  <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" title="Ingrese el código" onkeypress="return txtValida(event,'sin_espcio')" placeholder="Codigo" onblur="return existente()" required>
                <?php }  ?>
              </div>


              <div class="form-group" style="margin-top: -22px;">
                <label for="tipoR" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Recurso Financiero:</label>
                <?php if ($action == 1) { ?>
                  <select name="tipoR" id="tipoR" class="form-control" title="Seleccione el tipo recurso financiero" required>
                    <option value="<?php echo $row[0][3] ?>"><?php echo $row[0][4] ?></option>
                    <?php for ($t = 0; $t < count($tipoR); $t++) {  ?>
                      <option value="<?php echo $tipoR[$t][0] ?>">
                      <?php echo ucwords((strtolower($tipoR[$t][1])));
                    } ?>
                      </option>
                  </select>
                <?php } else { ?>
                  <select name="tipoR" id="tipoR" class="form-control" title="Seleccione el tipo recurso financiero" required>
                    <option value="">Seleccione el tipo de recurso financiero</option>
                    <?php for ($t = 0; $t < count($tipoR); $t++) {  ?>
                      <option value="<?php echo $tipoR[$t][0] ?>"><?php echo ucwords((strtolower($tipoR[$t][1]))); ?> </option>
                    <?php } ?>
                  </select>
                <?php }  ?>
              </div>



              <div align="center">
                <button type="submit" class="btn btn-primary sombra"><?php echo $titulo ?></button>
              </div>
      </div>

      <!-- DIV que contiene una clase oculta  -->
      <div class="texto" style="display: none"></div>

      <input type="hidden" name="MM_insert">
      </form>
      <!-- Fin de división y contenedor del formulario -->
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
</div>
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>


<!-- modal para la validacion del código -->
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

<div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El codigo ingresado ya existe. Por favor ingrese otro codigo.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<!-- validacion de los campos número y tipo de identificacion  -->
<script type="text/javascript">
  function agregar() {
    jsShowWindowLoad('Agregando Datos ...');
    var formData = new FormData($("#form")[0]);
    $.ajax({
      type: 'POST',
      url: "json/modificarRecursoFinancieroJson.php?action=2",
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
            document.location = 'listar_GF_RECURSO_FINANCIERO.php';
          })

        } else{
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
      url: "json/modificarRecursoFinancieroJson.php?action=3",
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
            document.location = 'listar_GF_RECURSO_FINANCIERO.php';
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

  function existente() {
    var codi = document.form.codigo.value;
    var result = '';

    if (codi == null || codi == '' || codi == "Codigo") {

      $("#myModal2").modal('show'); //consulta si el campo tiene algun valor, pero como es en el mdificar siempre va tener un dato, no se necesita

    } else { //se hace un envio por POST tomando el valor del camppo y consultando y como resultado me imprime un campo oculto con el ID y un modal preguntando si deseo cargar los datos.

      $.ajax({
        data: {
          "cod": codi
        },

        type: "POST",
        url: "consultarRecursoFinan.php",
        success: function(data) {
          if(data==2){
            $("#myModal2").modal('show');
          }
        }
      });
    }
  }
</script>

<script type="text/javascript">
  $('#ver1').click(function() {
    var id = document.getElementById("id").value;
    console.log(id);
    document.location = 'modificar_GF_RECURSO_FINANCIERO.php?id_recurso=' + id;
  });
</script>
<script type="text/javascript">
  $('#ver2').click(function() {
    var dato = document.form.dato.value;
    $("#codigo").val(dato)
  });
</script>
</body>

</html>