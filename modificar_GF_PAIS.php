<?php 
require_once 'head.php';
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();



//Captura de ID y consulta del resgistro correspondiente.
$id_pais = " ";
if (isset($_GET["id_pais"])){ 
  $id_pais = (($_GET["id_pais"]));

  switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT id_unico, nombre FROM gf_pais WHERE (id_unico) = '$id_pais'");
    break;
    case '2':
      $row = $con->Listar("SELECT id_unico, nombre FROM gf_pais WHERE (id_unico) = '$id_pais'");
  break;
  }
  $queryPais = ""; 

}
?>

  <title>Modificar Pais</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
                <?php require_once ('menu.php'); ?>
  
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Modificar Pais</h2>
      <!--volver-->
      <a href="listar_GF_PAIS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:96.5%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><span>Pais:<?php echo $row[1] ?></span></h5> 
      <!---->
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

      <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
          <?php for ($i=0; $i <count($row) ; $i++) {?>
  
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">
          

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required value="<?php echo $row[$i][1] ?>">
            </div>
         
            
            <div class="form-group" style="margin-top: 10px;">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >

            <?php } ?>
          </form>
        </div>

    </div>

  </div>
</div>
<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificar_GF_Pais_Json.php?action=3",
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
                                       
                                            document.location='listar_GF_PAIS.php';
                                       
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

