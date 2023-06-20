<?php
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
require_once 'head_listar.php';

$action = $_REQUEST['action'];
$id = $_SESSION['id_tercero'];
$perfil = $_SESSION['tipo_perfil'];

//print_r($_SESSION);

switch ($_SESSION['conexion']) {
  case 1:
    $rowP = $con->Listar("SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'");
    $idperfil = $rowP[0][0];
    //echo $idperfil;
    break;
  case 2:
    $rowP = "SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'";
    $stmt = oci_parse($oracle, $rowP);        // Preparar la sentencia
    $ok   = oci_execute($stmt);            // Ejecutar la sentencia
    if ($ok == true) {
      $objPerfil = oci_fetch_assoc($stmt);

      $idperfil = $objPerfil['ID_UNICO'];
      //echo $idperfil;
    }
    break;
  default:
    # code...
    break;
}


?>
<title>Perfil Condición</title>
</head>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<script type="text/javascript" src="js/select2.js"></script>

<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <!--Lllamado al menu    -->
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-8 text-left" style="margin-top:-5px">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px; height:45px;">Perfil condición</h2>
        <!-- Botón volver -->
        <a href="<?php echo "modificar_GF_BANCO_JURIDICA.php?action=1&id_bancoJur=" . base64_encode($id);  ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo 'Perfil: ' . $perfil; ?></h5>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top:5px;" class="client-form">
          <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javascript:agregar()">
            <p align="center" style="margin-bottom: 25px; margin-top:5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <input type="hidden" id="perfil" name="perfil" value="<?php echo $idperfil; ?>">
            <div class="form-group" class="col-sm-4" style="margin-top:-10px;" align="center">
              <label for="condicion" class="control-label"><strong style="color:#03C1FB;">*</strong>Condición:</label>
              <select style="display:inline-block; width:250px; margin-right:40px" name="condicion" id="condicion" class="form-control select2_single" title="Seleccione el condición" required="required">
                <option value="">Condición</option>
                <?php
                $condicion = $con->Listar("SELECT id_unico, nombre FROM gf_condicion ORDER BY nombre ASC");

                for ($rowCondicion = 0; $rowCondicion < count($condicion); $rowCondicion++) { ?>
                  <option value="<?php echo $condicion[$rowCondicion][0]; ?>">
                    <?php echo ucwords(strtolower($condicion[$rowCondicion][1])); ?>
                  </option>
                <?php  } ?>
              </select>
              <label for="condicion" class="control-label"><strong style="color:#03C1FB;">*</strong>Obligatorio:</label>
              <div style="display:inline; margin-right:20px">
                <input type="radio" name="obligatorio" id="obligatorio" value="0">Sí
                <input type="radio" name="obligatorio" id="obligatorio" value="1">No

              </div>
              <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;">Guardar</button>
            </div>
          </form>
        </div>

        <!--  tabla para LISTAR la informacion -->
        <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 5px; margin-bottom: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">

              <thead>
                <tr>
                  <td class="oculto">Identificador</td>
                  <td width="7%"></td>
                  <td class="cabeza"><strong>Condición</strong></td>
                  <td class="cabeza"><strong>Obligatorio</strong></td>
                </tr>

                <tr>
                  <th class="oculto">Identificador</th>
                  <th width="7%"></th>
                  <th>Condición</th>
                  <th>Obligatorio</th>
                </tr>
              </thead>

              <tbody>
                <?php
                //consulta para traer los datos a listar
                $perfilC = $con->Listar("SELECT pc.perfil, pc.condicion, c.nombre, pc.obligatorio, pc.id_unico 
                      FROM gf_perfil_condicion pc 
                      LEFT JOIN gf_condicion c ON c.id_unico = pc.condicion
                      WHERE pc.perfil = $idperfil");
                for ($rowPerCond = 0; $rowPerCond < count($perfilC); $rowPerCond++) { ?>
                  <tr>
                    <td style="display: none;"><?php echo $perfilC[$rowPerCond][4] ?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $perfilC[$rowPerCond][4] ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $perfilC[$rowPerCond][4] . ',' . $perfilC[$rowPerCond][0] . ',' . $perfilC[$rowPerCond][1] . ',' . $perfilC[$rowPerCond][3] ?>)"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                    </td>
                    <td class="campos"><?php echo ucwords(strtolower($perfilC[$rowPerCond][2])); ?></td>
                    <td class="campos"><?php if ($perfilC[$rowPerCond][3] == '0') {
                                          echo 'Sí';
                                        } else {
                                          echo 'No';
                                        } ?></td>
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
      <div class="col-sm-2 text-center" align="center" style="margin-top:-20px">
        <h2 class="titulo" align="center" style=" font-size:17px; height:45px;">Adicional</h2>
        <div align="center">
          <a href="Registrar_GF_CONDICION.php" class="btn btn-primary btnInfo">CONDICIÓN</a>
        </div>
      </div>
    </div>
  </div>

  <!--  LLamado al pie de pagina -->
  <?php require_once 'footer.php'; ?>

  <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({

        allowClear: true
      });
    });
  </script>

  <!--Modal Registrar-->
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
          <form name="form" method="POST" action="javascript:modificarItem()">
            <div style="margin-top: 13px;">
              <input type="hidden" id="perfil" name="perfil">
              <input type="hidden" id="id" name="id">
              <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Condición:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="condicionM" id="condicionM" class="form-control select2_single" title="Seleccione condición" required>
                  <?php
                  $condicion1 = $con->Listar("SELECT id_unico, nombre FROM gf_condicion ORDER BY nombre ASC");
                  for ($rc = 0; $rc < count($condicion1); $rc++) { ?>
                    <option value="<?php echo $condicion1[$rc][0]; ?>">
                      <?php echo ucwords(strtolower($condicion1[$rc][1])); ?>
                    </option>
                  <?php  } ?>
                </select>
              </div>
              <div class="form-group" style="margin-top: 13px;">

                <label for="obligatorio" style="margin-left: 10px;display:inline-block; width:140px;"><strong style="color:#03C1FB;">*</strong>Obligatorio:</label></td>

                <div align="left" style="display:inline-block; width:250px; margin-bottom:15px; height:40px">
                  <input type="radio" name="obli" id="obli1" value="0" checked>SI
                  <input type="radio" name="obli" id="obli2" value="1" checked>NO
                </div>
              </div>
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La informaci&oacute;n no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal8" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">

          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La Condici&oacuten Tercero ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
          <p> &iquest;Desea eliminar el registro seleccionado de Perfil Condici&oacute;n?</p>
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">

          <p>Informaci&oacute;n eliminada correctamente.</p>

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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la informaci&oacute;n, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>



  <!-- Función para retornar al formulario principal. -->
  <script type="text/javascript">
    $("#ver5").click(function() {

      document.location = "GF_PERFIL_CONDICION.php";
    });

    $("#ver1").click(function() {

      document.location = "GF_PERFIL_CONDICION.php";
    });

    $("#ver2").click(function() {

      document.location = "GF_PERFIL_CONDICION.php";
    });
  </script>


  <!-- Función para agregar-->
  <script type="text/javascript">
    function agregar() {
      jsShowWindowLoad('Agregando Datos ...');
      var formData = new FormData($("#form")[0]);
      $.ajax({
        type: 'POST',
        url: "json/modificarPerfilCondJson.php?action=2",
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
              document.location = 'GF_PERFIL_CONDICION.php';
            })

          } else if(response == 3){
            $("#mensaje").html('Este registro ya se encuentra en la base de datos. Registro fallido.');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })

          }else{
            $("#mensaje").html('No Se Ha Podido Agregar Información');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function() {
              $("#modalMensajes").modal("hide");
            })
          }
        }
      });
    }
  </script>

  <!-- Función para la opcion modificar. -->

  <script type="text/javascript">
    function modificarModal(id, perfil, condicion, obligatorio) {

      $("#condicionM").val(condicion);
      document.getElementById('condicionM').value = condicion;
      if (obligatorio === 0) {
        document.getElementsByName("obli")[0].checked = true;
      } else {
        document.getElementsByName("obli")[1].checked = true;
      }
      document.getElementById('id').value = id;
      document.getElementById('perfil').value = perfil;
      $("#myModalUpdate").modal('show');
    }

    function modificarItem() {
      var result = '';
      var id = document.getElementById('id').value;
      var perfil = document.getElementById('perfil').value;
      var condicion = document.getElementById('condicionM').value;
      if (document.getElementById('obli1').checked) {
        var obligatorio = '0';
      } else {
        var obligatorio = '1';
      }
      $.ajax({
        type: "GET",
        url: "json/modificarPerfilCondJson.php?action=3&p1=" + id + "&p2=" + perfil + "&p3=" + condicion + "&p4=" + obligatorio,
        success: function(data) {
          result = JSON.parse(data);

          if (result == '3') {
            $("#myModal8").modal('show');
            $("#ver8").click(function() {
              $("#myModal8").modal('hide');
              $("#myModalUpdate").modal('hide');
            });
          } else {
            if (result == true) {
              $("#myModal5").modal('show');
              $("#ver5").click(function() {
                $("#myModal5").modal('hide');
                $("#myModalUpdate").modal('hide');
              });

            } else {
              $("#myModal6").modal('show');
              $("#ver6").click(function() {
                $("#myModal6").modal('hide');
                $("#myModalUpdate").modal('hide');
              });


            }
          }
        }
      });
    }
  </script>

  <!-- Función para la opcion eliminar -->
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
          url: "Json/modificarPerfilCondJson.php?action==1",
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
              document.location = "GF_PERFIL_CONDICION.php";
            });
            $("#ver2").click(function() {
              document.location = "GF_PERFIL_CONDICION.php";
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
</body>

</html>