<?php

require_once 'head.php'; 
require 'Conexion/ConexionPDO.php';
$con        = new ConexionPDO();
//Captura de ID y consulta del resgistro correspondiente.
$id_ciudad = " ";
if (isset($_GET["id_ciudad"])){ 
  $id_ciudad = (($_GET["id_ciudad"]));
  switch ($_SESSION['conexion']) {
    case '1':
      $rowss = $con->Listar("SELECT * FROM gf_ciudad WHERE id_unico = '$id_ciudad'");
    break;
    case '2':
      $rowss = $con->Listar("SELECT * FROM gf_ciudad WHERE id_unico = '$id_ciudad'");
    break;
  }
 
   
   
 }?>
<title>Modificar Ciudad</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #nombre-error,#sltctai-error  {
        display: block;
        color: #155180;
        font-weight: normal;
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
            },
            rules: {
                sltmes: {
                    required: true
                },
                sltcni: {
                    required: true
                },
                sltAnnio: {
                    required: true
                }
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




<!-- contenedor principal -->
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>


<div class="col-sm-7 text-left" style="margin-top:-10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Modificar Ciudad</h2>

      <!--volver-->
      <a href="listar_GF_CIUDAD.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><span>Ciudad: <?php echo $row[1] ?></span></h5> 
      <!---->
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- inicio del formulario --> 
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

        
                <?php for ($i=0; $i <count($rowss) ; $i++) {?>

                    <input type="hidden" name="id" value="<?php echo $rowss[$i][0] ?>">
                    
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el Nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required  value="<?php echo $rowss[$i][1] ?>">
            </div>
                    <div class="form-group">
                        
                        <div class="form-group" style="margin-top: -10px;">
                          <label for="rss" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Rss:</label>
                            <input type="text" name="rss" id="rss" class="form-control" maxlength="10"  onkeypress="return txtValida(event, 'num')" placeholder="Rss" title="Ingrese el rss" value="<?php echo $rowss[$i][3] ?>">
                        </div>           
               

                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Departamento</label>
                            <select name="departamento" id="sltctai" required="true" style="height: auto" class="select2_single form-control" title="Seleccione Departamento">
                             <?php 
                                $idDepartamentoCiudad=$rowss[$i][2];
                                $rows = $con->Listar("SELECT Id_Unico, Nombre FROM gf_departamento WHERE Id_Unico = '$idDepartamentoCiudad'");
                                for ($e=0; $e <count($rows) ; $e++){?> 
                                <option value="<?php echo $rows[$e][0] ?>"><?php echo $rows[$e][1]; }?></option>
                                <?php 
                                $rows = $con->Listar("SELECT Id_Unico, Nombre FROM gf_departamento WHERE Id_Unico != '$idDepartamentoCiudad'");
                                for ($o=0; $o <count($rows) ; $o++){?> 
                                <option value="<?php echo $rows[$o][0] ?>"><?php echo $rows[$o][1]; }?></option>
                            </select>
                        </div><br>


                        
                      
                      <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                     </div>



              
                    </div>
                    <?php } ?>
                </form>
                <!-- Fin de división y contenedor del formulario -->           
                </div>     
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed" >
                    <thead>
                      <tr>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                    
                        <td>
                            <a href="registrar_GF_DEPARTAMENTO.php" class="btn btn-primary btnInfo">Departamento</a>
                        </td>
                      </tr>
                      <tr>
                
                    
                      </tr>
                      <tr>
                  
                        <td></td>
                      </tr>
                    </tbody>
                </table>                
            </div>
        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->
    </div>


  <!--
     Fin del Contenedor principal 
       Información adicional 
  -->
<script src="js/select/select2.full.js"></script>
<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificar_GF_CiudadJson.php?action=3",
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
                                       
                                            document.location='listar_GF_CIUDAD.php';
                                       
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
    <!-- Llamado al pie de pagina -->

</div>
<?php require_once 'footer.php' ?>
</body>
</html>