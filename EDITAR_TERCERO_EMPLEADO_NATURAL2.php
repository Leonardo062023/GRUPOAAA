<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$id_asoNat = "";
$queryAsociadoNat="";
if (isset($_GET["id"])){ 
    $id_asoNat = (($_GET["id"]));
    $queryAsociadoNat = "SELECT T.Id_Unico AS ID_UNICO_T,
             TI.Id_Unico AS TIPO_I, 
             TI.Nombre AS NOM_I,
             T.NumeroIdentificacion,
             T.NombreUno,
             T.NombreDos,
             T.ApellidoUno,
             T.ApellidoDos,         
             TR.Id_Unico AS TIPO_R, 
             TR.Nombre AS NOMBRE_R, 
             T.EMAILF, 
             z.id_unico AS ZONA, 
             z.nombre AS NOM_Z, 
             T.tarjeta_profesional 
    FROM gf_tercero T
    LEFT JOIN gf_tipo_identificacion TI  ON T.TipoIdentificacion = TI.Id_Unico
    LEFT JOIN gf_tipo_regimen TR  ON T.TipoRegimen = TR.Id_Unico 
    LEFT JOIN gf_zona z ON T.zona = z.id_unico 
    WHERE T.Id_Unico = $id_asoNat";
}

$stmt = oci_parse($oracle, $queryAsociadoNat);        // Preparar la sentencia
$ok   = oci_execute( $stmt );            // Ejecutar la sentencia
if( $ok == true ){
    $row = oci_fetch_assoc( $stmt );
    $id_t = $row['ID_UNICO_T'];
}


$_SESSION['id_tercero'] = $id_t;
$_SESSION['perfil'] = "N"; //Natural.
$_SESSION['url'] = "EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=".(($_GET["id"]));
$_SESSION['tipo_perfil']='Asociado natural';
#****** Tipo Identificación *********#
$idt = 0;
if(!empty($row['TIPO_I'])){$idt=$row['TIPO_I'];}
$idents = "SELECT Id_Unico, Nombre FROM gf_tipo_identificacion 
    WHERE Id_Unico != $idt  ORDER BY Nombre ASC";

$queryTI = oci_parse($oracle, $idents);        // Preparar la sentencia
$ident   = oci_execute($queryTI);    

#****** Tipo Régimen  *********#
$idtr = 0;
if(!empty($row['TIPO_R'])){$idtr=$row['TIPO_R'];}
$regimenes = "SELECT Id_Unico, Nombre 
    FROM gf_tipo_regimen WHERE Id_Unico !=$idtr ORDER BY Nombre ASC";
$queryTR = oci_parse($oracle, $regimenes);        // Preparar la sentencia
$regimen   = oci_execute($queryTR);     

#****** Zona *********#
if(!empty($row['ZONA'])){ $idz = $row['ZONA'];} else { $idz =0;}
$sqlZona = "SELECT Id_Unico, Nombre 
  FROM gf_zona 
  WHERE Id_Unico != $idz 
  ORDER BY Nombre ASC";
  $queryZ = oci_parse($oracle, $sqlZona);        // Preparar la sentencia
  $zona   = oci_execute($queryZ); 
 ?>
    <title>Modificar Empleado Natural</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoI-error,#numId-error,#correo-error, #primerN-error, #primerA-error{
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
    </style>
    <script>
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {
                    $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            $(".cancel").click(function () {
                validator.resetForm();
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content" >				
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px">
                <h2 class="tituloform" align="center">Modificar Empleado Natural</h2>
                <a href="LISTAR_TERCERO_EMPLEADO_NATURAL2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[4] . ' ' . $row[6] ?></h5>
                <div class="client-form contenedorForma">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEmpleadoNatural2Json.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="oculto">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row['ID_UNICO_T'] ?>">
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="tipoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Identificación:</label>
                            <select name="tipoI" id="tipoI" class="select2_single form-control" title="Seleccione el tipo identificación" required>
                                <option value="<?php echo $row['TIPO_I']; ?>"><?php echo ($row['NOM_I']); ?></option>
                                <?php while ($rowI = mysqli_fetch_assoc($ident)) { ?>
                                    <option value="<?php echo $rowI["Id_Unico"] ?>"><?php echo ucwords((mb_strtolower($rowI["Nombre"])));
                                } ?>
                                            <?php 
                                              if( $ident == true )
                                              {
                                                while  (($fileTI=oci_fetch_assoc($queryTI))!=false){?>
                                                  <option value="<?php echo $fileTI["ID_UNICO"]; ?>">
                                                  <?php echo ucwords((mb_strtolower($fileTI["NOMBRE"]))); ?>
                                              <?php }


                                              } ?>    
                            
                            </option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numId" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <input type="number" name="numId" id="numId" class="form-control col-sm-5" maxlength="20" title="Ingrese el número identificación" onkeypress="return txtValida(event,'num')" value="<?php echo $row['NUMEROIDENTIFICACION']; ?>" placeholder="Número identificación"  required/>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Nombre:</label>
                            <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row['NOMBREUNO']; ?>"   placeholder="Primer Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoN" class="col-sm-5 control-label">Segundo Nombre:</label>
                            <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row['NOMBREDOS']; ?>"  placeholder="Segundo Nombre">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Apellido:</label>
                            <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row['APELLIDOUNO']; ?>"  placeholder="Primer Apellido" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoA" class="col-sm-5 control-label">Segundo Apellido:</label>
                            <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row['APELLIDODOS']; ?>"  placeholder="Segundo Apellido">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="regimen" class="col-sm-5 control-label">Tipo Régimen:</label>
                            <select name="regimen" id="regimen" class="select2_single form-control" title="Seleccione el tipo régimen">
                                <?php
                                if (!empty($row['TIPO_R'])) {
                                    echo '<option value="' . $row['TIPO_R'] . '">' . ucwords((mb_strtolower($row['NOMBRE_R']))) . '</option>';
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                               
                                if( $regimen == true )
                                              {
                                                while  (($fileTR=oci_fetch_assoc($queryTR))!=false){?>
                                                  <option value="<?php echo $fileTR["ID_UNICO"]; ?>">
                                                  <?php echo ucwords((mb_strtolower($fileTR["NOMBRE"]))); ?>
                                              <?php }
                                } 
                                ?>                                                
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona" >
                                <?php 
                                if(!empty($row['ZONA'])){
                                    echo '<option value="'.$row['ZONA'].'">'.ucwords(mb_strtolower($row['NOM_Z'])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                    if( $zona == true )
                                                  {
                                                    while  (($fileZ=oci_fetch_assoc($queryZ))!=false){?>
                                                      <option value="<?php echo $fileZ["ID_UNICO"]; ?>">
                                                      <?php echo ucwords((mb_strtolower($fileZ["NOMBRE"]))); ?>
                                                  <?php }
                                    } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="tp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tarjeta Profesional:</label>
                            <input type="text" name="tarjetaP" id="tarjetaP" value="<?php echo $row['TARJETA_PROFESIONAL']?>" class="form-control" maxlength="500" title="Ingrese Tarjeta Profesional" placeholder="Tarjeta Profesional" >
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" value="<?php echo $row['EMAILF']?>" >
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom:-10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>        		
        <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
            <table class="tablaC table-condensed"  style="margin-left: -30px">
                <thead>
                    <tr>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="btnConsultas" style="margin-bottom: 1px;">
                                <a href="#">
                                    MOVIMIENTO CONTABLE
                                </a>
                            </div>
                        </td>
                        <td>
                             <a href="GF_DIRECCION_TERCERO.php" class="btn btn-primary btnInfo">DIRECCIÓN</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="btnConsultas" style="margin-bottom: 1px;">
                                <a href="#"> 
                                    MOVIMIENTO PRESUPUESTAL
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="GF_CUENTA_BANCARIA_TERCERO.php" class="btn btn-primary btnInfo">CUENTA BANCARIA</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="btnConsultas" style="margin-bottom: 1px;">
                                <a href="#"> 
                                    MOVIMIENTO<br/>ALMACEN
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="GF_TIPO_ACTIVIDAD_TERCERO.php" class="btn btn-primary btnInfo" >TIPO ACTIVIDAD</a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="btnConsultas" style="margin-bottom: 1px;">
                                <a href="#"> 
                                    TAREAS DE MANTENIMIENTO 
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="GF_TELEFONO.php" class="btn btn-primary btnInfo" >TELEFONO</a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="btnConsultas" style="margin-bottom: 1px;">
                                <a href="#"> 
                                    RETENCIONES EFECTUADAS
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="GF_CARGO_TERCERO.php" class="btn btn-primary btnInfo" >CARGO</a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <a href="GF_CONDICION_TERCERO.php" class="btn btn-primary btnInfo" >CONDICIÓN</a><br/>
                        </td>
                    </tr>     
                    <tr>
                        <td></td>
                        <td>
                            <a href="GF_PERFIL_CONDICION.php" class="btn btn-primary btnInfo" >PERFIL CONDICIÓN</a><br/>
                        </td>
                    </tr>  
                    <tr>
                        <td></td>
                        <td>
                            <a onclick="javascript:captura()" class="btn btn-primary btnInfo" >CAPTURAR HUELLA</a><br/>
                        </td>
                    </tr> 
                </tbody>
            </table>                
        </div>
    </div>		
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Captura de Huella</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <img src="images/Huella.png" style="width: 500px; height: 300px"/>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
<script>
    function captura(){
        
        $("#myModal1").modal('show');
    }
</script>