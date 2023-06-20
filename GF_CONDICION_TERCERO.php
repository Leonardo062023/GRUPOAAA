<?php
@session_start();
require_once('Conexion/conexion.php');
require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';

$con = new ConexionPDO();
$action = $_REQUEST['action'];

$id = $_SESSION['id_tercero'];
$conexion = $_SESSION['conexion'];
$perfil = $_SESSION['tipo_perfil'];

if ($_SESSION['perfil'] == "N") {
  switch ($conexion) {
    case 1:
      $rowTer = $con->Listar("SELECT CONCAT(t.NombreUno, ' ', t.NombreDos, ' ', t.ApellidoUno, ' ', t.ApellidoDos) AS NOMBRE, CONCAT(ti.Nombre, ': ', t.NumeroIdentificacion) AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
      WHERE t.Id_Unico = $id;
      ");
      break;
    case 2:
      $rowTer = $con->Listar("SELECT t.NombreUno || ' ' || t.NombreDos || ' ' || t.ApellidoUno || ' ' || t.ApellidoDos AS NOMBRE, ti.Nombre || ': ' || t.NumeroIdentificacion AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
      WHERE t.Id_Unico = $id");
      break;
    default:
      break;
  }
} elseif ($_SESSION['perfil'] == "J") {
  switch ($conexion) {
    case 1:
      $rowTer = $con->Listar("SELECT t.razonsocial, CONCAT(ti.Nombre, ': ', t.NumeroIdentificacion) AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
      WHERE t.Id_Unico = $id");
      break;
    case 2:
      $rowTer = $con->Listar("SELECT t.razonsocial, ti.Nombre || ': ' || t.NumeroIdentificacion AS identificacion
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
      WHERE t.Id_Unico = $id");
      break;
    default:
      break;
  }
}

switch ($_SESSION['conexion']) {
  case 1:
    $rowP = $con->Listar("SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'");
    $idperfil = $rowP[0][0];

    break;
  case 2:
    $rowP = "SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'";
    $stmt = oci_parse($oracle, $rowP);        // Preparar la sentencia
    $ok   = oci_execute($stmt);            // Ejecutar la sentencia
    if ($ok == true) {
      $objPerfil = oci_fetch_assoc($stmt);

      $idperfil = $objPerfil['ID_UNICO'];
    }
    break;
  default:
    break;
}
?>


<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
<script type="text/javascript" src="js/select2.js"></script>

<script src="js/jquery-ui.js"></script>
<link href="css/custom1.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<style>
  label#perfil-error,
  #valoraO-error,
  #valoranO-error,
  #valortaO-error,
  #valornO-error,
  #valorbO-error,
  #valorfO-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

  }
</style>

<script>
  $().ready(function() {
    var validator = $("#form").validate({
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
<script>
  $(function() {

    $.datepicker.regional['es'] = {
      closeText: 'Cerrar',
      prevText: 'Anterior',
      nextText: 'Siguiente',
      currentText: 'Hoy',
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
      dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mi�', 'Juv', 'Vie', 'S&aacute;b'],
      dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'S&aacute;'],
      weekHeader: 'Sm',
      dateFormat: 'dd/mm/yy',
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);


    $("#valorf").datepicker({
      changeMonth: true,
    }).val();
    $("#valorfM").datepicker({
      changeMonth: true,
    }).val();

  });
</script>
<title>Registrar Condici&oacute;n Tercero</title>
<style>
  .error-message {
    color: #155180;
    font-weight: normal;
    font-style: italic;
    display: none;

  }

  .prueba {
    margin-left: 140px;
    margin-top: -15px;
    margin-bottom: 5px;
  }
</style>
</head>

<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-8 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Condici&oacute;n Tercero</h2>
        <a href="<?php echo "modificar_GF_BANCO_JURIDICA.php?action=1&id_bancoJur=" . base64_encode($id); ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((strtolower($rowTer[0][0])));
                                                                                                                                                                                                                                ?></h5>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form ">
          <form id="form" name="form" class="form-horizontal form-label-left" method="POST" enctype="multipart/form-data" action="javascript:agregar()" novalidate style="margin-left:40px">
            <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <input type="hidden" name="tercero" value="<?php echo $id ?>">
            <div class="form-group form-inline" style="width:850px">
              <label for="perfil" class=" control-label col-sm-2" style="width:160px; margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Perfil Condici&oacute;n:</label>
              <select name="perfil" id="perfil" class="select2 form-control col-sm-2" title="Seleccione el perfil condici&oacute;n" required="required" style="width:200px; margin-top:-10px;" onchange="javaScript:valorI();">
                <option value="">Seleccione el Perfil Condici&oacute;n</option>
                <?php
                $perfilC = $con->Listar("SELECT pc.id_unico, c.nombre FROM gf_perfil_condicion pc LEFT JOIN gf_condicion c ON pc.condicion=c.id_unico WHERE pc.perfil=$idperfil ORDER BY c.nombre ASC");

                for ($rowC = 0; $rowC < count($perfilC); $rowC++) {  ?>
                  <option value="<?php echo $perfilC[$rowC][0] ?>"><?php echo ucwords((strtolower($perfilC[$rowC][1])));
                                                                  } ?></option>;
              </select>

              <!-- Campo por default-->
              <div id="default" style="display:inline; display: block; ">
                <label for="valord" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valord" id="valord" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2" style="width:200px; margin-top:-10px" required="required">
                <button type="submit" class="btn btn-primary sombra" style="margin-top:-11px;">Guardar</button>
              </div>
              <!-- Booleano Obligatorio-->
              <div id="booleanoO" style=" display:none; margin-top:-15px">
                <label for="valorbO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <div style=" display:inline; margin-top:-10px">
                  <input type="radio" name="valorbO" id="valorbO" value="Si">SI
                  <input type="radio" name="valorbO" id="valorbO" value="No" checked>NO
                </div>
                <button type="submit" class="btn btn-primary sombra" style="margin-top:1px;">Guardar</button>
              </div>
              <!-- Booleano No Obligatorio-->
              <div id="booleanoN" style=" display:none; margin-top:-15px">
                <label for="valorbN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <div style=" display:inline; margin-top:-10px">
                  <input type="radio" name="valorbN" id="valorbN" value="Si">SI
                  <input type="radio" name="valorbN" id="valorbN" value="No">NO
                </div>
                <button type="submit" class="btn btn-primary sombra" style="margin-top:1px;">Guardar</button>
              </div>

              <input type="hidden" name="MM_insert">
            </div>
            <div class="prueba">
              <span id="perfil-error" class="error-message">Este campo es obligatorio</span>
            </div>
          </form>
        </div>

        <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td class="oculto">Identificador</td>
                  <td width="7%"></td>
                  <td class="cabeza"><strong>Perfil Condici&oacute;n</strong></td>
                  <td class="cabeza"><strong>Valor</strong></td>
                </tr>
                <tr>
                  <th class="oculto">Identificador</th>
                  <th width="7%"></th>
                  <th>Perfil Condici�n</th>
                  <th>Valor</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $tipoAct2 = $con->Listar("SELECT A.id_unico,A.condicion,B.nombre, C.valor, C.perfilcondicion, B.id_unico 
                          FROM gf_perfil_condicion A 
                          LEFT JOIN  gf_condicion B ON  A.condicion = B.Id_Unico
                          LEFT JOIN gf_condicion_tercero C ON C.perfilcondicion = A.id_unico 
                          WHERE C.tercero = $id");
                for ($row = 0; $row < count($tipoAct2); $row++) { ?>
                  <tr>
                    <td style="display: none;"><?php echo $tipoAct2[$row][0] ?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $tipoAct2[$row][0]; ?>,<?php echo $id; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $id; ?>,<?php echo $tipoAct2[$row][0]; ?>,'<?php echo ($tipoAct2[$row][3]) ?>', '<?php echo ($tipoAct2[$row][2]) ?>');"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
                    </td>
                    <td class="campos"><?php echo ucwords(strtolower($tipoAct2[$row][2])); ?></td>

                    <td class="campos"><?php
                                        echo ucwords(strtolower($tipoAct2[$row][3]));
                                        ?></td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-sm-2 text-center" align="center" style="margin-top:-15px">
        <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
        <div align="center">
          <a href="Registrar_GF_CONDICION.php" class="btn btn-primary btnInfo">CONDICI&Oacute;N</a>
        </div>
      </div>
    </div>
    <?php require_once 'footer.php'; ?>
  </div>
  <!--Modal registrar-->
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

  <div class="modal fade" id="myModalUpdate" role="dialog" align="center">
    <script src="js/select/select2.full.js"></script>
    <script>
      function validarFormulario() {
        var perfilSelect = document.getElementById("perfil");
        var perfilError = document.getElementById("perfil-error");

        if (perfilSelect.value === "") {
          perfilError.style.display = "block";
        } else {
          perfilError.style.display = "none";
        }
      }
    </script>
    <script>
      $(document).ready(function() {
        $(".select2_single").select2({

          allowClear: true
        });
      });
    </script>
    <link href="css/custom1.css" rel="stylesheet">
    <script src="js/jquery-ui.js"></script>
    <script src="dist/jquery.validate.js"></script>

    <script>
      $().ready(function() {
        var validator = $("#formM").validate({

          errorPlacement: function(error, element) {

            $(element)
              .closest("formM")
              .find("label[for='" + element.attr("id") + "']")
              .append(error);
          },
          rules: {
            valoranMO: "required",
          }
        });

        $(".cancel").click(function() {
          validator.resetForm();
        });
      });
    </script>
    <style>
      label#perfilM-error,
      #valoraMO-error,
      #valoranMO-error,
      #valortaMO-error,
      #valornMO-error,
      #valorbMO-error,
      #valorfMO-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

      }
    </style>
    <div class="modal-dialog">
      <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
        </div>
        <?php
        $tipoAct3 = $con->Listar("SELECT pc.id_unico, c.nombre FROM gf_perfil_condicion pc LEFT JOIN gf_condicion c ON pc.condicion=c.id_unico WHERE pc.perfil=$idperfil ORDER BY c.nombre ASC");
        ?>
        <div class="modal-body ">
          <form name="formM" id="formM" method="POST" action="javascript:modificarItem()">
            <div class="form-group" style="margin-top: 13px;">
              <label for="perfilM" style="display:inline-block; width:140px; border: #000;"><strong style="color:#03C1FB;">*</strong>Perfil Condici&oacuten:</label>
              <input type="text" name="perfilM1" id="perfilM1" readonly style="margin-top:20px ">
            </div>
            <!-- Campo por default-->
            <div id="default1" style="display:inline; display: block; margin-left: 140px; margin-bottom: 50px; margin-top: 0px">
              <label for="valord" class="control-label col-sm-2" style="width:130px;margin-top: 0px;margin-left: 0px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
              <input type="text" name="valord1" id="valord1" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2" style="width:200px; margin-top:-10px; margin-bottom: 20px; margin-left: -15px" required="required">
            </div>
            <!-- Booleano Obligatorio-->
            <div id="booleanoO1" style=" display:none; margin-left: 100px; margin-bottom: 10px; margin-top: 20px">
              <label for="valorbO" style="display:inline-block; width:140px; margin-left: -130px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
              <div style=" display:inline; margin-right:110px">
                <input type="hidden" name="valord2" id="valord2">

                <input type="radio" name="valorbO1" id="valorbO1" value="1" onclick="radio1()">SI
                <input type="radio" name="valorbO2" id="valorbO2" value="2" onclick="radio2()">NO
              </div>
            </div>
            <!-- Booleano No Obligatorio-->
            <div id="booleanoN1" style=" display:none; margin-left: 100px; margin-bottom: 10px; margin-top: 20px">
              <label for="valorbMO" style="display:inline-block; width:140px; margin-left: -130px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
              <div style=" display:inline; margin-right:110px">
                <input type="hidden" name="valord2" id="valord2">
                <input type="radio" name="valorbN1" id="valorbN1" value="1" onclick="radio1()">SI
                <input type="radio" name="valorbN2" id="valorbN2" value="2" onclick="radio2()">NO
              </div>
            </div>

        </div>
        <input type="hidden" name="perfilM" id="perfilM">
        <input type="hidden" id="tercero" name="tercero">
        <input type="hidden" id="perfilA" name="perfilA">
      </div>
      <script type="text/javascript">
        function borrarRadio() {
          document.getElementsByName("valorbMN")[0].checked = false;
          document.getElementsByName("valorbMN")[1].checked = false;
        }
      </script>

      <div id="forma-modal" class="modal-footer">
        <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
      </div>
      </form>
    </div>
  </div>
  </div>
  <div class="modal fade" id="myModal5" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
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
  <div class="modal fade" id="myModal7" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El Perfil Condici&oacute;n ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p> &iquest;Desea eliminar el registro seleccionado de Condici&oacute;n Tercero?</p>
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
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
          <p>No se pudo eliminar la informaci&oacute;n, el registro seleccionado est&aacute; siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $("#ver5").click(function() {
      document.location = "GF_CONDICION_TERCERO.php";
    });
  </script>
  <script type="text/javascript">
    function modificarModal(tercero, perfil, valor, nombrePerfil) {
      $("#perfilM").val(perfil);
      $("#perfilM1").val(nombrePerfil);
      $("#valord1").val(valor);
      $("#valord2").val(valor);
      document.getElementById('tercero').value = tercero;
      document.getElementById('perfilA').value = perfil;
      $.ajax({
        type: "GET",
        url: "json/modificarCondicionTerJson.php?action=4&id=" + perfil,
        datatype: "json",
        success: function(data) {
          var d = data.split(" - ");
          var result = d[0];
          var obl = d[1];
          //alert(obl + " " + result)
          switch (true) {
            case (result == 'Booleano') && (obl == '0'):
              document.getElementById('default1').style.display = 'none';
              document.getElementById('booleanoO1').style.display = 'block';
              var input = document.getElementById('valord2');
              //alert($("#perfilM1").val())
              if (input == "Si") {
                $('#valorbO1').val(1);
                $('#valorbO1').prop('checked', true);
              } else {
                $('#valorbO2').val(2);
                $('#valorbO2').prop('checked', true);
              }

              break;
            case (result == 'Booleano') && (obl == '1'):
              //alert(input)
              document.getElementById('default1').style.display = 'none';
              document.getElementById('booleanoN1').style.display = 'block';
              var input = $('#valord1').val();
              //alert(input)
              if (input == "No") {
                //alert("entro")
                //$('#valorbN1').prop('checked', true);

                $('#valorbN2').val(2);
                $('#valorbN2').prop('checked', true);
                //alert($('#valorbN1').val())

              } else if (input == "Si") {
                //alert("entro elseif")
                $('#valorbN1').val(1);
                $('#valorbN1').prop('checked', true);
              }

              break;
            case (result == 'Fecha') && (obl == '0'):
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord1');
              input.type = "text";
              $('#valord1').datepicker();
              break;
            case (result == 'Fecha') && (obl == '1'):
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord1')
              input.type = "text";
              $('#valord1').datepicker();
              break;
            default:
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord1');
              input.type = "text";
              $('#valord1').datepicker("destroy");
              break;
          }
        }
      });
      $("#myModalUpdate").modal('show');
    }


    function radio1() {
      $('#valorbO2').prop('checked', false);
      $('#valorbN2').prop('checked', false);
      $('#valord1').val("Si");
      //valorbN2
    }

    function radio2() {
      $('#valorbO1').prop('checked', false);
      $('#valorbN1').prop('checked', false);
      $('#valord1').val("No");
    }

    function agregar() {
      var perfilSelect = document.getElementById("perfil");
      var perfilError = document.getElementById("perfil-error");

      if (perfilSelect.value === "") {
        perfilError.style.display = "block";
      } else {
        perfilError.style.display = "none";
        jsShowWindowLoad('Agregando Datos ...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
          type: 'POST',
          url: "json/modificarCondicionTerJson.php?action=2",
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
                document.location = 'GF_CONDICION_TERCERO.php';
              })

            } else if (response == 3) {
              $("#mensaje").html('Este registro ya existe. No se ha registrado la información correctamente');
              $("#modalMensajes").modal("show");
              $("#Aceptar").click(function() {
                $("#modalMensajes").modal("hide");
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

    }

    function modificarItem() {
      var tercero = document.getElementById('tercero').value;
      var perfilM = document.getElementById('perfilM').value;

      var valor = document.getElementById('valord1').value;
      var perfilA = document.getElementById('perfilA').value;
      //alert(valor)
      $.ajax({
        type: "GET",
        url: "json/modificarCondicionTerJson.php?action=3&p1=" + tercero + "&p2=" + perfilM + "&p3=" + valor + "&p4=" + perfilA,
        success: function(data) {
          result = JSON.parse(data);
          if (result == true) {
            $("#myModal5").modal('show');
            $("#ver5").click(function() {
              $("#myModal5").modal('hide');
              $("#myModalUpdate").modal('hide');
            });
          } else {
            if (result == '3') {
              $("#myModal7").modal('show');
              $("#ver7").click(function() {
                $("#myModal7").modal('hide');
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

  <script type="text/javascript">
    function eliminarItem(id, tercero) {
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
          url: "Json/modificarCondicionTerJson.php?action==1",
          data: form_data,
          success: function(response) {
            jsRemoveWindowLoad();
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
              document.location = "GF_CONDICION_TERCERO.php";
            });
            $("#ver2").click(function() {
              document.location = "GF_CONDICION_TERCERO.php";
            });
          }
        });
      });
    }
  </script>
  <script type="text/javascript">
    function valorI() {
      condicion = document.getElementById("perfil").value;
      resultado = '';
      $.ajax({
        type: "GET",
        url: "json/modificarCondicionTerJson.php?action=4&id=" + condicion,
        datatype: "json",
        success: function(data) {
          var d = data.split(" - ");
          var result = d[0];
          var obl = d[1];
          // alert(result)
          // alert(obl)
          switch (true) {
            case (result == 'Booleano') && (obl == '0'):
              document.getElementById('default').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'block';
              break;
            case (result == 'Booleano') && (obl == '1'):
              document.getElementById('default').style.display = 'none';
              document.getElementById('booleanoN').style.display = 'block';
              break;
            case (result == 'Fecha') && (obl == '0'):
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord');
              input.type = "text";
              $('#valord').datepicker();
              break;
            case (result == 'Fecha') && (obl == '1'):
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord')
              input.type = "text";
              $('#valord').datepicker();
              break;
            default:
              document.getElementById('default').style.display = 'block';
              document.getElementById('booleanoN').style.display = 'none';
              document.getElementById('booleanoO').style.display = 'none';
              var input = document.getElementById('valord');
              input.type = "text";
              $('#valord').datepicker("destroy");
              break;
          }
        }
      });
    }
  </script>
  <script type="text/javascript">
    function cambiarM() {
      condicion = document.getElementById("perfilM").value;
      $.ajax({
        type: "GET",
        url: "json/modificarCondicionTerJson.php?action=4&id=" + condicion,
        datatype: "json",
        success: function(data) {
          var d = data.split(" - ");
          var result = d[0];
          var obl = d[1];
          switch (true) {
            case (result == 'Booleano') && (obl == '0'):
              document.getElementById('default1').style.display = 'none';
              document.getElementById('booleanoO1').style.display = 'block';

              break;
            case (result == 'Booleano') && (obl == '1'):
              document.getElementById('default1').style.display = 'none';
              document.getElementById('booleanoN1').style.display = 'block';

              break;
            case (result == 'Fecha') && (obl == '0'):
              document.getElementById('default1').style.display = 'block';
              document.getElementById('booleanoN1').style.display = 'none';
              document.getElementById('booleanoO1').style.display = 'none';
              var input = document.getElementById('valord');
              input.type = "text";
              $('#valord1').datepicker();

              var valor = document.getElementById('valord').value;
              $('#valord1').val(valor);
              break;
            case (result == 'Fecha') && (obl == '1'):
              document.getElementById('default1').style.display = 'block';
              document.getElementById('booleanoN1').style.display = 'none';
              document.getElementById('booleanoO1').style.display = 'none';
              var input = document.getElementById('valord')
              input.type = "text";
              $('#valord1').datepicker();

              var valor = document.getElementById('valord').value;
              $('#valord1').val(valor);

              break;
            default:
              document.getElementById('default1').style.display = 'block';
              document.getElementById('booleanoN1').style.display = 'none';
              document.getElementById('booleanoO1').style.display = 'none';
              var input = document.getElementById('valord');
              input.type = "text";
              $('#valord1').datepicker("destroy");

              var valor = document.getElementById('valord').value;
              $('#valord1').val(valor);
              break;
          }
        }
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