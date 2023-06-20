<?php
require_once 'head.php';

require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$action = $_REQUEST['action'];
$id = " ";
$queryCond = "";
if (isset($_GET["id"])) {
    if ($action == 1) {
        $id = base64_decode(($_REQUEST["id"]));
        $_SESSION['url'] = 'Modificar_GF_DEPENDENCIA.php?id=' . $id;
        $row = $con->Listar("SELECT d.id_unico, d.nombre, d.sigla, d.movimiento, d.activa , dep.nombre, cc.nombre, td.nombre, 
          dep.id_unico, cc.id_unico, td.id_unico, dep.sigla 
          FROM gf_dependencia d 
          LEFT JOIN gf_dependencia dep ON d.predecesor = dep.id_unico 
          LEFT JOIN gf_centro_costo cc ON d.centrocosto=cc.id_unico 
          LEFT JOIN gf_tipo_dependencia td ON d.tipodependencia=td.id_unico 
          WHERE d.id_unico='$id'");
    }
}

?>
<?php $titulo = ""; ?>
<?php if ($action == 1) { 
    $titulo = "Modificar Dependencia" ?>
    <title><?php echo $titulo ?></title>
<?php } else { 
    $titulo = "Registrar Dependencia" ?>
    <title><?php echo $titulo ?></title>
<?php }


?>


<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<link href="css/bootstrap.min.js" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-top: -10px">
                <!--Titulo del formulario-->
                <?php if ($action == 1) { ?>
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>
                <?php } else{ ?>
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo ?></h2>
                <?php }


                ?>

                <a href="LISTAR_GF_DEPENDENCIA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">
                    <?php
                    echo $row[0][2] . ' - ' . $row[0][1];                    // Sigla - Nombre de la dependencia            
                    ?>
                </h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="controller/controllerGFDependencia.php?action=modify">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]; ?>">
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required value="<?php echo $row[0][1] ?>">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" required>
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sigla:</label>
                            <?php if ($action == 1) { ?>
                                <input type="text" name="sigla" id="sigla" class="form-control" placeholder="Sigla" onkeypress="return txtValida(event,'sin_espcio')" style="text-transform:uppercase;" maxlength="10" title="Ingrese sigla" value="<?php echo $row[0][2] ?>">
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="text" name="sigla" id="sigla" class="form-control" placeholder="Sigla" onkeypress="return txtValida(event,'sin_espcio')" style="text-transform:uppercase;" maxlength="10" title="Ingrese sigla">
                            <?php }


                            ?>

                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="movimiento" class="col-sm-5 control-label" style="margin-top:-4px;"><strong style="color:#03C1FB; ">*</strong>Movimiento:</label>
                            <?php if ($action == 1) { ?>
                                <?php if ($row[0][3] == '0') { ?>
                                    <input type="radio" name="movimiento" value="0" title="Seleccione si es auxiliar para movimiento" checked>Sí
                                    <input type="radio" name="movimiento" value="1" title="Seleccione si no es auxiliar para movimiento">No
                                <?php } else { ?>
                                    <input type="radio" name="movimiento" value="0" title="Seleccione si es auxiliar para movimiento">Sí
                                    <input type="radio" name="movimiento" value="1" title="Seleccione si no es auxiliar para movimiento" checked>No
                                <?php } ?>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="radio" name="movimiento" value="0" title="Seleccione si es auxiliar para movimiento">Sí
                                <input type="radio" name="movimiento" value="1" title="Seleccione si no es auxiliar para movimiento" checked>No
                            <?php }


                            ?>

                        </div>
                        <div class="form-group">
                            <label for="activa" class="col-sm-5 control-label" style="margin-top:-4px;"><strong style="color:#03C1FB;">*</strong>Activa:</label>
                            <?php if ($action == 1) { ?>
                                <?php if ($row[0][4] == '0') { ?>
                                    <input type="radio" name="activa" value="0" title="Seleccione si esta activa" checked>Sí
                                    <input type="radio" name="activa" value="1" title="Seleccione si no esta activa">No
                                <?php } else { ?>
                                    <input type="radio" name="activa" value="0" title="Seleccione si esta activa">Sí
                                    <input type="radio" name="activa" value="1" title="Seleccione si no esta activa" checked>No
                                <?php } ?>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <input type="radio" name="activa" value="0" title="Seleccione si esta activa">Sí
                                <input type="radio" name="activa" value="1" title="Seleccione si no esta activa" checked>No
                            <?php }


                            ?>

                        </div>
                        <div class="form-group">
                            <!-- Busqueda rapida Predecesor -->
                            <input type="hidden" id="predecesor" name="predecesor" title="Seleccione predecesor">
                            <label for="predecesor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Predecesor:</label>
                            <!-- Consulta campo predecesor si esta vacio-->
                            <?php if ($action == 1) { ?>
                                <?php
                                if (empty($row[0][5])) {
                                    $predv = $con->Listar("SELECT id_unico, nombre, sigla  
                                FROM gf_dependencia 
                                WHERE  compania = $compania
                                ORDER BY nombre ");
                                } else {
                                    $predv = $con->Listar("SELECT id_unico, nombre, sigla  
                                FROM gf_dependencia 
                                WHERE  compania = $compania
                                   AND id_unico != " . $row[0][8] . "
                                ORDER BY nombre ");
                                }

                                ?>
                                <select name="predecesor" id="predecesorSelect" title="Seleccione predecesor" class="select2_single form-control">
                                    <?php if ($row[0][5] == '' || $row[0][5] == NULL) { ?>
                                        <option>Predecesor</option>
                                    <?php } else { ?>
                                        <option value="<?php echo $row[0][8] ?>"><?php echo $row[0][11] . ' - ' . ucwords(strtolower($row[0][5])) ?></option>
                                    <?php }
                                    for ($i = 0; $i < count($predv); $i++) { ?>
                                        <option value="<?php echo $predv[$i][0]; ?>"><?php echo $predv[$i][2] . ' - ' . ucwords(strtolower($predv[$i][1])); ?></option>
                                    <?php } ?>
                                    <option value="">Predecesor</option>

                                </select>

                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <?php
                                $pred = $con->Listar("SELECT id_unico, nombre , sigla 
                            FROM gf_dependencia 
                            WHERE compania = $compania
                            ORDER BY nombre ASC");

                                ?>
                                <select name="predecesor" id="predecesorSelect" title="Seleccione predecesor" class="select2_single form-control">
                                    <option value="">Predecesor</option>
                                    <?php for ($i = 0; $i < count($pred); $i++) { ?>
                                        <option value="<?php echo $pred[$i][0]; ?>"><?php echo $pred[$i][2] . ' - ' . ucwords(strtolower($rowP[1])); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>



                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <!-- Busqueda rapida Centro de costo -->
                            <input type="hidden" id="ccosto" name="ccosto" title="Seleccione centro de costo">
                            <label for="centroC" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Centro Costo:</label>
                            <?php if ($action == 1) { ?>
                                <?php
                                $centroCv = $con->Listar("SELECT id_unico, nombre 
                             FROM gf_centro_costo 
                             WHERE parametrizacionanno = $anno 
                             ORDER BY nombre ASC");

                                ?>
                                <!-- Consulta campo centro costro si esta lleno-->
                                <?php
                                $idcentrocost = $row[0][9];
                                $centroC = $con->Listar("SELECT id_unico, nombre 
                            FROM gf_centro_costo 
                            WHERE id_unico != $idcentrocost
                            AND parametrizacionanno = $anno  
                            ORDER BY nombre ASC");

                                ?>
                                <select name="centroC" id="centroSelect" class="select2_single form-control" title="Seleccione centro costo">
                                    <?php if ($row[0][6] == '' || $row[0][6] == NULL) { ?>
                                        <option value="">Centro de costo</option>
                                        <?php for ($i = 0; $i < count($centroCv); $i++) { ?>
                                            <option value="<?php echo $centroCv[$i][0]; ?>"><?php echo ucwords(strtolower($centroCv[$i][1])); ?></option>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <option value="<?php echo $row[0][9] ?>"><?php echo ucwords(strtolower($row[0][6])) ?></option>
                                        <?php for ($i = 0; $i < count($centroC); $i++) { ?>
                                            <option value="<?php echo $centroC[$i][0]; ?>"><?php echo ucwords(strtolower($centroC[$i][1])); ?></option>
                                        <?php } ?>
                                        <option value=""></option>
                                    <?php } ?>
                                </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <?php
                                $centroC = $con->Listar("SELECT id_unico, nombre 
                            FROM gf_centro_costo 
                            WHERE parametrizacionanno = $anno 
                            ORDER BY nombre ASC");

                                ?>
                                <select name="centroC" id="centroSelect" title="Seleccione centro de costo" class="select2_single form-control">
                                    <option value="">Centro Costo</option>
                                    <?php for ($i = 0; $i < count($centroC); $i++) { ?>
                                        <option value="<?php echo $centroC[$i][0]; ?>"><?php echo ucwords(strtolower($centroC[$i][1])); ?></option>
                                    <?php } ?>
                                </select>
                            <?php }


                            ?>
                            <!-- Consulta campo centro costro si esta vacio-->


                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <!-- Busqueda rapida Tipo de Dependencia -->
                            <input type="hidden" id="tipod" name="tipod" title="Seleccione centro de costo" />
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Dependencia:</label>
                            <!-- Consulta campo tipo dependencia-->
                            <?php if ($action == 1) { ?>
                                <?php
                                $auxdepen=$row[0][10];
                            $depen = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_dependencia WHERE id_unico != $auxdepen  ORDER BY nombre ASC");

                            ?>
                            <select name="tipo" id="tipo" title="Seleccione tipo dependencia" class="select2_single form-control" required="required">
                                <option value="">Tipo Dependencia</option>
                                <option value="<?php echo $row[0][10]; ?>"><?php echo ucwords(strtolower($row[0][7])) ?></option>
                                <?php for ($i = 0; $i < count($depen); $i++) { ?>
                                    <option value="<?php echo $depen[$i][0]; ?>"><?php echo ucwords(strtolower($depen[$i][1])); ?></option>
                                <?php } ?>
                            </select>
                            <?php }

                            ?>
                            <?php if ($action == 2) { ?>
                                <?php 
                            $depen= $con->Listar("SELECT id_unico, nombre FROM gf_tipo_dependencia ORDER BY nombre ASC");
                           
                            ?>
                            
                            <select name="tipo" id="tipo" title="Seleccione tipo dependencia" class="select2_single form-control" required="required">
                                <option value="">Tipo Dependencia</option>
                                <?php for ($i = 0; $i < count($depen); $i++){ ?>
                                    <option value="<?php echo $depen[$i][0];?>"><?php echo ucwords(strtolower($depen[$i][1]));?></option>
                                <?php } ?>
                            </select>    
                            <?php }


                            ?>
                            
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <?php if ($action == 1) { ?>
                                <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>

                            <?php } else { ?>
                                <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            <?php } ?>
                        </div>
                        <input type="hidden" name="MM_insert">
                    </form>
                </div>
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed">
                    <thead>
                        <tr>
                            <th>
                                <h2 class="titulo" align="center">Consultas</h2>
                            </th>
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#">MOVIMIENTOS <br />ALMACÉN</a></div>
                            </td>
                            <td>
                                <a href="GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo md5($row[0]); ?>" class="btn btn-primary btnInfo">RESPONSABLE</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> ELEMENTOS DEVOLUTIVOS</a></div>
                            </td>
                            <td>
                                <a href="registrar_CENTRO_COSTO.php" class="btn btn-primary btnInfo">CENTRO DE COSTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br />COMPRAS</a></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript"></script>
    <script src="js/select/select2.full.js"></script>

    <script>
        $(document).ready(function() {
            $(".select2_single").select2({

                allowClear: true
            });


        });

        function agregar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "controller/controllerGFDependencia.php?action=2",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");

                        document.location = 'LISTAR_GF_DEPENDENCIA.php';

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

        function modificar() {
            jsShowWindowLoad('Agregando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "controller/controllerGFDependencia.php?action=3",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    jsRemoveWindowLoad();
                    console.log(response);
                    if (response == 1) {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");

                        document.location = 'LISTAR_GF_DEPENDENCIA.php';

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