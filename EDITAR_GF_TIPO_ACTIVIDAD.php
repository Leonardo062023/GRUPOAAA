<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once('Conexion/conexionPDO.php');
$con = new ConexionPDO();
	//session_start();

	require_once 'head.php'; 

	$id = "";

	if(isset($_GET["id"])){

		$id = $_GET["id"];
		switch ($_SESSION['conexion']) {
			case '1':
			  $row = $con->Listar("SELECT id_unico,codigo_actividad,nombre FROM gf_tipo_actividad WHERE (id_unico) = '$id'");
			break;
			case '2':
			  $row = $con->Listar("SELECT id_unico,codigo_actividad,nombre FROM gf_tipo_actividad WHERE (id_unico) = '$id'");
		  break;
		  }
		}
	require_once 'head.php';
?>
	<title>Modificar Tipo Actividad</title>
</head>
<body>
	<div class="container-fluid text-center">
		<div class="row content">
			<?php require_once 'menu.php'; ?>

			<!-- Inicio de Formulario -->
			<div class="col-sm-10 text-left">
				
				<!-- Inicio de Titulo -->
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"> Modificar Tipo Actividad</h2>
				<!-- Fin de Titulo -->

				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
					
				<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">

					<?php for ($i=0; $i <count($row) ; $i++) {?>

						<input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">
						
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
                                                
                                                <div class="form-group" style="margin-top: -10px;">
							<label for="codigoActividad" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Codigo Actividad:
							</label>
							<input type="text" name="codigoActividad" maxlength="100" id="codigoActividad" class="form-control" title="Ingrese código Actividad" value="<?php echo ucwords(  ($row[$i][1])); ?>" onkeypress="return txtValida(event,'num')" placeholder="Código Actividad">
						</div>
                                                
						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" value="<?php echo ucwords(mb_strtolower($row[$i][2])); ?>" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
						</div>


						<div class="form-group" style="margin-top: 10px;">
				            <label for="no" class="col-sm-5 control-label"></label>
                			<button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
						</div>

						<input type="hidden" name="MM_insert">
  						<?php } ?>
					</form>

				</div>
			</div>
			<!-- Fin de Formulario -->

		</div>
	</div>
	
<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipoActividadJson.php?action=3",
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
                                       
                                            document.location='LISTAR_GF_TIPO_ACTIVIDAD.php';
                                       
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