<?php 
require_once 'head.php'; 
	//llamado de la clase conexión
	require_once 'Conexion/conexion.php';
	//Creación de la session
	
	//llamado de la cabeza
	
?>
	<!-- Titulo de formulario -->
	<title>Registrar Tipo Cuenta</title>
	<!-- Fin de titullo -->
</head>
<body>
	<!-- Inicio de Contenedor principal -->
	<div class="container-fluid text-center">
	<!--  Inicio de contenido-->
		<div class="row content">
			<?php require_once 'menu.php'; ?>

			<!-- Inicio de Formulario -->
			<div class="col-sm-10 text-left">
				
				<!-- Inicio de Titulo -->
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"">Registrar Tipo Cuenta</h2>
				<!-- Fin de Titulo -->

				<!-- Inicio de contenedor de formulario -->
				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

					<!-- Inicio de formulario -->
					<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
	
						<!-- Inicio de párrafo de campos obligatorios -->
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<!-- Fin de parrafo de campos obligatorios -->

						<!-- Inicio de campo de texto -->
						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" name="nombre" id="nombre" class="form-control" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Nombre">
						</div>
						<!-- Fin de campo de  texto -->
							<!-- Inicio de campo de nomina E -->
							<div class="form-group" style="margin-top: -10px;">
							<label for="nominaE" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Equivalente Nómina Electrónica:
							</label>
							<input type="text" name="nominaE" id="nominaE" class="form-control" title="Ingrese el codigo de nomina electronica" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Codigo Nomina Electronica">
						</div>
						<!-- Fin de campo de  nomina E -->

						<!-- Inicio de Bóton de guardado -->
						<div class="form-group" style="margin-top: 10px;">
             				 <label for="no" class="col-sm-5 control-label"></label>
               				 <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
          				  </div>


						<!-- Fin de Bóton de guardado -->

						<input type="hidden" name="MM_insert">

					</form>
					<!-- Fin de formulario -->
				</div>
				<!-- Fin de contenedor de formulario -->
			</div>		
		<!-- Fin de contenido -->
		</div>
		<!-- Fin de Contenedor Principal -->
	</div>
	<!-- llamado de pie de pagina -->
	<script>
                            function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipoCuentaJson.php?action=2",
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
                                       
                                            document.location='LISTAR_GF_TIPO_CUENTA.php';
                                       
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