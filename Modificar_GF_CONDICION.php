<?php 
#05/04/2017 --- Nestor B --- se agregó el atributo mb para que tome las tildes

 require_once 'head.php';
 require_once('Conexion/conexionPDO.php');
 $con = new ConexionPDO();


//consulta para cargar la informacion guardada con ese id
$id = " ";
$queryCond="";
if (isset($_GET["id_cond"])){ 
  $id = (($_GET["id_cond"]));

  switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT C.Id_Unico, C.Nombre, C.TipoDato, TD.Nombre FROM gf_condicion C, gf_tipo_dato TD
      WHERE C.TipoDato = TD.Id_Unico AND (C.Id_Unico) = '$id'");
    break;
    case '2':
      $row = $con->Listar("SELECT C.Id_Unico, C.Nombre, C.TipoDato, TD.Nombre FROM gf_condicion C, gf_tipo_dato TD
      WHERE C.TipoDato = TD.Id_Unico AND (C.Id_Unico) = '$id'");
  break;
  }  
}
?>
  <!--Titulo de la página-->

<title>Modificar Condición</title>
<link href="css/select/select2.min.css" rel="stylesheet">
        
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
    
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
      <!--titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Condición</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

      <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

          <?php for ($i=0; $i <count($row) ; $i++) {?>

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">

              <!--Cargar los datos para la modificación-->
            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower( $row[$i][1])); ?>" required>
            </div>

            <div class="form-group">
              <label for="TipoDato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Dato:</label>
              <select name="TipoDato" id="TipoDato" class="select2_single form-control " title="Seleccione el tipo dato"   required>
                <option value="<?php echo $row[$i][2] ?>"><?php echo ucwords(mb_strtolower( $row[$i][3] ));?></option>
                <?php 
                $idTipoDato=$row[$i][2];
                $rowsss = $con->Listar("SELECT Id_Unico, Nombre FROM gf_tipo_dato WHERE Id_Unico != $idTipoDato ORDER BY Nombre ASC");
               for ($u=0; $u <count($rowsss) ; $u++){  ?> 
               
                <option value="<?php echo $rowsss[$u][0] ?>"><?php echo ucwords((mb_strtolower($rowsss[$u][1])));}?></option>;
              </select> 
            </div>
          
            
            
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
            
            <?php } ?>
          </form>
        </div>

      
      
    </div>

  </div>
</div>
<script src="js/select/select2.full.js"></script>
<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarCondicion.php?action=3",
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
                                       
                                            document.location='listar_GF_CONDICION.php';
                                       
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
        

<?php  require_once 'footer.php';?>
</body>
</html>
