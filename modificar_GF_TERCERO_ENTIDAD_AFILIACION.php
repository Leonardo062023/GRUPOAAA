<?php

# 22/05/2017 --- Nestor B --- se agergaron las librerias de busqueda rapida se ajunto el ancho de los selects, se agregó el campo de tipo entidad y se aarreglo ala consulta de sucursales
# 23/05/2017 --- Nestor B --- se agregaron divs de cierre
# 04/07/2017 --- Nestor B --- se agrego la validacion de los campos requeridos
require_once('Conexion/conexion.php');
require_once 'head.php';
require_once('Conexion/conexionPDO.php');

//obtiene los datos para la consulta
$action = $_REQUEST['action'];
$con = new ConexionPDO();
//Captura de ID y consulta del registro correspondiente.
$id = " ";
if (isset($_GET["id"])) {
    $id = base64_decode(($_REQUEST["id"]));
    $conexion=$_SESSION['conexion'];

    switch ($_SESSION['conexion']) {
        case 1:
            $row  = $con->Listar("SELECT 
                        t.id_unico, 
                        t.razonsocial,
                        t.tipoidentificacion,
                        ti.id_unico,
                        ti.nombre,
                        t.numeroidentificacion,
                        t.sucursal,
                        s.id_unico,
                        s.nombre,
                        t.tiporegimen,
                        tr.id_unico,
                        tr.nombre,
                        t.tipoempresa,
                        tem.id_unico,
                        tem.nombre,
                        t.tipoentidad,
                        ten.id_unico,
                        ten.nombre,
                        t.representantelegal,
                        r.id_unico,
                        CONCAT(r.nombreuno,' ',r.nombredos,' ',r.apellidouno,' ',r.apellidodos),
                        t.ciudadidentificacion,
                        t.contacto,
                        c.id_unico,
                        CONCAT(c.nombreuno,' ',c.nombredos,' ',c.apellidouno,' ',c.apellidodos),
                        t.zona,
                        z.id_unico,
                        z.nombre,
                        cd.departamento,
                        t.codigo_afp
        FROM gf_perfil_tercero pt
        LEFT JOIN gf_tercero t  		 	ON pt.tercero = t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico        
        LEFT JOIN gf_sucursal s             ON t.sucursal = s.id_unico
        LEFT JOIN gf_tipo_regimen tr        ON t.tiporegimen = tr.id_unico
        LEFT JOIN gf_tipo_empresa tem       ON t.tipoempresa = tem.id_unico
        LEFT JOIN gf_tipo_entidad ten       ON t.tipoentidad = ten.id_unico
        LEFT JOIN gf_tercero r  			ON t.representantelegal = r.id_unico
        LEFT JOIN gf_tercero c  			ON t.contacto = c.id_unico
        LEFT JOIN gf_zona z     			ON t.zona = z.id_unico
        LEFT JOIN gf_ciudad cd		        ON t.ciudadidentificacion = cd.id_unico
        LEFT JOIN gf_departamento d         ON cd.departamento = d.id_unico
        WHERE t.id_unico = '$id'");
            break;
        case 2:
            $row  = $con->Listar("SELECT 
            t.id_unico, 
            t.razonsocial,
            t.tipoidentificacion,
            ti.id_unico,
            ti.nombre,
            t.numeroidentificacion,
            t.sucursal,
            s.id_unico,
            s.nombre,
            t.tiporegimen,
            tr.id_unico,
            tr.nombre,
            t.tipoempresa,
            tem.id_unico,
            tem.nombre,
            t.tipoentidad,
            ten.id_unico,
            ten.nombre,
            t.representantelegal,
            r.id_unico,
            r.nombreuno||' '||r.nombredos||' '||r.apellidouno||' '||r.apellidodos,
            t.ciudadidentificacion,
            t.contacto,
            c.id_unico,
            c.nombreuno||' '||c.nombredos||' '||c.apellidouno||' '||c.apellidodos,
            t.zona,
            z.id_unico,
            z.nombre,
            cd.departamento,
            t.codigo_afp
FROM gf_perfil_tercero pt
LEFT JOIN gf_tercero t  		 	ON pt.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico        
LEFT JOIN gf_sucursal s             ON t.sucursal = s.id_unico
LEFT JOIN gf_tipo_regimen tr        ON t.tiporegimen = tr.id_unico
LEFT JOIN gf_tipo_empresa tem       ON t.tipoempresa = tem.id_unico
LEFT JOIN gf_tipo_entidad ten       ON t.tipoentidad = ten.id_unico
LEFT JOIN gf_tercero r  			ON t.representantelegal = r.id_unico
LEFT JOIN gf_tercero c  			ON t.contacto = c.id_unico
LEFT JOIN gf_zona z     			ON t.zona = z.id_unico
LEFT JOIN gf_ciudad cd		        ON t.ciudadidentificacion = cd.id_unico
LEFT JOIN gf_departamento d         ON cd.departamento = d.id_unico
WHERE t.id_unico ='$id'");
            break;
    
        default:
            # code...
            break;
    }
    //Consulta general
  
}




//Variables de sesión para determinar el id del tercero que se está consultando y la url para regresar.
$_SESSION['id_tercero'] = $row[0][0];
$_SESSION['perfil'] = "EA"; //Jurídica.
$_SESSION['url'] = "modificar_GF_TERCERO_ENTIDAD_AFILIACION.php?id=" . (($_GET["id"]));
$_SESSION['tipo_perfil'] = 'Entidad Afiliación';

//Consultas para el listado de los diferentes combos correspondientes.
//Tipo Identificación.
$idtipoIden = $row[0][2];
$tipoIden = $con->Listar("SELECT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  WHERE Id_Unico !=  $idtipoIden
  ORDER BY Nombre ASC");


//Sucursal.

if (!empty($row[6]) || $row[6] != "") {
    $idsucursal = $row[0][6];
    $sucursal = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_sucursal
    WHERE Id_Unico != $idsucursal
    ORDER BY Nombre ASC");
} else {
    $sucursal = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_sucursal
    ORDER BY Nombre ASC");
}

//Tipo Régimen.
$idtiporeg = $row[0][9];
$tipoReg  = $con->Listar("SELECT Id_Unico, Nombre 
  FROM gf_tipo_regimen
  WHERE Id_Unico !=  $idtiporeg
  ORDER BY Nombre ASC");


//Tipo Empresa.
if (!empty($row[0][12]) || $row[0][12] != "") {
    $idsqlTipoEmp = $row[0][12];
    $sqlTipoEmp = "SELECT Id_Unico, Nombre 
    FROM gf_tipo_empresa
    WHERE Id_Unico != $idsqlTipoEmp
    ORDER BY Nombre ASC";
} else {
    $tipoEmp = $con->Listar("SELECT Id_Unico, Nombre 
    FROM gf_tipo_empresa
    ORDER BY Nombre ASC");
}

//Tipo Entidad.
if (!empty($row[0][16]) || $row[0][16] != "") {
    $idtipoEnt = $row[0][16];
    $tipoEnt = $con->Listar("SELECT Id_Unico, Nombre 
      FROM gf_tipo_entidad
      WHERE Id_Unico != $idtipoEnt
      ORDER BY Nombre ASC");
} else {

    $tipoEnt  = $con->Listar("SELECT Id_Unico, Nombre 
      FROM gf_tipo_entidad
      ORDER BY Nombre ASC");
}


//Representante Legal.
if (!empty($row[0][18]) || $row[0][18] != "") {
    $idreprelegal = $row[0][18];
    $repreLegal = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
      FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
      WHERE t.TipoIdentificacion = ti.Id_Unico
      AND t.Id_Unico = pt.Tercero 
      AND pt.Perfil != 1
      AND t.Id_Unico != $idreprelegal
      ORDER BY t.NombreUno ASC");
} else {
    $repreLegal = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
      FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
      WHERE t.TipoIdentificacion = ti.Id_Unico
      AND t.Id_Unico = pt.Tercero 
      AND pt.Perfil != 1
      ORDER BY t.NombreUno ASC");
}
//Contacto.
$idcontacto = $row[0][22];
$contacto = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.Id_Unico 
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.Id_Unico != $idcontacto
  ORDER BY t.NombreUno ASC");


//Zona
$idzona = $row[0][25];
$zona = $con->Listar("SELECT Id_Unico, Nombre 
  FROM gf_zona
  WHERE Id_Unico != $idzona
  ORDER BY Nombre ASC");

//Fin de las consultas para combos.

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />

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

<style>
    label #razoSoci-error,
    #tipoEnt-error,
    #repreLegal-error,
    #depto-error,
    #ciudad-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body {
        font-size: 11px;
    }

    /* Estilos de tabla*/
    table.dataTable thead th,
    table.dataTable thead td {
        padding: 1px 18px;
        font-size: 10px
    }

    table.dataTable tbody td,
    table.dataTable tbody td {
        padding: 1px
    }

    .dataTables_wrapper .ui-toolbar {
        padding: 2px;
        font-size: 10px;
        font-family: Arial;
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
<script src="js/jquery-ui.js"></script>
<script>
    $(function() {
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if (dia < 10) {
            dia = "0" + dia;
        }
        if (mes < 10) {
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $("#sltFechaA").datepicker({
            changeMonth: true,
        }).val();
        $("#sltFechaI").datepicker({
            changeMonth: true,
        }).val();
        $("#sltFechaF").datepicker({
            changeMonth: true,
        }).val();
        $("#sltFechaID").datepicker({
            changeMonth: true,
        }).val();
        $("#sltFechaFD").datepicker({
            changeMonth: true,
        }).val();


    });
</script>




<title>Modificar Entidad de Afiliación</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css" />
</head>

<body>
    <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center">
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Lllamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px;">
                <!-- Titulo de Formulario -->
                <h2 align="center" class="tituloform">Modificar Entidad de Afiliación</h2>
                <!-- Contenedor del formulario -->
                <div class="client-form contenedorForma">
                    <!-- Inicio de Formulario -->
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/modificarEntidadAfiliacionJson.php">
                        <!-- Párrafo de texto-->
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>


                        <input type="hidden" name="id" value="<?php echo $row[0][0]; ?>">

                        <div class="form-group form-inline" style="margin-top:-20px">

                            <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>

                            <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px" title="Tipo Identificación" required>
                                <option value="<?php echo $row[0][3]; ?>"><?php echo $row[0][4]; ?></option>
                                <?php for ($i = 0; $i < count($tipoIden); $i++) { ?>
                                    <option value="<?php echo $tipoIden[$i][0]; ?>"><?php echo ucwords((mb_strtolower($tipoIden[$i][1]))); ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>

                            <input type="text" value="<?php echo $row[0][5]; ?>" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();return existente()" />

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                            <input type="text" value="<?php echo $row[0][3]; ?>" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px" />

                        </div>
                        <!--Modificación de Sucursal-->
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="sucursal" class="col-sm-5 control-label">Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control col-sm-1" title="Seleccione le tipo de sucursal" style="width: 34%" required="required">
                                <?php
                                if (empty($row[0][6]) || $row[0][6] = "") { ?>
                                    <option value="">-</option>
                                <?php
                                } else { ?>
                                    <option value="<?php echo $row[0][7]; ?>"><?php echo ucwords(mb_strtolower($row[0][8])); ?></option>
                                <?php }
                                for ($i = 0; $i < count($sucursal); $i++) {  ?>
                                    <option value="<?php echo $sucursal[$i][0] ?>"><?php echo ucwords((mb_strtolower($sucursal[$i][1]))); ?></option>
                                <?php
                                }  ?>
                            </select>
                        </div>

                        <!--Modificación de Razón Social-->
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo ($row[0][1]); ?>" onkeypress="return txtValida(event,'num_car')" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required style="width: 34%">
                        </div>

                        <!--Modificación de Tipo Régimen-->
                        <div class="form-group" style="margin-top: -18px; ">
                            <label for="tipoReg" class="col-sm-5 control-label">Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control  col-sm-1" title="Ingrese el tipo de régimen" style="width: 34%">
                                <?php
                                if (empty($row[0][9]) || $row[0][9] = "") { ?>
                                    <option value="">-</option>
                                <?php
                                } else { ?>
                                    <option value="<?php echo $row[0][10]; ?>"><?php echo ($row[0][11]); ?></option>
                                <?php  }
                                for ($i = 0; $i < count($tipoReg); $i++) {  ?>
                                    <option value="<?php echo $tipoReg[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoReg[$i][1]))); ?></option>
                                <?php
                                }  ?>
                            </select>
                        </div>


                        <!--Modificación de Tipo Empresa-->
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label">Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control  col-sm-1" title="Ingrese el tipo de empresa" style="width: 34%">
                                <?php
                                if (empty($row[0][12]) || $row[0][12] = "") { ?>
                                    <option value="">-</option>
                                <?php
                                } else { ?>
                                    <option value="<?php echo $row[0][13]; ?>"><?php echo ($row[0][14]); ?></option>
                                <?php }
                                for ($i = 0; $i < count($tipoEmp); $i++) {  ?>
                                    <option value="<?php echo $tipoEmp[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoEmp[$i][0]))); ?></option>
                                <?php
                                }  ?>
                            </select>
                        </div>


                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEnt" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Entidad:</label>
                            <select name="tipoEnt" id="tipoEnt" class="select2_single form-control  col-sm-1" title="Ingrese el tipo de entidad" required style="width: 34%">
                                <?php
                                if (empty($row[0][16]) || $row[0][16] = "") { ?>
                                    <option value="">-</option>
                                <?php
                                } else { ?>
                                    <option value="<?php echo $row[0][15]; ?>"><?php echo ucwords(mb_strtolower($row[17])); ?></option>
                                <?php }
                                for ($i = 0; $i < count($tipoEnt); $i++) {  ?>
                                    <option value="<?php echo $tipoEnt[$i][0] ?>"><?php echo ucwords((mb_strtolower($tipoEnt[$i][1]))); ?></option>
                                <?php
                                }  ?>
                            </select>
                        </div>

                        <!--Modificación de Representante Legal-->
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control  col-sm-1" title="Ingrese el representante legal" style="width: 34%">
                                <?php
                                $idsqlElReprLeg = $row[0][18];
                                $rowElReprLeg  = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                                 FROM gf_tercero t, gf_tipo_identificacion ti  
                                                 WHERE t.TipoIdentificacion = ti.Id_Unico
                                                 AND t.Id_Unico =     $idsqlElReprLeg");

                                ?>
                                <?php
                                if (empty($row[0][18]) || $row[0][18] = "") { ?>
                                    <option value="">-</option>
                                <?php
                                } else { ?>
                                    <option value="<?php echo $rowElReprLeg[0][0] ?>">
                                        <?php echo ucwords((mb_strtolower($rowElReprLeg[0][1] . " " . $rowElReprLeg[0][2] . " " . $rowElReprLeg[0][3] . " " . $rowElReprLeg[0][4] . " (" . $rowElReprLeg[0][6] . ", " . $rowElReprLeg[0][5] . ")"))); ?>
                                    </option>

                                <?php }
                                for ($i = 0; $i < count($repreLegal); $i++) {  ?>
                                    <option value="<?php echo $repreLegal[$i][0] ?>">
                                        <?php echo ucwords((mb_strtolower($repreLegal[$i][1] . " " . $repreLegal[$i][2] . " " . $repreLegal[$i][3] . " " . $repreLegal[$i][4] . " (" . $repreLegal[$i][6] . ", " . $repreLegal[$i][5] . ")"))); ?>
                                    </option>
                                <?php
                                }  ?>
                            </select>
                        </div>


                        <!--  Inicio combos dinámicos -->
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>

                            <div class="classDepto">

                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <option value="">Departamento</option>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $.ajax({
                                            data: {
                                                "id_ciudad_depto": "<?php echo $row[0][28]; ?>"
                                            },
                                            type: "POST",
                                            url: "MDepartamento.php",
                                            success: function(response) {
                                                $('.classDepto select').html(response).fadeIn();
                                                $('#depto').css('display', 'none');
                                            }
                                        });
                                    });
                                </script>
                            </div>

                            <span class="col-sm-1" style="width:1px"></span>

                            <div class="ClassCiudad">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="select2_single form-control col-sm-1" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        var cambio = 0;
                                        //Este evento change funciona cuando se cambia de departamento.
                                        $(".classDepto select").change(function() {
                                            cambio = 1;
                                            var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$(".classDepto select").val()
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function(response) {
                                                    $('.ClassCiudad select').html(response).fadeIn();
                                                    $('#ciudad').css('display', 'none');
                                                }
                                            });
                                        });

                                        // Se eliminó el evento click y el select caragará junto con la página.
                                        //$(".ClassCiudad select").click(function()
                                        //{
                                        if (cambio == 0) {
                                            //cambio = 1;
                                            $.ajax({
                                                data: {
                                                    "id_ciudad": "<?php echo $row[0][21]; ?>",
                                                    "id_ciudad_depto": "<?php echo $row[0][28]; ?>"
                                                },
                                                type: "POST",
                                                url: "MCiudad.php",
                                                success: function(response) {
                                                    $('.ClassCiudad select').html(response).fadeIn();
                                                    $('#ciudad').css('display', 'none');
                                                }
                                            });

                                        }
                                        //});

                                    });
                                </script>
                            </div>
                        </div>
                        <!--  Fin combos dinámicos  -->
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
                            <select name="contacto" id="contacto" class="select2_single form-control col-sm-1" title="Ingrese el contacto" style="width: 34%">
                                <?php
                                if (!empty($row[0][21])) {
                                    $idrowElCon=$row[0][21];
                                    $rowElCon = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                                    AND t.Id_Unico = $idrowElCon");
                                  
                                    echo '<option value="' . $row[0][13] . '">' . ucwords(mb_strtolower($rowElCon[0][1] . " " . $rowElCon[0][2] . " " . $rowElCon[0][3] . " " . $rowElCon[0][4] . " (" . $rowElCon[0][6] . ", " . $rowElCon[0][5] . ")")) . '</option>';
                                    $idcontactos =$row[0][21];
                                    $contactos  =$con->Listar( "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                                   FROM gf_tercero t
                                                   LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
                                                   LEFT JOIN gf_perfil_tercero pt   ON   t.Id_Unico = pt.Tercero 
                                                   WHERE pt.Perfil = 10
                                                   AND t.Id_Unico != $idcontactos 
                                                   ORDER BY t.NombreUno ASC");
                                    
                                    for ($i = 0; $i < count($contactos); $i++){
                                        echo '<option value="' . $contactos[$i][0] . '">' . $contactos[$i][1] . ' ' . $contactos[$i][2] . ' ' . $contactos[$i][3] . ' ' . $contactos[$i][4] . '(' . $contactos[$i][5] . ' - ' . $contactos[$i][6] . ')' . '</option>';
                                    }
                                    echo '<option value=""></option>';
                                } else {
                                    echo '<option value="">Contacto</option>';
                                    $contactos = $con->Listar("SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                                   FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
                                                   WHERE t.TipoIdentificacion = ti.Id_Unico 
                                                   AND t.Id_Unico = pt.Tercero 
                                                   AND pt.Perfil = 10
                                                   ORDER BY t.NombreUno ASC");
                                    
                                    for ($i = 0; $i < count($contactos); $i++){
                                        echo '<option value="' . $contactos[$i][0] . '">' .$contactos[$i][1] . ' ' . $contactos[$i][2] . ' ' . $contactos[$i][3] . ' ' . $contactos[$i][4] . '(' . $contactos[$i][5] . ' - ' . $contactos[$i][6] . ')' . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>


                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="form-control" title="Ingrese la zona">
                                <?php
                                if (empty($row[0][25])) {
                                    echo '<option value="">Zona</option>';
                                    $zonas =$con->Listar( "SELECT Id_Unico, Nombre 
                              FROM gf_zona  
                              ORDER BY Nombre ASC");
                                     
                                   for ($i = 0; $i < count($zonas); $i++) {
                                        echo '<option value="' . $zonas[$i][0] . '">' . $zonas[$i][1] . '</option>';
                                    }
                                } else { ?>
                                    <option value="<?php echo $row[0][25]; ?>"><?php echo ($row[0][27]); ?></option>
                                    <?php   for ($i = 0; $i < count($zonas); $i++)  {  ?>
                                        <option value="<?php echo $zonas[$i][0] ?>"><?php echo ucwords((mb_strtolower($zonas[$i][0]))); ?></option>
                                    <?php
                                    }      ?>
                                <?php
                                    echo '<option value=""></option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigo_afp" class="col-sm-5 control-label">Código AFP:</label>
                            <input type="text" name="codigo_afp" id="codigo_afp" class="form-control" maxlength="500" title="Ingrese la Código AFP" onkeypress="return txtValida(event)" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Código AFP" required style="width: 34%" value="<?php echo ($row[0][29]); ?>">

                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -40px; margin-bottom:-30px; margin-left: 0px;">Guardar</button>
                        </div>


                        <input type="hidden" name="MM_insert">
                    </form>
                </div>
            </div> <!-- Cierra clase col-sm-7 text-left -->

            <!-- Botones de consulta -->
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px;margin-top:-22">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO CONTABLE
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_DIRECCION_TERCERO.php" style="margin-top:-10px"><button class="btn btn-primary btnInfo" style="margin-bottom:10px">DIRECCIÓN</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO PRESUPUESTAL
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_CUENTA_BANCARIA_TERCERO.php"><button class="btn btnInfo btn-primary">CUENTA BANCARIA</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO<br />ALMACEN
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        TAREAS DE MANTENIMIENTO
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TELEFONO.php"><button class="btn btn-primary btnInfo">TELEFONO</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        RETENCIONES EFECTUADAS
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php"><button class="btn btn-primary btnInfo">CONDICIÓN</button></a><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="registrar_TERCERO_CONTACTO_NATURAL.php" class="btn btnInfo btn-primary">CONTACTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php" class="btn btnInfo btn-primary" style="margin-top:15px">PERFIL CONDICIÓN</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Fin de Contenedor Principal -->
    <?php require_once('footer.php'); ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2_single").select2({

            allowClear: true
        });
    </script>

</body>

</html>