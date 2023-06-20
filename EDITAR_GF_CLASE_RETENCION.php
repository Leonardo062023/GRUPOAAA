<?php
###################################################################################################
#**************************************** Modificaciones ****************************************##
###################################################################################################
#31/01/2018 | Erica G. |Campo Clase Descuento
###################################################################################################
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head.php';
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
$id = "";
if (isset($_GET['ide'])) {
    $id = $_GET['ide'];
    switch ($_SESSION['conexion']) {
        case '1':
          $row = $con->Listar("SELECT Id_Unico,Nombre, clase_sia FROM gf_clase_retencion 
          WHERE (Id_Unico) = '$id'");
        break;
        case '2':
          $row = $con->Listar("SELECT Id_Unico,Nombre, clase_sia FROM gf_clase_retencion 
          WHERE (Id_Unico) = '$id'");
      break;
      }
}
?>
<html>
    <head>
        <title>Modificar Clase Retención</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!--######VALIDACIONES#####-->
        <style>
            label #nombre-error, #sia-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
        }
        body{
            font-size: 12px;
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
            }
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>
        <style>
            .form-control {font-size: 12px;}
        </style>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"">Modificar Clase Retención</h2>
                    <a href="LISTAR_GF_CLASE_RETENCION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo 'Clase:'.ucwords(mb_strtolower($row[1])); ?> </h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

                        <?php for ($i=0; $i <count($row) ; $i++) {?>
      
                            <p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son oligatorios.
                            </p>
                            <input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label">
                                    <strong style="color:#03C1FB;">*</strong>Nombre:
                                </label>
                                <input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" value="<?php echo ucwords(mb_strtolower($row[$i][1])); ?>" placeholder="Nombre">
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sia" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Clase Descuento</label>
                                <select name="clase_sia" id="clase_sia" class="form-control select2_single" title="Seleccione Clase Descuento" required="required">
                                    <?php if(($row[$i][2])==1) {
                                        echo '<option value="1">Descuento Retenciones</option>
                                              <option value="2">Otros Descuentos</option>';
                                    } else {
                                        echo '<option value="2">Otros Descuentos</option>
                                            <option value="1">Descuento Retenciones</option>  ';
                                    }?>
                                    
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert">
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function() {
              $(".select2_single").select2({
                allowClear: true
              });
            });

            function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarClaseRetencionJson.php?action=3",
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
                                       
                                            document.location='LISTAR_GF_CLASE_RETENCION.php';
                                       
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
    <?php require_once 'footer.php'; ?>
    </body>
</html>