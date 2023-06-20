<?php

require_once('Conexion/conexionPDO.php');
require_once 'head_listar.php';
$con = new ConexionPDO();

//$anno = $_SESSION['anno'];
$conexion = $_SESSION['conexion'];

$_SESSION['perfil'] = "J";
$_SESSION['url'] = "registrar_GF_BANCO_JURIDICA.php";
$compania = $_SESSION['compania'];

#****** Tipo Identificación *********#
/*$sqlTipoIden = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  ORDER BY Nombre ASC";
$tipoIden = $mysqli->query($sqlTipoIden);*/

$tipoIden = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_identificacion
ORDER BY Nombre ASC");

#****** Sucursal *********#
/*$sqlSucursal = "SELECT Id_Unico, Nombre 
  FROM gf_sucursal
  ORDER BY Nombre ASC";
$sucursal = $mysqli->query($sqlSucursal);*/
$sucursal = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_sucursal
ORDER BY Nombre ASC");

#****** Tipo Régimen *********#
/*$sqlTipoReg = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_regimen
  ORDER BY Nombre ASC";
$tipoReg = $mysqli->query($sqlTipoReg);*/

$tipoReg = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_regimen
ORDER BY Nombre ASC");

#****** Tipo Empresa *********#
/*$sqlTipoEmp = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_empresa
  ORDER BY Nombre ASC";
$tipoEmp = $mysqli->query($sqlTipoEmp);*/
$tipoEmp = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_empresa
ORDER BY Nombre ASC");

#****** Tipo Entidad *********#
/*$sqlTipoEnt = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_entidad
  ORDER BY Nombre ASC";
$tipoEnt = $mysqli->query($sqlTipoEnt);*/

$tipoEnt = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_entidad
ORDER BY Nombre ASC");
#****** Representante Legal *********#
// $sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
//   FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
//   WHERE t.TipoIdentificacion = ti.Id_Unico
//   AND t.Id_Unico = pt.Tercero 
//   AND pt.Perfil = 10 AND t.compania = $compania 
//   ORDER BY t.NombreUno ASC";
// $repreLegal = $mysqli->query($sqlReprLeg);

$repreLegal = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
   FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
   WHERE t.TipoIdentificacion = ti.Id_Unico
   AND t.Id_Unico = pt.Tercero 
   AND pt.Perfil = 10 AND t.compania = $compania 
   ORDER BY t.NombreUno ASC");

#****** Contacto *********#
// $sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
//   FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
//   WHERE t.TipoIdentificacion = ti.Id_Unico
//   AND t.Id_Unico = pt.Tercero 
//   AND pt.Perfil = 10 AND t.compania = $compania 
//   ORDER BY t.NombreUno ASC";
// $contacto = $mysqli->query($sqlContacto);

$contacto = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
   FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
   WHERE t.TipoIdentificacion = ti.Id_Unico
   AND t.Id_Unico = pt.Tercero 
   AND pt.Perfil = 10 AND t.compania = $compania 
   ORDER BY t.NombreUno ASC");

#****** Zona *********#
// $sqlZona = "SELECT Id_Unico, Nombre 
//   FROM gf_zona
//   ORDER BY Nombre ASC";
// $zona = $mysqli->query($sqlZona);

$zona = $con->Listar("SELECT Id_Unico, Nombre 
   FROM gf_zona
   ORDER BY Nombre ASC");

#****** Departamento  *********#
// $sqlDep = "SELECT Id_Unico, Nombre 
//   FROM gf_departamento 
//   ORDER BY Nombre ASC";
// $dep = $mysqli->query($sqlDep);

$dep = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_departamento 
ORDER BY Nombre ASC");

?>

<!-- Script para calcular el dígito de verificación. -->
<script type="text/javascript">
    function CalcularDv() {
        var arreglo, x, y, z, i, nit1, dv1;
        nit1 = document.form.noIdent.value;
        if (isNaN(nit1)) {
            document.form.digitVerif.value = "X";
            alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');
        } else {
            arreglo = new Array(16);
            x = 0;
            y = 0;
            z = nit1.length;
            arreglo[1] = 3;
            arreglo[2] = 7;
            arreglo[3] = 13;
            arreglo[4] = 17;
            arreglo[5] = 19;
            arreglo[6] = 23;
            arreglo[7] = 29;
            arreglo[8] = 37;
            arreglo[9] = 41;
            arreglo[10] = 43;
            arreglo[11] = 47;
            arreglo[12] = 53;
            arreglo[13] = 59;
            arreglo[14] = 67;
            arreglo[15] = 71;
            for (i = 0; i < z; i++) {
                y = (nit1.substr(i, 1));
                x += (y * arreglo[z - i]);
            }
            y = x % 11
            if (y > 1) {
                dv1 = 11 - y;
            } else {
                dv1 = y;
            }
            document.form.digitVerif.value = dv1;
        }
    }
</script>


<title>Registrar Banco Jurídica</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #tipoIdent-error,
    #razoSoci-error,
    #tipoEntidad-error,
    #tipoReg-error,
    #tipoEmp-error,
    #depto-error,
    #ciudad-error,
    #correo-error {
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
</head>

<body>
    <div class="container-fluid text-center">
        <div class="content row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px">
                <h2 align="center" class="tituloform">Registrar Banco Jurídica</h2>
                <a href="listar_GF_BANCO_JURIDICA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px"> R</h5>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/registrarBancoJurJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom: -1px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top: 20px;">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                    <option value="">Tipo Ident.</option>
                                    <?php for ($t=0; $t < count($tipoIden); $t++) { ?>
                                        <option value="<?php echo $tipoIden[$t][0]; ?>">
                                            <?php echo ucwords((mb_strtolower($tipoIden[$t][1]))); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-3">
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();    return existente()" />
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal">
                                <option value="">Sucursal</option>
                                <?php  for ($s=0; $s < count($sucursal); $s++) { ?>
                                    <option value="<?php echo $sucursal[$s][0] ?>"><?php echo ucwords((mb_strtolower($sucursal[$s][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen">
                                <option value="">Tipo Régimen</option>
                                <?php for ($tr=0; $tr < count($tipoReg); $tr++) { ?>
                                    <option value="<?php echo $tipoReg[$tr][0] ?>"><?php echo ucwords((mb_strtolower($tipoReg[$tr][1]))); ?></option>
                                <?php }  ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa">
                                <option value="">Tipo Empresa</option>
                                <?php for ($te=0; $te < count($tipoEmp); $te++) { ?>
                                    <option value="<?php echo $tipoEmp[$te][0] ?>"><?php echo ucwords((mb_strtolower($tipoEmp[$te][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad">
                                <option value="">Tipo Entidad</option>
                                <?php for($tip = 0; $tip < count($tipoEnt); $tip++) { ?>
                                    <option value="<?php echo $tipoEnt[$tip][0] ?>"><?php echo ucwords((mb_strtolower($tipoEnt[$tip][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                <option value="">Representante Legal</option>
                                <?php for ($r=0; $r < count($repreLegal); $r++) { ?>
                                    <option value="<?php echo $repreLegal[$r][0] ?>">
                                        <?php echo ucwords((mb_strtolower($repreLegal[$r][1] . " " . $repreLegal[$r][2] . " " . $repreLegal[$r][3] . " " . $repreLegal[$r][4] . " (" . $repreLegal[$r][6] . ", " . $repreLegal[$r][5] . ")"))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <option value="">Departamento</option>
                                    <?php for ($d=0; $d < count($dep); $d++) { ?>
                                        <option value="<?php echo $dep[$d][0] ?>"><?php echo ucwords((mb_strtolower($dep[$d][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1"></div>
                            <div class="form-group form-inline col-sm-3">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $("#depto").change(function() {
                                            var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$("#depto").val()
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function(response) {
                                                    $('#ciudad').html(response).fadeIn();
                                                    $('#ciudad').select2();
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <label for="ciudad" class=""></label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
                            <select name="contacto" id="contacto" class="select2_single form-control" title="Ingrese el contacto">
                                <option value="">Contacto</option>
                                <?php for ($c=0; $c < count($contacto); $c++) { ?>
                                    <option value="<?php echo $contacto[$c][0] ?>"><?php echo ucwords((mb_strtolower($contacto[$c][1] . " " . $contacto[$c][2] . " " . $contacto[$c][3] . " " . $contacto[$c][4] . " (" . $contacto[$c][6] . ", " . $contacto[$c][5] . ")"))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona">
                                <option value="">Zona</option>
                                <?php for ($z=0; $z < count($zona); $z++) { ?>
                                    <option value="<?php echo $zona[$z][0] ?>"><?php echo ucwords((mb_strtolower($zona[$z][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nominaE" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente Nómina Electrónica:</label>
                            <input type="number" name="nominaE" id="nominaE" class="form-control" maxlength="500" title="Ingrese Codigo Nómina Electrónica" placeholder="Codigo Nómina Electrónica">
                        </div>
                        <div class="form-group" style="margin-top: 5px">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <div class="texto" style="display:none"></div>
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="MM_insert">
                    </form>
                </div>
            </div>
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px">
                    <thead>
                        <th>
                            <h2></h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary btnInfo" disabled="true">DIRECCIÓN</button>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btn-primary btnInfo" <?php
                                                                                                                if (!isset($_SESSION['id_tercero'])) {
                                                                                                                    echo ' disabled title="Debe primero ingresar un  asociado jurídica."';
                                                                                                                }
                                                                                                                ?> disabled="true">TIPO ACTIVIDAD</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TELEFONO.php">
                                    <button class="btn btn-primary btnInfo" <?php
                                                                            if (!isset($_SESSION['id_tercero'])) {
                                                                                echo ' disabled title="Debe primero ingresar un banco jurídica."';
                                                                            }
                                                                            ?> disabled="true">TELEFONO</button>
                                </a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php">
                                    <button class="btn btn-primary btnInfo" <?php
                                                                            if (!isset($_SESSION['id_tercero'])) {
                                                                                echo ' disabled title="Debe primero ingresar un banco jurídica."';
                                                                            }
                                                                            ?> disabled="true">CONDICIÓN</button>
                                </a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="registrar_TERCERO_CONTACTO_NATURAL.php" <?php
                                                                                    if (!isset($_SESSION['id_tercero'])) {
                                                                                        echo ' disabled title="Debe primero ingresar una compañía."';
                                                                                    }
                                                                                    ?> class="btn btnInfo btn-primary" disabled="true">CONTACTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary btnInfo" disabled="true">PERFIL CONDICIÓN</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once('footer.php'); ?>
    <div class="modal fade" id="myModal1" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este número de identificación ya existe.¿Desea actualizar la información?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" id="ver2">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un Tipo Identificación.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>

                </div>
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>
    <script type="text/javascript">
        function existente() {
            var tipoD = document.form.tipoIdent.value;
            var numI = document.form.noIdent.value;
            var perfil = 9;
            var result = '';
            if (tipoD == null || tipoD == '' || tipoD == "Tipo Ident." || numI == null) {
                $("#myModal2").modal('show');
            } else {
                $.ajax({
                    data: {
                        "numI": numI,
                        perfil: perfil,
                        action: 2
                    },
                    type: "POST",
                    url: "jsonPptal/gf_tercerosJson.php",
                    success: function(data) {
                        var resultado = JSON.parse(data);
                        var rta = resultado["rta"];
                        var id = resultado["id"];
                        console.log(data);
                        if (rta == 0) {
                            if (id != 0) {
                                $("#id").val(md5(id));
                                $("#myModal1").modal('show');
                            }
                        } else {
                            $("#myModal4").modal('show');
                            $('#ver4').click(function() {
                                $("#noIdent").val('');
                                $("#digitVerif").val('');
                            });
                        }
                    }
                });
            }
        }
    </script>
    <script type="text/javascript">
        $('#ver1').click(function() {
            var id = document.form.id.value;
            document.location = 'modificar_GF_BANCO_JURIDICA.php?id_bancoJur=' + id;
        });
    </script>
</body>

</html>