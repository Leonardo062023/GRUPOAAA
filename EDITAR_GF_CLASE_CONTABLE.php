<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes y se agrego la busqueda rápido en el select

require_once('Conexion/conexion.php');
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
//session_start();

$id_clase = " ";
if (isset($_GET["id_clase"])){ 
  $id_clase = (($_GET["id_clase"]));

  switch ($_SESSION['conexion']) {
    case '1':
      $row = $con->Listar("SELECT Cl.Id_Unico, Cl.Nombre,Cl.claseaso FROM gf_clase_contable Cl
      WHERE (Cl.Id_Unico) = '$id_clase'");
    break;
    case '2':
      $row = $con->Listar("SELECT Cl.Id_Unico, Cl.Nombre,Cl.claseaso FROM gf_clase_contable Cl
      WHERE (Cl.Id_Unico) = '$id_clase'");
  break;
  }
}
require_once ('head.php');
?>
<title> Modificar Clase Contable</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Clase Contable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

      <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

        <?php for ($i=0; $i <count($row) ; $i++) {?>
          
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[$i][1] ?>" required>
            </div>   
          
            <div class="form-group">
              <label for="claseC" class="col-sm-5 control-label">Clase asociada:</label>
              <select name="claseaso" id="claseaso" class="select2_single form-control" title="Seleccione clase asociada" > 

                      
              <?php    
                    if(!empty($row[$i][2])){
                      $idClaseC=$row[$i][2]; 
                      $idClaseCon=$row[$i][0];
                      $rows = $con->Listar("SELECT Id_Unico, Nombre FROM gf_clase_contable 
                      WHERE Id_Unico = '$idClaseC'");

                      for ($e=0; $e <count($rows) ; $e++){ ?>
                         <option value="<?php echo $rows[$e][0] ?>"><?php echo $rows[$e][1]; }?></option>
                      
                      <?php   
                       $rowss = $con->Listar("SELECT Id_Unico, Nombre FROM gf_clase_contable 
                       WHERE Id_Unico != '$idClaseC' AND Id_Unico != '$idClaseCon'");
                       
                       for ($o=0; $o <count($rowss) ; $o++){  ?>  
                        <option value="<?php echo $rowss[$o][0] ?>"><?php echo $rowss[$o][1]; }?></option> 
                        <option value=""></option>
                        <?php   
                  }else{ ?> 
                        <option value="">Seleccione la clase asociada</option>
                        <?php 
                         $idClaseCon=$row[$i][0];
                       $rowsss = $con->Listar("SELECT id_unico,nombre FROM gf_clase_contable 
                       WHERE id_unico != $idClaseCon ORDER BY nombre ASC");
                      for ($u=0; $u <count($rowsss) ; $u++){  ?> 
                            <option value="<?php echo $rowsss[$u][0] ?>"><?php echo $rowsss[$u][1]; }?></option> 
                            <?php   
                  } ?>  
              </select> 
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


  <?php require_once 'footer.php';  ?>
  <script src="js/select/select2.full.js"></script>
  <script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarClaseContableJson.php?action=3",
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
                                       
                                            document.location='LISTAR_GF_CLASE_CONTABLE.php';
                                       
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
