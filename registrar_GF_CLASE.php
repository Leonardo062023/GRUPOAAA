<?php 
require_once 'head.php';

//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
 // session_start();
 require_once('Conexion/conexionPDO.php');
 //Consulta para el listado de registro de la tabla .

 $con = new ConexionPDO();
 
 switch ($_SESSION['conexion']) {
     case '1':
       $row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_clase  ORDER BY Nombre ASC");
      
     break;
     case '2':
       $row = $con->Listar("SELECT Id_Unico, Nombre FROM gf_clase  ORDER BY Nombre ASC");
     break;
 }
 
?>

<!-- Llamado a la cabecera del formulario -->
  <title>Registar Clase</title>
</head>
<body>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario --> 
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Clase</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
  <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
            </div>

            <div class="form-group">
              <label for="ClaseAso" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Clase Asociada:</label>
              <select name="ClaseAso" id="ClaseAso" class="form-control" title="Seleccione la clase asociada" >
              <option value="">Clase Asociada</option>
                <?php 
                 for ($i=0; $i <count($row) ; $i++) {?>
                <option value="<?php echo $row[$i][0] ?>"><?php echo ucwords(mb_strtolower($row[$i][1]));}?></option>;
              </select> 
            </div> 
            
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>

            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->          
        </div>     
    </div>
  </div>
    <!-- Fin del Contenedor principal -->  
</div>

<script>
                            function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarClaseJson.php?action=2",
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
                                       
                                            document.location='listar_GF_CLASE.php';
                                       
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
    <?php require_once 'footer.php'; ?>
  </div>
</body>
</html>

