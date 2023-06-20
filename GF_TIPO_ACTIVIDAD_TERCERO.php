<?php
require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';

$con = new ConexionPDO();
$action = $_REQUEST['action'];

$id = $_SESSION['id_tercero'];
$datosTercero = "";
$conexion = $_SESSION['conexion'];

if ($_SESSION['perfil'] == "N") {
  //Consulta para el listado de registro de la tabla gf_tercero para naturales.
  switch ($conexion) {
    case 1:
      $rowTer = $con->Listar("SELECT t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
      WHERE t.Id_Unico = $id      
      ");
      break;
    case 2:
      $rowTer = $con->Listar("SELECT t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, '(' || ti.Nombre || ': ' || t.NumeroIdentificacion || ')' AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
      WHERE t.Id_Unico = $id");
      break;
    default:
      break;
  }
} elseif ($_SESSION['perfil'] == "J") {
  //Consulta para el listado de registro de la tabla gf_tercero para juridicos.
  switch ($conexion) {
    case 1:
      $rowTer = $con->Listar("SELECT t.razonsocial, CONCAT('', s.nombre) AS sucursal, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
      WHERE t.Id_Unico = $id;
      ");
      break;
    case 2:
      $rowTer = $con->Listar("SELECT t.razonsocial, '' || s.nombre AS sucursal, '(' || ti.Nombre || ': ' || t.NumeroIdentificacion || ')' AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
      WHERE t.Id_Unico = $id");
      break;
    default:
      # code...
      break;
  }
}

?>

<title>Tipo Actividad Tercero</title>
</head>

<body>
  <div class="container-fluid text-center">
    <div class="row content">

      <!--Llamado al menu    -->
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-8 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Tipo Actividad Tercero</h2>
        <!-- Bot�n volver -->
        <a href="<?php echo "modificar_GF_BANCO_JURIDICA.php?action=1&id_bancoJur=" . base64_encode($id); ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <!-- Nombre del tercero -->
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((strtolower($rowTer[0][0])));
                                                                                                                                                                                                                                ?></h4>
          <!-- Caja para REGISTRAR la informacion -->
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
            <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
              <p align="center" style="margin-bottom: 25px; margin-top:5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="tercero" value="<?php echo $id ?>">

              <!-- Combo TIPO ACTIVIDAD -->
              <div class="form-group" class="col-sm-4" style="margin-top:-30px;" align="center">
                <label for="tipoA" class="control-label "><strong style="color:#03C1FB;">*</strong>Tipo Actividad:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px;  text-align-last:left; margin-top: 20px" name="tipoA" id="tipoA" class="select2 form-control" title="Seleccione el tipo actividad" onblur="return existente()" required>
                  <option value="">Tipo Actividad</option>
                  <?php
                  //consulta para trear los datos del combo tipo actividad
                  $tipoAct = $con->Listar("SELECT id_unico, nombre, codigo_actividad FROM gf_tipo_actividad ORDER BY nombre ASC");
                  for ($t = 0; $t < count($tipoAct); $t++) { ?>
                    <option value="<?php echo $tipoAct[$t][0]; ?>">
                      <?php echo ucwords((strtolower($tipoAct[$t][1]) . "   (C&oacute;digo: " . ($tipoAct[$t][2]) . ")")); ?>
                    </option>
                  <?php  } ?>
                </select>
                <input type="hidden" name="tercero" value="<?php echo $id ?>">
                <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;">Guardar</button>
              </div>

              <div align="center"></div>
              <div class="texto" style="display:none"></div>
              <input type="hidden" name="MM_insert">
            </form>
          </div>



          <!--  tabla para LISTAR la informacion -->
          <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
              <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">

                <thead>
                  <tr>
                    <td class="oculto">Identificador</td>
                    <td width="7%"></td>
                    <td class="cabeza"><strong>Tipo Actividad</strong></td>
                  </tr>

                  <tr>
                    <th class="oculto">Identificador</th>
                    <th width="7%"></th>
                    <th>Tipo Actividad</th>
                </thead>

                <tbody>
                  <?php

                  //consulta para traer los datos a listar
                  $tipoAct2 = $con->Listar("SELECT A.Id_Unico, A.Nombre, B.tipoactividad, A.codigo_actividad  
                        FROM gf_tipo_actividad A 
                        LEFT JOIN gf_tipo_actividad_tercero B ON B.tipoactividad = A.id_unico 
                        WHERE B.Tercero = $id");
                  for ($t1 = 0; $t1 < count($tipoAct2); $t1++) { ?>
                    <tr>
                      <td style="display: none;"><?php echo $tipoAct2[$t1][0] ?></td>
                      <td align="center" class="campos">
                        <a href="#" onclick="javascript:eliminarItem(<?php echo $tipoAct2[$t1][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                        <a onclick="modificarModal(<?php echo $id; ?>,<?php echo $tipoAct2[$t1][2]; ?>)"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                      </td>
                      <td class="campos"><?php echo ($tipoAct2[$t1][1]) . "  (C&oacute;digo: " . ($tipoAct2[$t1][3]) . ")"; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
      </div>


      <!--  Botones opcionales del lado derecho  -->
      <div class="col-sm-2 text-center" align="center">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Adicional</h2>
        <div align="center">
          <a href="GF_TIPO_ACTIVIDAD.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">TIPO ACTIVIDAD</a>

        </div>
      </div>
    </div>
  </div>

  <!--  LLamado al pie de pagina -->
  <?php require_once 'footer.php'; ?>

  <!-- Modal registrar-->
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
  <!--  MODAL y opcion  MODIFICAR  informacion  -->
  <div class="modal fade" id="myModalUpdate" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
        </div>

        <div class="modal-body ">
          <form name="form1" id="form1" method="POST" action="javascript:modificar()">
            <div style="margin-top: 13px;">
              <label style="display:inline-block; width:140px">Tipo Actividad:</label>
              <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="form-control" title="Seleccione tipo actividad" required>
                <?php

                //consultas para modificar el campo TIPO ACTIVIDAD
                $tipoAct3 = $con->Listar("SELECT id_unico, nombre, codigo_actividad FROM gf_tipo_actividad ORDER BY nombre ASC");
                for ($t2 = 0; $t2 < count($tipoAct3); $t2++) {  ?>
                  <option value="<?php echo $tipoAct3[$t2][0]; ?>">
                    <?php echo ucwords((strtolower($tipoAct3[$t2][1]) . "   (C&oacute;digo: " . ($tipoAct3[$t2][2]) . ")")); ?>
                  </option>
                <?php

                } ?>
              </select>
              <input type="hidden" id="id" name="id">
            </div>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
          <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>

        </div>
        </form>
      </div>
    </div>
  </div>



  <!--  MODAL para los mensajes del  modificar -->

  <div class="modal fade" id="myModal5" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Informaci&oacute;n modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal6" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci�n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La informaci�n no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!--  MODAL para los mensajes de la opcion  eliminar -->

  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>�Desea eliminar el registro seleccionado de Tipo Actividad Tercero?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci�n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">

          <p>Informaci�n eliminada correctamente.</p>

        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci�n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la informaci�n, el registro seleccionado est� siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!-- librerias -->
  <script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/select2.css">
  <link rel="stylesheet" href="css/select2-bootstrap.min.css" />
  <script type="text/javascript" src="js/select2.js"></script>

  <!-- Funci�n para retornar al formulario principal. -->
  <script type="text/javascript">
    $("#ver5").click(function() {

      document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
    });

    $("#ver1").click(function() {

      document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
    });

    $("#ver2").click(function() {

      document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
    });
  </script>

  <!-- Funci�n para la opcion modificar. -->

  <script type="text/javascript">
    function modificarModal(id, tipoA) {

      $("#tipoActmodal").val(tipoA);
      document.getElementById('id').value = id;
      $("#myModalUpdate").modal('show');
    }

    function agregar() {
      jsShowWindowLoad('Agregando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "json/modificarTipoActividadTerJson.php?action=2",
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
              document.location = 'GF_TIPO_ACTIVIDAD_TERCERO.php';
            })

          } else {
            $("#mensaje").html('No Se Ha Podido Agregar Información');
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
      var formData = new FormData($("#form1")[0]);

      $.ajax({
        type: 'POST',
        url: "json/modificarTipoActividadTerJson.php?action=3",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          jsRemoveWindowLoad();
          if (response == 1) {
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function() {

              $("#myModal5").modal('hide');
              document.location = 'GF_TIPO_ACTIVIDAD_TERCERO.php';
            })
          } else {
            $("#myModal6").modal('show');
            $("#ver6").click(function() {
              $("#myModal6").modal('hide');
            });

          }
        }
      });
    }
  </script>

  <!-- Funci�n para la opcion eliminar -->

  <script type="text/javascript">
    function eliminarItem(id) {
      $("#myModal").modal('show');
      $("#ver").click(function() {
        jsShowWindowLoad('Eliminando Datos ...');
        $("#mymodal").modal('hide');
        var form_data = {
          action: 1,
          id: id
        };
        $.ajax({
          type: 'POST',
          url: "Json/modificarTipoActividadTerJson.php?action==1",
          data: form_data,
          success: function(response) {
            jsRemoveWindowLoad();
            console.log(response);
            if (response == 1) {
              $("#mensaje").html('Información Eliminada Correctamente');
              $("#myModal1").modal('show');
            } else {
              $("#mensaje").html('No se puede eliminar la información, ya que la actividad posee Seguimiento(s)');
              $("#modalMensajes").modal("show");
              $("#Aceptar").click(function() {
                $("#modalMensajes").modal("hide");
              })
            }
            $("#ver1").click(function() {
              document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php";
            });
            $("#ver2").click(function() {
              document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php";
            });
          }
        });
      });
    }
  </script>

  <script type="text/javascript">
    $(".select2").select2();

    $().ready(function() {
      var validator = $("#form").validate({
        ignore: "",
        errorElement: "em",
        errorPlacement: function(error, element) {
          error.addClass('help-block');
        },
        highlight: function(element, errorClass, validClass) {
          var elem = $(element);
          if (elem.hasClass('select2-offscreen')) {
            $("#s2id_" + elem.attr("id")).addClass('has-error').removeClass('has-success');
          } else {
            $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
            $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
            $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
          }
          if ($(element).attr('type') == 'radio') {
            $(element.form).find("input[type=radio]").each(function(which) {
              $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
              $(this).addClass("has-error");
            });
          } else {
            $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
            $(element).addClass("has-error");
          }
        },
        unhighlight: function(element, errorClass, validClass) {
          var elem = $(element);
          if (elem.hasClass('select2-offscreen')) {
            $("#s2id_" + elem.attr("id")).addClass('has-success').removeClass('has-error');
          } else {
            $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
            $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
            $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
          }
          if ($(element).attr('type') == 'radio') {
            $(element.form).find("input[type=radio]").each(function(which) {
              $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
              $(this).addClass("has-success").removeClass("has-error");
            });
          } else {
            $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
            $(element).addClass("has-success").removeClass("has-error");
          }
        }
      });
    });
  </script>



</body>

</html>