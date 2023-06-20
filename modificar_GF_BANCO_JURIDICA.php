<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#31/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$action = $_REQUEST['action'];
$con = new ConexionPDO();
if ($action == 1) {


    $id_bancoJur = " ";
    $id_bancoJur = base64_decode(($_REQUEST["id_bancoJur"]));
    $row = $con->Listar("SELECT t.Id_Unico, 
    t.RazonSocial, 
    t.NumeroIdentificacion,
    t.DigitoVerficacion, 
    ti.Id_Unico, 
    ti.Nombre, 
    s.Id_Unico, 
    s.nombre, 
    t.RepresentanteLegal, 
    ci.Id_Unico, 
    ci.Nombre, 
    tr.Id_Unico, 
    tr.Nombre, 
    t.Contacto,
    te.Id_Unico, 
    te.Nombre, 
    tipen.Id_Unico, 
    tipen.Nombre, 
    ci.Departamento,
    z.Id_Unico, 
    z.Nombre,
    DP.Id_Unico,
    DP.Nombre, 
    t.email, 
    ci.id_unico,
    DP.id_unico, 
    DP.nombre, ci.nombre,t.equivalente_NE  
FROM gf_tercero t
LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico
LEFT JOIN gf_zona z ON t.Zona = z.Id_Unico
LEFT JOIN gf_departamento DP ON ci.departamento = DP.Id_Unico
WHERE t.Id_Unico = '$id_bancoJur'");

    $_SESSION['id_tercero'] = $row[0][0];
    $_SESSION['perfil'] = "J"; //Jurídica.
    $_SESSION['url'] = "modificar_GF_BANCO_JURIDICA.php?id_bancoJur=" . (($_GET["id_bancoJur"]));
    $_SESSION['tipo_perfil'] = 'Banco jurídica';
    
    #****** Tipo Identificación *********#
    $auxidtipoi = $row[$i][4];
    $tipoIden = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_identificacion
WHERE Id_Unico != $auxidtipoi
ORDER BY Nombre ASC");
    #****** Sucursal *********#
    if (empty($row[0][6])) {
        $ids = 0;
    } else {
        $ids = $row[0][6];
    }
    $sucursal = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_sucursal
WHERE Id_Unico != $ids 
ORDER BY Nombre ASC");


    #****** Tipo Régimen *********#
    if (empty($row[0][11])) {
        $idtr = 0;
    } else {
        $idtr = $row[0][11];
    }
    $tipoReg = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_regimen
WHERE Id_Unico != $idtr 
ORDER BY Nombre ASC");


    #****** Tipo Empresa *********#
    if (empty($row[0][14])) {
        $idemp = 0;
    } else {
        $idemp = $row[0][14];
    }
    $tipoEmp = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_empresa
WHERE Id_Unico != $idemp 
ORDER BY Nombre ASC");


    #****** Tipo Entidad *********#
    if (empty($row[0][16])) {
        $idte = 0;
    } else {
        $idte = $row[0][16];
    }
    $tipoEnt  = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_tipo_entidad
WHERE Id_Unico != $idte 
ORDER BY Nombre ASC");


    #****** Representante Legal *********#
    if (empty($row[0][8])) {
        $idrl = 0;
    } else {
        $idrl = $row[0][8];
    }
    $repreLegal = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
WHERE t.TipoIdentificacion = ti.Id_Unico
AND t.Id_Unico = pt.Tercero 
AND pt.Perfil = 10 
AND t.Id_Unico != $idrl 
ORDER BY t.NombreUno ASC");


    #****** Contacto *********#
    if (empty($row[0][13])) {
        $idcn = 0;
    } else {
        $idcn = $row[0][13];
    }
    $contacto = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
WHERE t.TipoIdentificacion = ti.Id_Unico 
AND t.Id_Unico = pt.Tercero 
AND pt.Perfil = 10
AND t.Id_Unico != $idcn 
ORDER BY t.NombreUno ASC");


    #****** Zona *********#
    if (empty($row[0][19])) {
        $idz = 0;
    } else {
        $idz = $row[0][19];
    }
    $zona  = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_zona
WHERE Id_Unico != $idz 
ORDER BY Nombre ASC");

    #****** Departamento  *********#
    if (empty($row[0][25])) {
        $de = 0;
    } else {
        $de = $row[0][25];
    }
    $dep = $con->Listar("SELECT Id_Unico, Nombre 
  FROM gf_departamento  
  WHERE id_unico != $de 
  ORDER BY Nombre ASC");

    #****** Ciudad  *********#
    if (empty($row[0][24]) && empty($row[0][25])) {
        $de = 0;
        $cd = 0;
    } else {
        $de = $row[0][25];
        $cd = $row[0][24];
    }
    $ciu = $con->Listar("SELECT c.id_unico, c.nombre 
    FROM gf_ciudad c   
    LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
    WHERE c.id_unico != $cd AND c.departamento = $de  
    ORDER BY c.nombre ASC");
}
if ($action == 2) {
    $_SESSION['perfil'] = "J";
    $_SESSION['url'] = "registrar_GF_BANCO_JURIDICA.php";
    $compania = $_SESSION['compania'];
    #****** Tipo Identificación *********#
    $tipoIden  = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_tipo_identificacion
    ORDER BY Nombre ASC");


    #****** Sucursal *********#
    $sqlSucursal = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_sucursal
    ORDER BY Nombre ASC");


    #****** Tipo Régimen *********#
    $tipoReg = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_tipo_regimen
    ORDER BY Nombre ASC");


    #****** Tipo Empresa *********#
    $tipoEmp = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_tipo_empresa
    ORDER BY Nombre ASC");


    #****** Tipo Entidad *********#
    $tipoEnt = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_tipo_entidad
    ORDER BY Nombre ASC");


    #****** Representante Legal *********#
    $repreLegal = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
    FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
    WHERE t.TipoIdentificacion = ti.Id_Unico
    AND t.Id_Unico = pt.Tercero 
    AND pt.Perfil = 10 AND t.compania = $compania 
    ORDER BY t.NombreUno ASC");


    #****** Contacto *********#
    $contacto   = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
    FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
    WHERE t.TipoIdentificacion = ti.Id_Unico
    AND t.Id_Unico = pt.Tercero 
    AND pt.Perfil = 10 AND t.compania = $compania 
    ORDER BY t.NombreUno ASC");


    #****** Zona *********#
    $zona = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_zona
    ORDER BY Nombre ASC");

    #****** Departamento  *********#
    $dep  = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_departamento 
    ORDER BY Nombre ASC");
}

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
<?php if ($action == 1) { ?>
    <title>Modificar Banco Jurídica</title>
<?php }

?>
<?php if ($action == 2) { ?>
    <title>Registrar Banco Jurídica</title>
<?php }


?>

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
            <div class="col-sm-7 text-left" style="margin-left:-16px;margin-top: -20px">
                <?php if ($action == 1) { ?>
                    <h2 align="center" class="tituloform">Modificar Banco Jurídica</h2>
                <?php }

                ?>
                <?php if ($action == 2) { ?>
                    <h2 align="center" class="tituloform">Registrar Banco Jurídica</h2>
                <?php }


                ?>

                <a href="listar_GF_BANCO_JURIDICA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"> <?php echo ($row[1]); ?></h5>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                        <p align="center" class="parrafoO" style="margin-bottom: -1px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="perfil" value=9>
                        <input type="hidden" name="id" value="<?php echo $row[0][0]; ?>">
                        <div class="form-group form-inline " style="margin-top: 20px;">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <?php if ($action == 1) { ?>
                                    <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                        <option value="<?php echo $row[0][4]; ?>"><?php echo $row[0][5]; ?></option>
                                        <?php for ($i = 0; $i < count($tipoIden); $i++) { ?>
                                            <option value="<?php echo $tipoIden[$i][0]; ?>">
                                                <?php echo ucwords((mb_strtolower($tipoIden[$i][1]))); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php }

                                ?>
                                <?php if ($action == 2) { ?>
                                    <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                        <option value="">Tipo Ident.</option>
                                        <?php for ($i = 0; $i < count($tipoIden); $i++) { ?>
                                            <option value="<?php echo $tipoIden[$i][0]; ?>">
                                                <?php echo ucwords((mb_strtolower($tipoIden[$i][1]))); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php }


                                ?>

                            </div>
                            <div class="form-group form-inline col-sm-3">
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv(); " value="<?php echo $row[0][2]; ?>" />
                                <?php }

                                ?>
                                <?php if ($action == 2) { ?>
                                    <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();    return existente()" />
                                <?php }


                                ?>

                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <?php if ($action == 1) { ?>
                                    <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px" value="<?php echo $row[0][3]; ?>" />
                                <?php }

                                ?>
                                <?php if ($action == 2) { ?>
                                    <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px" />
                                <?php }


                                ?>

                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <?php if ($action == 1) { ?>
                                <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal">
                                    <?php
                                    if (!empty($ro[0][6])) {
                                        echo "<option value='" . $row[0][6] . "'>" . (ucwords(mb_strtolower($row[0][7]))) . "</option>";
                                        echo '<option value=""> - </option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($sucursal); $i++) { ?>
                                        <option value="<?php echo $sucursal[$i][0] ?>"><?php echo ucwords((mb_strtolower($sucursal[$i][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal">
                                    <option value="">Sucursal</option>
                                    <?php for ($i = 0; $i < count($sucursal); $i++) { ?>
                                        <option value="<?php echo $sucursal[$i][0] ?>"><?php echo ucwords((mb_strtolower($sucursal[$i][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo ($row[0][1]); ?>" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <?php if ($action == 1) { ?>
                                <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen">
                                    <?php
                                    if (!empty($row[0][11])) {
                                        echo "<option value='" . $row[0][11] . "'>" . (ucwords(mb_strtolower($row[0][12]))) . "</option>";
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($tipoReg); $i++) {
                                        echo "<option value='" . $tipoReg[$i][0] . "'>" . (ucwords(mb_strtolower($tipoReg[$i][1]))) . "</option>";
                                    } ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen">
                                    <option value="">Tipo Régimen</option>
                                    <?php for ($i = 0; $i < count($tipoReg); $i++) {  ?>
                                        <option value="<?php echo $tipoReg[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoReg[$i][1]))); ?></option>
                                    <?php }  ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <?php if ($action == 1) { ?>
                                <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa">
                                    <?php
                                    if (!empty($row[0][14])) {
                                        echo "<option value='" . $row[0][14] . "'>" . ucwords(mb_strtolower($row[0][15])) . "</option>";
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($tipoEmp); $i++) {
                                        echo "<option value='" . $tipoEmp[$i][0] . "'>" . ucwords((mb_strtolower($tipoEmp[$i][1]))) . "</option>";
                                    } ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa">
                                    <option value="">Tipo Empresa</option>
                                    <?php for ($i = 0; $i < count($tipoEmp); $i++) { ?>
                                        <option value="<?php echo $tipoEmp[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoEmp[$i][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <?php if ($action == 1) { ?>
                                <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad">
                                    <?php
                                    if (!empty($row[0][16])) {
                                        echo "<option value='" . $row[0][16] . "'>" . ucwords(mb_strtolower($row[0][17])) . "</option>";
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($tipoEnt); $i++) {
                                        echo "<option value='" . $tipoEnt[$i][0] . "'>" . ucwords(mb_strtolower($tipoEnt[$i][1])) . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad">
                                    <option value="">Tipo Entidad</option>
                                    <?php for ($i = 0; $i < count($tipoEnt); $i++) { ?>
                                        <option value="<?php echo  $tipoEnt[$i][0]  ?>"><?php echo ucwords((mb_strtolower($tipoEnt[$i][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <?php if ($action == 1) { ?>
                                <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                    <?php
                                    if (!empty($row[0][8])) {
                                        $auxreplegal = $row[0][8];
                                        $rowElReprLeg = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = $auxreplegal");
                                        echo "<option value='" . $rowElReprLeg[0][0] . "'>" . ucwords((mb_strtolower($rowElReprLeg[0][1] . " " . $rowElReprLeg[0][2] . " " . $rowElReprLeg[0][3] . " " . $rowElReprLeg[0][4] . " (" . $rowElReprLeg[0][6] . ", " . $rowElReprLeg[0][5] . ")"))) . "</option>";
                                        echo '<option value=""> - </option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($repreLegal); $i++) {
                                        echo "<option value='" . $repreLegal[$i][0] . "'>" . ucwords((mb_strtolower($repreLegal[$i][1] . " " . $repreLegal[$i][2] . " " . $repreLegal[$i][3] . " " . $repreLegal[$i][4] . " (" . $repreLegal[$i][6] . " - " . $repreLegal[$i][5] . ")"))) . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                    <option value="">Representante Legal</option>
                                    <?php for ($i = 0; $i < count($repreLegal); $i++) { ?>
                                        <option value="<?php echo $repreLegal[$i][0] ?>">
                                            <?php echo ucwords((mb_strtolower($repreLegal[$i][1] . " " . $repreLegal[$i][2]  . " " . $repreLegal[$i][3] . " " . $repreLegal[$i][4]  . " (" . $repreLegal[$i][6] . ", " . $repreLegal[$i][5]  . ")"))); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <?php if ($action == 1) { ?>
                                    <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                        <?php
                                        if (!empty($row[0][25])) {
                                            echo '<option value="' . $row[0][25] . '">' . ucwords(mb_strtolower($row[0][26])) . '</option>';
                                        } else {
                                            echo '<option value=""> - </option>';
                                        } ?>
                                        <?php for ($i = 0; $i < count($dep); $i++) { ?>
                                            <option value="<?php echo $dep[$i][0] ?>"><?php echo ucwords((mb_strtolower($dep[$i][1]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php }

                                ?>
                                <?php if ($action == 2) { ?>
                                    <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                        <option value="">Departamento</option>
                                        <?php for ($i = 0; $i < count($dep); $i++) { ?>
                                            <option value="<?php echo $dep[$i][0] ?>"><?php echo ucwords((mb_strtolower($dep[$i][1]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php }


                                ?>

                            </div>
                            <div class="form-group form-inline col-sm-1"></div>
                            <div class="form-group form-inline col-sm-3">
                                <?php if ($action == 1) { ?>

                                    <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="select2_single form-control" title="Seleccione Ciudad" required>
                                        <?php
                                        if (!empty($row[0][24])) {
                                            echo '<option value="' . $row[0][24] . '">' . ucwords(mb_strtolower($row[0][27])) . '</option>';
                                        } else {
                                            echo '<option value=""> - </option>';
                                        } ?>
                                        <?php for ($i = 0; $i < count($ciu); $i++) { ?>
                                            <option value="<?php echo $ciu[$i][0] ?>"><?php echo ucwords((mb_strtolower($ciu[$i][1]))); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php }

                                ?>
                                <?php if ($action == 2) { ?>
                                    <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                        <option value="">Ciudad</option>
                                    </select>
                                <?php }


                                ?>

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
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="contacto" class="col-sm-5 control-label">Contacto:</label>

                            <?php if ($action == 1) { ?>
                                <select name="contacto" id="contacto" class="select2_single form-control" title="Ingrese el contacto">
                                    <?php
                                    if (!empty($row[0][13])) {
                                        $auxElCon = $row[0][13];
                                        $rowElCon = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = $auxElCon");
                                        echo '<option value="' . $row[0][13] . '">' . ucwords(mb_strtolower($rowElCon[0][1] . " " . $rowElCon[0][2] . " " . $rowElCon[0][3] . " " . $rowElCon[0][4] . " (" . $rowElCon[0][6] . ", " . $rowElCon[0][5] . ")")) . '</option>';
                                        echo '<option value=""> - </option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($contacto); $i++) {
                                        echo '<option value="' . $contacto[$i][0] . '">' . ucwords(mb_strtolower($contacto[$i][1] . ' ' . $contacto[$i][2] . ' ' . $contacto[$i][3] . ' ' . $contacto[$i][4])) . '(' . $contacto[$i][5] . ' - ' . $contacto[$i][6] . ')' . '</option>';
                                    }
                                    ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="contacto" id="contacto" class="select2_single form-control" title="Ingrese el contacto">
                                    <option value="">Contacto</option>
                                    <?php for ($i = 0; $i < count($contacto); $i++) { ?>
                                        <option value="<?php echo  $contacto[$i][0] ?>"><?php echo ucwords((mb_strtolower($contacto[$i][1] . " " .  $contacto[$i][2] . " " .  $contacto[$i][3] . " " .  $contacto[$i][4] . " (" .  $contacto[$i][6] . ", " .  $contacto[$i][5] . ")"))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <?php if ($action == 1) { ?>
                                <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona">
                                    <?php
                                    if (!empty($row[0][19])) {
                                        echo '<option value="' . $row[0][19] . '">' . $row[0][20] . '</option>';
                                        echo '<option value=""> - </option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }
                                    for ($i = 0; $i < count($zona); $i++) {
                                        echo '<option value="' . $zona[$i][0] . '">' . $zona[$i][1] . '</option>';
                                    }
                                    ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona">
                                    <option value="">Zona</option>
                                    <?php for ($i = 0; $i < count($zona); $i++) {  ?>
                                        <option value="<?php echo $zona[$i][0]  ?>"><?php echo ucwords((mb_strtolower($zona[$i][1]))); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <?php if ($action == 1) { ?>
                                <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" value="<?php echo $row[0][23] ?>">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nominaE" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente Nómina Electrónica:</label>

                            <?php if ($action == 1) { ?>
                                <input type="number" name="nominaE" id="nominaE" class="form-control" maxlength="500" title="Ingrese Codigo Nómina Electrónica" placeholder="Codigo Nómina Electrónica" value="<?php echo $row[0][28] ?>">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="number" name="nominaE" id="nominaE" class="form-control" maxlength="500" title="Ingrese Codigo Nómina Electrónica" placeholder="Codigo Nómina Electrónica">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top:-5px;">
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
            </div> <!-- Cierra clase col-sm-7 text-left -->

            <!-- Botones de consulta -->
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
                                <a href="GF_DIRECCION_TERCERO.php"><button class="btn btn-primary btnInfo">DIRECCIÓN</button></a>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TELEFONO.php"><button class="btn btn-primary btnInfo">TELEFONO</button></a>
                                <br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php"><button class="btn btn-primary btnInfo">CONDICIÓN</button></a>
                                <br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="modificar_TERCERO_CONTACTO_NATURAL.php?action=2" class="btn btnInfo btn-primary">CONTACTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php" class="btn btnInfo btn-primary">PERFIL CONDICIÓN</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fin de Contenedor Principal -->
    <?php require_once('footer.php'); ?>
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
                url: "jsonPptal/gf_tercerosJson.php?action=14",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");

                        document.location = 'listar_GF_BANCO_JURIDICA.php';

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
                url: "jsonPptal/gf_tercerosJson.php?action=15",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");

                        document.location = 'listar_GF_BANCO_JURIDICA.php';

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