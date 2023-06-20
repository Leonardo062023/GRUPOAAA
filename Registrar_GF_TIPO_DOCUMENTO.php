<?php 
require_once 'head.php';
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
$compania       = $_SESSION['compania'];
#FORMATO
$row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_formato WHERE compania = $compania ORDER BY Nombre ASC");

//$condicion = "";
//$cond =   $mysqli->query($condicion);

#CLASE
$rows = $con->Listar("SELECT id_unico, nombre FROM gf_clase_informe ORDER BY nombre ASC");

//$cl = "";
//$cl = $mysqli->query($cl);

#DEPENDENCIA
$rowss = $con->Listar("SELECT id_unico, nombre FROM gf_dependencia WHERE compania = $compania ORDER BY nombre ASC");

//$dep = "";
//$dep = $mysqli->query($dep);
?>
<title>Registrar Tipo Documento</title>
</head>
<body>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#nombre-error, #dependencia1-error{
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
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Registrar Tipo Documento</h2>
            <a href="listar_GF_TIPO_DOCUMENTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Tipo</h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form"> 
               <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <!--Ingresa la información-->
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
                    </div>
                    <div class="form-group" style="margin-top:-10px">
                        <label for="obligatorio" class="col-sm-5 control-label" style="margin-top:-5px"><strong style="color:#03C1FB;">*</strong>¿Es obligatorio?:</label>
                        <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si es obligatorio" value="1">Sí
                        <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si no es obligatorio" value="2" checked="checked">No
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="consecutivo" class="col-sm-5 control-label" style="margin-top:-5px"><strong style="color:#03C1FB;">*</strong>¿Es consecutivo único?:</label>
                        <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si es consecutivo único" value="1">Sí
                        <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si no es consecutivo único" value="2" checked="checked">No
                    </div>
                   
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="formato" class="col-sm-5 control-label">Formato:</label>
                        <select name="formato" id="formato" class="select2_single form-control col-sm-1" title="Seleccione el formato">
                          <option value="">Formato</option>
                          <?php for ($i=0; $i <count($row) ; $i++) {?>
                          <option value="<?php echo $row[$i][0] ?>"><?php echo ucwords((mb_strtolower($row[$i][1])));}?></option>;
                        </select> 
                    </div>

                    <div class="form-group" style="margin-top: -5px;">
                        <label for="clase" class="col-sm-5 control-label">Clase Informe:</label>
                        <select name="clase" id="clase" class="select2_single form-control col-sm-1" title="Seleccione Clase Informe" >
                          <option value="">Clase Informe</option>
                          <?php for ($i=0; $i <count($rows) ; $i++) {?>
                          <option value="<?php echo $rows[$i][0]?>"><?php echo ucwords((mb_strtolower($rows[$i][1])));}?></option>;
                        </select> 
                    </div>

                    <div class="form-group" id="dependencia" style="margin-top: -5px;">
                        <label for="dependencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Dependencia:</label>
                        <select name="dependencia" id="dependencia" class="select2_single form-control col-sm-1" title="Seleccione Dependencia" >
                          <option value="">Dependencia</option>
                          <?php for ($i=0; $i <count($rowss) ; $i++) {?>
                          <option value="<?php echo $rowss[$i][0]?>"><?php echo ucwords((mb_strtolower($rowss[$i][1])));}?></option>;
                        </select> 
                    </div>

                    <div class="form-group" style="margin-top: -5px;">
                        <label for="vigencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Días Vigencia:</label>
                        <input type="text" name="vigencia" id="vigencia" class="form-control" maxlength="150" title="Ingrese Días Vigencia" onkeypress="return txtValida(event,'num')" placeholder="Días de Vigencia" >
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                      <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
        <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >RESPONSABLE</button><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >CARACTERISTICA</button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
      
<?php require_once 'footer.php';?>
<script src="js/select/select2.full.js"></script>

  <script>
                             function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipo_Documento.php?action=2",
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
                                       
                                            document.location='listar_GF_TIPO_DOCUMENTO.php';
                                       
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

