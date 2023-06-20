<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#14/12/2017 | Parametrización, tildes, diseño
########################################################################################
require_once 'head.php';
require_once('Conexion/conexionPDO.php');
//Consulta para el listado de registro de la tabla .
$con = new ConexionPDO();
$id_cent_cost = " ";
$anno = $_SESSION['anno'];
$id_cent_cost_aux = " ";
$action=$_REQUEST['action'];

switch ($action) {
    case 1:
        if (isset($_REQUEST["id"])) {
            $id_cent_cost = base64_decode($_REQUEST["id"]);
            
            $row = $con->Listar("SELECT cc.Id_Unico, cc.Nombre, cc.Movimiento, 
            cc.Sigla, cc.TipoCentroCosto, tcc.Nombre, cc.Predecesor, (select Nombre from gf_centro_costo where Id_Unico = cc.Predecesor) nombrePredecesor, 
            cc.ClaseServicio, cs.Nombre, cc.cantidad_distribucion 
            FROM gf_centro_costo cc 
            LEFT JOIN gf_tipo_centro_costo tcc ON cc.TipoCentroCosto = tcc.Id_Unico
            LEFT JOIN gf_clase_servicio cs ON cc.ClaseServicio = cs.Id_Unico
            WHERE cc.Id_Unico = '$id_cent_cost'");
        }
        
//Hacer la consulta en la tabla gf_centro_costo para determinar que si el registro a modificar existe como un predecesor de otro registro.
$num = 0;
$queryPred = $con->Listar("SELECT Id_Unico FROM gf_centro_costo WHERE Predecesor = ".$row[0][0]);

//Consultas para el listado de los diferentes combos correspondientes.
//Tipo Centro Costo.
$sqlTipoCentCost =$con->Listar( "SELECT Id_Unico, Nombre 
FROM gf_tipo_centro_costo 
WHERE Id_Unico != ".$row[0][4]."
ORDER BY Nombre ASC");

//Predecesor.
//Movimiento es un campo tipo bit, por tanto tres es No.
$sqlPredecesor = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_centro_costo 
WHERE Movimiento = 2
AND Id_Unico != ".$row[0][6]."
AND Id_Unico != ".$row[0][0]."
AND parametrizacionanno = $anno
ORDER BY Nombre ASC");

//Clase Servicio.
$sqlClaseServ = $con->Listar("SELECT Id_Unico, Nombre 
FROM gf_clase_servicio 
WHERE Id_Unico != ".$row[0][8]."
ORDER BY Nombre ASC");
?>
<html>

<head>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <title>Modificar Centro Costo</title>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Centro Costo</h2>
                <a href="CENTRO_COSTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Centro Costo: <?php echo ucwords(mb_strtolower($row[0][1])) ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form  name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"><?php echo $row[0][0]; ?>Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" id="id" name="id" value="<?php echo $row[0][0]; ?>">
                        <div class="form-group" style="margin-top: -20px; ">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car');" placeholder="Nombre" value="<?php echo ucwords((mb_strtolower($row[0][1]))); ?>" required>
                        </div>
                        <div class="form-group" style="margin-top: -20px; ">
                            <label for="movimiento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                            <div class="form-inline">
                                <?php if ($row[0][2] == 1) { ?>

                                    <input type="radio" name="movimiento" value="1" onchange="javaScript:cambio(1);" checked="checked" title="Seleccione si tiene movimiento o no." /> Sí&nbsp &nbsp
                                    <input type="radio" name="movimiento" value="2" onchange="javaScript:cambio(2);" title="Seleccione si tiene movimiento o no." /> No
                                <?php } else { ?>
                                    <input type="radio" name="movimiento" value="1" onchange="javaScript:cambio(1);" title="Seleccione si tiene movimiento o no." /> Sí&nbsp &nbsp
                                    <input type="radio" name="movimiento" value="2" onchange="javaScript:cambio(2);" checked="checked" title="Seleccione si tiene movimiento o no." /> No
                                <?php } ?>
                            </div>
                            <br />
                        </div>
                        <div class="form-group" style="margin-top: -20px; ">
                            <label for="sigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                            <input type="text" name="sigla" id="sigla" class="form-control" maxlength="10" title="Ingrese la sigla" onkeypress="return txtValida(event,'num_car')" value="<?php echo utf8_encode($row[0][3]); ?>" placeholder="Sigla" required>
                        </div>
                        <div class="form-group" style="margin-top: -20px; ">
                            <label for="tipoCentCost" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Centro Costo:</label>
                            <select name="tipoCentCost" id="tipoCentCost" class="select2_single form-control" title="Ingrese el tipo de centro de costo" required>
                                <option value="<?php echo $row[0][4]; ?>"><?php echo ucwords((mb_strtolower($row[0][5]))); ?></option>
                                <?php for ($i=0; $i <count($sqlTipoCentCost) ; $i++) {  ?>
                                    <option value="<?php echo $sqlTipoCentCost[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlTipoCentCost[$i][1]))); ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="predecesor" class="col-sm-5 control-label">Predecesor:</label>
                            <select name="predecesor" id="predecesor" class="select2_single form-control" title="Ingrese el predecesor">
                                <option value="<?php echo $row[0][6]; ?>">
                                    <?php
                                    if (($row[0][6] == '') || ($row[0][6] == 0))
                                        echo "No hay predecesor";
                                    else
                                        echo ucwords((mb_strtolower($row[0][7])));
                                    ?>
                                </option>
                                <?php for ($i=0; $i <count($sqlPredecesor) ; $i++) { ?>
                                    <option value="<?php echo $sqlPredecesor[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlPredecesor[$i][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="claseServ" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Servicio:</label>
                            <select name="claseServ" id="claseServ" class="select2_single form-control" title="Ingrese la clase de servicio" required>
                                <option value="<?php echo $row[0][8]; ?>"><?php echo ucwords((mb_strtolower($row[0][9]))); ?></option>
                                <?php for ($i=0; $i <count($sqlClaseServ) ; $i++)  {    ?>
                                    <option value="<?php echo $sqlClaseServ[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlClaseServ[$i][1]))); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" id="divcantidad" <?php if ($row[0][2] == 1) { ?> style="display: block; margin-top: -10px; " <?php } else { ?> style="display: none; margin-top: -10px; " <?php } ?>>
                            <label for="cantidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cantidad Distribución:</label>
                            <input type="text" name="cantidad" id="cantidad" class="form-control" value="<?php echo $row[10]; ?>" title="Ingrese la cantidad" onkeypress="return txtValida(event,'num');" placeholder="Cantidad Distribución">
                        </div>
                        <?php if ($row[0][2] == 1) {
                            $row[0][10];
                        } ?>
                        <div align="center">
                            <button type="submit" onclick="modificar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert">
                    </form>
                </div>
            </div> <!-- Cierre col-sm-7 text-left -->
            <!-- Botones de consulta -->
            <div class="col-sm-7 col-sm-3">
                <table class="tablaC table-condensed" style="margin-left: -10px">
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
                                <button class="btn btn-primary btnInfo">CLASE SERVICIO</button>
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

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO<br />ALMACÉN
                                    </a>
                                </div>
                            </td>
                            <td>

                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div> <!-- Cierre row content -->
    </div> <!-- Cierre container-fluid text-center -->
    <?php               
        break;
    case 2:
        $anno = $_SESSION['anno'];
        $conexion=$_SESSION['conexion'];
        #Tipo Centro
        $sqlTipoCentCost = $con->Listar(  "SELECT Id_Unico, Nombre 
        FROM gf_tipo_centro_costo 
        ORDER BY Nombre ASC");
        #Predecesor
        $sqlPredecesor = $con->Listar( "SELECT Id_Unico, Nombre 
        FROM gf_centro_costo 
        WHERE Movimiento = 2  AND parametrizacionanno = $anno
        ORDER BY sigla ASC");
        #Clase Servicio.
        $sqlClaseServ = $con->Listar("SELECT Id_Unico, Nombre 
        FROM gf_clase_servicio  
        ORDER BY Nombre ASC");
      ?>
      <html>
          <head>
              <title>Registrar Centro Costo</title>
              <link href="css/select/select2.min.css" rel="stylesheet">
          </head>
          <body>
              <div class="container-fluid text-center">
                  <div class="row content">
                      <?php require_once 'menu.php'; ?>
                      <div class="col-sm-7 text-left">
                          <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Centro Costo</h2>
                          <a href="CENTRO_COSTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                          <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Centro Costo</h5>
                          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form  name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                                  <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                  <div class="form-group" style="margin-top: -20px; ">
                                      <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                      <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car');"  placeholder="Nombre" required>
                                  </div>
                                  <div class="form-group" style="margin-top: -20px; ">
                                      <label for="movimiento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                                      <div class="form-inline" >
                                          <!-- 1 es Sí y 2 es No. -->
                                          <input type="radio" name="movimiento" value="1" onchange="javaScript:cambio(1);" title="Seleccione si tiene movimiento o no." /> Sí&nbsp &nbsp
                                          <input type="radio" name="movimiento" value="2" onchange="javaScript:cambio(2);" checked="checked" title="Seleccione si tiene movimiento o no." /> No
                                      </div>
                                      <br/>
                                  </div>
                                  <div class="form-group" style="margin-top: -20px; ">
                                      <label for="sigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                                      <input type="text" name="sigla" id="sigla" class="form-control" maxlength="10" title="Ingrese la sigla" onkeypress="return txtValida(event,'num_car')"  placeholder="Sigla" required> 
                                  </div>
                                  <div class="form-group" style="margin-top: -20px; ">
                                      <label for="tipoCentCost" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Centro Costo:</label>
                                      <select name="tipoCentCost" id="tipoCentCost" class="select2_single form-control" title="Ingrese el tipo de centro de costo" required>
                                          <option value="">Tipo Centro Costo</option>
                                          <?php for ($i=0; $i <count($sqlTipoCentCost) ; $i++) {
                                              ?>
                                              <option value="<?php echo $sqlTipoCentCost[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlTipoCentCost[$i][1]))); ?></option>
                                          <?php }
                                          ?>
                                      </select> 
                                  </div>
                                  <div class="form-group" style="margin-top: -10px; ">
                                      <label for="predecesor" class="col-sm-5 control-label">Predecesor:</label>
                                      <select name="predecesor" id="predecesor" class="select2_single form-control" title="Ingrese el predecesor" >
                                          <option value="">Predecesor</option>
                                          <?php for ($i=0; $i <count($sqlPredecesor) ; $i++) {
                                              ?>
                                              <option value="<?php echo $sqlPredecesor[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlPredecesor[$i][1]))); ?></option>
                                          <?php }
                                          ?>
                                      </select> 
                                  </div>
                                  <div class="form-group" style="margin-top: -10px; ">
                                      <label for="claseServ" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Servicio:</label>
                                      <select name="claseServ" id="claseServ" class="select2_single form-control" title="Ingrese la clase de servicio" required>
                                          <option value="">Clase Servicio</option>
                                          <?php for ($i=0; $i <count($sqlClaseServ) ; $i++)  {
                                              ?>
                                              <option value="<?php echo $sqlClaseServ[$i][0]; ?>"><?php echo ucwords((mb_strtolower($sqlClaseServ[$i][1]))); ?></option>
                                          <?php }
                                          ?>
                                      </select> 
                                  </div>
                                  <div class="form-group" id="divcantidad" style="display: none; margin-top: -10px; ">
                                      <label for="cantidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cantidad Distribución:</label>
                                      <input type="text" name="cantidad" id="cantidad" class="form-control"  title="Ingrese la cantidad" onkeypress="return txtValida(event,'num');"  placeholder="Cantidad Distribución">
                                  </div>
                                  <div align="center">
                                  <button type="submit" onclick="agregar()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                                 </div>
                                  <input type="hidden" name="MM_insert" >
                              </form>
                          </div>
                      </div> <!-- Cierra col-sm-7 text-left -->
                     <!-- Botones de consulta -->
                      <div class="col-sm-7 col-sm-3">
                          <table class="tablaC table-condensed" style="margin-left: -10px">
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
                                          <button class="btn btn-primary btnInfo">CLASE SERVICIO</button> 
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
      
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>
                                          <div class="btnConsultas">
                                              <a href="#"> 
                                                  MOVIMIENTO<br/>ALMACÉN 
                                              </a>
                                          </div>
                                      </td>
                                      <td>
      
                                      </td>
                                  </tr>
      
                              </tbody>
                          </table>
                      </div>
                  </div> <!-- Cierra row content -->
              </div> <!-- Cierra container-fluid text-center -->
    <?php
        break;
    default:
        # code...
        break;
}
?>
    <?php require_once 'footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>
    <script>
        function cambio(valor) {
            console.log(valor);
            if (valor == 1) {
                $("#divcantidad").css('display', 'block');
            } else {
                console.log('asc');
                $("#divcantidad").css('display', 'none');
                $("#cantidad").val('');
            }
        }
    </script>
    <script>
        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificar_CENTRO_COSTOJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                       
                                            document.location='CENTRO_COSTO.php';
                                       
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                }
                function agregar(){
                            jsShowWindowLoad('Agregando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificar_CENTRO_COSTOJson.php?action=2",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                       
                                            document.location='CENTRO_COSTO.php';
                                       
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                }
                    </script>
</body>
</html>